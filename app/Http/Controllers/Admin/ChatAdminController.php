<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\User;
use App\Services\Chat\ChatConversationService;
use Illuminate\Http\Request;

class ChatAdminController extends Controller
{
    public function __construct(private ChatConversationService $chat) {}

    public function index(Request $request)
    {
        $status = $request->query('status');
        $conversations = ChatConversation::query()
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('last_message_at')
            ->paginate(20);

        return view('admin.chat.index', compact('conversations', 'status'));
    }

    public function show(ChatConversation $conversation)
    {
        $messages = $conversation->messages()->orderBy('id')->get();
        $admins = User::query()->where('is_active', true)->whereIn('role', ['super_admin', 'admin', 'advokasi'])->get();
        return view('admin.chat.show', compact('conversation', 'messages', 'admins'));
    }

    public function reply(Request $request, ChatConversation $conversation)
    {
        $request->validate(['body' => 'required|string|max:1000']);
        \App\Models\ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_user_id' => $request->user()->id,
            'sender_type' => 'admin',
            'message_type' => 'text',
            'body' => $request->input('body'),
        ]);
        $conversation->update(['last_message_at' => now(), 'status' => 'assigned', 'handler_mode' => 'human']);
        return redirect()->route('admin.chat.show', $conversation);
    }

    public function assign(Request $request, ChatConversation $conversation)
    {
        $request->validate(['admin_id' => 'nullable|exists:users,id']);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $conversation) {
                $locked = ChatConversation::where('id', $conversation->id)->lockForUpdate()->firstOrFail();

                $targetId = $request->input('admin_id');
                if ($targetId !== null) {
                    $target = User::where('id', $targetId)->where('is_active', true)->first();
                    if (! $target || ! $target->canAccessModule('chat')) {
                        abort(422, 'Petugas target tidak valid.');
                    }
                }

                if ($locked->assigned_admin_id !== null && $locked->assigned_admin_id !== $request->input('admin_id') && $locked->assigned_admin_id !== $request->user()->id) {
                    abort(409, 'Percakapan sudah ditugaskan kepada petugas lain.');
                }

                $locked->update([
                    'assigned_admin_id' => $targetId,
                    'status' => 'assigned',
                    'handler_mode' => 'human',
                ]);

                \App\Models\AdminAuditLog::record('chat_assign', 'Assignment percakapan chat diperbarui.', $locked, ['admin_id' => $targetId]);
            });
        } catch (\Throwable $e) {
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                throw $e;
            }
            throw $e;
        }

        return redirect()->route('admin.chat.show', $conversation);
    }

    public function resolve(ChatConversation $conversation)
    {
        $conversation->update(['status' => 'resolved', 'handler_mode' => 'human']);
        return redirect()->route('admin.chat.show', $conversation);
    }
}
