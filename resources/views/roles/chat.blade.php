@extends('layouts.wana', ['title' => 'Daftar Chat | Wana Cafe'])

@section('content')
    @php
        $roleName = ucfirst(auth()->user()->role);
        $targets = collect(['Owner', 'Kasir', 'Dapur'])->reject(fn ($target) => $target === $roleName)->values();
    @endphp

    <section class="chat-hero">
        <div class="chat-hero-copy">
            <div class="eyebrow">Chat</div>
            <h1>Daftar Chat</h1>
            <p class="lead">Pantau pesan internal untuk {{ $roleName }} dan kirim koordinasi cepat ke tim operasional.</p>
            <div class="chat-hero-pills">
                <span>{{ $roleName }} online</span>
                <span id="chatMetric">0 pesan</span>
                <span>Internal team</span>
            </div>
        </div>

        <div class="chat-hero-visual" aria-hidden="true">
            <img src="https://images.unsplash.com/photo-1556740758-90de374c12ad?auto=format&fit=crop&w=900&q=85" alt="">
            <div class="hero-bubble">
                <span>Wana Cafe</span>
                <strong>Team Chat</strong>
            </div>
        </div>
    </section>

    <section class="chat-layout">
        <div class="chat-board">
            <div class="chat-board-head">
                <div>
                    <h2>Inbox Percakapan</h2>
                    <p>Pesan terbaru dari owner, kasir, dan dapur.</p>
                </div>
                <span class="badge">{{ $roleName }}</span>
            </div>

            <div class="chat-filter">
                <button class="chat-chip active" type="button" data-filter="Semua">Semua</button>
                @foreach ($targets as $target)
                    <button class="chat-chip" type="button" data-filter="{{ $target }}">{{ $target }}</button>
                @endforeach
            </div>

            <div id="roleChatList" class="role-chat-list"></div>
        </div>

        <aside class="compose-panel">
            <div class="compose-cover" aria-hidden="true">
                <img src="https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=900&q=80" alt="">
                <div>
                    <span>Quick Message</span>
                    <strong>Kirim Pesan</strong>
                </div>
            </div>

            <div class="compose-body">
                <div class="field">
                    <label>Kirim ke</label>
                    <div class="target-buttons" role="group" aria-label="Pilih penerima chat">
                        @foreach ($targets as $index => $target)
                            <button type="button" class="target-button {{ $index === 0 ? 'active' : '' }}" data-target="{{ $target }}">{{ $target }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="field message-field">
                    <label for="roleChatInput">Tulis pesan</label>
                    <textarea id="roleChatInput" placeholder="Tulis pesan singkat untuk tim..."></textarea>
                </div>

                <button class="btn primary" type="button" onclick="sendRoleChat()">Kirim Pesan</button>
            </div>
        </aside>
    </section>
@endsection

@push('styles')
<style>
    .chat-hero {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(300px, 420px);
        gap: 28px;
        align-items: center;
        min-height: 320px;
        margin-bottom: 22px;
        padding: 34px;
        overflow: hidden;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 30px;
        background:
            linear-gradient(135deg, rgba(255, 250, 242, .97), rgba(245, 234, 219, .84) 52%, rgba(100, 122, 84, .18)),
            url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1600&q=80') center/cover;
        box-shadow: 0 34px 80px rgba(49, 29, 15, .12);
    }

    .chat-hero::before {
        content: "";
        position: absolute;
        inset: 18px;
        border: 1px solid rgba(255, 255, 255, .62);
        border-radius: 24px;
        pointer-events: none;
    }

    .chat-hero-copy,
    .chat-hero-visual {
        position: relative;
        z-index: 1;
    }

    .chat-hero h1 {
        margin-top: 8px;
        font-size: clamp(42px, 5vw, 72px);
    }

    .chat-hero .lead {
        max-width: 720px;
        font-size: 16px;
        font-weight: 700;
    }

    .chat-hero-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 24px;
    }

    .chat-hero-pills span {
        display: inline-flex;
        align-items: center;
        min-height: 36px;
        padding: 0 13px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 999px;
        color: var(--coffee);
        background: rgba(255, 250, 242, .84);
        font-size: 12px;
        font-weight: 900;
        box-shadow: 0 12px 26px rgba(49, 29, 15, .07);
    }

    .chat-hero-visual {
        min-height: 260px;
        display: grid;
        place-items: center;
    }

    .chat-hero-visual img {
        width: min(360px, 80vw);
        aspect-ratio: 1 / .82;
        object-fit: cover;
        border: 10px solid rgba(255, 250, 242, .96);
        border-radius: 28px;
        box-shadow: 0 34px 80px rgba(43, 22, 11, .24);
        transform: rotate(-2deg);
    }

    .hero-bubble {
        position: absolute;
        right: 0;
        bottom: 24px;
        display: grid;
        gap: 4px;
        min-width: 150px;
        padding: 14px 16px;
        border: 1px solid rgba(255, 250, 242, .72);
        border-radius: 18px;
        background: rgba(43, 22, 11, .82);
        color: #fff8ed;
        box-shadow: 0 22px 50px rgba(43, 22, 11, .22);
        backdrop-filter: blur(12px);
    }

    .hero-bubble span,
    .compose-cover span {
        color: rgba(255, 248, 237, .76);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .hero-bubble strong,
    .compose-cover strong {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 28px;
        line-height: 1;
    }

    .chat-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(330px, .85fr);
        gap: 24px;
        align-items: start;
    }

    .chat-board,
    .compose-panel {
        overflow: hidden;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 28px;
        background: rgba(255, 253, 249, .94);
        box-shadow: 0 30px 80px rgba(49, 29, 15, .11);
    }

    .chat-board {
        padding: 24px;
    }

    .chat-board-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .chat-board-head h2 {
        color: var(--coffee);
        font-size: 24px;
    }

    .chat-board-head p {
        margin-top: 6px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .chat-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 18px;
    }

    .chat-chip {
        min-height: 40px;
        padding: 0 15px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 999px;
        background: #fbf6ef;
        color: var(--muted);
        font-size: 13px;
        font-weight: 900;
        transition: transform .18s ease, background .18s ease, color .18s ease;
    }

    .chat-chip:hover {
        transform: translateY(-1px);
    }

    .chat-chip.active {
        color: #fff8ed;
        background: var(--coffee);
    }

    .role-chat-list {
        display: grid;
        gap: 14px;
    }

    .chat-message {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        gap: 14px;
        align-items: start;
        padding: 16px;
        border: 1px solid rgba(83, 58, 38, .10);
        border-radius: 20px;
        background: #fffaf4;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .chat-message:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 42px rgba(49, 29, 15, .08);
    }

    .chat-avatar {
        display: grid;
        place-items: center;
        width: 44px;
        height: 44px;
        border-radius: 16px;
        color: #fff8ed;
        background: linear-gradient(135deg, #3d2317, #b8845b);
        font-size: 14px;
        font-weight: 900;
    }

    .chat-message h3 {
        margin: 0;
        color: var(--coffee);
        font-size: 15px;
        line-height: 1.25;
    }

    .chat-message small {
        display: block;
        margin-top: 4px;
        color: var(--sage);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .chat-message p {
        margin-top: 8px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .chat-time {
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .compose-panel {
        position: sticky;
        top: 94px;
    }

    .compose-cover {
        position: relative;
        min-height: 150px;
        display: grid;
        align-items: end;
        padding: 20px;
        overflow: hidden;
        color: #fff8ed;
    }

    .compose-cover img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .compose-cover::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(22, 12, 7, .10), rgba(22, 12, 7, .78));
    }

    .compose-cover > div {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 5px;
    }

    .compose-body {
        display: grid;
        gap: 18px;
        padding: 22px;
    }

    .target-buttons {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-top: 8px;
    }

    .target-button {
        min-height: 46px;
        padding: 0 18px;
        border-radius: 16px;
        border: 1px solid rgba(140, 118, 98, .18);
        background: #fcfaf7;
        color: var(--ink);
        font-size: 14px;
        font-weight: 800;
        transition: all .2s ease;
    }

    .target-button.active {
        border-color: rgba(100, 122, 84, .35);
        background: #f1f0e9;
        color: var(--coffee);
        box-shadow: inset 0 0 0 1px rgba(100, 122, 84, .12);
    }

    .message-field textarea {
        min-height: 140px;
        border-radius: 18px;
        background: #fcfaf7;
        border: 1px solid rgba(140, 118, 98, .18);
        padding: 16px;
    }

    .compose-body .btn.primary {
        width: 100%;
        min-height: 54px;
        border-radius: 18px;
    }

    .chat-empty {
        display: grid;
        place-items: center;
        gap: 10px;
        min-height: 220px;
        padding: 28px;
        border: 1px dashed rgba(83, 58, 38, .18);
        border-radius: 20px;
        color: var(--muted);
        background: #fffaf4;
        text-align: center;
    }

    .chat-empty strong {
        color: var(--ink);
        font-size: 18px;
    }

    @media (max-width: 1080px) {
        .chat-hero,
        .chat-layout {
            grid-template-columns: 1fr;
        }

        .compose-panel {
            position: static;
        }
    }

    @media (max-width: 700px) {
        .chat-hero,
        .chat-board {
            padding: 20px;
            border-radius: 22px;
        }

        .chat-hero::before {
            inset: 10px;
            border-radius: 18px;
        }

        .chat-message {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .chat-time {
            grid-column: 2;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    const roleName = @json($roleName);
    let activeChatFilter = 'Semua';

    function normalizeRole(value) {
        const text = String(value || '').toLowerCase();
        return text ? text.charAt(0).toUpperCase() + text.slice(1) : '';
    }

    function roleMessages() {
        return getComplaints().filter((chat) => {
            const sender = normalizeRole(chat.sender);
            const recipient = normalizeRole(chat.recipient);
            const inConversation = sender === roleName || recipient === roleName;
            const matchesFilter = activeChatFilter === 'Semua' || sender === activeChatFilter || recipient === activeChatFilter;
            return inConversation && matchesFilter;
        });
    }

    function renderRoleChatList() {
        const messages = roleMessages();
        document.getElementById('chatMetric').textContent = `${messages.length} pesan`;
        document.getElementById('roleChatList').innerHTML = messages.slice(-14).reverse().map((chat) => {
            const sender = normalizeRole(chat.sender);
            const recipient = normalizeRole(chat.recipient);
            const initials = (sender || 'WC').split(' ').map((part) => part[0]).join('').slice(0, 2);
            const header = recipient ? `${sender} -> ${recipient}` : sender;
            const status = sender === roleName ? (chat.read ? 'Dibaca' : 'Terkirim') : (chat.read ? 'Arsip' : 'Masuk');
            return `
                <article class="chat-message">
                    <div class="chat-avatar">${initials}</div>
                    <div>
                        <h3>${header}</h3>
                        <small>${status}</small>
                        <p>${chat.message}</p>
                    </div>
                    <span class="chat-time">${chat.time}</span>
                </article>
            `;
        }).join('') || `
            <div class="chat-empty">
                <strong>Belum ada percakapan</strong>
                <span>Kirim pesan pertama ke tim melalui panel di samping.</span>
            </div>
        `;
    }

    function getRoleChatTarget() {
        const activeButton = document.querySelector('.target-button.active');
        return activeButton ? activeButton.dataset.target : 'Owner';
    }

    async function sendRoleChat() {
        const input = document.getElementById('roleChatInput');
        if (!input.value.trim()) return notify('Isi pesan dulu.');
        const recipient = getRoleChatTarget();
        try {
            await saveChatMessage(recipient, input.value.trim());
        } catch (error) {
            notify(error.message);
            return;
        }
        input.value = '';
        renderRoleChatList();
        notify(`Pesan terkirim ke ${recipient}.`);
    }

    document.querySelectorAll('.target-button').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.target-button').forEach((btn) => btn.classList.remove('active'));
            button.classList.add('active');
        });
    });

    document.querySelectorAll('.chat-chip').forEach((button) => {
        button.addEventListener('click', () => {
            activeChatFilter = button.dataset.filter;
            document.querySelectorAll('.chat-chip').forEach((chip) => chip.classList.toggle('active', chip === button));
            renderRoleChatList();
        });
    });

    window.addEventListener('wana:storage', renderRoleChatList);
    renderRoleChatList();
</script>
@endpush
