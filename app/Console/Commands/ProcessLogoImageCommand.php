<?php

namespace App\Console\Commands;

use App\Services\LogoProcessingService;
use Illuminate\Console\Command;
use Throwable;

class ProcessLogoImageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logo:process-image 
                            {input : Path berkas gambar masukan} 
                            {output : Path berkas hasil pemrosesan} 
                            {--tolerance=28 : Toleransi jarak Euclidean warna background (0-100)} 
                            {--padding=16 : Ukuran padding transparan di sekeliling logo (piksel)} 
                            {--feather=2 : Tingkat kehalusan tepi anti-aliasing} 
                            {--no-remove-bg : Lewati penghapusan background, hanya lakukan perapian/pangkas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Proses segmentasi foreground logo, penghapusan background, dan pemangkasan ruang kosong';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $input = $this->argument('input');
        $output = $this->argument('output');

        $options = [
            'tolerance' => (int) $this->option('tolerance'),
            'padding' => (int) $this->option('padding'),
            'feather' => (int) $this->option('feather'),
            'remove_bg' => ! $this->option('no-remove-bg'),
        ];

        try {
            $result = LogoProcessingService::executeProcessing($input, $output, $options);
            $this->output->write(json_encode($result));

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
