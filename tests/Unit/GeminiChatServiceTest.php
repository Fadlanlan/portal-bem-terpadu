<?php

namespace Tests\Unit;

use App\Services\Chat\GeminiChatService;
use App\Services\Chat\GeminiConfigurationException;
use App\Services\Chat\GeminiServiceException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiChatServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['services.gemini' => [
            'api_key' => 'test-secret-key',
            'model' => 'gemini-test-model',
            'base_url' => 'https://example.test/v1beta',
            'timeout' => 5,
        ]]);
    }

    public function test_not_configured_when_key_missing(): void
    {
        config(['services.gemini.api_key' => null]);
        $service = new GeminiChatService;
        $this->assertFalse($service->isConfigured());
    }

    public function test_configured_when_all_present(): void
    {
        $service = new GeminiChatService;
        $this->assertTrue($service->isConfigured());
    }

    public function test_success_extracts_text(): void
    {
        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'Halo dunia']]]],
                ],
            ], 200),
        ]);
        $service = new GeminiChatService;
        $result = $service->generateText('hi');
        $this->assertSame('Halo dunia', $result['text']);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://example.test/v1beta/models/gemini-test-model:generateContent'
                && $request->hasHeader('x-goog-api-key', 'test-secret-key')
                && $request['contents'][0]['parts'][0]['text'] === 'hi'
                && $request['generationConfig']['temperature'] === 0.3;
        });
    }

    public function test_history_is_sent_as_gemini_user_and_model_turns(): void
    {
        Http::fake(['*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Ya, saya ingat.']]]]],
        ], 200)]);

        (new GeminiChatService)->generateText('Apa yang tadi saya tanyakan?', [
            ['role' => 'user', 'text' => 'Jelaskan demokrasi.'],
            ['role' => 'model', 'text' => 'Demokrasi adalah pemerintahan oleh rakyat.'],
        ]);

        Http::assertSent(fn ($request) => $request['contents'][0]['role'] === 'user'
            && $request['contents'][0]['parts'][0]['text'] === 'Jelaskan demokrasi.'
            && $request['contents'][1]['role'] === 'model'
            && $request['contents'][1]['parts'][0]['text'] === 'Demokrasi adalah pemerintahan oleh rakyat.'
            && $request['contents'][2]['parts'][0]['text'] === 'Apa yang tadi saya tanyakan?');
    }

    public function test_gemini_three_uses_low_thinking_without_unsupported_sampling_parameters(): void
    {
        config(['services.gemini.model' => 'gemini-3.8-flash']);
        Http::fake(['*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Hai.']]]]],
        ], 200)]);

        (new GeminiChatService)->generateText('Hai');

        Http::assertSent(fn ($request) => $request['generationConfig']['thinkingConfig']['thinkingLevel'] === 'low'
            && ! isset($request['generationConfig']['temperature']));
    }

    public function test_temporary_provider_failure_is_retried_and_can_succeed(): void
    {
        Http::fake(['*' => Http::sequence()
            ->push(['error' => ['status' => 'UNAVAILABLE']], 503)
            ->push([
                'candidates' => [['content' => ['parts' => [['text' => 'Balasan setelah retry']]]]],
            ], 200)]);

        $result = (new GeminiChatService)->generateText('Hai');

        $this->assertSame('Balasan setelah retry', $result['text']);
        Http::assertSentCount(2);
    }

    public function test_rate_limit_throws(): void
    {
        Http::fake(['*' => Http::response([], 429)]);
        $service = new GeminiChatService;
        try {
            $service->generateText('hi');
            $this->fail('Expected exception');
        } catch (GeminiServiceException $e) {
            $this->assertSame(429, $e->getCode());
        }
    }

    public function test_auth_failure_throws(): void
    {
        Http::fake(['*' => Http::response([], 401)]);
        $service = new GeminiChatService;
        try {
            $service->generateText('hi');
            $this->fail('Expected exception');
        } catch (GeminiServiceException $e) {
            $this->assertSame(503, $e->getCode());
            $this->assertStringNotContainsString('test-secret-key', $e->getMessage());
        }
    }

    public function test_invalid_api_key_response_is_safe(): void
    {
        Http::fake(['*' => Http::response([
            'error' => ['status' => 'API_KEY_INVALID', 'message' => 'API key not valid. Please pass a valid API key.'],
        ], 400)]);

        try {
            (new GeminiChatService)->generateText('hi');
            $this->fail('Expected exception');
        } catch (GeminiServiceException $e) {
            $this->assertSame(503, $e->getCode());
            $this->assertStringNotContainsString('test-secret-key', $e->getMessage());
            $this->assertStringNotContainsString('API_KEY_INVALID', $e->getMessage());
        }
    }

    public function test_network_timeout_throws_safe_service_error(): void
    {
        Http::fake(fn () => throw new ConnectionException('Connection timed out'));

        try {
            (new GeminiChatService)->generateText('hi');
            $this->fail('Expected exception');
        } catch (GeminiServiceException $e) {
            $this->assertSame(503, $e->getCode());
            $this->assertStringNotContainsString('timed out', $e->getMessage());
        }
    }

    public function test_server_error_throws(): void
    {
        Http::fake(['*' => Http::response([], 500)]);
        $service = new GeminiChatService;
        try {
            $service->generateText('hi');
            $this->fail('Expected exception');
        } catch (GeminiServiceException $e) {
            $this->assertSame(503, $e->getCode());
        }

        Http::assertSentCount(2);
    }

    public function test_model_not_found_is_distinguished(): void
    {
        Http::fake(['*' => Http::response([], 404)]);
        try {
            (new GeminiChatService)->generateText('hi');
            $this->fail('Expected exception');
        } catch (GeminiServiceException $e) {
            $this->assertStringContainsString('Model AI', $e->getMessage());
        }
    }

    public function test_extracts_text_from_later_candidate_when_first_has_no_text(): void
    {
        Http::fake(['*' => Http::response([
            'candidates' => [
                ['content' => ['parts' => [['inlineData' => 'ignored']]]],
                ['content' => ['parts' => [['text' => 'Halo']]]],
            ],
        ], 200)]);

        $this->assertSame('Halo', (new GeminiChatService)->generateText('hai')['text']);
    }

    public function test_invalid_response_throws(): void
    {
        Http::fake(['*' => Http::response(['foo' => 'bar'], 200)]);
        $service = new GeminiChatService;
        try {
            $service->generateText('hi');
            $this->fail('Expected exception');
        } catch (GeminiServiceException $e) {
            $this->assertSame(502, $e->getCode());
        }
    }

    public function test_unconfigured_throws_before_request(): void
    {
        config(['services.gemini.api_key' => '']);
        Http::fake();
        $service = new GeminiChatService;
        try {
            $service->generateText('hi');
            $this->fail('Expected exception');
        } catch (GeminiConfigurationException $e) {
            $this->assertTrue(true);
        }
        Http::assertNothingSent();
    }
}
