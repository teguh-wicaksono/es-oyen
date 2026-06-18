@extends('layouts.wana', ['title' => 'Home Dapur | Wana Cafe'])

@section('content')
    <section class="dapur-hero">
        <div class="hero-copy">
            <div class="eyebrow">Selamat Datang, Dapur</div>
            <h1>Dashboard Dapur</h1>
            <p class="lead" id="dapurLiveClock">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }} - {{ now()->format('H:i:s') }} WIB</p>
            <div class="hero-actions">
                <a class="btn hero-primary" href="{{ route('dapur.antrian') }}">Buka Antrian</a>
                <a class="btn hero-secondary" href="{{ route('dapur.stok') }}">Cek Stok</a>
            </div>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <div class="coffee-orbit">
                <img src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=900&q=85" alt="">
            </div>
            <div class="floating-note top-note">
                <span>Live Shift</span>
                <strong>Dapur Utama</strong>
            </div>
            <div class="floating-note bottom-note">
                <span>Menu Aktif</span>
                <strong>{{ count($products) }} item</strong>
            </div>
        </div>
    </section>

    <section class="dashboard-cards">
        <article class="dashboard-card card-primary">
            <span>Pesanan Aktif</span>
            <strong id="activeOrders">0</strong>
            <p>Order yang masih dalam proses produksi.</p>
        </article>
        <article class="dashboard-card card-secondary">
            <span>Menu Aktif</span>
            <strong>{{ count($products) }}</strong>
            <p>Produk tersinkron dengan kasir.</p>
        </article>
        <article class="dashboard-card card-tertiary">
            <span>Stok Rendah</span>
            <strong id="lowStock">0</strong>
            <p>Bahan yang perlu segera restock.</p>
        </article>
    </section>

    <section class="dashboard-grid">
        <div class="dashboard-left">
            <div class="panel dapur-panel">
                <div class="panel-title">
                    <h2>Menu Kerja</h2>
                    <span class="badge">Dapur</span>
                </div>
                <div class="action-grid">
                    <a class="action-card" href="{{ route('dapur.notifikasi') }}">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M10 21h4"/></svg>
                        </div>
                        <div>
                            <strong>Notifikasi Dapur</strong>
                            <p>Lihat pesanan masuk dari kasir secara real-time.</p>
                        </div>
                    </a>
                    <a class="action-card" href="{{ route('dapur.antrian') }}">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h12v18l-2-1.2-2 1.2-2-1.2-2 1.2-2-1.2L6 21V3Z"/><path d="M9 8h6"/><path d="M9 12h6"/><path d="M9 16h4"/></svg>
                        </div>
                        <div>
                            <strong>Antrian Pesanan</strong>
                            <p>Kelola antrean order yang sedang diproses.</p>
                        </div>
                    </a>
                    <a class="action-card" href="{{ route('dapur.produk') }}">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 8h10v6a5 5 0 0 1-5 5 5 5 0 0 1-5-5V8Z"/><path d="M15 10h2a3 3 0 0 1 0 6h-2"/><path d="M6 3v2"/><path d="M10 3v2"/><path d="M14 3v2"/></svg>
                        </div>
                        <div>
                            <strong>Produk & Menu</strong>
                            <p>Tambah, edit, dan hapus item menu dapur.</p>
                        </div>
                    </a>
                    <a class="action-card" href="{{ route('dapur.stok') }}">
                        <div class="action-icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21c4.5-2.5 7-6.3 7-11.5V5l-7-3-7 3v4.5C5 14.7 7.5 18.5 12 21Z"/><path d="M9 12l2 2 4-5"/></svg>
                        </div>
                        <div>
                            <strong>Stok Bahan</strong>
                            <p>Kelola persediaan bahan dan ketersediaan menu.</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="panel dapur-panel">
                <div class="panel-title">
                    <h2>Status Terakhir</h2>
                    <a class="btn ghost" href="{{ route('dapur.antrian') }}">Lihat Semua</a>
                </div>
                <div id="quickOrders" class="order-list"></div>
            </div>
        </div>

        <div class="panel dapur-panel chat-panel">
            <div class="panel-title">
                <h2>Chat</h2>
                <span class="badge">Kasir & Owner</span>
            </div>
            <div id="homeKitchenChat" class="chat-list"></div>
            <div class="field target-field">
                <label>Kirim ke</label>
                <div class="target-buttons" role="group" aria-label="Pilih penerima chat">
                    <button type="button" class="target-button active" data-target="Kasir">Kasir</button>
                    <button type="button" class="target-button" data-target="Owner">Owner</button>
                </div>
            </div>
            <div class="field message-field">
                <label for="homeKitchenInput">Tulis pesan</label>
                <textarea id="homeKitchenInput" placeholder="Tulis pesan..."></textarea>
            </div>
            <button class="btn ghost" onclick="sendKitchenComplaint()">Kirim</button>
        </div>
    </section>

    <div id="newOrderPopup" class="new-order-popup" aria-hidden="true">
        <div class="new-order-dialog" role="dialog" aria-modal="true" aria-labelledby="newOrderTitle">
            <button class="new-order-close" type="button" onclick="closeNewOrderPopup()" aria-label="Tutup popup pesanan">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12"></path><path d="M18 6 6 18"></path></svg>
            </button>
            <div class="new-order-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M6 3h12v18l-2-1.2-2 1.2-2-1.2-2 1.2-2-1.2L6 21V3Z"></path><path d="M9 8h6"></path><path d="M9 12h6"></path><path d="M9 16h4"></path></svg>
            </div>
            <div>
                <span class="popup-eyebrow">Pesanan Baru</span>
                <h2 id="newOrderTitle">Order masuk ke dapur</h2>
                <p id="newOrderMeta">Menunggu data pesanan...</p>
            </div>
            <div id="newOrderItems" class="new-order-items"></div>
            <div class="new-order-actions">
                <button class="btn ghost" type="button" onclick="closeNewOrderPopup()">Nanti</button>
                <a class="btn hero-primary" href="{{ route('dapur.antrian') }}">Buka Antrian</a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .dapur-hero {
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
            url('https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&w=1600&q=80') center/cover;
        box-shadow: 0 34px 80px rgba(49, 29, 15, .12);
    }

    .dapur-hero::before {
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
        grid-template-columns: repeat(3, minmax(0, 1fr));
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

    .dapur-panel {
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

    .action-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .action-card {
        display: grid;
        grid-template-columns: auto 1fr;
        align-items: center;
        gap: 16px;
        padding: 22px;
        border: 1px solid rgba(140, 118, 98, .2);
        border-radius: 20px;
        background: #fbf6ef;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 40px rgba(49, 29, 15, .08);
    }

    .action-icon {
        width: 46px;
        height: 46px;
        display: grid;
        place-items: center;
        border-radius: 16px;
        background: rgba(100, 122, 84, .18);
        color: var(--coffee);
    }

    .action-icon svg {
        width: 23px;
        height: 23px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .action-card strong {
        font-size: 16px;
        line-height: 1.25;
    }

    .action-card p {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.7;
    }

    .order-list, .chat-list {
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

    .chat-list .mini-item {
        display: grid;
        gap: 10px;
        background: #fff;
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
        min-height: 120px;
        border-radius: 18px;
        background: #fcfaf7;
        border: 1px solid rgba(140, 118, 98, .18);
        padding: 16px;
    }

    .new-order-popup {
        position: fixed;
        inset: 0;
        z-index: 100000;
        display: grid;
        place-items: center;
        padding: 18px;
        background: rgba(24, 16, 10, .48);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity .18s ease, visibility .18s ease;
    }

    .new-order-popup.show {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .new-order-dialog {
        position: relative;
        display: grid;
        gap: 16px;
        width: min(430px, 100%);
        padding: 26px;
        border: 1px solid rgba(83, 58, 38, .14);
        border-radius: 24px;
        background: #fffaf4;
        box-shadow: 0 34px 100px rgba(20, 10, 5, .28);
    }

    .new-order-close {
        position: absolute;
        top: 12px;
        right: 12px;
        display: grid;
        place-items: center;
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 12px;
        color: var(--muted);
        background: transparent;
    }

    .new-order-close:hover {
        color: var(--coffee);
        background: rgba(200, 132, 79, .12);
    }

    .new-order-close svg,
    .new-order-icon svg {
        width: 20px;
        height: 20px;
        stroke: currentColor;
        stroke-width: 2.2;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .new-order-icon {
        display: grid;
        place-items: center;
        width: 58px;
        height: 58px;
        border-radius: 18px;
        color: #fff8ed;
        background: linear-gradient(135deg, var(--coffee), #9a603a);
        box-shadow: 0 18px 40px rgba(43, 22, 11, .18);
    }

    .new-order-icon svg {
        width: 30px;
        height: 30px;
    }

    .popup-eyebrow {
        color: var(--sage);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .12em;
        text-transform: uppercase;
    }

    .new-order-dialog h2 {
        margin: 5px 0 0;
        color: var(--coffee);
        font-size: 26px;
    }

    .new-order-dialog p {
        margin: 8px 0 0;
        color: var(--muted);
        line-height: 1.5;
    }

    .new-order-items {
        display: grid;
        gap: 8px;
        padding: 12px;
        border-radius: 16px;
        background: #fbf1e6;
    }

    .new-order-items div {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        color: var(--coffee);
        font-size: 13px;
        font-weight: 800;
    }

    .new-order-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .btn.ghost {
        width: 100%;
        color: var(--coffee);
        background: #f9f4ec;
        border: 1px solid rgba(140, 118, 98, .18);
    }

    @media (max-width: 1080px) {
        .dapur-hero {
            grid-template-columns: 1fr;
        }

        .dashboard-cards {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .dashboard-grid {
            grid-template-columns: 1fr;
        }

        .action-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 700px) {
        .dapur-hero {
            padding: 20px;
            border-radius: 22px;
        }

        .dapur-hero::before {
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

        .dapur-panel {
            padding: 20px;
        }

        .new-order-actions {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let lastSeenNewOrderId = sessionStorage.getItem('wana_dapur_last_seen_order') || '';

    function updateDapurLiveClock() {
        const clock = document.getElementById('dapurLiveClock');
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

    function activeKitchenOrders() {
        return getOrders().filter((order) => order.status !== 'Selesai');
    }

    function renderDapurHome() {
        const orders = activeKitchenOrders();
        document.getElementById('activeOrders').textContent = orders.length;
        document.getElementById('lowStock').textContent = getProducts().filter((product) => product.stock <= 10).length;
        document.getElementById('quickOrders').innerHTML = orders.slice(0, 5).map((order) => `
            <div class="mini-item">
                <div>
                    <strong>${order.id} - ${order.customer}</strong>
                    <span>${order.items.map((item) => `${item.qty}x ${item.name}`).join(', ')}</span>
                </div>
                <span>${order.status}</span>
            </div>
        `).join('') || '<div class="empty">Belum ada antrean.</div>';

    }

    function showLatestNewOrderPopup(force = false) {
        const latest = getOrders().find((order) => order.status === 'Masuk');
        if (!latest) return;
        if (!force && latest.id === lastSeenNewOrderId) return;

        lastSeenNewOrderId = latest.id;
        sessionStorage.setItem('wana_dapur_last_seen_order', latest.id);
        openNewOrderPopup(latest);
    }

    function openNewOrderPopup(order) {
        document.getElementById('newOrderTitle').textContent = `${order.id} - ${order.customer || 'Pelanggan'}`;
        document.getElementById('newOrderMeta').textContent = `${order.table || 'Meja -'} | ${order.createdAt || 'Baru saja'} | ${order.cashier || 'Kasir'}`;
        document.getElementById('newOrderItems').innerHTML = order.items.map((item) => `
            <div>
                <span>${item.qty}x ${item.name}</span>
                <strong>${rupiah(Number(item.price || 0) * Number(item.qty || 0))}</strong>
            </div>
        `).join('');

        const popup = document.getElementById('newOrderPopup');
        popup.classList.add('show');
        popup.setAttribute('aria-hidden', 'false');
    }

    function closeNewOrderPopup() {
        const popup = document.getElementById('newOrderPopup');
        popup.classList.remove('show');
        popup.setAttribute('aria-hidden', 'true');
    }

    async function refreshDapurOrdersFromDatabase() {
        try {
            const payload = await wanaRequest('{{ route('dapur.pesanan.feed') }}', { method: 'GET' });
            setOrders(payload.orders || []);
        } catch (error) {
            console.warn(error.message);
        }
        renderDapurHome();
    }

    function renderKitchenChat() {
        const messages = getComplaints().filter((chat) => chat.sender === 'Dapur' || chat.recipient === 'Dapur');
        document.getElementById('homeKitchenChat').innerHTML = messages.slice(-6).map((chat) => `
            <div class="mini-item">
                <div>
                    <strong>${chat.sender}</strong>
                    <span>${chat.message}</span>
                </div>
                <span>${chat.time}</span>
            </div>
        `).join('') || '<div class="empty">Belum ada pesan untuk Dapur.</div>';
    }

    function getHomeChatTarget() {
        const activeButton = document.querySelector('.target-button.active');
        return activeButton ? activeButton.dataset.target : 'Kasir';
    }

    async function sendKitchenComplaint() {
        const input = document.getElementById('homeKitchenInput');
        if (!input.value.trim()) return notify('Isi pesan dulu.');
        const recipient = getHomeChatTarget();
        try {
            await saveChatMessage(recipient, input.value.trim());
        } catch (error) {
            notify(error.message);
            return;
        }
        input.value = '';
        renderKitchenChat();
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
        renderKitchenChat();
        renderDapurHome();
    });

    document.getElementById('newOrderPopup')?.addEventListener('click', (event) => {
        if (event.target.id === 'newOrderPopup') {
            closeNewOrderPopup();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeNewOrderPopup();
        }
    });

    updateDapurLiveClock();
    setInterval(updateDapurLiveClock, 1000);
    setInterval(refreshDapurOrdersFromDatabase, 5000);
    refreshDapurOrdersFromDatabase();
    renderKitchenChat();
</script>
@endpush
