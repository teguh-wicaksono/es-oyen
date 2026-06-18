@extends('layouts.wana', ['title' => 'Home Kasir | Wana Cafe'])

@section('content')
    <section class="kasir-hero">
        <div class="hero-copy">
            <div class="eyebrow">Selamat Datang, Kasir</div>
            <h1>Dashboard Kasir</h1>
            <p class="lead" id="kasirLiveClock">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }} - {{ now()->format('H:i:s') }} WIB</p>
            <div class="hero-actions">
                <a class="btn hero-primary" href="{{ route('kasir.pesanan') }}">Buka Pesanan</a>
                <a class="btn hero-secondary" href="{{ route('kasir.riwayat') }}">Lihat Riwayat</a>
            </div>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <div class="coffee-orbit">
                <img src="https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=900&q=85" alt="">
            </div>
            <div class="floating-note top-note">
                <span>Live Shift</span>
                <strong>Kasir Pagi</strong>
            </div>
            <div class="floating-note bottom-note">
                <span>Menu Siap</span>
                <strong>{{ count($products) }} item</strong>
            </div>
        </div>
    </section>

    <section class="dashboard-cards">
        <article class="dashboard-card card-primary">
            <span>Transaksi Hari Ini</span>
            <strong id="orderMetric">0</strong>
            <p>Menampilkan jumlah transaksi yang selesai hari ini.</p>
        </article>
        <article class="dashboard-card card-secondary">
            <span>Total Penjualan</span>
            <strong id="salesMetric">Rp 0</strong>
            <p>Nilai penjualan yang dicapai oleh shift kasir.</p>
        </article>
        <article class="dashboard-card card-tertiary">
            <span>Pesanan Aktif</span>
            <strong id="activeMetric">0</strong>
            <p>Pesanan yang saat ini masih diproses.</p>
        </article>
        <article class="dashboard-card card-quaternary">
            <span>Menu Tersedia</span>
            <strong>{{ count($products) }}</strong>
            <p>Item siap dijual di kasir.</p>
        </article>
    </section>

    <section class="dashboard-grid">
        <div class="dashboard-left">
            <div class="panel kasir-panel status-panel">
                <div class="panel-title">
                    <h2>Status Terakhir</h2>
                    <a class="btn ghost" href="{{ route('kasir.riwayat') }}">Lihat Semua</a>
                </div>
                <div id="latestOrders" class="order-list"></div>
            </div>
        </div>

        <div class="panel kasir-panel chat-panel">
            <div class="panel-title">
                <h2>Chat</h2>
                <span class="badge">Owner & Dapur</span>
            </div>
            <div id="homeComplaintList" class="chat-list"></div>
            <div class="field target-field">
                <label>Kirim ke</label>
                <div class="target-buttons" role="group" aria-label="Pilih penerima chat">
                    <button type="button" class="target-button active" data-target="Owner">Owner</button>
                    <button type="button" class="target-button" data-target="Dapur">Dapur</button>
                </div>
            </div>
            <div class="field message-field">
                <label for="homeComplaintInput">Tulis pesan</label>
                <textarea id="homeComplaintInput" placeholder="Tulis pesan..."></textarea>
            </div>
            <button class="btn ghost" onclick="sendHomeComplaint()">Kirim</button>
        </div>
    </section>
@endsection

@push('styles')
<style>
    .kasir-hero {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 440px);
        gap: 28px;
        align-items: center;
        min-height: 330px;
        margin-bottom: 22px;
        padding: 34px;
        overflow: hidden;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 30px;
        background:
            linear-gradient(135deg, rgba(255, 250, 242, .97) 0%, rgba(245, 234, 219, .84) 50%, rgba(100, 122, 84, .18) 100%),
            url('https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=1600&q=80') center/cover;
        box-shadow: 0 34px 80px rgba(49, 29, 15, .12);
    }

    .kasir-hero::before {
        content: "";
        position: absolute;
        inset: 18px;
        border: 1px solid rgba(255, 255, 255, .62);
        border-radius: 24px;
        pointer-events: none;
    }

    .hero-copy,
    .hero-visual {
        position: relative;
        z-index: 1;
    }

    .hero-copy .eyebrow {
        letter-spacing: .16em;
    }

    .hero-copy h1 {
        max-width: 680px;
        margin-top: 8px;
        font-size: clamp(42px, 5vw, 72px);
    }

    .hero-copy .lead {
        margin-top: 14px;
        color: var(--muted);
        font-size: 16px;
        font-weight: 700;
    }

    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 26px;
    }

    .hero-actions .btn {
        width: auto;
        min-height: 46px;
        border-radius: 999px;
        box-shadow: 0 16px 34px rgba(43, 22, 11, .12);
    }

    .hero-primary {
        background: var(--coffee);
    }

    .hero-secondary {
        color: var(--coffee);
        background: rgba(255, 250, 242, .88);
        border: 1px solid rgba(83, 58, 38, .13);
    }

    .hero-visual {
        min-height: 270px;
        display: grid;
        place-items: center;
    }

    .coffee-orbit {
        position: relative;
        z-index: 1;
        width: min(320px, 78vw);
        aspect-ratio: 1;
        border-radius: 50%;
        padding: 12px;
        background: linear-gradient(135deg, rgba(255, 248, 237, .92), rgba(227, 180, 103, .36));
        box-shadow: 0 34px 80px rgba(43, 22, 11, .24);
    }

    .coffee-orbit::before,
    .coffee-orbit::after {
        content: "";
        position: absolute;
        border-radius: 50%;
        border: 1px solid rgba(43, 22, 11, .12);
    }

    .coffee-orbit::before {
        inset: -18px;
    }

    .coffee-orbit::after {
        inset: 26px;
        border-color: rgba(255, 255, 255, .58);
    }

    .coffee-orbit img {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        border-radius: 50%;
        border: 8px solid rgba(255, 250, 242, .95);
    }

    .floating-note {
        position: absolute;
        z-index: 3;
        display: grid;
        gap: 4px;
        min-width: 158px;
        padding: 14px 16px;
        border: 1px solid rgba(255, 250, 242, .68);
        border-radius: 18px;
        background: rgba(255, 253, 249, .86);
        box-shadow: 0 20px 46px rgba(49, 29, 15, .13);
        backdrop-filter: blur(12px);
    }

    .floating-note span {
        color: var(--sage);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .floating-note strong {
        color: var(--coffee);
        font-size: 16px;
        line-height: 1.1;
    }

    .top-note {
        top: 32px;
        right: -34px;
    }

    .bottom-note {
        left: -34px;
        bottom: 32px;
    }

    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .dashboard-card {
        position: relative;
        min-height: 176px;
        overflow: hidden;
        padding: 24px;
        border-radius: 24px;
        background: linear-gradient(180deg, #fffdf9, #f8f0e7);
        box-shadow: 0 22px 48px rgba(49, 29, 15, .08);
        display: grid;
        gap: 12px;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .dashboard-card::after {
        content: "";
        position: absolute;
        right: -24px;
        bottom: -28px;
        width: 112px;
        height: 112px;
        border-radius: 50%;
        background: rgba(200, 132, 79, .12);
    }

    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 28px 60px rgba(49, 29, 15, .12);
    }

    .dashboard-card span {
        position: relative;
        z-index: 1;
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .dashboard-card strong {
        position: relative;
        z-index: 1;
        font-size: 32px;
        margin-top: 4px;
        line-height: 1.05;
    }

    .dashboard-card p {
        position: relative;
        z-index: 1;
        margin: 0;
        color: var(--sage);
        font-size: 13px;
        line-height: 1.6;
    }

    .card-primary { border: 1px solid rgba(166, 79, 83, .12); }
    .card-primary::after { background: rgba(166, 79, 83, .12); }
    .card-secondary { border: 1px solid rgba(100, 122, 84, .14); }
    .card-secondary::after { background: rgba(100, 122, 84, .14); }
    .card-tertiary { border: 1px solid rgba(200, 132, 79, .18); }
    .card-tertiary::after { background: rgba(227, 180, 103, .18); }
    .card-quaternary { border: 1px solid rgba(83, 58, 38, .14); }
    .card-quaternary::after { background: rgba(83, 58, 38, .10); }

    .dashboard-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr);
        gap: 24px;
    }

    .dashboard-left {
        display: grid;
        gap: 24px;
    }

    .kasir-panel {
        padding: 28px;
        border-radius: 24px;
        background: rgba(255, 255, 255, .92);
        box-shadow: 0 20px 50px rgba(49, 29, 15, .06);
    }

    .panel-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 20px;
    }

    .panel-title h2 {
        font-size: 18px;
        font-weight: 800;
    }

    .panel-title .btn.ghost {
        width: auto;
        min-height: 40px;
        padding: 0 16px;
        border-radius: 999px;
    }

    .order-list,
    .chat-list {
        display: grid;
        gap: 14px;
    }

    .order-list .mini-item,
    .chat-list .mini-item {
        padding: 18px;
        border-radius: 18px;
        background: #fbf8f2;
        border: 1px solid rgba(220, 205, 186, .4);
    }

    .order-list .mini-item {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
    }

    .order-list .mini-item strong {
        font-size: 14px;
        font-weight: 700;
    }

    .order-list .mini-item span {
        display: block;
        color: var(--sage);
        font-size: 13px;
        margin-top: 6px;
    }

    .chat-panel {
        position: sticky;
        top: 96px;
        align-self: start;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, .94), rgba(250, 244, 236, .96)),
            url('https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=1000&q=70') center/cover;
    }

    .chat-list .mini-item {
        display: grid;
        gap: 10px;
        background: rgba(255, 255, 255, .9);
        backdrop-filter: blur(8px);
    }

    .chat-list .mini-item strong {
        font-size: 14px;
        font-weight: 700;
    }

    .chat-list .mini-item small {
        display: block;
        margin-top: 4px;
        color: var(--sage);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .chat-list .mini-item span {
        color: var(--muted);
        font-size: 13px;
        line-height: 1.6;
    }

    .target-field {
        margin-bottom: 18px;
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
        background: rgba(252, 250, 247, .9);
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

    .chat-list .mini-item:last-child {
        margin-bottom: 0;
    }

    .message-field textarea {
        min-height: 120px;
        border-radius: 18px;
        background: rgba(252, 250, 247, .92);
        border: 1px solid rgba(140, 118, 98, .18);
        padding: 16px;
    }

    .btn.ghost {
        width: 100%;
        color: var(--coffee);
        background: #f9f4ec;
        border: 1px solid rgba(140, 118, 98, .18);
    }

    @media (max-width: 1080px) {
        .kasir-hero {
            grid-template-columns: 1fr;
        }

        .hero-visual {
            min-height: 250px;
        }

        .dashboard-cards {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .chat-panel {
            position: static;
        }
    }

    @media (max-width: 700px) {
        .kasir-hero {
            padding: 20px;
            border-radius: 22px;
        }

        .kasir-hero::before {
            inset: 10px;
            border-radius: 18px;
        }

        .hero-actions .btn {
            width: 100%;
        }

        .coffee-orbit {
            width: min(260px, 76vw);
        }

        .floating-note {
            position: relative;
            inset: auto;
            width: 100%;
            margin-top: 10px;
        }

        .hero-visual {
            display: grid;
            gap: 10px;
        }

        .dashboard-cards {
            grid-template-columns: 1fr;
        }

    }
</style>
@endpush

@push('scripts')
<script>
    function updateKasirLiveClock() {
        const clock = document.getElementById('kasirLiveClock');
        if (!clock) return;

        const now = new Date();
        const dateParts = new Intl.DateTimeFormat('en-GB', {
            weekday: 'long',
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            timeZone: 'Asia/Jakarta',
        }).formatToParts(now).reduce((parts, part) => {
            parts[part.type] = part.value;
            return parts;
        }, {});
        const time = new Intl.DateTimeFormat('en-GB', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
            timeZone: 'Asia/Jakarta',
        }).format(now);

        clock.textContent = `${dateParts.weekday}, ${dateParts.day} ${dateParts.month} ${dateParts.year} - ${time} WIB`;
    }

    const orders = getOrders();
    document.getElementById('orderMetric').textContent = orders.length;
    document.getElementById('salesMetric').textContent = rupiah(orders.reduce((sum, order) => sum + order.total, 0));
    document.getElementById('activeMetric').textContent = orders.filter((order) => order.status !== 'Selesai').length;
    document.getElementById('latestOrders').innerHTML = orders.slice(0, 5).map((order) => `
        <div class="mini-item"><div><strong>${order.id} - ${order.customer}</strong><span>${order.status}</span></div><span>${rupiah(order.total)}</span></div>
    `).join('') || '<div class="empty">Belum ada pesanan hari ini.</div>';

    function renderHomeComplaints() {
        const messages = getComplaints().filter((chat) => chat.sender === 'Kasir' || chat.recipient === 'Kasir');
        document.getElementById('homeComplaintList').innerHTML = messages.slice(-6).map((chat) => {
            const header = chat.sender === 'Kasir' && chat.recipient ? `${chat.sender} -> ${chat.recipient}` : chat.sender;
            return `
                <div class="mini-item"><div><strong>${header}</strong><small>${chat.recipient || 'Umum'}</small><span>${chat.message}</span></div><span>${chat.time}</span></div>
            `;
        }).join('') || '<div class="empty">Belum ada pesan.</div>';
    }

    function getHomeChatTarget() {
        const activeButton = document.querySelector('.target-button.active');
        return activeButton ? activeButton.dataset.target : 'Owner';
    }

    async function sendHomeComplaint() {
        const input = document.getElementById('homeComplaintInput');
        if (!input.value.trim()) return notify('Isi pesan dulu.');
        const recipient = getHomeChatTarget();
        try {
            await saveChatMessage(recipient, input.value.trim());
        } catch (error) {
            notify(error.message);
            return;
        }
        input.value = '';
        renderHomeComplaints();
        notify(`Pesan terkirim ke ${recipient}.`);
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.target-button').forEach((button) => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.target-button').forEach((btn) => btn.classList.remove('active'));
                button.classList.add('active');
            });
        });
    });

    window.addEventListener('wana:storage', () => {
        renderHomeComplaints();
        const updatedOrders = getOrders();
        document.getElementById('orderMetric').textContent = updatedOrders.length;
        document.getElementById('salesMetric').textContent = rupiah(updatedOrders.reduce((sum, order) => sum + order.total, 0));
        document.getElementById('activeMetric').textContent = updatedOrders.filter((o) => o.status !== 'Selesai').length;
    });

    updateKasirLiveClock();
    setInterval(updateKasirLiveClock, 1000);
    renderHomeComplaints();
</script>
@endpush
