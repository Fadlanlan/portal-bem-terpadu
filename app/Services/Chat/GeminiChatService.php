<?php

namespace App\Services\Chat;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GeminiChatService
{
    public function isConfigured(): bool
    {
        return filled(config('services.gemini.api_key'))
            && filled(config('services.gemini.model'))
            && filled(config('services.gemini.base_url'));
    }

    /**
     * @param  list<array{role: 'user'|'model', text: string}>  $history
     * @return array{text: string}
     */
    public function generateText(string $prompt, array $history = []): array
    {
        if (! $this->isConfigured()) {
            throw new GeminiConfigurationException('Konfigurasi Gemini belum lengkap.');
        }

        $apiKey = (string) config('services.gemini.api_key');
        $model = (string) config('services.gemini.model');
        $baseUrl = rtrim((string) config('services.gemini.base_url'), '/');
        $timeout = (int) config('services.gemini.timeout', 30);

        $url = $baseUrl.'/models/'.rawurlencode($model).':generateContent';
        $startedAt = hrtime(true);

        Log::info('Gemini request started', ['model' => $model]);

        $contents = [];
        foreach ($history as $turn) {
            if (! in_array($turn['role'] ?? null, ['user', 'model'], true) || ! is_string($turn['text'] ?? null) || trim($turn['text']) === '') {
                continue;
            }

            $contents[] = [
                'role' => $turn['role'],
                'parts' => [['text' => $turn['text']]],
            ];
        }
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $prompt]],
        ];
        $generationConfig = ['maxOutputTokens' => 1024];
        $isGeminiThreeOrLater = preg_match('/^gemini-(?:[3-9])(?:\.|-)/', $model) === 1;

        if ($isGeminiThreeOrLater) {
            $generationConfig['thinkingConfig'] = ['thinkingLevel' => 'low'];
        } else {
            $generationConfig['temperature'] = 0.3;
        }

        try {
            $response = Http::timeout($timeout)
                ->connectTimeout(min($timeout, 10))
                ->retry(
                    2,
                    250,
                    fn (\Throwable $exception): bool => $exception instanceof ConnectionException
                        || ($exception instanceof RequestException && $exception->response->serverError()),
                    throw: false,
                )
                ->withHeaders([
                    'x-goog-api-key' => $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'contents' => $contents,
                    'generationConfig' => $generationConfig,
                ]);
        } catch (ConnectionException $e) {
            Log::warning('Gemini request failed', [
                'model' => $model,
                'http_status' => null,
                'duration_ms' => (int) ((hrtime(true) - $startedAt) / 1_000_000),
                'failure_category' => 'network_or_timeout',
            ]);
            throw new GeminiServiceException('Layanan AI tidak dapat dihubungi saat ini.', 503);
        }

        $status = $response->status();
        $duration = (int) ((hrtime(true) - $startedAt) / 1_000_000);

        if ($status === 429) {
            $this->logFailure($model, $status, $duration, 'quota_or_rate_limit');
            throw new GeminiServiceException('Kuota atau batas permintaan AI sedang habis. Coba lagi nanti.', 429);
        }

        if (in_array($status, [401, 403], true)) {
            $this->logFailure($model, $status, $duration, 'invalid_api_key_or_permission');
            throw new GeminiServiceException('Konfigurasi AI tidak valid. Hubungi administrator.', 503);
        }

        if ($status === 400 && $this->isInvalidApiKeyResponse($response)) {
            $this->logFailure($model, $status, $duration, 'invalid_api_key');
            throw new GeminiServiceException('Konfigurasi AI tidak valid. Hubungi administrator.', 503);
        }

        if ($status === 404) {
            $this->logFailure($model, $status, $duration, 'model_not_found');
            throw new GeminiServiceException('Model AI yang dikonfigurasi tidak tersedia.', 503);
        }

        if ($status >= 500) {
            $this->logFailure($model, $status, $duration, 'provider_server_error');
            throw new GeminiServiceException('Layanan AI sedang gangguan. Coba lagi nanti.', 503);
        }

        if (! $response->successful()) {
            $this->logFailure($model, $status, $duration, 'api_request_error');
            throw new GeminiServiceException('Layanan AI mengembalikan respons yang tidak dapat diproses.', 502);
        }

        $text = $this->extractText($response);
        if ($text === null) {
            $this->logFailure($model, $status, $duration, 'response_without_text');
            throw new GeminiServiceException('Respons AI tidak valid.', 502);
        }

        Log::info('Gemini request completed', [
            'model' => $model,
            'http_status' => $status,
            'duration_ms' => $duration,
            'failure_category' => null,
        ]);

        return ['text' => $text];
    }

    private function logFailure(string $model, int $status, int $duration, string $category): void
    {
        Log::warning('Gemini request failed', [
            'model' => $model,
            'http_status' => $status,
            'duration_ms' => $duration,
            'failure_category' => $category,
        ]);
    }

    private function isInvalidApiKeyResponse(Response $response): bool
    {
        $error = $response->json('error');
        if (! is_array($error)) {
            return false;
        }

        $status = strtolower((string) ($error['status'] ?? ''));
        $message = strtolower((string) ($error['message'] ?? ''));

        return str_contains($status, 'api_key_invalid')
            || str_contains($message, 'api key not valid')
            || str_contains($message, 'invalid api key');
    }

    private function extractText(Response $response): ?string
    {
        $json = $response->json();
        if (! is_array($json)) {
            return null;
        }

        $candidates = $json['candidates'] ?? null;
        if (! is_array($candidates) || empty($candidates)) {
            return null;
        }

        $texts = [];
        foreach ($candidates as $candidate) {
            $parts = is_array($candidate) ? ($candidate['content']['parts'] ?? null) : null;
            if (! is_array($parts)) {
                continue;
            }

            foreach ($parts as $part) {
                if (is_array($part) && isset($part['text']) && is_string($part['text'])) {
                    $texts[] = $part['text'];
                }
            }
        }

        $joined = trim(implode("\n", $texts));

        return $joined === '' ? null : $joined;
    }
}

class GeminiConfigurationException extends RuntimeException {}
class GeminiServiceException extends RuntimeException {}
