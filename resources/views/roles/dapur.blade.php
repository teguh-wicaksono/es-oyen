@extends('layouts.wana', ['title' => 'Antrian Pesanan | Wana Cafe'])

@section('content')
    <section class="dapur-hero">
        <div class="hero-copy">
            <div class="eyebrow">Role Dapur</div>
            <h1>Antrian Pesanan</h1>
            <p class="lead">Pantau pesanan masuk dari kasir, ubah status produksi, dan fokus ke order yang paling prioritas.</p>
            <div class="hero-actions">
                <span class="live-pill"><span></span> Live Queue</span>
                <button class="btn hero-secondary" type="button" onclick="renderKitchen()">Refresh</button>
            </div>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <div class="coffee-orbit">
                <img src="https://images.unsplash.com/photo-1551218808-94e220e084d2?auto=format&fit=crop&w=1200&q=80" alt="Dapur dan persiapan pesanan">
            </div>
            <div class="floating-note top-note">
                <span>Pesanan Aktif</span>
                <strong id="activeCountNote">0 order</strong>
            </div>
            <div class="floating-note bottom-note">
                <span>Masuk Hari Ini</span>
                <strong id="newCountNote">0 order</strong>
            </div>
        </div>
    </section>

    <section class="dashboard-cards" aria-label="Ringkasan pesanan dapur">
        <article class="dashboard-card card-primary">
            <span>Pesanan Aktif</span>
            <strong id="activeCount">0</strong>
            <p>Order yang masih dalam proses produksi.</p>
        </article>
        <article class="dashboard-card card-secondary">
            <span>Baru Masuk</span>
            <strong id="newCount">0</strong>
            <p>Pesanan yang baru diterima dari kasir.</p>
        </article>
        <article class="dashboard-card card-tertiary">
            <span>Diproses</span>
            <strong id="processCount">0</strong>
            <p>Order yang sedang dikerjakan tim dapur.</p>
        </article>
        <article class="dashboard-card card-quaternary">
            <span>Selesai Hari Ini</span>
            <strong id="doneCount">0</strong>
            <p>Pesanan yang sudah ditandai selesai.</p>
        </article>
    </section>

    <section class="panel queue-panel">
        <div class="queue-toolbar">
            <div class="order-search">
                <span class="search-icon" aria-hidden="true"></span>
                <input id="orderSearch" type="search" placeholder="Cari kode, pelanggan, meja, atau menu..." oninput="renderKitchen()">
            </div>
            <div class="order-tabs" aria-label="Filter status pesanan">
                <button class="active" data-filter="Aktif" type="button" onclick="setKitchenFilter('Aktif')">Aktif</button>
                <button data-filter="Masuk" type="button" onclick="setKitchenFilter('Masuk')">Masuk</button>
                <button data-filter="Diproses" type="button" onclick="setKitchenFilter('Diproses')">Diproses</button>
                <button data-filter="Selesai" type="button" onclick="setKitchenFilter('Selesai')">Selesai</button>
                <button data-filter="Semua" type="button" onclick="setKitchenFilter('Semua')">Semua</button>
            </div>
        </div>

        <div class="queue-summary" id="kitchenNotif">0 pesanan aktif</div>

        <div id="orderList" class="kitchen-board"></div>
    </section>

    <div id="orderModal" class="order-modal hidden" onclick="closeOrderModal(event)">
        <div class="order-dialog" onclick="event.stopPropagation()">
            <div class="order-dialog-head">
                <div>
                    <div class="eyebrow">Detail Pesanan</div>
                    <h2 id="modalOrderTitle">Pesanan</h2>
                    <p id="modalOrderMeta"></p>
                </div>
                <button class="icon-button" type="button" onclick="closeOrderModal()" aria-label="Tutup">x</button>
            </div>

            <div id="modalOrderItems" class="modal-items"></div>

            <div class="status-editor">
                <label for="modalStatus">Status Pesanan</label>
                <div class="modern-select">
                <select id="modalStatus">
                    <option>Masuk</option>
                    <option>Diproses</option>
                    <option>Selesai</option>
                </select>
                </div>
            </div>

            <div class="dialog-actions">
                <button class="btn secondary" type="button" onclick="closeOrderModal()">Tutup</button>
                <button class="btn" type="button" onclick="saveModalStatus()">Simpan Status</button>
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
            url('https://images.unsplash.com/photo-1551218808-94e220e084d2?auto=format&fit=crop&w=1600&q=80') center/cover;
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
        line-height: 1.7;
    }

    .hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 26px;
        align-items: center;
    }

    .hero-actions .btn {
        width: auto;
        min-height: 46px;
        border-radius: 999px;
        box-shadow: 0 16px 34px rgba(43, 22, 11, .12);
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

    .panel {
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 28px;
        background: rgba(255, 253, 249, .94);
        box-shadow: 0 30px 80px rgba(49, 29, 15, .11);
        overflow: hidden;
    }

    .queue-panel {
        padding: 22px;
    }

    .queue-toolbar {
        display: grid;
        grid-template-columns: minmax(300px, 1fr) auto;
        gap: 14px;
        align-items: center;
        margin-bottom: 14px;
    }

    .order-search {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 48px;
        padding: 0 15px;
        border: 1px solid rgba(83, 58, 38, .14);
        border-radius: 999px;
        background: #fffaf2;
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

    .order-search input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--ink);
    }

    .order-tabs {
        display: inline-flex;
        gap: 6px;
        padding: 5px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 999px;
        background: #f6efe7;
    }

    .order-tabs button {
        min-height: 38px;
        padding: 0 14px;
        border: 0;
        border-radius: 999px;
        background: transparent;
        color: var(--muted);
        font-weight: 900;
    }

    .order-tabs button.active {
        color: #fff8ed;
        background: var(--coffee);
    }

    .queue-summary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 16px;
        margin-bottom: 16px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 999px;
        background: #fffaf2;
        color: var(--coffee);
        font-weight: 900;
    }

    .kitchen-board {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .order-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 18px 20px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 12px 28px rgba(49, 29, 15, .05);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 42px rgba(49, 29, 15, .08);
    }

    .order-card.priority {
        border-color: rgba(166, 79, 83, .32);
        box-shadow: 0 16px 40px rgba(166, 79, 83, .09);
    }

    .order-card-body {
        display: grid;
        gap: 6px;
        min-width: 0;
    }

    .order-card-body h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 900;
        line-height: 1.1;
    }

    .order-card-body .order-date {
        color: var(--muted);
        font-size: 13px;
        font-weight: 700;
        line-height: 1.4;
    }

    .order-detail-button {
        min-width: 120px;
        padding: 11px 14px;
        border: 0;
        border-radius: 14px;
        background: #f2e7dc;
        color: var(--coffee);
        font-weight: 900;
        cursor: pointer;
        transition: transform .18s ease, background .18s ease;
    }

    .order-detail-button:hover {
        transform: translateY(-1px);
        background: #e6d2be;
    }

    .order-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgba(24, 16, 10, .42);
        backdrop-filter: blur(6px);
    }

    .order-modal.hidden {
        display: none;
    }

    .order-dialog {
        width: min(620px, 100%);
        max-height: 92vh;
        overflow: auto;
        padding: 24px;
        border-radius: 24px;
        background: #fffdf9;
        box-shadow: 0 30px 90px rgba(24, 16, 10, .24);
    }

    .order-dialog-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .order-dialog-head h2 {
        margin-top: 6px;
        font-size: 28px;
    }

    .order-dialog-head p {
        margin-top: 8px;
        color: var(--muted);
        font-weight: 800;
    }

    .icon-button {
        width: 40px;
        height: 40px;
        border: 0;
        border-radius: 50%;
        background: #f2e7dc;
        color: var(--coffee);
        font-size: 24px;
        line-height: 1;
    }

    .modal-items {
        display: grid;
        gap: 8px;
        margin-bottom: 16px;
    }

    .modal-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }

    .modal-info {
        padding: 12px;
        border: 1px solid rgba(83, 58, 38, .10);
        border-radius: 12px;
        background: #fff8ee;
    }

    .modal-info span {
        display: block;
        color: var(--sage);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .modal-info strong {
        display: block;
        margin-top: 5px;
        color: var(--coffee);
        font-size: 14px;
        line-height: 1.3;
        word-break: break-word;
    }

    .modal-section-title {
        margin: 2px 0 10px;
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .modal-total-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin: -4px 0 16px;
        padding: 13px 14px;
        border-radius: 12px;
        color: #fff8ed;
        background: var(--coffee);
        font-weight: 900;
    }

    .status-editor {
        display: grid;
        gap: 8px;
        margin-bottom: 16px;
    }

    .status-editor label {
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .modern-select {
        position: relative;
    }

    .modern-select::after {
        content: "";
        position: absolute;
        top: 50%;
        right: 16px;
        width: 9px;
        height: 9px;
        border-right: 2px solid var(--muted);
        border-bottom: 2px solid var(--muted);
        transform: translateY(-65%) rotate(45deg);
        pointer-events: none;
    }

    .status-editor select {
        appearance: none;
        width: 100%;
        min-height: 56px;
        border: 1px solid rgba(83, 58, 38, .14);
        border-radius: 16px;
        background:
            linear-gradient(180deg, rgba(255, 253, 249, .98), rgba(250, 241, 229, .94));
        padding: 0 46px 0 16px;
        color: var(--ink);
        font-weight: 900;
        outline: 0;
        box-shadow: 0 14px 34px rgba(49, 29, 15, .06), inset 0 1px 0 rgba(255, 255, 255, .7);
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    .status-editor select:focus {
        border-color: rgba(200, 132, 79, .52);
        box-shadow: 0 0 0 4px rgba(200, 132, 79, .13), 0 18px 40px rgba(49, 29, 15, .08);
        transform: translateY(-1px);
    }

    .status-editor select option:disabled {
        color: #a99a90;
    }

    .dialog-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    @media (max-width: 1180px) {
        .dashboard-cards {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .kitchen-board,
        .queue-toolbar {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .dapur-hero {
            padding: 20px;
            border-radius: 22px;
            grid-template-columns: 1fr;
        }

        .dapur-hero::before {
            inset: 10px;
            border-radius: 18px;
        }

        .hero-actions .btn,
        .live-pill,
        .queue-summary,
        .order-tabs {
            width: 100%;
        }

        .hero-visual {
            min-height: 220px;
        }

        .dashboard-cards,
        .kitchen-board {
            grid-template-columns: 1fr;
        }

        .order-detail-grid,
        .modal-info-grid {
            grid-template-columns: 1fr;
        }

        .queue-panel {
            padding: 18px;
        }

        .order-tabs {
            justify-content: flex-start;
            overflow-x: auto;
            border-radius: 12px;
        }

        .dialog-actions {
            flex-direction: column;
        }

        .dialog-actions .btn {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let activeKitchenFilter = 'Aktif';
    let selectedOrderId = null;

    function activeOrders() {
        return getOrders().filter((order) => order.status !== 'Selesai');
    }

    function statusClass(status) {
        return status === 'Masuk' ? 'masuk' : (status === 'Selesai' ? 'selesai' : '');
    }

    function canTransitionStatus(current, target) {
        if (current === target) return false;
        if (current === 'Masuk') return target === 'Diproses';
        if (current === 'Diproses') return target === 'Selesai';
        return false;
    }

    function nextStatusHint(status) {
        if (status === 'Masuk') return 'Mulai Proses';
        if (status === 'Diproses') return 'Selesaikan';
        return 'Selesai';
    }

    function orderMatchesSearch(order, search) {
        if (!search) return true;
        const haystack = [
            order.id,
            order.customer,
            order.table,
            order.cashier,
            order.status,
            ...order.items.map((item) => item.name)
        ].join(' ').toLowerCase();
        return haystack.includes(search);
    }

    function getOrderTotal(order) {
        return Number(order.total || order.items.reduce((sum, item) => sum + Number(item.price || 0) * Number(item.qty || 0), 0));
    }

    function filteredKitchenOrders() {
        const search = document.getElementById('orderSearch')?.value.trim().toLowerCase() || '';
        return getOrders().filter((order) => {
            const matchesFilter = activeKitchenFilter === 'Semua'
                || (activeKitchenFilter === 'Aktif' && order.status !== 'Selesai')
                || order.status === activeKitchenFilter;
            return matchesFilter && orderMatchesSearch(order, search);
        });
    }

    function renderKitchenMetrics(orders) {
        const active = orders.filter((order) => order.status !== 'Selesai').length;
        const newCount = orders.filter((order) => order.status === 'Masuk').length;
        const processCount = orders.filter((order) => order.status === 'Diproses').length;
        const doneCount = orders.filter((order) => order.status === 'Selesai').length;

        document.getElementById('activeCount').textContent = active;
        document.getElementById('newCount').textContent = newCount;
        document.getElementById('processCount').textContent = processCount;
        document.getElementById('doneCount').textContent = doneCount;
        document.getElementById('activeCountNote').textContent = `${active} order`;
        document.getElementById('newCountNote').textContent = `${newCount} order`;
        document.getElementById('kitchenNotif').textContent = `${active} pesanan aktif`;
    }

    function renderKitchen() {
        const allOrders = getOrders();
        const orders = filteredKitchenOrders();
        renderKitchenMetrics(allOrders);

        document.getElementById('orderList').innerHTML = orders.length
            ? orders.map((order) => {
                const totalQty = order.items.reduce((sum, item) => sum + Number(item.qty || 0), 0);
                const priority = order.status === 'Masuk' && totalQty >= 4;
                return `
                    <article class="order-card ${priority ? 'priority' : ''}">
                        <div class="order-card-body">
                            <h3>${order.id}</h3>
                            <div class="order-date">${order.createdAt}</div>
                        </div>
                        <button class="order-detail-button" type="button" onclick="viewOrder('${order.id}')">Detail</button>
                    </article>
                `;
            }).join('')
            : '<div class="empty">Belum ada pesanan pada filter ini.</div>';
    }

    function setKitchenFilter(filter) {
        activeKitchenFilter = filter;
        document.querySelectorAll('.order-tabs button').forEach((button) => {
            button.classList.toggle('active', button.dataset.filter === filter);
        });
        renderKitchen();
    }

    async function updateOrderStatus(orderId, status) {
        const previous = getOrders().find((order) => order.id === orderId);
        if (!previous) return notify('Pesanan tidak ditemukan.');
        if (!canTransitionStatus(previous.status, status)) {
            notify(`Pesanan ${orderId} harus berjalan berurutan. Langkah berikutnya: ${nextStatusHint(previous.status)}.`);
            return;
        }

        try {
            await wanaRequest(`/dapur/pesanan/${orderId}/status`, {
                method: 'PUT',
                body: JSON.stringify({ status })
            });
        } catch (error) {
            notify(error.message);
            return;
        }

        const orders = getOrders().map((order) => order.id === orderId ? { ...order, status } : order);
        setOrders(orders);
        renderKitchen();
        notify(status === 'Selesai'
            ? `Pesanan ${orderId} selesai dan hilang dari antrian aktif.`
            : `Status ${orderId} menjadi ${status}.`
        );
    }

    function viewOrder(orderId) {
        const order = getOrders().find((item) => item.id === orderId);
        if (!order) return notify('Pesanan tidak ditemukan.');

        selectedOrderId = orderId;
        const modalStatus = document.getElementById('modalStatus');
        const totalQty = order.items.reduce((sum, item) => sum + Number(item.qty || 0), 0);
        const totalPrice = getOrderTotal(order);
        document.getElementById('modalOrderTitle').textContent = `${order.id} - ${order.customer}`;
        document.getElementById('modalOrderMeta').textContent = `${order.createdAt} oleh ${order.cashier} - ${order.table || 'Meja -'}`;
        [...modalStatus.options].forEach((option) => {
            option.disabled = option.value !== order.status && !canTransitionStatus(order.status, option.value);
        });
        modalStatus.value = order.status;
        document.getElementById('modalOrderItems').innerHTML = `
            <div class="modal-info-grid">
                <div class="modal-info"><span>Kode</span><strong>${order.id}</strong></div>
                <div class="modal-info"><span>Status</span><strong>${order.status || 'Masuk'}</strong></div>
                <div class="modal-info"><span>Pembeli</span><strong>${order.customer || 'Pelanggan walk-in'}</strong></div>
                <div class="modal-info"><span>Meja</span><strong>${order.table || 'Meja -'}</strong></div>
                <div class="modal-info"><span>Kasir</span><strong>${order.cashier || 'Kasir'}</strong></div>
                <div class="modal-info"><span>Waktu</span><strong>${order.createdAt || '-'}</strong></div>
                <div class="modal-info"><span>Total Item</span><strong>${totalQty} item</strong></div>
                <div class="modal-info"><span>Total Harga</span><strong>${rupiah(totalPrice)}</strong></div>
            </div>
            <div class="modal-section-title">Daftar Pesanan</div>
            ${order.items.map((item) => `
            <div class="item-row">
                <span>${item.name}</span>
                <span>${item.qty}x${item.price ? ` - ${rupiah(item.price * item.qty)}` : ''}</span>
            </div>
            `).join('')}
            <div class="modal-total-row">
                <span>Total</span>
                <span>${rupiah(totalPrice)}</span>
            </div>
        `;
        document.getElementById('orderModal').classList.remove('hidden');
    }

    function saveModalStatus() {
        if (!selectedOrderId) return;
        const order = getOrders().find((item) => item.id === selectedOrderId);
        const status = document.getElementById('modalStatus').value;
        if (!order || !canTransitionStatus(order.status, status)) {
            notify(`Status harus berurutan. Langkah berikutnya: ${nextStatusHint(order?.status || 'Masuk')}.`);
            return;
        }
        updateOrderStatus(selectedOrderId, status);
        closeOrderModal();
    }

    function closeOrderModal(event) {
        if (event && event.target !== document.getElementById('orderModal')) return;
        document.getElementById('orderModal').classList.add('hidden');
        selectedOrderId = null;
    }

    window.addEventListener('storage', renderKitchen);
    window.addEventListener('wana:storage', renderKitchen);
    setInterval(renderKitchen, 4000);
    renderKitchen();
</script>
@endpush
