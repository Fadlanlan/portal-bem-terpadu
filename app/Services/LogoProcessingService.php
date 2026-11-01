<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use InvalidArgumentException;

class LogoProcessingService
{
    /**
     * Batas ukuran maksimum berkas gambar masukan dalam byte (2 MB).
     */
    public const MAX_FILE_SIZE = 2 * 1024 * 1024;

    /**
     * Dimensi batas toleransi keamanan untuk mencegah dekompresi ekstrem.
     */
    public const MIN_DIMENSION = 1;
    public const MAX_DIMENSION = 4000;

    /**
     * MIME types yang sah dan didukung sistem.
     */
    public const ALLOWED_MIMES = [
        'image/png' => 'png',
        'image/jpeg' => 'jpeg',
        'image/jpg' => 'jpeg',
        'image/webp' => 'webp',
    ];

    /**
     * Validasi integritas, tipe MIME, dan dimensi berkas gambar.
     *
     * @throws InvalidArgumentException
     */
    public static function validateImage(string $filePath): array
    {
        if (! file_exists($filePath)) {
            throw new InvalidArgumentException('Berkas gambar tidak ditemukan atau tidak dapat dibaca.');
        }

        // Gunakan filesize sebagai pengganti is_readable karena is_readable()
        // tidak reliable pada lingkungan Windows (selalu false di beberapa konfigurasi).
        $fileSize = @filesize($filePath);
        if ($fileSize === false || $fileSize === 0) {
            throw new InvalidArgumentException('Berkas gambar kosong (0 byte) atau tidak dapat dibaca.');
        }

        if ($fileSize > self::MAX_FILE_SIZE) {
            throw new InvalidArgumentException('Ukuran berkas melebihi batas maksimum 2MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        if (! array_key_exists($mime, self::ALLOWED_MIMES)) {
            throw new InvalidArgumentException('Format berkas tidak didukung. Harap unggah berkas PNG, JPEG, atau WebP.');
        }

        $imageInfo = @getimagesize($filePath);
        if ($imageInfo === false || empty($imageInfo[0]) || empty($imageInfo[1])) {
            throw new InvalidArgumentException('Berkas gambar rusak atau tidak valid.');
        }

        [$width, $height] = $imageInfo;

        if ($width < self::MIN_DIMENSION || $height < self::MIN_DIMENSION) {
            throw new InvalidArgumentException("Resolusi gambar terlalu kecil (minimal " . self::MIN_DIMENSION . "x" . self::MIN_DIMENSION . " piksel).");
        }

        if ($width > self::MAX_DIMENSION || $height > self::MAX_DIMENSION) {
            throw new InvalidArgumentException("Resolusi gambar terlalu besar (maksimal " . self::MAX_DIMENSION . "x" . self::MAX_DIMENSION . " piksel).");
        }

        return [
            'width' => $width,
            'height' => $height,
            'mime' => $mime,
            'size' => $fileSize,
        ];
    }

    /**
     * Jalankan pipeline penghapusan background, pemangkasan ruang kosong, dan perapian logo.
     *
     * @param string $inputPath Path berkas masukan
     * @param string $outputPath Path berkas hasil keluaran (PNG)
     * @param array<string, mixed> $options
     * @return array<string, mixed> Metadata hasil pemrosesan
     * @throws Exception
     */
    public static function process(string $inputPath, string $outputPath, array $options = []): array
    {
        self::validateImage($inputPath);

        $outputDir = dirname($outputPath);
        if (! is_dir($outputDir)) {
            @mkdir($outputDir, 0755, true);
        }

        if (extension_loaded('gd')) {
            return self::executeProcessing($inputPath, $outputPath, $options);
        }

        return self::executeViaCli($inputPath, $outputPath, $options);
    }

    /**
     * Eksekusi langsung pemrosesan gambar menggunakan ekstensi GD PHP.
     */
    public static function executeProcessing(string $inputPath, string $outputPath, array $options = []): array
    {
        $removeBg = (bool) ($options['remove_bg'] ?? true);
        $tolerance = (int) ($options['tolerance'] ?? 28);
        $padding = (int) ($options['padding'] ?? 16);
        $feather = (int) ($options['feather'] ?? 2);

        $imageInfo = getimagesize($inputPath);
        $mime = $imageInfo['mime'] ?? 'image/png';

        $source = match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($inputPath),
            'image/webp' => @imagecreatefromwebp($inputPath),
            default => @imagecreatefrompng($inputPath),
        };

        if (! $source) {
            throw new Exception('Gagal memuat data piksel berkas gambar.');
        }

        $origW = imagesx($source);
        $origH = imagesy($source);

        // Siapkan canvas truecolor 32-bit dengan alpha channel
        $canvas = imagecreatetruecolor($origW, $origH);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        // Salin sumber ke canvas
        imagecopy($canvas, $source, 0, 0, 0, 0, $origW, $origH);
        imagedestroy($source);

        $bgColorHex = '#FFFFFF';
        $isBackgroundPixel = array_fill(0, $origH, array_fill(0, $origW, false));

        if ($removeBg) {
            // Sampel warna background dari 4 sudut terluar dan titik tengah tepi
            $samplePoints = [
                [0, 0],
                [$origW - 1, 0],
                [0, $origH - 1],
                [$origW - 1, $origH - 1],
                [(int) ($origW / 2), 0],
                [(int) ($origW / 2), $origH - 1],
                [0, (int) ($origH / 2)],
                [$origW - 1, (int) ($origH / 2)],
            ];

            $rSum = 0; $gSum = 0; $bSum = 0; $validSamples = 0;
            foreach ($samplePoints as $pt) {
                $rgb = imagecolorat($canvas, $pt[0], $pt[1]);
                $a = ($rgb >> 24) & 0x7F;
                // Jika piksel sudah transparan, abaikan dari rata-rata
                if ($a < 120) {
                    $rSum += ($rgb >> 16) & 0xFF;
                    $gSum += ($rgb >> 8) & 0xFF;
                    $bSum += $rgb & 0xFF;
                    $validSamples++;
                }
            }

            if ($validSamples > 0) {
                $bgR = (int) round($rSum / $validSamples);
                $bgG = (int) round($gSum / $validSamples);
                $bgB = (int) round($bSum / $validSamples);
            } else {
                $bgR = 255; $bgG = 255; $bgB = 255;
            }

            $bgColorHex = sprintf('#%02X%02X%02X', $bgR, $bgG, $bgB);

            // Boundary-Connected Flood Fill (BFS) dari 4 tepi terluar
            $visited = array_fill(0, $origH, array_fill(0, $origW, false));
            $queue = new \SplQueue();

            // Masukkan seluruh perimeter terluar yang cocok dengan warna background
            for ($x = 0; $x < $origW; $x++) {
                foreach ([0, $origH - 1] as $y) {
                    $rgb = imagecolorat($canvas, $x, $y);
                    $dist = self::colorDistance($rgb, $bgR, $bgG, $bgB);
                    if ($dist <= $tolerance && ! $visited[$y][$x]) {
                        $visited[$y][$x] = true;
                        $isBackgroundPixel[$y][$x] = true;
                        $queue->enqueue([$x, $y]);
                    }
                }
            }

            for ($y = 0; $y < $origH; $y++) {
                foreach ([0, $origW - 1] as $x) {
                    $rgb = imagecolorat($canvas, $x, $y);
                    $dist = self::colorDistance($rgb, $bgR, $bgG, $bgB);
                    if ($dist <= $tolerance && ! $visited[$y][$x]) {
                        $visited[$y][$x] = true;
                        $isBackgroundPixel[$y][$x] = true;
                        $queue->enqueue([$x, $y]);
                    }
                }
            }

            // Jalankan penjelajahan flood-fill
            $directions = [[1, 0], [-1, 0], [0, 1], [0, -1]];
            while (! $queue->isEmpty()) {
                [$cx, $cy] = $queue->dequeue();

                foreach ($directions as $dir) {
                    $nx = $cx + $dir[0];
                    $ny = $cy + $dir[1];

                    if ($nx >= 0 && $nx < $origW && $ny >= 0 && $ny < $origH && ! $visited[$ny][$nx]) {
                        $visited[$ny][$nx] = true;
                        $rgb = imagecolorat($canvas, $nx, $ny);
                        $dist = self::colorDistance($rgb, $bgR, $bgG, $bgB);

                        if ($dist <= $tolerance) {
                            $isBackgroundPixel[$ny][$nx] = true;
                            $queue->enqueue([$nx, $ny]);
                        }
                    }
                }
            }

            // Terapkan transparansi dan perapian tepi (feathering)
            $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
            for ($y = 0; $y < $origH; $y++) {
                for ($x = 0; $x < $origW; $x++) {
                    if ($isBackgroundPixel[$y][$x]) {
                        imagesetpixel($canvas, $x, $y, $transparent);
                    } elseif ($feather > 0) {
                        // Periksa apakah piksel bertetangga langsung dengan background untuk anti-aliasing
                        $hasAdjacentBg = false;
                        if (($x > 0 && $isBackgroundPixel[$y][$x - 1])
                            || ($x < $origW - 1 && $isBackgroundPixel[$y][$x + 1])
                            || ($y > 0 && $isBackgroundPixel[$y - 1][$x])
                            || ($y < $origH - 1 && $isBackgroundPixel[$y + 1][$x])
                        ) {
                            $hasAdjacentBg = true;
                        }

                        if ($hasAdjacentBg) {
                            $dist = self::colorDistance(imagecolorat($canvas, $x, $y), $bgR, $bgG, $bgB);
                            if ($dist <= ($tolerance + ($feather * 8))) {
                                $rgb = imagecolorat($canvas, $x, $y);
                                $r = ($rgb >> 16) & 0xFF;
                                $g = ($rgb >> 8) & 0xFF;
                                $b = $rgb & 0xFF;
                                $origA = ($rgb >> 24) & 0x7F;

                                // Alpha blending halus (0 = solid, 127 = transparan)
                                $alphaRatio = max(0, min(1, ($dist - $tolerance) / ($feather * 8)));
                                $targetAlpha = (int) round(127 - ($alphaRatio * (127 - $origA)));

                                $featherColor = imagecolorallocatealpha($canvas, $r, $g, $b, max($origA, $targetAlpha));
                                imagesetpixel($canvas, $x, $y, $featherColor);
                            }
                        }
                    }
                }
            }
        }

        // Hitung bounding box foreground (piksel tidak transparan)
        $minX = $origW; $maxX = 0; $minY = $origH; $maxY = 0;
        $foregroundPixels = 0;

        for ($y = 0; $y < $origH; $y++) {
            for ($x = 0; $x < $origW; $x++) {
                $rgb = imagecolorat($canvas, $x, $y);
                $a = ($rgb >> 24) & 0x7F;

                // Jika bukan transparan penuh
                if ($a < 124) {
                    $minX = min($minX, $x);
                    $maxX = max($maxX, $x);
                    $minY = min($minY, $y);
                    $maxY = max($maxY, $y);
                    $foregroundPixels++;
                }
            }
        }

        // Jika gambar tidak memiliki foreground sama sekali
        if ($foregroundPixels === 0 || $minX > $maxX || $minY > $maxY) {
            $minX = 0; $maxX = $origW - 1;
            $minY = 0; $maxY = $origH - 1;
        }

        $fgW = $maxX - $minX + 1;
        $fgH = $maxY - $minY + 1;

        // Pangkas (Trim) dan tambahkan padding proporsional
        $finalW = $fgW + (2 * $padding);
        $finalH = $fgH + (2 * $padding);

        $finalCanvas = imagecreatetruecolor($finalW, $finalH);
        imagealphablending($finalCanvas, false);
        imagesavealpha($finalCanvas, true);

        $fullTrans = imagecolorallocatealpha($finalCanvas, 0, 0, 0, 127);
        imagefill($finalCanvas, 0, 0, $fullTrans);

        imagecopy($finalCanvas, $canvas, $padding, $padding, $minX, $minY, $fgW, $fgH);
        imagedestroy($canvas);

        imagepng($finalCanvas, $outputPath, 8);
        imagedestroy($finalCanvas);

        $finalSize = file_exists($outputPath) ? filesize($outputPath) : 0;
        $origArea = $origW * $origH;
        $finalArea = $finalW * $finalH;
        $trimmedPercent = $origArea > 0 ? max(0, round((($origArea - $finalArea) / $origArea) * 100, 1)) : 0;

        return [
            'success' => true,
            'original_width' => $origW,
            'original_height' => $origH,
            'trimmed_width' => $finalW,
            'trimmed_height' => $finalH,
            'aspect_ratio' => round($finalW / max(1, $finalH), 3),
            'bg_color' => $bgColorHex,
            'whitespace_trimmed_percent' => $trimmedPercent,
            'foreground_pixels' => $foregroundPixels,
            'file_size' => $finalSize,
        ];
    }

    /**
     * Hitung jarak Euclidean dalam ruang warna RGB.
     */
    private static function colorDistance(int $rgb, int $targetR, int $targetG, int $targetB): float
    {
        $a = ($rgb >> 24) & 0x7F;
        if ($a >= 120) {
            return 0.0; // Jika sudah transparan, anggap cocok dengan background
        }

        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;

        return sqrt(($r - $targetR) ** 2 + ($g - $targetG) ** 2 + ($b - $targetB) ** 2);
    }

    /**
     * Resolusi path binary PHP CLI yang valid (mengutamakan PHP XAMPP).
     */
    public static function resolvePhpBinary(): string
    {
        $xamppPhp = 'C:\\xampp\\php\\php.exe';
        if (file_exists($xamppPhp) && is_executable($xamppPhp)) {
            return $xamppPhp;
        }

        if (defined('PHP_BINARY') && PHP_BINARY) {
            $bin = PHP_BINARY;
            $lower = strtolower($bin);
            if (str_ends_with($lower, 'php.exe') && file_exists($bin)) {
                return $bin;
            }
        }

        return 'php';
    }

    /**
     * Resolusi path php.ini yang sah dan aktif.
     */
    public static function resolvePhpIni(): ?string
    {
        $loaded = php_ini_loaded_file();
        if ($loaded && file_exists($loaded)) {
            return $loaded;
        }

        $xamppIni = 'C:\\xampp\\php\\php.ini';
        if (file_exists($xamppIni)) {
            return $xamppIni;
        }

        return null;
    }

    /**
     * Eksekusi melalui sub-proses PHP CLI jika GD belum dimuat pada runtime utama.
     */
    private static function executeViaCli(string $inputPath, string $outputPath, array $options = []): array
    {
        $phpBinary = self::resolvePhpBinary();
        $phpIni = self::resolvePhpIni();

        $args = [
            $phpBinary,
        ];

        if ($phpIni) {
            $args[] = '-c';
            $args[] = $phpIni;
        }

        // Konfigurasi extension_dir eksplisit ke C:\xampp\php\ext jika direktori tersedia
        $extDir = 'C:\\xampp\\php\\ext';
        if (is_dir($extDir)) {
            $args[] = '-dextension_dir=' . $extDir;
        }

        // Muat GD secara dinamis tanpa duplikasi cURL
        $args[] = '-dextension=gd';

        $args[] = base_path('artisan');
        $args[] = 'logo:process-image';
        $args[] = $inputPath;
        $args[] = $outputPath;

        if (isset($options['tolerance'])) {
            $args[] = '--tolerance=' . (int) $options['tolerance'];
        }
        if (isset($options['padding'])) {
            $args[] = '--padding=' . (int) $options['padding'];
        }
        if (isset($options['feather'])) {
            $args[] = '--feather=' . (int) $options['feather'];
        }
        if (isset($options['remove_bg']) && ! $options['remove_bg']) {
            $args[] = '--no-remove-bg';
        }

        // Prioritaskan C:\xampp\php pada PATH agar library OpenSSL yang cocok digunakan
        $env = [
            'PATH' => 'C:\\xampp\\php;' . (getenv('PATH') ?: ''),
            'SystemRoot' => getenv('SystemRoot') ?: 'C:\\Windows',
            'WINDIR' => getenv('WINDIR') ?: 'C:\\Windows',
        ];

        $process = Process::env($env)->timeout(30)->run($args);

        if (! $process->successful()) {
            Log::error('Logo processing CLI sub-process failed', [
                'exit_code' => $process->exitCode(),
                'stderr' => $process->errorOutput(),
                'stdout' => $process->output(),
                'args' => $args,
            ]);

            throw new Exception('Gagal memproses gambar logo secara otomatis melalui sub-proses.');
        }

        $output = trim($process->output());
        $json = json_decode($output, true);

        if (! is_array($json) || empty($json['success'])) {
            Log::error('Logo processing CLI sub-process returned invalid response', [
                'output' => $output,
                'stderr' => $process->errorOutput(),
            ]);

            throw new Exception('Respon pemrosesan gambar logo tidak valid.');
        }

        return $json;
    }
}
