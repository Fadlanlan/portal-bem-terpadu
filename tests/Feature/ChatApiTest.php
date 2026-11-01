<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_flow_returns_ai_reply_and_sources(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'Jawaban dari Gemini']]]],
                ],
            ], 200),
        ]);

        config(['services.gemini' => [
            'api_key' => 'test-key',
            'model' => 'test-model',
            'base_url' => 'https://example.test/v1beta',
            'timeout' => 5,
        ]]);

        Article::create([
            'title' => 'Info UKT',
            'slug' => 'info-ukt',
            'excerpt' => 'Informasi UKT',
            'content' => 'Pembayaran UKT',
            'category' => 'Akademik',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'author_name' => 'BEM',
        ]);

        $create = $this->postJson('/chat/conversations', ['identity_mode' => 'account']);
        $create->assertOk();
        $conversationId = $create->json('conversation_id');

        $message = $this->postJson('/chat/conversations/'.$conversationId.'/messages', [
            'body' => 'info UKT',
        ]);
        $message->assertOk();
        $message->assertJsonPath('ai_message.body', 'Jawaban dari Gemini');
        $this->assertNotEmpty($message->json('ai_message.sources'));
    }

    public function test_greeting_reply_from_gemini_is_returned_to_widget_endpoint(): void
    {
        $this->actingAs(User::factory()->create());
        Http::fake(['*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Hai! Ada yang bisa saya bantu?']]]]],
        ], 200)]);
        config(['services.gemini' => [
            'api_key' => 'test-key',
            'model' => 'my-configured-model',
            'base_url' => 'https://example.test/v1beta',
            'timeout' => 5,
        ]]);

        $create = $this->postJson('/chat/conversations', ['identity_mode' => 'account']);
        $conversationId = $create->json('conversation_id');
        $response = $this->postJson('/chat/conversations/'.$conversationId.'/messages', ['body' => 'hai']);

        $response->assertOk()->assertJsonPath('ai_message.body', 'Hai! Ada yang bisa saya bantu?');
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/models/my-configured-model:generateContent')
            && $request->hasHeader('x-goog-api-key', 'test-key'));
    }

    public function test_message_endpoint_validates_message_body(): void
    {
        $this->actingAs(User::factory()->create());
        $conversationId = $this->postJson('/chat/conversations', ['identity_mode' => 'account'])
            ->assertOk()
            ->json('conversation_id');

        $this->postJson('/chat/conversations/'.$conversationId.'/messages', ['body' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('body');
    }
}
