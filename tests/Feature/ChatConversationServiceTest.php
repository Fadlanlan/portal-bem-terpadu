<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Services\Chat\ChatConversationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatConversationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_send_message_stores_ai_reply_with_sources(): void
    {
        Http::fake([
            '*' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'Jawaban AI']]]],
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

        $service = app(ChatConversationService::class);
        $created = $service->createConversation(['identity_mode' => 'anonymous']);
        $conversation = $created['conversation'];

        $result = $service->sendMessage($conversation, 'info UKT');

        $this->assertNotNull($result['user_message']);
        $this->assertNotNull($result['ai_message']);
        $this->assertSame('Jawaban AI', $result['ai_message']->body);
    }

    public function test_access_denied_for_other_user(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $service = app(ChatConversationService::class);
        $created = $service->createConversation(['identity_mode' => 'account', 'user_id' => $owner->id]);
        $conversation = $created['conversation'];

        $this->assertFalse($service->canAccess($conversation, $other, null));
        $this->assertTrue($service->canAccess($conversation, $owner, null));
    }

    public function test_gemini_failure_does_not_block_the_conversation(): void
    {
        Http::fake(['*' => Http::response([], 500)]);

        config(['services.gemini' => [
            'api_key' => 'test-key',
            'model' => 'test-model',
            'base_url' => 'https://example.test/v1beta',
            'timeout' => 5,
        ]]);

        $service = app(ChatConversationService::class);
        $created = $service->createConversation(['identity_mode' => 'anonymous']);
        $conversation = $created['conversation'];

        $result = $service->sendMessage($conversation, 'Apa jadwal kegiatan BEM?');

        $this->assertNotNull($result['ai_message']);
        $this->assertFalse($result['handoff']);
    }

    public function test_greeting_calls_gemini_without_portal_sources(): void
    {
        Http::fake(['*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Hai! Ada yang bisa saya bantu?']]]]],
        ], 200)]);
        $this->configureGemini();
        $conversation = app(ChatConversationService::class)
            ->createConversation(['identity_mode' => 'anonymous'])['conversation'];

        $result = app(ChatConversationService::class)->sendMessage($conversation, 'hai');

        $this->assertSame('Hai! Ada yang bisa saya bantu?', $result['ai_message']->body);
        $this->assertFalse($result['handoff']);
        Http::assertSent(fn ($request) => str_ends_with($request->url(), '/models/test-model:generateContent')
            && str_contains($request['contents'][0]['parts'][0]['text'], 'Pertanyaan pengguna: hai'));
    }

    public function test_general_question_calls_gemini_without_sources(): void
    {
        Http::fake(['*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Demokrasi adalah...']]]]],
        ], 200)]);
        $this->configureGemini();
        $conversation = app(ChatConversationService::class)
            ->createConversation(['identity_mode' => 'anonymous'])['conversation'];

        $result = app(ChatConversationService::class)->sendMessage($conversation, 'apa itu demokrasi?');

        $this->assertSame('Demokrasi adalah...', $result['ai_message']->body);
        $this->assertFalse($result['handoff']);
        Http::assertSentCount(1);
    }

    public function test_website_information_is_included_in_gemini_prompt_and_sources(): void
    {
        Http::fake(['*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Visi portal adalah membangun iklim kemahasiswaan yang kritis, inklusif, dan transparan.']]]]],
        ], 200)]);
        $this->configureGemini();
        $conversation = app(ChatConversationService::class)
            ->createConversation(['identity_mode' => 'anonymous'])['conversation'];

        $result = app(ChatConversationService::class)->sendMessage($conversation, 'Apa visi dan misi Portal BEM?');

        $this->assertStringContainsString('inklusif', $result['ai_message']->body);
        $this->assertFalse($result['handoff']);
        $this->assertSame('halaman_website', $result['sources'][0]['type']);
        $this->assertStringContainsString('/tentang', $result['sources'][0]['url']);
        Http::assertSent(fn ($request) => str_contains($request['contents'][0]['parts'][0]['text'], 'Tentang, visi, misi, dan nilai Portal BEM')
            && str_contains($request['contents'][0]['parts'][0]['text'], 'terwujudnya iklim kemahasiswaan')
            && str_contains($request['contents'][0]['parts'][0]['text'], 'Gunakan informasi dari halaman publik Portal BEM'));
    }

    public function test_follow_up_message_sends_previous_turns_as_gemini_history(): void
    {
        Http::fake(['*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Tadi Anda bertanya tentang demokrasi.']]]]],
        ], 200)]);
        $this->configureGemini();
        $service = app(ChatConversationService::class);
        $conversation = $service->createConversation(['identity_mode' => 'anonymous'])['conversation'];

        $service->sendMessage($conversation, 'Jelaskan demokrasi.');
        $service->sendMessage($conversation, 'Apa yang tadi saya tanyakan?');

        Http::assertSent(fn ($request) => count($request['contents']) === 3
            && $request['contents'][0]['role'] === 'user'
            && $request['contents'][0]['parts'][0]['text'] === 'Jelaskan demokrasi.'
            && $request['contents'][1]['role'] === 'model'
            && $request['contents'][1]['parts'][0]['text'] === 'Tadi Anda bertanya tentang demokrasi.'
            && str_contains($request['contents'][2]['parts'][0]['text'], 'Apa yang tadi saya tanyakan?'));
    }

    public function test_all_reported_greetings_and_general_bem_definition_call_gemini(): void
    {
        Http::fake(['*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Jawaban umum dari Gemini']]]]],
        ], 200)]);
        $this->configureGemini();
        $service = app(ChatConversationService::class);

        foreach ([
            'hai',
            'halo',
            'selamat pagi',
            'apa kabar?',
            'siapa kamu?',
            'apa itu BEM?',
            'apa itu organisasi?',
            'apa perbedaan organisasi dan universitas?',
        ] as $question) {
            $conversation = $service->createConversation(['identity_mode' => 'anonymous'])['conversation'];
            $result = $service->sendMessage($conversation, $question);

            $this->assertSame('Jawaban umum dari Gemini', $result['ai_message']->body, $question);
            $this->assertFalse($result['handoff'], $question);
        }

        Http::assertSentCount(8);
    }

    public function test_official_question_without_source_calls_gemini_without_handing_off(): void
    {
        Http::fake(['*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Saya belum menemukan jadwal resmi. Anda dapat memeriksa pengumuman BEM atau menghubungi sekretariat.']]]]],
        ], 200)]);
        $this->configureGemini();
        $conversation = app(ChatConversationService::class)
            ->createConversation(['identity_mode' => 'anonymous'])['conversation'];

        $result = app(ChatConversationService::class)->sendMessage($conversation, 'Apa jadwal ZQZX-2099?');

        $this->assertSame('Saya belum menemukan jadwal resmi. Anda dapat memeriksa pengumuman BEM atau menghubungi sekretariat.', $result['ai_message']->body);
        $this->assertFalse($result['handoff']);
        Http::assertSent(fn ($request) => str_contains($request['contents'][0]['parts'][0]['text'], 'Tidak ada sumber Portal BEM yang cocok')
            && str_contains($request['contents'][0]['parts'][0]['text'], 'Apa jadwal ZQZX-2099?'));
    }

    public function test_official_question_without_source_does_not_handoff_when_gemini_fails(): void
    {
        Http::fake(['*' => Http::response([], 500)]);
        $this->configureGemini();
        $conversation = app(ChatConversationService::class)
            ->createConversation(['identity_mode' => 'anonymous'])['conversation'];

        $result = app(ChatConversationService::class)->sendMessage($conversation, 'Apa jadwal ZQZX-2099?');

        $this->assertStringContainsString('tidak dapat terhubung', $result['ai_message']->body);
        $this->assertFalse($result['handoff']);
    }

    public function test_handoff_blocks_gemini_reply(): void
    {
        Http::fake();
        $this->configureGemini();
        $conversation = app(ChatConversationService::class)
            ->createConversation(['identity_mode' => 'anonymous'])['conversation'];
        $conversation->update(['status' => 'handoff', 'handler_mode' => 'human']);

        $result = app(ChatConversationService::class)->sendMessage($conversation, 'hai');

        $this->assertTrue($result['handoff']);
        $this->assertStringContainsString('ditangani petugas', $result['ai_message']->body);
        Http::assertNothingSent();
    }

    public function test_private_aspiration_data_is_not_sent_to_gemini(): void
    {
        Http::fake(['*' => Http::response([
            'candidates' => [['content' => ['parts' => [['text' => 'Hai!']]]]],
        ], 200)]);
        $this->configureGemini();
        $now = now();
        $aspirationId = DB::table('aspirations')->insertGetId([
            'ticket_number' => 'PRIVATE-TEST-001',
            'tracking_code_hash' => hash('sha256', 'private-test-code'),
            'category' => 'lainnya',
            'subject' => 'PRIVATE-ASPIRATION-SUBJECT',
            'message' => 'PRIVATE-ASPIRATION-MESSAGE',
            'reporter_name' => 'PRIVATE-REPORTER-NAME',
            'reporter_email' => 'private@example.test',
            'status' => 'in_progress',
            'admin_response' => 'PRIVATE-ADMIN-RESPONSE',
            'submitted_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('aspiration_status_histories')->insert([
            'aspiration_id' => $aspirationId,
            'old_status' => 'submitted',
            'new_status' => 'in_progress',
            'note' => 'PRIVATE-INTERNAL-NOTE',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $conversation = app(ChatConversationService::class)
            ->createConversation(['identity_mode' => 'anonymous'])['conversation'];

        app(ChatConversationService::class)->sendMessage($conversation, 'hai');

        Http::assertSent(function ($request) {
            $body = $request->body();

            return ! str_contains($body, 'PRIVATE-ASPIRATION')
                && ! str_contains($body, 'PRIVATE-REPORTER')
                && ! str_contains($body, 'PRIVATE-ADMIN')
                && ! str_contains($body, 'PRIVATE-INTERNAL');
        });
    }

    public function test_general_question_api_failure_is_not_reported_as_missing_portal_information(): void
    {
        Http::fake(['*' => Http::response([], 500)]);
        $this->configureGemini();
        $conversation = app(ChatConversationService::class)
            ->createConversation(['identity_mode' => 'anonymous'])['conversation'];

        $result = app(ChatConversationService::class)->sendMessage($conversation, 'hai');

        $this->assertStringContainsString('tidak dapat terhubung', $result['ai_message']->body);
        $this->assertStringNotContainsString('belum tersedia di Portal', $result['ai_message']->body);
    }

    private function configureGemini(): void
    {
        config(['services.gemini' => [
            'api_key' => 'test-key',
            'model' => 'test-model',
            'base_url' => 'https://example.test/v1beta',
            'timeout' => 5,
        ]]);
    }
}
