<div id="bem-chat-widget" class="fixed bottom-4 right-4 z-50"
    data-conversation-url="{{ route('chat.conversations.store') }}"
    data-messages-url="{{ route('chat.conversations.messages', ['conversation' => '__CONVERSATION__']) }}"
    data-message-url="{{ route('chat.conversations.message', ['conversation' => '__CONVERSATION__']) }}"
    data-handoff-url="{{ route('chat.conversations.handoff', ['conversation' => '__CONVERSATION__']) }}"
    data-upload-url="{{ route('chat.conversations.attachments.store', ['conversation' => '__CONVERSATION__']) }}">
    <button id="bem-chat-toggle" type="button" aria-label="Chat dengan BEM" aria-expanded="false" class="relative rounded-full bg-[var(--theme-primary,#1e40af)] px-4 py-3 text-white shadow-lg hover:opacity-90">Chat BEM <span id="bem-chat-badge" class="hidden absolute -top-1 -right-1 rounded-full bg-red-600 px-1.5 py-0.5 text-xs">0</span></button>
    <div id="bem-chat-panel" class="hidden mt-3 w-[360px] max-w-[calc(100vw-2rem)] rounded-2xl border bg-white shadow-2xl" role="dialog" aria-label="Asisten BEM Universitas">
        <div class="flex items-center justify-between rounded-t-2xl bg-[var(--theme-primary,#1e40af)] px-4 py-3 text-white">
            <div>
                <div class="font-semibold">Asisten BEM Universitas</div>
                <div class="text-xs opacity-80">AI berbasis informasi resmi portal</div>
            </div>
            <button id="bem-chat-close" type="button" aria-label="Tutup chat" class="text-white">&times;</button>
        </div>
        <div id="bem-chat-messages" class="h-80 overflow-y-auto p-4 space-y-3"></div>
        <div class="border-t p-3">
            <div class="flex gap-2">
                <textarea id="bem-chat-input" rows="1" maxlength="1000" class="flex-1 resize-none rounded-lg border px-3 py-2 text-sm" placeholder="Tulis pesan..."></textarea>
                <button id="bem-chat-send" type="button" class="rounded-lg bg-[var(--theme-primary,#1e40af)] px-3 py-2 text-white">Kirim</button>
            </div>
            <div id="bem-chat-status" class="mt-2 text-xs text-slate-500"></div>
            <div class="mt-2 flex gap-2">
                <input id="bem-chat-file" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="text-xs" />
                <button id="bem-chat-handoff" type="button" class="text-xs text-blue-600">Hubungi Petugas</button>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    const toggle = document.getElementById('bem-chat-toggle');
    const widget = document.getElementById('bem-chat-widget');
    const panel = document.getElementById('bem-chat-panel');
    const closeBtn = document.getElementById('bem-chat-close');
    const messages = document.getElementById('bem-chat-messages');
    const input = document.getElementById('bem-chat-input');
    const send = document.getElementById('bem-chat-send');
    const status = document.getElementById('bem-chat-status');
    const badge = document.getElementById('bem-chat-badge');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const conversationStorageKey = 'bem-chat-conversation-{{ auth()->id() ?? "anonymous" }}';
    let conversationId = sessionStorage.getItem(conversationStorageKey);
    let sending = false;
    let pollTimer = null;
    const seenIds = new Set();
    const POLL_INTERVAL = 4000;

    function conversationUrl(template) {
        return template.replace('__CONVERSATION__', encodeURIComponent(conversationId));
    }

    function forgetConversation() {
        conversationId = null;
        sessionStorage.removeItem(conversationStorageKey);
        seenIds.clear();
        messages.replaceChildren();
    }

    function appendBubble(msg) {
        if (seenIds.has(msg.id)) return;
        seenIds.add(msg.id);
        const div = document.createElement('div');
        div.className = msg.sender_type === 'user' ? 'ml-auto max-w-[85%] rounded-lg bg-slate-100 px-3 py-2 text-sm' : 'mr-auto max-w-[85%] rounded-lg bg-blue-50 px-3 py-2 text-sm';
        div.textContent = msg.body;
        messages.appendChild(div);
        if (Array.isArray(msg.sources)) {
            msg.sources.forEach((s) => {
                const a = document.createElement('a');
                a.href = s.url;
                a.textContent = s.title;
                a.target = '_blank';
                a.rel = 'noopener noreferrer';
                a.className = 'block text-xs text-blue-600 underline';
                messages.appendChild(a);
            });
        }
        messages.scrollTop = messages.scrollHeight;
    }

    function updateBadge(unread) {
        if (unread > 0) {
            badge.textContent = String(unread);
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    function setOpen(open) {
        panel.classList.toggle('hidden', !open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (open) {
            startPolling();
        } else {
            stopPolling();
        }
    }

    function stopPolling() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }

    async function loadMessages() {
        if (!conversationId) return;
        try {
            const res = await fetch(conversationUrl(widget.dataset.messagesUrl), {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            });
            if (res.status === 403 || res.status === 404) {
                forgetConversation();
                return;
            }
            if (!res.ok) return;
            const data = await res.json();
            const list = Array.isArray(data.messages) ? data.messages : [];
            let unread = 0;
            list.forEach((m) => {
                if (m.sender_type !== 'user' && !m.read_at) unread++;
                appendBubble(m);
            });
            if (panel.classList.contains('hidden')) updateBadge(unread);
            else updateBadge(0);
        } catch (e) {}
    }

    function startPolling() {
        if (pollTimer) return;
        loadMessages();
        pollTimer = setInterval(loadMessages, POLL_INTERVAL);
    }

    toggle.addEventListener('click', () => setOpen(panel.classList.contains('hidden')));
    closeBtn.addEventListener('click', () => setOpen(false));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setOpen(false); });

    async function ensureConversation() {
        if (conversationId) return conversationId;
        const res = await fetch(widget.dataset.conversationUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            credentials: 'same-origin',
        });
        if (!res.ok) throw new Error('Gagal membuat percakapan');
        const data = await res.json();
        conversationId = data.conversation_id;
        sessionStorage.setItem(conversationStorageKey, conversationId);
        return conversationId;
    }

    async function sendMessage() {
        if (sending) return;
        const body = input.value.trim();
        if (!body) return;
        sending = true;
        send.disabled = true;
        status.textContent = 'Mengirim...';
        try {
            const id = await ensureConversation();
            const res = await fetch(conversationUrl(widget.dataset.messageUrl), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ body }),
            });
            if (!res.ok) throw new Error('Gagal mengirim pesan');
            const data = await res.json();
            await loadMessages();
            status.textContent = data.handoff ? 'Dialihkan ke petugas manusia.' : '';
            input.value = '';
        } catch (e) {
            status.textContent = 'Koneksi gagal. Coba lagi.';
        } finally {
            sending = false;
            send.disabled = false;
        }
    }

    send.addEventListener('click', sendMessage);

    const fileInput = document.getElementById('bem-chat-file');
    const handoffBtn = document.getElementById('bem-chat-handoff');
    if (fileInput) {
        fileInput.addEventListener('change', async () => {
            if (!fileInput.files.length) return;
            try {
                const id = await ensureConversation();
                const fd = new FormData();
                fd.append('file', fileInput.files[0]);
                const res = await fetch(conversationUrl(widget.dataset.uploadUrl), {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                    credentials: 'same-origin',
                    body: fd,
                });
                if (!res.ok) throw new Error('Upload gagal');
                status.textContent = 'Lampiran terkirim.';
                fileInput.value = '';
                await loadMessages();
            } catch (e) {
                status.textContent = 'Upload gagal.';
            }
        });
    }
    if (handoffBtn) {
        handoffBtn.addEventListener('click', async () => {
            try {
                const id = await ensureConversation();
                const res = await fetch(conversationUrl(widget.dataset.handoffUrl), {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                if (!res.ok) throw new Error('Handoff gagal');
                status.textContent = 'Dialihkan ke petugas.';
                await loadMessages();
            } catch (e) {
                status.textContent = 'Handoff gagal.';
            }
        });
    }
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
})();
</script>
