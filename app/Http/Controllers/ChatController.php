<?php

namespace App\Http\Controllers;

use App\Models\ChatAttachment;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Services\Chat\ChatConversationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function __construct(private ChatConversationService $chat) {}

    public function store(Request $request): JsonResponse
    {
        $identityMode = auth()->check() ? 'account' : 'anonymous';
        $result = $this->chat->createConversation([
            'identity_mode' => $identityMode,
            'user_id' => auth()->id(),
        ]);

        $conversation = $result['conversation'];
        $response = response()->json([
            'conversation_id' => $conversation->id,
            'messages' => [],
        ]);

        if ($result['anonymous_token']) {
            $response->cookie('chat_anon_token', $result['anonymous_token'], 60 * 24 * 30, null, null, false, true, false, 'lax');
        }

        return $response;
    }

    public function messages(Request $request, ChatConversation $conversation): JsonResponse
    {
        $token = $request->cookie('chat_anon_token');
        if (! $this->chat->canAccess($conversation, auth()->user(), $token)) {
            abort(403);
        }

        $messages = $conversation->messages()->orderBy('id')->get()->map(fn ($m) => [
            'id' => $m->id,
            'sender_type' => $m->sender_type,
            'body' => $m->body,
            'sources' => $m->sources,
            'read_at' => $m->read_at?->toIso8601String(),
            'created_at' => $m->created_at?->toIso8601String(),
        ]);

        // mark user-visible messages read
        $conversation->messages()
            ->whereNull('read_at')
            ->where('sender_type', '!=', 'user')
            ->update(['read_at' => now()]);

        return response()->json(['messages' => $messages]);
    }

    public function message(Request $request, ChatConversation $conversation): JsonResponse
    {
        $token = $request->cookie('chat_anon_token');
        if (! $this->chat->canAccess($conversation, auth()->user(), $token)) {
            abort(403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $result = $this->chat->sendMessage($conversation, $validated['body'], auth()->user());

        return response()->json([
            'user_message' => ['body' => $result['user_message']->body],
            'ai_message' => ['body' => $result['ai_message']->body, 'sources' => $result['sources']],
            'handoff' => $result['handoff'],
        ]);
    }

    public function upload(Request $request, ChatConversation $conversation): JsonResponse
    {
        $token = $request->cookie('chat_anon_token');
        if (! $this->chat->canAccess($conversation, auth()->user(), $token)) {
            abort(403);
        }

        $request->validate([
            'file' => 'required|file|max:5120|mimes:pdf,jpg,jpeg,png,webp',
        ]);

        $file = $request->file('file');
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
        if (! in_array($file->getMimeType(), $allowedMimes, true)) {
            abort(422, 'Tipe file tidak diizinkan.');
        }

        $storedName = Str::random(40).'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('chat-attachments', $storedName, 'local');

        $attachment = ChatAttachment::create([
            'conversation_id' => $conversation->id,
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);

        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_user_id' => auth()->id(),
            'sender_type' => 'user',
            'message_type' => 'attachment',
            'body' => $attachment->original_name,
        ]);

        return response()->json(['attachment_id' => $attachment->id]);
    }

    public function download(ChatAttachment $attachment, Request $request)
    {
        $conversation = $attachment->conversation;
        $token = $request->cookie('chat_anon_token');
        if (! $this->chat->canAccess($conversation, auth()->user(), $token)) {
            abort(403);
        }

        if (! Storage::disk('local')->exists($attachment->storage_path)) {
            abort(404);
        }

        return Storage::disk('local')->download($attachment->storage_path, $attachment->original_name, [
            'Content-Type' => $attachment->mime_type,
            'Content-Disposition' => 'attachment; filename="'.$attachment->original_name.'"',
        ]);
    }

    public function handoff(Request $request, ChatConversation $conversation): JsonResponse
    {
        $token = $request->cookie('chat_anon_token');
        if (! $this->chat->canAccess($conversation, auth()->user(), $token)) {
            abort(403);
        }

        $conversation->update(['status' => 'handoff', 'handler_mode' => 'human']);
        ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_type' => 'system',
            'message_type' => 'text',
            'body' => 'Percakapan dialihkan ke petugas manusia.',
        ]);

        return response()->json(['status' => 'handoff']);
    }
}
