@extends('layouts.wana', ['title' => 'Riwayat Kasir | Wana Cafe'])

@section('content')
    <section class="history-hero">
        <div class="history-hero-copy">
            <div class="eyebrow">Kasir</div>
            <h1>Riwayat Pesanan Kasir</h1>
            <p class="lead">Pantau transaksi, cek status dapur, dan temukan kembali pesanan pelanggan dengan cepat.</p>
            <div class="history-hero-pills">
                <span>Transaksi shift</span>
                <span id="heroOrders">0 transaksi tercatat</span>
                <span>Kasir aktif</span>
            </div>
        </div>

        <div class="history-hero-visual" aria-hidden="true">
            <img src="https://images.unsplash.com/photo-1556745757-8d76bdb6984b?auto=format&fit=crop&w=900&q=85" alt="">
            <div class="hero-card">
                <span>Penjualan Shift</span>
                <strong id="heroRevenue">Rp 0</strong>
            </div>
        </div>
    </section>

    <section class="history-metrics" aria-label="Ringkasan riwayat pesanan">
        <article class="history-metric">
            <span>Total Pesanan</span>
            <strong id="metricOrders">0</strong>
        </article>
        <article class="history-metric">
            <span>Pesanan Aktif</span>
            <strong id="metricActive">0</strong>
        </article>
        <article class="history-metric">
            <span>Item Terjual</span>
            <strong id="metricItems">0</strong>
        </article>
        <article class="history-metric">
            <span>Rata-rata Bill</span>
            <strong id="metricAverage">Rp 0</strong>
        </article>
    </section>

    <section class="history-board">
        <div class="history-toolbar">
            <div class="history-search">
                <svg viewBox="0 0 24 24" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m16.5 16.5 4 4"></path>
                </svg>
                <input id="historySearch" type="search" placeholder="Cari kode, pelanggan, meja, atau menu..." autocomplete="off">
            </div>

            <div class="history-controls" aria-label="Filter riwayat">
                <button class="filter-chip active" type="button" data-status="Semua">Semua</button>
                <button class="filter-chip" type="button" data-status="Masuk">Masuk</button>
                <button class="filter-chip" type="button" data-status="Diproses">Diproses</button>
                <button class="filter-chip" type="button" data-status="Selesai">Selesai</button>
            </div>

            <select id="historySort" class="history-sort" aria-label="Urutkan riwayat">
                <option value="newest">Terbaru</option>
                <option value="oldest">Terlama</option>
                <option value="highest">Total terbesar</option>
                <option value="lowest">Total terkecil</option>
                <option value="items">Item terbanyak</option>
            </select>
        </div>

        <div id="historyList" class="history-list"></div>
    </section>
@endsection

@push('styles')
<style>
    .history-hero {
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
            url('https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=1600&q=80') center/cover;
        box-shadow: 0 34px 80px rgba(49, 29, 15, .12);
    }

    .history-hero::before {
        content: "";
        position: absolute;
        inset: 18px;
        border: 1px solid rgba(255, 255, 255, .62);
        border-radius: 24px;
        pointer-events: none;
    }

    .history-hero-copy,
    .history-hero-visual {
        position: relative;
        z-index: 1;
    }

    .history-hero h1 {
        max-width: 780px;
        margin-top: 8px;
        font-size: clamp(42px, 5vw, 72px);
    }

    .history-hero .lead {
        max-width: 760px;
        font-size: 16px;
        font-weight: 700;
    }

    .history-hero-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 24px;
    }

    .history-hero-pills span {
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

    .history-hero-visual {
        min-height: 260px;
        display: grid;
        place-items: center;
    }

    .history-hero-visual img {
        width: min(360px, 80vw);
        aspect-ratio: 1 / .82;
        object-fit: cover;
        border: 10px solid rgba(255, 250, 242, .96);
        border-radius: 28px;
        box-shadow: 0 34px 80px rgba(43, 22, 11, .24);
        transform: rotate(2deg);
    }

    .hero-card {
        position: absolute;
        left: 0;
        bottom: 24px;
        display: grid;
        gap: 5px;
        min-width: 210px;
        padding: 16px 18px;
        border: 1px solid rgba(255, 250, 242, .72);
        border-radius: 18px;
        color: #fff8ed;
        background: rgba(43, 22, 11, .82);
        box-shadow: 0 22px 50px rgba(43, 22, 11, .22);
        backdrop-filter: blur(12px);
    }

    .hero-card span {
        color: inherit;
        opacity: .76;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .history-metric span {
        color: inherit;
        opacity: .78;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .hero-card strong {
        font-size: clamp(28px, 3vw, 38px);
        line-height: 1.05;
    }

    .history-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 20px;
    }

    .history-metric {
        min-height: 116px;
        display: grid;
        align-content: space-between;
        gap: 12px;
        padding: 20px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 20px;
        background: rgba(255, 253, 249, .92);
        box-shadow: 0 18px 42px rgba(49, 29, 15, .06);
    }

    .history-metric strong {
        font-size: clamp(24px, 2.4vw, 32px);
        line-height: 1;
    }

    .history-board {
        padding: 18px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 26px;
        background: rgba(255, 255, 255, .82);
        box-shadow: 0 30px 80px rgba(49, 29, 15, .10);
        backdrop-filter: blur(10px);
    }

    .history-toolbar {
        display: grid;
        grid-template-columns: minmax(240px, 1fr) auto minmax(160px, auto);
        gap: 12px;
        align-items: center;
        margin-bottom: 16px;
    }

    .history-search {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 48px;
        padding: 0 16px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 16px;
        background: #fffaf4;
    }

    .history-search svg {
        width: 20px;
        height: 20px;
        color: var(--sage);
        stroke: currentColor;
        stroke-width: 2;
        fill: none;
        flex: 0 0 auto;
    }

    .history-search input {
        width: 100%;
        min-width: 0;
        border: 0;
        outline: 0;
        color: var(--ink);
        background: transparent;
        font-weight: 700;
    }

    .history-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .filter-chip,
    .history-sort {
        min-height: 44px;
        border: 1px solid rgba(83, 58, 38, .13);
        border-radius: 999px;
        background: #fbf6ef;
        color: var(--muted);
        font-size: 13px;
        font-weight: 900;
    }

    .filter-chip {
        padding: 0 14px;
        transition: transform .18s ease, background .18s ease, color .18s ease, box-shadow .18s ease;
    }

    .filter-chip:hover {
        transform: translateY(-1px);
    }

    .filter-chip.active {
        color: #fff8ed;
        background: var(--coffee);
        box-shadow: 0 14px 28px rgba(43, 22, 11, .16);
    }

    .history-sort {
        min-width: 164px;
        padding: 0 14px;
        outline: 0;
    }

    .history-list {
        display: grid;
        gap: 12px;
    }

    .history-card {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        padding: 18px;
        overflow: hidden;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 20px;
        background: #fffdf9;
        box-shadow: 0 16px 36px rgba(49, 29, 15, .05);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .history-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: var(--gold);
    }

    .history-card.status-selesai::before { background: var(--sage); }
    .history-card.status-diproses::before { background: var(--caramel); }
    .history-card.status-masuk::before { background: var(--berry); }

    .history-card:hover {
        transform: translateY(-2px);
        border-color: rgba(100, 122, 84, .26);
        box-shadow: 0 22px 50px rgba(49, 29, 15, .10);
    }

    .history-main {
        min-width: 0;
        display: grid;
        gap: 12px;
    }

    .history-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .history-id {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .history-id strong {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: 16px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        color: var(--coffee);
        background: #f8e7cd;
        font-size: 11px;
        font-weight: 900;
        white-space: nowrap;
    }

    .status-badge.status-selesai {
        color: #334525;
        background: #e7efdf;
    }

    .status-badge.status-masuk {
        color: #7a3032;
        background: #f8e3e4;
    }

    .history-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 0 10px;
        border-radius: 999px;
        color: var(--muted);
        background: #f8f0e7;
        font-size: 12px;
        font-weight: 800;
    }

    .history-items {
        color: var(--muted);
        font-size: 13px;
        line-height: 1.65;
    }

    .history-side {
        display: grid;
        align-content: space-between;
        justify-items: end;
        gap: 12px;
        min-width: 160px;
    }

    .history-total {
        font-size: 18px;
        font-weight: 900;
        white-space: nowrap;
    }

    .detail-toggle {
        min-height: 38px;
        padding: 0 13px;
        border: 1px solid rgba(83, 58, 38, .13);
        border-radius: 999px;
        color: var(--coffee);
        background: #fbf6ef;
        font-size: 12px;
        font-weight: 900;
        transition: background .18s ease, color .18s ease;
    }

    .detail-toggle:hover {
        color: #fff8ed;
        background: var(--coffee);
    }

    .history-detail {
        grid-column: 1 / -1;
        display: none;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        padding: 14px;
        border-radius: 16px;
        background: #fbf6ef;
    }

    .history-card.open .history-detail {
        display: grid;
        animation: detailIn .18s ease;
    }

    .detail-list {
        display: grid;
        gap: 8px;
    }

    .detail-line {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
    }

    .detail-line strong {
        color: var(--ink);
    }

    .history-empty {
        display: grid;
        place-items: center;
        gap: 10px;
        min-height: 260px;
        padding: 28px;
        border: 1px dashed rgba(83, 58, 38, .18);
        border-radius: 20px;
        color: var(--muted);
        background: #fffaf4;
        text-align: center;
    }

    .history-empty strong {
        color: var(--ink);
        font-size: 18px;
    }

    @keyframes detailIn {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1080px) {
        .history-hero,
        .history-toolbar {
            grid-template-columns: 1fr;
        }

        .history-hero-visual {
            min-height: 260px;
        }

        .history-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .history-controls,
        .history-sort {
            width: 100%;
        }
    }

    @media (max-width: 700px) {
        .history-hero,
        .history-board {
            border-radius: 20px;
            padding: 16px;
        }

        .history-hero::before {
            inset: 10px;
            border-radius: 18px;
        }

        .history-hero-visual img {
            width: min(300px, 78vw);
        }

        .hero-card {
            position: relative;
            inset: auto;
            width: 100%;
            margin-top: -12px;
        }

        .history-metrics,
        .history-detail {
            grid-template-columns: 1fr;
        }

        .history-card {
            grid-template-columns: 1fr;
        }

        .history-row {
            align-items: flex-start;
            flex-direction: column;
        }

        .history-side {
            width: 100%;
            min-width: 0;
            grid-template-columns: 1fr auto;
            align-items: center;
            justify-items: start;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let activeHistoryStatus = 'Semua';

    const getOrderQty = (order) => (order.items || []).reduce((sum, item) => sum + Number(item.qty || 0), 0);
    const normalizeHistoryText = (value) => String(value || '').trim().toLowerCase();
    const normalizeHistoryStatus = (status) => {
        const normalized = normalizeHistoryText(status || 'Masuk');

        if (normalized === 'selesai') return 'Selesai';
        if (normalized === 'diproses') return 'Diproses';

        return 'Masuk';
    };
    const getStatusClass = (status) => `status-${normalizeHistoryStatus(status).toLowerCase().replace(/\s+/g, '-')}`;

    function parseHistoryDate(order) {
        const value = String(order.createdAt || '').trim();
        const match = value.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})(?:\s+(\d{1,2}):(\d{1,2}))?/);

        if (match) {
            const [, day, month, year, hour = '0', minute = '0'] = match;
            return new Date(
                Number(year),
                Number(month) - 1,
                Number(day),
                Number(hour),
                Number(minute)
            ).getTime();
        }

        const parsed = Date.parse(value);
        return Number.isNaN(parsed) ? 0 : parsed;
    }

    function getHistorySearch() {
        return document.getElementById('historySearch').value.trim().toLowerCase();
    }

    function filteredHistoryOrders() {
        const search = getHistorySearch();
        const sort = document.getElementById('historySort').value;

        let orders = getOrders().filter((order) => {
            const status = normalizeHistoryStatus(order.status);
            const text = [
                order.id,
                order.customer,
                order.table,
                order.cashier,
                status,
                order.createdAt,
                (order.items || []).map((item) => item.name).join(' ')
            ].join(' ').toLowerCase();

            const matchesStatus = activeHistoryStatus === 'Semua' || status === activeHistoryStatus;
            const matchesSearch = !search || text.includes(search);

            return matchesStatus && matchesSearch;
        });

        return orders.sort((a, b) => {
            if (sort === 'highest') return Number(b.total || 0) - Number(a.total || 0);
            if (sort === 'lowest') return Number(a.total || 0) - Number(b.total || 0);
            if (sort === 'items') return getOrderQty(b) - getOrderQty(a);
            if (sort === 'oldest') {
                const timeDiff = parseHistoryDate(a) - parseHistoryDate(b);
                return timeDiff || String(a.id || '').localeCompare(String(b.id || ''), undefined, { numeric: true });
            }

            const timeDiff = parseHistoryDate(b) - parseHistoryDate(a);
            return timeDiff || String(b.id || '').localeCompare(String(a.id || ''), undefined, { numeric: true });
        });
    }

    function renderHistoryMetrics(orders) {
        const totalRevenue = orders.reduce((sum, order) => sum + Number(order.total || 0), 0);
        const activeOrders = orders.filter((order) => normalizeHistoryStatus(order.status) !== 'Selesai').length;
        const totalItems = orders.reduce((sum, order) => sum + getOrderQty(order), 0);
        const average = orders.length ? totalRevenue / orders.length : 0;

        document.getElementById('heroRevenue').textContent = rupiah(totalRevenue);
        document.getElementById('heroOrders').textContent = `${orders.length} transaksi tercatat`;
        document.getElementById('metricOrders').textContent = orders.length;
        document.getElementById('metricActive').textContent = activeOrders;
        document.getElementById('metricItems').textContent = totalItems;
        document.getElementById('metricAverage').textContent = rupiah(average);
    }

    function renderHistory() {
        const orders = filteredHistoryOrders();
        renderHistoryMetrics(orders);

        const historyList = document.getElementById('historyList');
        historyList.innerHTML = orders.length
            ? orders.map((order, index) => {
                const status = normalizeHistoryStatus(order.status);
                const statusClass = getStatusClass(status);
                const itemSummary = (order.items || []).map((item) => `${item.qty}x ${item.name}`).join(', ');
                const detailRows = (order.items || []).map((item) => `
                    <div class="detail-line">
                        <span>${item.qty}x ${item.name}</span>
                        <strong>${rupiah(item.price * item.qty)}</strong>
                    </div>
                `).join('');

                return `
                    <article class="history-card ${statusClass}" data-index="${index}">
                        <div class="history-main">
                            <div class="history-row">
                                <div class="history-id">
                                    <strong>${order.id} - ${order.customer}</strong>
                                    <span class="status-badge ${statusClass}">${status}</span>
                                </div>
                            </div>
                            <div class="history-meta">
                                <span class="meta-pill">${order.table || 'Meja -'}</span>
                                <span class="meta-pill">${order.createdAt || 'Waktu tidak tercatat'}</span>
                                <span class="meta-pill">${order.cashier || 'Kasir'}</span>
                                <span class="meta-pill">${getOrderQty(order)} item</span>
                            </div>
                            <p class="history-items">${itemSummary}</p>
                        </div>
                        <div class="history-side">
                            <span class="history-total">${rupiah(order.total)}</span>
                            <button class="detail-toggle" type="button" onclick="toggleHistoryDetail(this)">Detail</button>
                        </div>
                        <div class="history-detail">
                            <div class="detail-list">
                                ${detailRows}
                            </div>
                            <div class="detail-list">
                                <div class="detail-line"><span>Kode pesanan</span><strong>${order.id}</strong></div>
                                <div class="detail-line"><span>Pelanggan</span><strong>${order.customer}</strong></div>
                                <div class="detail-line"><span>Kasir</span><strong>${order.cashier || 'Kasir'}</strong></div>
                                <div class="detail-line"><span>Total transaksi</span><strong>${rupiah(order.total)}</strong></div>
                            </div>
                        </div>
                    </article>
                `;
            }).join('')
            : `
                <div class="history-empty">
                    <strong>Riwayat tidak ditemukan</strong>
                    <span>Coba ubah kata kunci pencarian atau filter status pesanan.</span>
                </div>
            `;
    }

    function toggleHistoryDetail(button) {
        const card = button.closest('.history-card');
        const isOpen = card.classList.toggle('open');
        button.textContent = isOpen ? 'Tutup' : 'Detail';
    }

    document.getElementById('historySearch').addEventListener('input', renderHistory);
    document.getElementById('historySort').addEventListener('change', renderHistory);
    document.querySelectorAll('.filter-chip').forEach((button) => {
        button.addEventListener('click', () => {
            activeHistoryStatus = button.dataset.status;
            document.querySelectorAll('.filter-chip').forEach((chip) => chip.classList.toggle('active', chip === button));
            renderHistory();
        });
    });

    window.addEventListener('wana:storage', renderHistory);
    renderHistory();
</script>
@endpush
