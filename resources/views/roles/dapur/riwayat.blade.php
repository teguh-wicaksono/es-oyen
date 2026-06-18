@extends('layouts.wana', ['title' => 'Riwayat Dapur | Wana Cafe'])

@section('content')
    <section class="history-head">
        <div class="hero-copy">
            <div class="eyebrow">Dapur</div>
            <h1>Riwayat Aktivitas</h1>
            <p class="lead">Tracking aktivitas dapur berdasarkan perubahan pesanan, produk, dan stok bahan.</p>
            <div class="hero-actions">
                <button class="btn hero-action" type="button" onclick="renderHistory()">Refresh</button>
            </div>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <div class="coffee-orbit">
                <img src="https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=1200&q=85" alt="">
            </div>
            <div class="floating-note top-note">
                <span>Total Log</span>
                <strong id="heroTotalHistory">0 log</strong>
            </div>
            <div class="floating-note bottom-note">
                <span>Pesanan</span>
                <strong id="heroOrderHistory">0 log</strong>
            </div>
        </div>
    </section>

    <section class="history-metrics">
        <div class="history-metric">
            <span>Pesanan</span>
            <strong id="orderHistoryCount">0</strong>
        </div>
        <div class="history-metric">
            <span>Produk</span>
            <strong id="productHistoryCount">0</strong>
        </div>
        <div class="history-metric">
            <span>Stok</span>
            <strong id="stockHistoryCount">0</strong>
        </div>
        <div class="history-metric">
            <span>Total Log</span>
            <strong id="totalHistoryCount">0</strong>
        </div>
    </section>

    <section class="history-panel">
        <div class="history-toolbar">
            <div class="history-tabs" aria-label="Filter riwayat">
                <button class="active" data-filter="Semua" type="button" onclick="setHistoryFilter('Semua')">Semua</button>
                <button data-filter="Pesanan" type="button" onclick="setHistoryFilter('Pesanan')">Pesanan</button>
                <button data-filter="Produk" type="button" onclick="setHistoryFilter('Produk')">Produk</button>
                <button data-filter="Stok" type="button" onclick="setHistoryFilter('Stok')">Stok</button>
            </div>
            <div class="history-search">
                <span class="search-icon" aria-hidden="true"></span>
                <input id="historySearch" type="search" placeholder="Cari aktivitas..." oninput="renderHistory()">
            </div>
            <select id="historySort" class="history-sort" aria-label="Urutkan riwayat" onchange="renderHistory()">
                <option value="newest">Terbaru</option>
                <option value="oldest">Terlama</option>
                <option value="type">Kategori A-Z</option>
                <option value="actor">Aktor A-Z</option>
            </select>
        </div>

        <div id="historyList" class="history-list"></div>
    </section>
@endsection

@push('styles')
<style>
    .history-head {
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
            url('https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&w=1600&q=80') center/cover;
        box-shadow: 0 34px 80px rgba(49, 29, 15, .12);
    }

    .history-head::before {
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
        max-width: 760px;
        margin-top: 8px;
        font-size: clamp(42px, 5vw, 72px);
    }

    .hero-copy .lead {
        max-width: 760px;
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

    .hero-action {
        width: auto;
        min-height: 48px;
        padding: 0 20px;
        border-radius: 999px;
        background: var(--coffee);
        box-shadow: 0 18px 40px rgba(43, 22, 11, .12);
    }

    .hero-action:hover {
        background: #432414;
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

    .history-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .history-metric {
        position: relative;
        min-height: 176px;
        overflow: hidden;
        padding: 24px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 24px;
        background: linear-gradient(180deg, #fffdf9, #f8f0e6);
        box-shadow: 0 22px 48px rgba(49, 29, 15, .08);
        display: grid;
        gap: 12px;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .history-metric::after {
        content: "";
        position: absolute;
        right: -24px;
        bottom: -28px;
        width: 112px;
        height: 112px;
        border-radius: 50%;
        background: rgba(200, 132, 79, .12);
    }

    .history-metric:hover {
        transform: translateY(-3px);
        box-shadow: 0 28px 60px rgba(49, 29, 15, .12);
    }

    .history-metric span {
        position: relative;
        z-index: 1;
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .history-metric strong {
        position: relative;
        z-index: 1;
        display: block;
        margin-top: 4px;
        color: var(--coffee);
        font-size: 32px;
        line-height: 1;
    }

    .history-panel {
        padding: 22px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 8px;
        background: rgba(255, 253, 249, .94);
        box-shadow: 0 22px 52px rgba(49, 29, 15, .08);
    }

    .history-toolbar {
        display: grid;
        grid-template-columns: auto minmax(260px, 1fr) minmax(160px, auto);
        gap: 14px;
        align-items: center;
        margin-bottom: 18px;
    }

    .history-tabs {
        display: inline-flex;
        gap: 6px;
        padding: 5px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 999px;
        background: #f6efe7;
    }

    .history-tabs button {
        min-height: 38px;
        padding: 0 15px;
        border: 0;
        border-radius: 999px;
        background: transparent;
        color: var(--muted);
        font-weight: 900;
    }

    .history-tabs button.active {
        color: #fff8ed;
        background: var(--coffee);
    }

    .history-search,
    .history-sort {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 48px;
        padding: 0 15px;
        border: 1px solid rgba(83, 58, 38, .14);
        border-radius: 999px;
        background: #fffaf2;
    }

    .history-sort {
        min-width: 170px;
        color: var(--muted);
        font-weight: 900;
        outline: 0;
    }

    .search-icon {
        position: relative;
        width: 17px;
        height: 17px;
        flex: 0 0 auto;
        border: 2px solid var(--sage);
        border-radius: 50%;
    }

    .search-icon::after {
        content: "";
        position: absolute;
        right: -6px;
        bottom: -4px;
        width: 8px;
        height: 2px;
        border-radius: 999px;
        background: var(--sage);
        transform: rotate(45deg);
    }

    .history-search input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--ink);
    }

    .history-list {
        display: grid;
        gap: 10px;
    }

    .history-item {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        gap: 14px;
        align-items: start;
        padding: 15px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 8px;
        background: #fff;
    }

    .history-mark {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        color: #fff8ed;
        background: var(--sage);
        font-weight: 900;
    }

    .history-mark.produk { background: var(--coffee); }
    .history-mark.stok { background: var(--caramel); }

    .history-copy strong {
        display: block;
        margin-bottom: 4px;
        font-size: 16px;
    }

    .history-copy p {
        color: var(--muted);
        font-size: 14px;
        line-height: 1.5;
    }

    .history-meta {
        text-align: right;
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .history-meta span {
        display: block;
        margin-top: 5px;
        color: var(--sage);
    }

    @media (max-width: 900px) {
        .history-head,
        .history-toolbar {
            grid-template-columns: 1fr;
        }

        .history-sort {
            width: 100%;
        }

        .hero-visual {
            min-height: 240px;
        }

        .history-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 620px) {
        .history-metrics,
        .history-item {
            grid-template-columns: 1fr;
        }

        .history-head {
            min-height: auto;
            gap: 18px;
            padding: 20px;
            border-radius: 22px;
        }

        .history-head::before {
            inset: 10px;
            border-radius: 18px;
        }

        .hero-copy h1 {
            font-size: clamp(34px, 12vw, 44px);
        }

        .hero-actions .btn {
            width: 100%;
        }

        .hero-visual {
            min-height: 210px;
        }

        .coffee-orbit {
            width: min(260px, 76vw);
        }

        .floating-note {
            position: relative;
            inset: auto;
            width: 100%;
            min-width: 0;
        }

        .history-metric {
            min-height: auto;
            padding: 18px;
        }

        .history-tabs {
            overflow-x: auto;
            border-radius: 8px;
        }

        .history-meta {
            text-align: left;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let activeHistoryFilter = 'Semua';

    function parseHistoryTime(value) {
        const normalized = String(value || '').replace(/^(\d{2})\/(\d{2})\/(\d{4})/, '$3-$2-$1');
        const parsed = Date.parse(normalized);
        return Number.isNaN(parsed) ? 0 : parsed;
    }

    function orderHistoryEntries() {
        return getOrders().map((order) => ({
            id: `ORDER-${order.id}`,
            type: 'Pesanan',
            title: `${order.id} - ${order.status}`,
            detail: `${order.customer} (${order.table || 'Meja -'}): ${order.items.map((item) => `${item.qty}x ${item.name}`).join(', ')}`,
            actor: order.cashier || 'Kasir',
            time: order.createdAt || '-'
        }));
    }

    function allHistoryEntries() {
        return [...getKitchenHistory(), ...orderHistoryEntries()];
    }

    function sortHistoryEntries(entries) {
        const sort = document.getElementById('historySort').value;
        return [...entries].sort((a, b) => {
            if (sort === 'oldest') {
                return (parseHistoryTime(a.time) - parseHistoryTime(b.time)) || String(a.id || '').localeCompare(String(b.id || ''), undefined, { numeric: true });
            }
            if (sort === 'type') {
                return String(a.type || '').localeCompare(String(b.type || '')) || (parseHistoryTime(b.time) - parseHistoryTime(a.time));
            }
            if (sort === 'actor') {
                return String(a.actor || '').localeCompare(String(b.actor || '')) || (parseHistoryTime(b.time) - parseHistoryTime(a.time));
            }

            return (parseHistoryTime(b.time) - parseHistoryTime(a.time)) || String(b.id || '').localeCompare(String(a.id || ''), undefined, { numeric: true });
        });
    }

    function setHistoryFilter(filter) {
        activeHistoryFilter = filter;
        document.querySelectorAll('.history-tabs button').forEach((button) => {
            button.classList.toggle('active', button.dataset.filter === filter);
        });
        renderHistory();
    }

    function renderHistoryMetrics(entries) {
        const orderCount = entries.filter((item) => item.type === 'Pesanan').length;
        document.getElementById('orderHistoryCount').textContent = orderCount;
        document.getElementById('productHistoryCount').textContent = entries.filter((item) => item.type === 'Produk').length;
        document.getElementById('stockHistoryCount').textContent = entries.filter((item) => item.type === 'Stok').length;
        document.getElementById('totalHistoryCount').textContent = entries.length;
        document.getElementById('heroTotalHistory').textContent = `${entries.length} log`;
        document.getElementById('heroOrderHistory').textContent = `${orderCount} log`;
    }

    function renderHistory() {
        const entries = allHistoryEntries();
        const search = document.getElementById('historySearch').value.trim().toLowerCase();
        const filtered = sortHistoryEntries(entries.filter((item) => {
            const matchesFilter = activeHistoryFilter === 'Semua' || item.type === activeHistoryFilter;
            const haystack = `${item.type} ${item.title} ${item.detail} ${item.actor} ${item.time}`.toLowerCase();
            return matchesFilter && (!search || haystack.includes(search));
        }));

        renderHistoryMetrics(entries);
        document.getElementById('historyList').innerHTML = filtered.length
            ? filtered.map((item) => {
                const typeClass = item.type.toLowerCase();
                return `
                    <article class="history-item">
                        <div class="history-mark ${typeClass}">${item.type.charAt(0)}</div>
                        <div class="history-copy">
                            <strong>${item.title}</strong>
                            <p>${item.detail || '-'}</p>
                        </div>
                        <div class="history-meta">
                            ${item.time || '-'}
                            <span>${item.actor || 'Dapur'}</span>
                        </div>
                    </article>
                `;
            }).join('')
            : '<div class="empty">Belum ada aktivitas pada kategori ini.</div>';
    }

    window.addEventListener('wana:storage', renderHistory);
    renderHistory();
</script>
@endpush
