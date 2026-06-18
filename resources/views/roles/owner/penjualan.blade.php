@extends('layouts.wana', ['title' => 'Laporan Penjualan | Wana Cafe'])

@section('content')
<div class="sales-dashboard">
    <section class="sales-hero">
        <div class="sales-hero-copy">
            <div class="eyebrow">Owner Sales Center</div>
            <h1>Laporan Penjualan Wana Cafe</h1>
            <p class="lead">Pantau omzet, status transaksi, performa menu, dan aktivitas kasir dalam tampilan yang lebih cepat dibaca.</p>
            <div class="sales-hero-actions">
                <button class="btn sales-primary" type="button" onclick="refreshSalesFromDatabase()">Refresh Data</button>
                <a class="btn sales-secondary" href="{{ route('owner.export') }}">Export Laporan</a>
            </div>
        </div>

        <div class="sales-hero-visual" aria-hidden="true">
            <div class="sales-console">
                <img src="https://images.unsplash.com/photo-1559925393-8be0ec4767c8?auto=format&fit=crop&w=900&q=85" alt="">
                <div class="sales-float revenue">
                    <span>Total Omzet</span>
                    <strong id="heroSalesRevenue">Rp 0</strong>
                </div>
                <div class="sales-float orders">
                    <span>Transaksi</span>
                    <strong id="heroSalesOrders">0 order</strong>
                </div>
                <div id="heroSalesBars" class="sales-bars"></div>
            </div>
        </div>
    </section>

    <section class="sales-metrics" aria-label="Ringkasan penjualan">
        <article class="sales-metric metric-revenue">
            <span>Omzet Filter</span>
            <strong id="metricRevenue">Rp 0</strong>
            <p>Total nilai dari transaksi yang sedang tampil.</p>
        </article>
        <article class="sales-metric metric-orders">
            <span>Total Transaksi</span>
            <strong id="metricOrders">0</strong>
            <p>Jumlah transaksi sesuai filter aktif.</p>
        </article>
        <article class="sales-metric metric-average">
            <span>Rata-rata Bill</span>
            <strong id="metricAverage">Rp 0</strong>
            <p>Nilai rata-rata setiap pesanan.</p>
        </article>
        <article class="sales-metric metric-done">
            <span>Selesai</span>
            <strong id="metricDone">0%</strong>
            <p>Persentase transaksi yang sudah selesai.</p>
        </article>
    </section>

    <section class="sales-layout">
        <div class="sales-main">
            <div class="sales-panel">
                <div class="sales-toolbar">
                    <div class="sales-search">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m16.5 16.5 4 4"></path></svg>
                        <input id="salesSearch" type="search" placeholder="Cari kode, pelanggan, kasir, meja, atau menu..." autocomplete="off">
                    </div>
                    <div class="sales-segment" role="group" aria-label="Filter status transaksi">
                        <button class="active" type="button" data-sales-status="Semua">Semua</button>
                        <button type="button" data-sales-status="Masuk">Masuk</button>
                        <button type="button" data-sales-status="Diproses">Diproses</button>
                        <button type="button" data-sales-status="Selesai">Selesai</button>
                    </div>
                    <select id="salesSort" class="sales-sort" aria-label="Urutkan laporan">
                        <option value="newest">Terbaru</option>
                        <option value="highest">Omzet terbesar</option>
                        <option value="lowest">Omzet terkecil</option>
                        <option value="items">Item terbanyak</option>
                    </select>
                </div>
                <div id="salesRows" class="sales-list"></div>
            </div>
        </div>

        <aside class="sales-side">
            <div class="sales-panel">
                <div class="sales-panel-head">
                    <div>
                        <h2>Komposisi Status</h2>
                        <span>Live dari database</span>
                    </div>
                    <button class="mini-refresh" type="button" onclick="refreshSalesFromDatabase()">Refresh</button>
                </div>
                <div class="sales-ring">
                    <svg viewBox="0 0 120 120" aria-hidden="true">
                        <circle cx="60" cy="60" r="48"></circle>
                        <circle id="salesDoneRing" cx="60" cy="60" r="48"></circle>
                    </svg>
                    <div>
                        <strong id="sideDonePercent">0%</strong>
                        <span>selesai</span>
                    </div>
                </div>
                <div class="sales-progress">
                    <div class="progress-row"><div><span>Masuk</span><strong id="countMasuk">0</strong></div><i><b id="barMasuk"></b></i></div>
                    <div class="progress-row"><div><span>Diproses</span><strong id="countDiproses">0</strong></div><i><b id="barDiproses"></b></i></div>
                    <div class="progress-row"><div><span>Selesai</span><strong id="countSelesai">0</strong></div><i><b id="barSelesai"></b></i></div>
                </div>
            </div>

            <div class="sales-panel">
                <div class="sales-panel-head">
                    <div>
                        <h2>Menu Terjual</h2>
                        <span>Top item dari transaksi</span>
                    </div>
                </div>
                <div id="topSalesItems" class="top-sales-items"></div>
            </div>

            <div class="sales-panel">
                <div class="sales-panel-head">
                    <div>
                        <h2>Insight Cepat</h2>
                        <span>Ringkasan operasional</span>
                    </div>
                </div>
                <div id="salesInsights" class="sales-insights"></div>
            </div>
        </aside>
    </section>
</div>
@endsection

@push('styles')
<style>
    .sales-dashboard {
        width: min(100%, 1360px);
        margin: 0 auto;
        display: grid;
        gap: 20px;
    }

    .sales-hero {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(320px, 420px);
        gap: 30px;
        align-items: center;
        min-height: 340px;
        padding: 32px;
        overflow: hidden;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 30px;
        background:
            linear-gradient(110deg, rgba(43, 22, 11, .94) 0%, rgba(65, 38, 23, .86) 48%, rgba(143, 99, 61, .5) 100%),
            url('https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=1600&q=85') center/cover;
        box-shadow: 0 34px 80px rgba(49, 29, 15, .12);
    }

    .sales-hero::before {
        content: "";
        position: absolute;
        inset: 18px;
        border: 1px solid rgba(255, 255, 255, .62);
        border-radius: 24px;
        pointer-events: none;
    }

    .sales-hero-copy,
    .sales-hero-visual {
        position: relative;
        z-index: 1;
    }

    .sales-hero-copy .eyebrow {
        color: #f0d7a7;
    }

    .sales-hero-copy h1 {
        max-width: 820px;
        margin-top: 8px;
        color: #fff8ed;
        font-size: clamp(42px, 5vw, 72px);
        line-height: .98;
    }

    .sales-hero-copy .lead {
        max-width: 720px;
        margin-top: 16px;
        color: rgba(255, 248, 237, .84);
        font-size: 16px;
        font-weight: 750;
    }

    .sales-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 26px;
    }

    .sales-hero-actions .btn,
    .mini-refresh {
        width: auto;
        min-height: 44px;
        border-radius: 999px;
    }

    .sales-primary {
        background: var(--coffee);
        color: #fff8ed;
        box-shadow: 0 16px 34px rgba(43, 22, 11, .18);
    }

    .sales-secondary,
    .mini-refresh {
        color: var(--coffee);
        border: 1px solid rgba(83, 58, 38, .13);
        background: rgba(255, 250, 242, .88);
    }

    .sales-hero-visual {
        min-height: 276px;
        display: grid;
        place-items: center;
    }

    .sales-console {
        position: relative;
        width: min(390px, 100%);
        min-height: 272px;
        padding: 14px;
        border: 1px solid rgba(255, 248, 237, .42);
        border-radius: 28px;
        background: rgba(255, 250, 242, .14);
        box-shadow: 0 34px 80px rgba(20, 10, 5, .24);
        backdrop-filter: blur(10px);
    }

    .sales-console img {
        width: 100%;
        height: 238px;
        display: block;
        object-fit: cover;
        border: 1px solid rgba(255, 248, 237, .44);
        border-radius: 22px;
    }

    .sales-float {
        position: absolute;
        display: grid;
        gap: 4px;
        min-width: 150px;
        padding: 14px 16px;
        border: 1px solid rgba(255, 250, 242, .78);
        border-radius: 16px;
        background: rgba(255, 253, 249, .92);
        box-shadow: 0 20px 46px rgba(49, 29, 15, .13);
        backdrop-filter: blur(12px);
    }

    .sales-float.revenue {
        top: 28px;
        right: -18px;
    }

    .sales-float.orders {
        left: -18px;
        bottom: 32px;
    }

    .sales-float span {
        color: var(--sage);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .sales-float strong {
        font-size: 20px;
    }

    .sales-bars {
        position: absolute;
        right: 22px;
        bottom: 22px;
        display: flex;
        align-items: end;
        gap: 7px;
        width: 116px;
        height: 66px;
        padding: 10px;
        border-radius: 16px;
        background: rgba(43, 22, 11, .76);
        box-shadow: 0 16px 34px rgba(20, 10, 5, .22);
    }

    .sales-bars i {
        flex: 1;
        min-width: 0;
        height: var(--h, 28%);
        border-radius: 999px;
        background: linear-gradient(180deg, #f3d7a7, #c8844f);
        transition: height .24s ease;
    }

    .sales-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .sales-metric,
    .sales-panel {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(83, 58, 38, .1);
        border-radius: 22px;
        background: rgba(255, 253, 249, .9);
        box-shadow: 0 24px 58px rgba(49, 29, 15, .08);
    }

    .sales-metric {
        min-height: 144px;
        padding: 20px;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .sales-metric:hover {
        transform: translateY(-4px);
        box-shadow: 0 30px 70px rgba(49, 29, 15, .13);
    }

    .sales-metric::after {
        content: "";
        position: absolute;
        right: -34px;
        bottom: -42px;
        width: 128px;
        height: 128px;
        border-radius: 999px;
        background: rgba(100, 122, 84, .12);
    }

    .metric-revenue::after { background: rgba(198, 122, 64, .14); }
    .metric-orders::after { background: rgba(100, 122, 84, .14); }
    .metric-average::after { background: rgba(227, 180, 103, .18); }
    .metric-done::after { background: rgba(176, 80, 82, .14); }

    .sales-metric span,
    .sales-panel-head span {
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .sales-metric strong {
        position: relative;
        z-index: 1;
        display: block;
        margin-top: 14px;
        font-size: clamp(28px, 3vw, 40px);
        line-height: 1;
    }

    .sales-metric p {
        position: relative;
        z-index: 1;
        margin-top: 12px;
        color: var(--sage);
        font-weight: 700;
        line-height: 1.5;
    }

    .sales-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.42fr) minmax(330px, .58fr);
        gap: 20px;
        align-items: start;
    }

    .sales-main,
    .sales-side {
        display: grid;
        gap: 18px;
    }

    .sales-panel {
        padding: 18px;
    }

    .sales-panel-head,
    .sales-toolbar {
        display: grid;
        gap: 12px;
    }

    .sales-panel-head {
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: start;
        margin-bottom: 16px;
    }

    .sales-panel-head h2 {
        margin: 0;
        font-size: 18px;
    }

    .sales-toolbar {
        grid-template-columns: minmax(240px, 1fr) auto minmax(160px, auto);
        align-items: center;
        margin-bottom: 16px;
    }

    .sales-search {
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 50px;
        padding: 0 16px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 18px;
        background: #fffaf4;
    }

    .sales-search svg {
        width: 20px;
        height: 20px;
        color: var(--sage);
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        flex: 0 0 auto;
    }

    .sales-search input {
        width: 100%;
        min-width: 0;
        border: 0;
        outline: 0;
        color: var(--ink);
        background: transparent;
        font-weight: 800;
    }

    .sales-segment {
        display: flex;
        gap: 4px;
        padding: 5px;
        border: 1px solid rgba(83, 58, 38, .1);
        border-radius: 999px;
        background: rgba(245, 234, 219, .72);
    }

    .sales-segment button,
    .sales-sort,
    .mini-refresh {
        font-size: 13px;
        font-weight: 900;
    }

    .sales-segment button {
        min-height: 38px;
        padding: 0 14px;
        border: 0;
        border-radius: 999px;
        color: var(--muted);
        background: transparent;
    }

    .sales-segment button.active {
        color: #fff8ed;
        background: var(--coffee);
        box-shadow: 0 12px 24px rgba(49, 29, 15, .13);
    }

    .sales-sort {
        min-height: 48px;
        min-width: 170px;
        padding: 0 14px;
        border: 1px solid rgba(83, 58, 38, .13);
        border-radius: 999px;
        color: var(--muted);
        background: #fbf6ef;
        outline: 0;
    }

    .sales-list {
        display: grid;
        gap: 12px;
        max-height: 620px;
        overflow: auto;
        padding-right: 5px;
    }

    .sales-card {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 16px;
        align-items: center;
        padding: 18px;
        overflow: hidden;
        border: 1px solid rgba(83, 58, 38, .09);
        border-radius: 18px;
        background: rgba(255, 250, 242, .74);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
    }

    .sales-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: var(--gold);
    }

    .sales-card.status-selesai::before { background: var(--sage); }
    .sales-card.status-diproses::before { background: var(--caramel); }
    .sales-card.status-masuk::before { background: var(--berry); }

    .sales-card:hover {
        transform: translateY(-2px);
        border-color: rgba(100, 122, 84, .24);
        box-shadow: 0 22px 50px rgba(49, 29, 15, .1);
    }

    .sales-card h3 {
        margin: 0;
        font-size: 17px;
    }

    .sales-card p {
        margin-top: 8px;
        color: var(--muted);
        font-weight: 700;
        line-height: 1.55;
    }

    .sales-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }

    .sales-pill {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        color: var(--coffee);
        background: rgba(245, 234, 219, .86);
        font-size: 12px;
        font-weight: 900;
    }

    .sales-pill.status-selesai {
        color: #334525;
        background: #e7efdf;
    }

    .sales-pill.status-masuk {
        color: #7a3032;
        background: #f8e3e4;
    }

    .sales-total {
        display: grid;
        gap: 9px;
        justify-items: end;
        min-width: 150px;
        text-align: right;
    }

    .sales-total strong {
        font-size: 20px;
    }

    .sales-ring {
        position: relative;
        display: grid;
        place-items: center;
        min-height: 190px;
    }

    .sales-ring svg {
        width: 166px;
        height: 166px;
        transform: rotate(-90deg);
    }

    .sales-ring circle {
        fill: none;
        stroke-width: 12;
        stroke: rgba(83, 58, 38, .1);
    }

    .sales-ring #salesDoneRing {
        stroke: #647a54;
        stroke-linecap: round;
        stroke-dasharray: 301.59;
        stroke-dashoffset: 301.59;
        transition: stroke-dashoffset .35s ease;
    }

    .sales-ring div {
        position: absolute;
        display: grid;
        gap: 6px;
        text-align: center;
    }

    .sales-ring strong {
        font-size: 38px;
        line-height: 1;
    }

    .sales-ring span {
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sales-progress,
    .top-sales-items,
    .sales-insights {
        display: grid;
        gap: 12px;
    }

    .progress-row {
        display: grid;
        gap: 8px;
    }

    .progress-row > div {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        font-weight: 900;
    }

    .progress-row span {
        color: var(--muted);
        font-size: 13px;
    }

    .progress-row i {
        display: block;
        height: 11px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(83, 58, 38, .1);
    }

    .progress-row b {
        display: block;
        width: 0;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #647a54, #c8844f);
        transition: width .3s ease;
    }

    .top-sales-item,
    .sales-insight {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
        padding: 14px;
        border: 1px solid rgba(83, 58, 38, .09);
        border-radius: 16px;
        background: rgba(255, 250, 242, .74);
    }

    .top-sales-item strong,
    .sales-insight strong {
        display: block;
        font-size: 14px;
    }

    .top-sales-item span,
    .sales-insight span {
        color: var(--muted);
        font-size: 12px;
        font-weight: 750;
    }

    .top-sales-bar {
        grid-column: 1 / -1;
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(83, 58, 38, .1);
    }

    .top-sales-bar i {
        display: block;
        width: var(--bar, 0%);
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #c8844f, #647a54);
    }

    .sales-empty {
        padding: 28px;
        border: 1px dashed rgba(83, 58, 38, .18);
        border-radius: 18px;
        color: var(--muted);
        background: rgba(255, 250, 242, .62);
        text-align: center;
        font-weight: 800;
    }

    @media (max-width: 1100px) {
        .sales-hero,
        .sales-layout,
        .sales-toolbar {
            grid-template-columns: 1fr;
        }

        .sales-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .sales-hero {
            min-height: auto;
            padding: 24px;
            border-radius: 24px;
        }

        .sales-hero::before {
            inset: 12px;
            border-radius: 18px;
        }

        .sales-hero-copy h1 {
            font-size: clamp(36px, 13vw, 54px);
        }

        .sales-hero-actions .btn,
        .mini-refresh,
        .sales-sort {
            width: 100%;
        }

        .sales-console {
            width: min(340px, 100%);
        }

        .sales-float.revenue {
            right: 4px;
        }

        .sales-float.orders {
            left: 4px;
            bottom: 22px;
        }

        .sales-bars {
            right: 12px;
            bottom: 12px;
        }

        .sales-metrics,
        .sales-card,
        .sales-panel-head {
            grid-template-columns: 1fr;
        }

        .sales-panel,
        .sales-metric {
            border-radius: 18px;
            padding: 18px;
        }

        .sales-segment {
            width: 100%;
            overflow-x: auto;
        }

        .sales-segment button {
            flex: 1 0 auto;
        }

        .sales-total {
            justify-items: start;
            text-align: left;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let activeSalesStatus = 'Semua';

    const salesQty = (order) => (order.items || []).reduce((sum, item) => sum + Number(item.qty || 0), 0);
    const normalizeSales = (value) => String(value || '').trim().toLowerCase();
    const normalizeSalesStatus = (status) => {
        const normalized = normalizeSales(status || 'Masuk');
        if (normalized === 'selesai') return 'Selesai';
        if (normalized === 'diproses') return 'Diproses';
        return 'Masuk';
    };
    const salesStatusClass = (status) => `status-${normalizeSalesStatus(status).toLowerCase()}`;

    function salesDate(order) {
        const value = String(order.createdAt || '').trim();
        const match = value.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})(?:\s+(\d{1,2}):(\d{1,2}))?/);
        if (match) {
            const [, day, month, year, hour = '0', minute = '0'] = match;
            return new Date(Number(year), Number(month) - 1, Number(day), Number(hour), Number(minute)).getTime();
        }
        const parsed = Date.parse(value);
        return Number.isNaN(parsed) ? 0 : parsed;
    }

    function salesEscape(value) {
        return String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[char]));
    }

    function salesItemSummary(order) {
        return (order.items || []).map((item) => `${salesEscape(item.qty || 1)}x ${salesEscape(item.name)}`).join(', ') || 'Belum ada detail item.';
    }

    function filteredSalesOrders() {
        const search = normalizeSales(document.getElementById('salesSearch').value);
        const sort = document.getElementById('salesSort').value;

        return getOrders().filter((order) => {
            const status = normalizeSalesStatus(order.status);
            const text = [
                order.id,
                order.customer,
                order.cashier,
                order.table,
                order.createdAt,
                status,
                (order.items || []).map((item) => item.name).join(' ')
            ].join(' ').toLowerCase();
            return (activeSalesStatus === 'Semua' || status === activeSalesStatus) && (!search || text.includes(search));
        }).sort((a, b) => {
            if (sort === 'highest') return Number(b.total || 0) - Number(a.total || 0);
            if (sort === 'lowest') return Number(a.total || 0) - Number(b.total || 0);
            if (sort === 'items') return salesQty(b) - salesQty(a);
            return (salesDate(b) - salesDate(a)) || String(b.id || '').localeCompare(String(a.id || ''), undefined, { numeric: true });
        });
    }

    function statusCount(orders, status) {
        return orders.filter((order) => normalizeSalesStatus(order.status) === status).length;
    }

    function setSalesBar(id, value, total) {
        const bar = document.getElementById(id);
        if (!bar) return;
        bar.style.width = `${total ? Math.min(100, Math.round((value / total) * 100)) : 0}%`;
    }

    function renderSalesPage() {
        const allOrders = getOrders();
        const orders = filteredSalesOrders();
        const revenue = orders.reduce((sum, order) => sum + Number(order.total || 0), 0);
        const allRevenue = allOrders.reduce((sum, order) => sum + Number(order.total || 0), 0);
        const done = statusCount(orders, 'Selesai');
        const donePercent = orders.length ? Math.round((done / orders.length) * 100) : 0;
        const totalItems = orders.reduce((sum, order) => sum + salesQty(order), 0);

        document.getElementById('heroSalesRevenue').textContent = rupiah(allRevenue);
        document.getElementById('heroSalesOrders').textContent = `${allOrders.length} order`;
        document.getElementById('metricRevenue').textContent = rupiah(revenue);
        document.getElementById('metricOrders').textContent = orders.length;
        document.getElementById('metricAverage').textContent = rupiah(orders.length ? revenue / orders.length : 0);
        document.getElementById('metricDone').textContent = `${donePercent}%`;
        document.getElementById('sideDonePercent').textContent = `${donePercent}%`;

        const ring = document.getElementById('salesDoneRing');
        const circumference = 301.59;
        ring.style.strokeDashoffset = circumference - (circumference * donePercent / 100);

        const incoming = statusCount(orders, 'Masuk');
        const processing = statusCount(orders, 'Diproses');
        document.getElementById('countMasuk').textContent = incoming;
        document.getElementById('countDiproses').textContent = processing;
        document.getElementById('countSelesai').textContent = done;
        setSalesBar('barMasuk', incoming, orders.length);
        setSalesBar('barDiproses', processing, orders.length);
        setSalesBar('barSelesai', done, orders.length);

        const recentTotals = [...allOrders].sort((a, b) => salesDate(a) - salesDate(b)).slice(-5).map((order) => Number(order.total || 0));
        const maxRecent = Math.max(...recentTotals, 1);
        document.getElementById('heroSalesBars').innerHTML = (recentTotals.length ? recentTotals : [1, 1, 1, 1, 1]).map((total) => {
            const height = Math.max(22, Math.round((total / maxRecent) * 92));
            return `<i style="--h:${height}%"></i>`;
        }).join('');

        document.getElementById('salesRows').innerHTML = orders.length
            ? orders.map((order) => {
                const status = normalizeSalesStatus(order.status);
                const statusClass = salesStatusClass(status);
                return `
                    <article class="sales-card ${statusClass}">
                        <div>
                            <h3>${salesEscape(order.id)} - ${salesEscape(order.customer || 'Pelanggan')}</h3>
                            <p>${salesItemSummary(order)}</p>
                            <div class="sales-meta">
                                <span class="sales-pill ${statusClass}">${status}</span>
                                <span class="sales-pill">${salesEscape(order.table || 'Meja -')}</span>
                                <span class="sales-pill">${salesEscape(order.createdAt || 'Waktu tidak tercatat')}</span>
                                <span class="sales-pill">${salesQty(order)} item</span>
                            </div>
                        </div>
                        <div class="sales-total">
                            <strong>${rupiah(order.total || 0)}</strong>
                            <span class="sales-pill">${salesEscape(order.cashier || 'Kasir')}</span>
                        </div>
                    </article>
                `;
            }).join('')
            : '<div class="sales-empty">Belum ada laporan penjualan pada filter ini.</div>';

        const itemMap = new Map();
        allOrders.forEach((order) => (order.items || []).forEach((item) => {
            const name = item.name || 'Menu';
            const current = itemMap.get(name) || { qty: 0, revenue: 0 };
            current.qty += Number(item.qty || 0);
            current.revenue += Number(item.qty || 0) * Number(item.price || 0);
            itemMap.set(name, current);
        }));

        const topItems = [...itemMap.entries()].sort((a, b) => b[1].revenue - a[1].revenue).slice(0, 5);
        const topMax = Math.max(...topItems.map(([, item]) => item.revenue), 1);
        document.getElementById('topSalesItems').innerHTML = topItems.length
            ? topItems.map(([name, item]) => `
                <div class="top-sales-item">
                    <div>
                        <strong>${salesEscape(name)}</strong>
                        <span>${item.qty} item terjual</span>
                    </div>
                    <b>${rupiah(item.revenue)}</b>
                    <div class="top-sales-bar"><i style="--bar:${Math.max(8, Math.round((item.revenue / topMax) * 100))}%"></i></div>
                </div>
            `).join('')
            : '<div class="sales-empty">Belum ada menu terjual.</div>';

        const bestOrder = [...orders].sort((a, b) => Number(b.total || 0) - Number(a.total || 0))[0];
        const bestCashier = Object.entries(allOrders.reduce((carry, order) => {
            const cashier = order.cashier || 'Kasir';
            carry[cashier] = (carry[cashier] || 0) + Number(order.total || 0);
            return carry;
        }, {})).sort((a, b) => b[1] - a[1])[0];

        document.getElementById('salesInsights').innerHTML = `
            <div class="sales-insight">
                <div><strong>Order terbesar</strong><span>${bestOrder ? `${salesEscape(bestOrder.id)} - ${salesEscape(bestOrder.customer || 'Pelanggan')}` : 'Belum ada order.'}</span></div>
                <b>${bestOrder ? rupiah(bestOrder.total || 0) : '-'}</b>
            </div>
            <div class="sales-insight">
                <div><strong>Kasir tertinggi</strong><span>${bestCashier ? salesEscape(bestCashier[0]) : 'Belum ada transaksi.'}</span></div>
                <b>${bestCashier ? rupiah(bestCashier[1]) : '-'}</b>
            </div>
            <div class="sales-insight">
                <div><strong>Item tampil</strong><span>Total item dari filter aktif</span></div>
                <b>${totalItems}</b>
            </div>
        `;
    }

    async function refreshSalesFromDatabase() {
        try {
            const payload = await wanaRequest('{{ route('owner.dashboard.feed') }}', { method: 'GET' });
            setProducts(payload.products || []);
            setOrders(payload.orders || []);
            setComplaints(payload.chats || []);
            if (typeof setMaterialsStore === 'function') setMaterialsStore(payload.materials || []);
            setKitchenHistory(payload.activities || []);
        } catch (error) {
            notify(error.message);
            return;
        }
        renderSalesPage();
    }

    document.getElementById('salesSearch').addEventListener('input', renderSalesPage);
    document.getElementById('salesSort').addEventListener('change', renderSalesPage);
    document.querySelectorAll('[data-sales-status]').forEach((button) => {
        button.addEventListener('click', () => {
            activeSalesStatus = button.dataset.salesStatus;
            document.querySelectorAll('[data-sales-status]').forEach((item) => item.classList.toggle('active', item === button));
            renderSalesPage();
        });
    });

    window.addEventListener('wana:storage', renderSalesPage);
    refreshSalesFromDatabase();
</script>
@endpush
