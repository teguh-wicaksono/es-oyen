@extends('layouts.wana', ['title' => 'Kasir | Wana Cafe'])

@section('content')
    <section class="order-hero">
        <div class="order-hero-copy">
            <div class="eyebrow">Menu Kasir</div>
            <h1>Kelola Menu & Pesanan</h1>
            <p class="lead">Cari, pilih, dan tambahkan pesanan langsung dalam satu halaman dengan pengalaman kasir yang lebih cepat dan rapi.</p>
            <div class="hero-stats">
                <span>{{ count($products) }} menu tersedia</span>
                <span>Checkout cepat</span>
                <span>Struk siap cetak</span>
            </div>
        </div>

        <div class="order-hero-visual" aria-hidden="true">
            <img src="https://images.unsplash.com/photo-1442512595331-e89e73853f31?auto=format&fit=crop&w=900&q=85" alt="">
            <div class="hero-ticket">
                <span>Wana Cafe</span>
                <strong>Fresh Order</strong>
            </div>
        </div>
    </section>

    <section class="order-layout">
        <div class="menu-panel">
            <div class="menu-toolbar">
                <div class="search-wrap menu-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m16.5 16.5 4 4"></path>
                    </svg>
                    <input id="searchInput" type="search" placeholder="Cari nama atau deskripsi menu..." oninput="renderMenu()">
                </div>

                <div class="filter-row">
                    <button class="pill active" data-filter="Semua" type="button" onclick="setFilter('Semua')">Semua</button>
                    <button class="pill" data-filter="Minuman" type="button" onclick="setFilter('Minuman')">Minuman</button>
                    <button class="pill" data-filter="Makanan" type="button" onclick="setFilter('Makanan')">Makanan</button>
                    <button class="pill" data-filter="Snack" type="button" onclick="setFilter('Snack')">Snack</button>
                </div>
            </div>

            <div id="menuGrid" class="menu-grid"></div>
        </div>

        <aside class="sidebar-panel">
            <div class="cart-panel">
                <div class="cart-cover" aria-hidden="true">
                    <img src="https://images.unsplash.com/photo-1498654896293-37aacf113fd9?auto=format&fit=crop&w=900&q=80" alt="">
                    <div>
                        <span>Order Summary</span>
                        <strong>Keranjang</strong>
                    </div>
                </div>

                <div class="panel-title">
                    <div>
                        <h2>Keranjang Pesanan</h2>
                        <p class="mini">Ringkas, bisa langsung checkout dan cetak struk.</p>
                    </div>
                    <span class="badge" id="cartCount">0 item</span>
                </div>

                <div class="field-grid">
                    <div class="field">
                        <label for="tableNumber">Meja</label>
                        <input id="tableNumber" type="text" placeholder="Meja 04" />
                    </div>
                    <div class="field">
                        <label for="customerName">Nama Pelanggan</label>
                        <input id="customerName" type="text" placeholder="Pelanggan walk-in" />
                    </div>
                    <div class="field">
                        <label for="cashierName">Nama Kasir</label>
                        <input id="cashierName" type="text" value="{{ auth()->user()->name }}" placeholder="Nama kasir" />
                    </div>
                </div>

                <div id="cartList" class="cart-list"></div>

                <div class="total-row">
                    <span>Total</span>
                    <strong id="cartTotal">Rp 0</strong>
                </div>

                <div class="payment-box">
                    <div class="field">
                        <label>Metode Pembayaran</label>
                        <div class="payment-methods" role="radiogroup" aria-label="Metode pembayaran">
                            <label class="payment-method-option active">
                                <input type="radio" name="paymentMethod" value="Tunai" checked>
                                <span>Tunai</span>
                            </label>
                            <label class="payment-method-option">
                                <input type="radio" name="paymentMethod" value="QRIS">
                                <span>QRIS</span>
                            </label>
                        </div>
                    </div>
                    <div id="qrisPreview" class="qris-preview" hidden>
                        <span>Scan QRIS Pembayaran</span>
                        <img id="qrisImage" alt="QRIS pembayaran" loading="lazy">
                        <strong id="qrisAmount">Rp 0</strong>
                    </div>
                    <div id="cashField" class="field">
                        <label for="customerPaid">Uang Pelanggan</label>
                        <input id="customerPaid" type="number" min="0" inputmode="numeric" placeholder="0" oninput="renderCart()" />
                    </div>
                    <div id="changeRow" class="total-row change-row">
                        <span>Kembalian</span>
                        <strong id="cartChange">Rp 0</strong>
                    </div>
                </div>

                <button class="btn primary" onclick="checkout()">Checkout & Cetak</button>
            </div>
        </aside>
    </section>
@endsection

@push('styles')
<style>
    .order-hero {
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
            linear-gradient(135deg, rgba(255, 250, 242, .97), rgba(245, 234, 219, .84) 52%, rgba(166, 79, 83, .14)),
            url('https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=1600&q=80') center/cover;
        box-shadow: 0 34px 80px rgba(49, 29, 15, .12);
    }

    .order-hero::before {
        content: "";
        position: absolute;
        inset: 18px;
        border: 1px solid rgba(255, 255, 255, .62);
        border-radius: 24px;
        pointer-events: none;
    }

    .order-hero-copy,
    .order-hero-visual {
        position: relative;
        z-index: 1;
    }

    .order-hero h1 {
        max-width: 780px;
        margin-top: 8px;
        font-size: clamp(42px, 5vw, 72px);
    }

    .order-hero .lead {
        max-width: 720px;
        font-size: 16px;
        font-weight: 700;
    }

    .hero-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 24px;
    }

    .hero-stats span {
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

    .order-hero-visual {
        min-height: 260px;
        display: grid;
        place-items: center;
    }

    .order-hero-visual img {
        width: min(360px, 80vw);
        aspect-ratio: 1 / .82;
        object-fit: cover;
        border: 10px solid rgba(255, 250, 242, .96);
        border-radius: 28px;
        box-shadow: 0 34px 80px rgba(43, 22, 11, .24);
        transform: rotate(2deg);
    }

    .hero-ticket {
        position: absolute;
        left: 0;
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

    .hero-ticket span {
        color: rgba(255, 248, 237, .72);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .hero-ticket strong {
        font-size: 18px;
        line-height: 1.1;
    }

    .order-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.62fr) minmax(340px, .92fr);
        gap: 24px;
        align-items: start;
    }

    .menu-panel {
        min-width: 0;
    }

    .menu-toolbar {
        position: sticky;
        top: 94px;
        z-index: 8;
        display: grid;
        gap: 14px;
        margin-bottom: 18px;
        padding: 16px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 24px;
        background: rgba(255, 253, 249, .88);
        box-shadow: 0 22px 52px rgba(49, 29, 15, .08);
        backdrop-filter: blur(12px);
    }

    .search-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 52px;
        width: 100%;
        padding: 0 16px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 18px;
        background: #fffaf4;
    }

    .search-wrap svg {
        width: 20px;
        height: 20px;
        color: var(--sage);
        stroke: currentColor;
        stroke-width: 2;
        fill: none;
        flex: 0 0 auto;
    }

    .search-wrap input {
        width: 100%;
        min-width: 0;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--ink);
        font-size: 15px;
        font-weight: 700;
    }

    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .filter-row .pill {
        min-height: 42px;
        padding: 0 16px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 999px;
        background: #fbf6ef;
        color: var(--muted);
        font-size: 13px;
        font-weight: 900;
        transition: transform .18s ease, background .18s ease, color .18s ease, box-shadow .18s ease;
    }

    .filter-row .pill:hover {
        transform: translateY(-1px);
    }

    .filter-row .pill.active {
        color: #fff8ed;
        background: var(--coffee);
        box-shadow: 0 14px 28px rgba(43, 22, 11, .16);
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
    }

    .product-card {
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        min-height: 100%;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 24px;
        background: rgba(255, 253, 249, .92);
        box-shadow: 0 20px 46px rgba(49, 29, 15, .08);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .product-card:hover {
        transform: translateY(-4px);
        border-color: rgba(100, 122, 84, .28);
        box-shadow: 0 30px 70px rgba(49, 29, 15, .14);
    }

    .product-media {
        position: relative;
        min-height: 210px;
        overflow: hidden;
        background: #efe5d8;
    }

    .product-media::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(22, 12, 7, .30), rgba(22, 12, 7, 0) 46%, rgba(22, 12, 7, .12));
        pointer-events: none;
    }

    .product-media img {
        width: 100%;
        height: 210px;
        display: block;
        object-fit: cover;
        transition: transform .35s ease;
    }

    .product-card:hover .product-media img {
        transform: scale(1.045);
    }

    .product-media .badge {
        position: absolute;
        z-index: 1;
        left: 14px;
        top: 14px;
        min-height: 30px;
        padding: 0 12px;
        border-radius: 999px;
        color: #fff8ed;
        background: rgba(26, 17, 11, .72);
        font-size: 12px;
        font-weight: 900;
        backdrop-filter: blur(10px);
    }

    .product-media .stock-badge {
        left: auto;
        right: 14px;
        color: var(--coffee);
        background: rgba(255, 250, 242, .92);
    }

    .product-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
        padding: 18px;
    }

    .product-headline {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .product-body h3 {
        margin: 0;
        color: var(--coffee);
        font-family: "Playfair Display", Georgia, serif;
        font-size: 22px;
        line-height: 1.12;
    }

    .price {
        flex: 0 0 auto;
        color: var(--ink);
        font-size: 17px;
        font-weight: 900;
        white-space: nowrap;
    }

    .product-body p {
        min-height: 50px;
        margin: 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .product-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid rgba(83, 58, 38, .10);
    }

    .product-foot .btn {
        min-height: 44px;
        border-radius: 15px;
    }

    .menu-grid.list-view {
        grid-template-columns: 1fr;
    }

    .product-card.list {
        flex-direction: row;
        align-items: stretch;
        gap: 1rem;
    }

    .product-card.list .product-media {
        flex: 0 0 220px;
        min-height: auto;
    }

    .product-card.list .product-media img {
        height: 100%;
    }

    .sidebar-panel {
        position: sticky;
        top: 94px;
    }

    .cart-panel {
        display: flex;
        flex-direction: column;
        gap: 18px;
        overflow: hidden;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 28px;
        background: rgba(255, 253, 249, .94);
        box-shadow: 0 30px 80px rgba(49, 29, 15, .13);
    }

    .cart-cover {
        position: relative;
        min-height: 142px;
        display: grid;
        align-items: end;
        padding: 18px;
        overflow: hidden;
        color: #fff8ed;
    }

    .cart-cover img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cart-cover::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(22, 12, 7, .10), rgba(22, 12, 7, .78));
    }

    .cart-cover > div {
        position: relative;
        z-index: 1;
        display: grid;
        gap: 4px;
    }

    .cart-cover span {
        color: rgba(255, 248, 237, .76);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .cart-cover strong {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 30px;
        line-height: 1;
    }

    .cart-panel .panel-title,
    .cart-panel .field-grid,
    .cart-panel .cart-list,
    .cart-panel .total-row,
    .cart-panel .payment-box,
    .cart-panel > .btn {
        margin-left: 22px;
        margin-right: 22px;
    }

    .cart-panel > .btn {
        margin-bottom: 22px;
    }

    .panel-title {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: flex-start;
    }

    #cartCount {
        flex: 0 0 auto;
        min-width: 72px;
        min-height: 38px;
        padding: 0 14px;
        border-radius: 999px;
        white-space: nowrap;
        color: #fff8ed;
        background: var(--sage);
        font-size: 13px;
        font-weight: 900;
        box-shadow: 0 12px 26px rgba(100, 122, 84, .18);
    }

    .panel-title h2 {
        margin: 0;
        color: var(--coffee);
        font-size: 22px;
    }

    .panel-title .mini {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.55;
    }

    .field-grid {
        display: grid;
        gap: 14px;
    }

    .field {
        margin: 0;
    }

    .field input {
        min-height: 50px;
        border-radius: 16px;
        background: #fffaf4;
    }

    .cart-list {
        display: grid;
        gap: 12px;
        max-height: 310px;
        overflow: auto;
        padding-right: 3px;
    }

    .mini-item {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;
        padding: 14px;
        border: 1px solid rgba(83, 58, 38, .10);
        border-radius: 18px;
        background: #fbf6ef;
    }

    .mini-item div {
        min-width: 0;
    }

    .mini-item strong {
        display: block;
        margin-bottom: 4px;
        color: var(--coffee);
        font-size: 14px;
    }

    .mini-item span {
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
    }

    .qty {
        display: flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        background: #fffaf4;
        padding: 4px;
    }

    .qty button {
        width: 30px;
        height: 30px;
        border: 0;
        border-radius: 50%;
        background: #f2dfc1;
        color: var(--coffee);
        font-size: 16px;
        font-weight: 900;
    }

    .qty b {
        min-width: 18px;
        text-align: center;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 18px;
        border-top: 1px solid rgba(83, 58, 38, .12);
        font-size: 18px;
        font-weight: 900;
    }

    .payment-box {
        display: grid;
        gap: 12px;
        margin-top: 12px;
    }

    .payment-box .field {
        margin: 0;
    }

    .payment-methods {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
    }

    .payment-method-option {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        border: 1px solid rgba(83, 58, 38, .14);
        border-radius: 14px;
        background: #fffaf4;
        color: var(--ink);
        font-size: 13px;
        font-weight: 900;
        cursor: pointer;
    }

    .payment-method-option input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .payment-method-option.active {
        border-color: var(--coffee);
        background: var(--coffee);
        color: #fffaf4;
    }

    .qris-preview {
        display: grid;
        place-items: center;
        gap: 10px;
        padding: 14px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 18px;
        background: #fff7ed;
        text-align: center;
    }

    .qris-preview[hidden] {
        display: none;
    }

    .qris-preview span {
        color: var(--muted);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .qris-preview img {
        width: 168px;
        height: 168px;
        padding: 8px;
        border-radius: 14px;
        background: #fff;
        object-fit: contain;
    }

    .qris-preview strong {
        color: var(--ink);
        font-size: 18px;
        font-weight: 900;
    }

    .change-row {
        margin: 0;
        padding: 12px 14px;
        border: 1px solid rgba(83, 58, 38, .10);
        border-radius: 16px;
        background: #fff7ed;
        font-size: 15px;
    }

    .change-row strong.is-minus {
        color: var(--berry);
    }

    .btn.primary {
        width: auto;
        min-height: 56px;
        border-radius: 18px;
    }

    @media (max-width: 1100px) {
        .order-hero,
        .order-layout {
            grid-template-columns: 1fr;
        }

        .menu-toolbar,
        .sidebar-panel {
            position: static;
        }
    }

    @media (max-width: 720px) {
        .order-hero {
            padding: 20px;
            border-radius: 22px;
        }

        .order-hero::before {
            inset: 10px;
            border-radius: 18px;
        }

        .order-hero-visual img {
            width: min(300px, 78vw);
        }

        .hero-ticket {
            position: relative;
            inset: auto;
            width: 100%;
            margin-top: -12px;
        }

        .menu-grid {
            grid-template-columns: 1fr;
        }

        .product-card.list {
            flex-direction: column;
        }

        .product-card.list .product-media {
            flex-basis: auto;
        }

        .cart-panel .panel-title,
        .cart-panel .field-grid,
        .cart-panel .cart-list,
        .cart-panel .total-row,
        .cart-panel .payment-box,
        .cart-panel > .btn {
            margin-left: 16px;
            margin-right: 16px;
        }

        .cart-panel .panel-title {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: start;
        }

        #cartCount {
            min-width: 64px;
            min-height: 34px;
            padding: 0 12px;
            font-size: 12px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let activeFilter = 'Semua';
    let activeView = 'grid';
    let cart = [];

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[char]));
    }

    function getSearchValue() {
        return document.getElementById('searchInput').value.trim().toLowerCase();
    }

    function selectedPaymentMethod() {
        return document.querySelector('input[name="paymentMethod"]:checked')?.value || 'Tunai';
    }

    function qrisPayload(total = null, orderId = 'DRAFT') {
        const amount = total ?? cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        return `WANA CAFE|QRIS|ORDER=${orderId}|TOTAL=${amount}`;
    }

    function qrisImageUrl(total = null, orderId = 'DRAFT') {
        return `https://api.qrserver.com/v1/create-qr-code/?size=220x220&margin=12&data=${encodeURIComponent(qrisPayload(total, orderId))}`;
    }

    function updatePaymentUi() {
        const method = selectedPaymentMethod();
        const isQris = method === 'QRIS';
        const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        document.querySelectorAll('.payment-method-option').forEach((option) => {
            option.classList.toggle('active', option.querySelector('input')?.value === method);
        });
        document.getElementById('cashField').hidden = isQris;
        document.getElementById('changeRow').hidden = isQris;
        document.getElementById('qrisPreview').hidden = !isQris;
        if (isQris) {
            document.getElementById('qrisAmount').textContent = rupiah(total);
            document.getElementById('qrisImage').src = qrisImageUrl(total);
            document.getElementById('customerPaid').value = total || '';
        }
    }

    function filteredProducts() {
        const products = getProducts();
        const search = getSearchValue();

        let filtered = activeFilter === 'Semua'
            ? products
            : products.filter((product) => product.category === activeFilter);

        if (search) {
            filtered = filtered.filter((product) =>
                product.name.toLowerCase().includes(search) ||
                product.description.toLowerCase().includes(search)
            );
        }

        return filtered;
    }

    function renderMenu() {
        const menuGrid = document.getElementById('menuGrid');
        menuGrid.classList.toggle('list-view', activeView === 'list');

        const products = filteredProducts();
        menuGrid.innerHTML = products.length
            ? products.map((product) => `
                <article class="product-card ${activeView === 'list' ? 'list' : ''}">
                    <div class="product-media">
                        <span class="badge">${product.category}</span>
                        <span class="badge stock-badge">${product.stock} stok</span>
                        <img src="${product.image}" alt="${product.name}">
                    </div>
                    <div class="product-body">
                        <div class="product-headline">
                            <h3>${product.name}</h3>
                            <span class="price">${rupiah(product.price)}</span>
                        </div>
                        <p>${product.description}</p>
                        <div class="product-foot">
                            <button class="btn secondary" onclick="addToCart(${product.id})">+ Keranjang</button>
                        </div>
                    </div>
                </article>
            `).join('')
            : '<div class="empty">Menu tidak ditemukan. Coba ubah kata kunci atau filter.</div>';
    }

    function setFilter(filter) {
        activeFilter = filter;
        document.querySelectorAll('.filter-row .pill').forEach((button) => {
            button.classList.toggle('active', button.dataset.filter === filter);
        });
        renderMenu();
    }

    function toggleView(view) {
        activeView = view;
        document.querySelectorAll('.view-toggle .pill').forEach((button) => {
            button.classList.toggle('active', button.dataset.view === view);
        });
        renderMenu();
    }

    function addToCart(productId) {
        const product = getProducts().find((item) => item.id === productId);
        const current = cart.find((item) => item.id === productId);

        if (!product || product.stock <= (current?.qty || 0)) {
            notify('Stok menu tidak cukup.');
            return;
        }

        if (current) {
            current.qty += 1;
        } else {
            cart.push({ ...product, qty: 1 });
        }

        renderCart();
    }

    function updateQty(productId, delta) {
        const item = cart.find((entry) => entry.id === productId);
        const product = getProducts().find((entry) => entry.id === productId);
        if (!item || !product) return;

        item.qty += delta;
        if (item.qty <= 0) {
            cart = cart.filter((entry) => entry.id !== productId);
        }

        if (item.qty > product.stock) {
            item.qty = product.stock;
            notify('Jumlah sudah mencapai stok tersedia.');
        }

        renderCart();
    }

    function renderCart() {
        const cartList = document.getElementById('cartList');
        const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        const totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
        updatePaymentUi();
        const paid = selectedPaymentMethod() === 'QRIS' ? total : Number(document.getElementById('customerPaid')?.value || 0);
        const change = paid - total;
        const changeLabel = document.getElementById('cartChange');

        document.getElementById('cartCount').textContent = `${totalQty} item`;
        document.getElementById('cartTotal').textContent = rupiah(total);
        if (changeLabel) {
            changeLabel.classList.toggle('is-minus', paid > 0 && change < 0);
            changeLabel.textContent = paid > 0 && change < 0
                ? `Kurang ${rupiah(Math.abs(change))}`
                : rupiah(Math.max(change, 0));
        }

        cartList.innerHTML = cart.length
            ? cart.map((item) => `
                <div class="mini-item">
                    <div>
                        <strong>${item.name}</strong>
                        <span>${rupiah(item.price)} x ${item.qty}</span>
                    </div>
                    <div class="qty">
                        <button onclick="updateQty(${item.id}, -1)">-</button>
                        <b>${item.qty}</b>
                        <button onclick="updateQty(${item.id}, 1)">+</button>
                    </div>
                </div>
            `).join('')
            : '<div class="empty">Keranjang masih kosong.</div>';
    }

    async function checkout() {
        if (!cart.length) {
            notify('Tambahkan menu ke keranjang dulu.');
            return;
        }

        const table = document.getElementById('tableNumber').value.trim() || 'Meja -';
        const customer = document.getElementById('customerName').value.trim() || 'Pelanggan walk-in';
        const cashier = document.getElementById('cashierName').value.trim() || @json(auth()->user()->name);
        const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked')?.value || 'Tunai';
        const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
        const paid = paymentMethod === 'QRIS' ? total : Number(document.getElementById('customerPaid').value || 0);

        if (paid < total) {
            notify('Uang pelanggan kurang dari total pesanan.');
            return;
        }

        const now = new Date();
        const createdAt = now.toLocaleString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
        const products = getProducts();
        const updatedProducts = products.map((product) => {
            const ordered = cart.find((item) => item.id === product.id);
            return ordered ? { ...product, stock: product.stock - ordered.qty } : product;
        });
        const orders = getOrders();
        const order = {
            id: `WN-${Date.now().toString().slice(-6)}`,
            table,
            customer,
            cashier,
            status: 'Masuk',
            createdAt,
            items: cart.map((item) => ({ id: item.id, name: item.name, qty: item.qty, price: item.price })),
            total,
            paid,
            change: paid - total,
            paymentMethod
        };

        let savedOrder = order;
        let savedProducts = updatedProducts;

        try {
            const response = await wanaRequest('/kasir/pesanan', {
                method: 'POST',
                body: JSON.stringify(order)
            });
            savedOrder = response.order;
            savedOrder.paymentMethod = paymentMethod;
            savedProducts = response.products || updatedProducts;
        } catch (error) {
            notify(error.message);
            return;
        }

        setProducts(savedProducts);
        setOrders([savedOrder, ...orders]);
        cart = [];
        document.getElementById('tableNumber').value = '';
        document.getElementById('customerName').value = '';
        document.getElementById('cashierName').value = cashier;
        document.getElementById('customerPaid').value = '';
        updatePaymentUi();
        renderMenu();
        renderCart();
        notify(`Pesanan ${savedOrder.id} dikirim ke dapur.`);
        showReceipt(savedOrder);
    }

    function showReceipt(order) {
        let modal = document.getElementById('receiptModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'receiptModal';
            modal.style.position = 'fixed';
            modal.style.left = 0;
            modal.style.top = 0;
            modal.style.width = '100%';
            modal.style.height = '100%';
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.background = 'rgba(0,0,0,0.4)';
            modal.style.zIndex = 9999;
            document.body.appendChild(modal);
        }

        const receiptHtml = `
            <div id="receiptContent" style="background:#fffaf4;color:#2b160b;padding:0;border-radius:22px;max-width:390px;width:92%;font-family:Inter,system-ui,sans-serif;overflow:hidden;box-shadow:0 30px 90px rgba(20,10,5,.28)">
                <div style="padding:20px 20px 18px;background:linear-gradient(135deg,#2b160b,#8b5737);color:#fff8ed">
                    <div style="display:flex;align-items:center;gap:12px">
                        <div style="display:grid;place-items:center;width:48px;height:48px;border-radius:16px;background:rgba(255,248,237,.13);box-shadow:inset 0 0 0 1px rgba(255,248,237,.22)">
                            <svg viewBox="0 0 64 64" style="width:30px;height:30px;stroke:currentColor;stroke-width:4;fill:none;stroke-linecap:round;stroke-linejoin:round" aria-hidden="true"><path d="M18 24h25v12a12 12 0 0 1-12 12h-1a12 12 0 0 1-12-12V24Z"/><path d="M43 29h4a7 7 0 0 1 0 14h-5"/><path d="M24 18v-4"/><path d="M32 18v-4"/><path d="M40 18v-4"/><path d="M20 51h28"/></svg>
                        </div>
                        <div>
                            <h3 style="margin:0;font-family:Georgia,serif;font-size:28px;line-height:1">Wana Cafe</h3>
                            <div style="margin-top:4px;font-size:12px;font-weight:800;opacity:.76">Order Receipt</div>
                        </div>
                    </div>
                    <div style="display:flex;justify-content:space-between;gap:12px;margin-top:16px;padding-top:14px;border-top:1px solid rgba(255,248,237,.22);font-size:12px;font-weight:800">
                        <span>${escapeHtml(order.id)}</span>
                        <span>${escapeHtml(order.createdAt)}</span>
                    </div>
                </div>

                <div style="padding:18px 20px 20px">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px">
                        <div style="padding:11px 12px;border:1px solid rgba(83,58,38,.12);border-radius:14px;background:#fff">
                            <span style="display:block;color:#765f4d;font-size:11px;font-weight:900;text-transform:uppercase">Meja</span>
                            <strong style="display:block;margin-top:4px;font-size:14px">${escapeHtml(order.table)}</strong>
                        </div>
                        <div style="padding:11px 12px;border:1px solid rgba(83,58,38,.12);border-radius:14px;background:#fff">
                            <span style="display:block;color:#765f4d;font-size:11px;font-weight:900;text-transform:uppercase">Kasir</span>
                            <strong style="display:block;margin-top:4px;font-size:14px">${escapeHtml(order.cashier)}</strong>
                        </div>
                        <div style="grid-column:1/-1;padding:11px 12px;border:1px solid rgba(83,58,38,.12);border-radius:14px;background:#fff">
                            <span style="display:block;color:#765f4d;font-size:11px;font-weight:900;text-transform:uppercase">Pelanggan</span>
                            <strong style="display:block;margin-top:4px;font-size:14px">${escapeHtml(order.customer)}</strong>
                        </div>
                        <div style="grid-column:1/-1;padding:11px 12px;border:1px solid rgba(83,58,38,.12);border-radius:14px;background:#fff">
                            <span style="display:block;color:#765f4d;font-size:11px;font-weight:900;text-transform:uppercase">Waktu Pesan</span>
                            <strong style="display:block;margin-top:4px;font-size:14px">${escapeHtml(order.createdAt)}</strong>
                        </div>
                    </div>

                    <div style="display:flex;justify-content:space-between;gap:12px;margin-bottom:10px;color:#765f4d;font-size:12px;font-weight:900;text-transform:uppercase">
                        <span>Detail Belanja</span>
                        <span>${escapeHtml(order.createdAt)}</span>
                    </div>
                    <div style="border-top:1px dashed rgba(83,58,38,.28);border-bottom:1px dashed rgba(83,58,38,.28);padding:12px 0">
                        ${order.items.map(i => `
                            <div style="display:grid;grid-template-columns:minmax(0,1fr) auto;gap:14px;margin-bottom:10px">
                                <div>
                                    <strong style="display:block;font-size:14px;line-height:1.25">${escapeHtml(i.name)}</strong>
                                    <span style="display:block;margin-top:3px;color:#765f4d;font-size:12px">${escapeHtml(i.qty)} x ${rupiah(i.price)}</span>
                                </div>
                                <strong style="font-size:14px;white-space:nowrap">${rupiah(i.price * i.qty)}</strong>
                            </div>
                        `).join('')}
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:16px;padding:14px 16px;border-radius:16px;background:#2b160b;color:#fff8ed;font-weight:900">
                        <span>Total</span>
                        <span style="font-size:20px">${rupiah(order.total)}</span>
                    </div>

                    <div style="display:grid;gap:8px;margin-top:12px;padding:12px 14px;border:1px solid rgba(83,58,38,.12);border-radius:14px;background:#fff;font-size:13px;font-weight:800">
                        <div style="display:flex;justify-content:space-between;gap:12px">
                            <span style="color:#765f4d">Uang Pelanggan</span>
                            <strong>${rupiah(order.paid || order.total)}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;gap:12px">
                            <span style="color:#765f4d">Metode Bayar</span>
                            <strong>${escapeHtml(order.paymentMethod || 'Tunai')}</strong>
                        </div>
                        <div style="display:flex;justify-content:space-between;gap:12px">
                            <span style="color:#765f4d">Kembalian</span>
                            <strong>${rupiah(order.change || 0)}</strong>
                        </div>
                    </div>

                    ${order.paymentMethod === 'QRIS' ? `
                        <div style="display:grid;place-items:center;gap:8px;margin-top:12px;padding:12px;border:1px solid rgba(83,58,38,.12);border-radius:14px;background:#fff">
                            <span style="color:#765f4d;font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase">QRIS Pembayaran</span>
                            <img src="${qrisImageUrl(order.total, order.id)}" alt="QRIS pembayaran" style="width:156px;height:156px;object-fit:contain">
                        </div>
                    ` : ''}

                    <div style="margin-top:14px;color:#765f4d;text-align:center;font-size:12px;line-height:1.5">
                        Terima kasih sudah berkunjung.<br>Pesanan sedang diproses oleh dapur.
                    </div>

                    <div class="receipt-actions" style="margin-top:16px;display:flex;gap:10px;justify-content:flex-end">
                        <button class="btn ghost" onclick="closeReceipt()">Tutup</button>
                        <button class="btn" onclick="printReceipt()">Cetak</button>
                    </div>
                </div>
            </div>
        `;

        modal.innerHTML = receiptHtml;
        modal.onclick = function(e) { if (e.target === modal) closeReceipt(); };
    }

    function closeReceipt() {
        const modal = document.getElementById('receiptModal');
        if (modal) modal.remove();
    }

    function printReceipt() {
        const content = document.getElementById('receiptContent');
        if (!content) return;
        const w = window.open('', '_blank', 'width=400,height=600');
        w.document.write(`<!doctype html><html><head><title>Struk ${new Date().toISOString()}</title><style>body{margin:0;background:#fff;font-family:Arial,sans-serif;padding:10px}#receiptContent{width:100%!important;max-width:none!important;box-shadow:none!important;border-radius:0!important}.receipt-actions{display:none!important}@media print{body{padding:0}}</style></head><body>${content.outerHTML}</body></html>`);
        w.document.close();
        w.focus();
        setTimeout(() => { w.print(); w.close(); }, 300);
        closeReceipt();
    }

    window.addEventListener('wana:storage', () => {
        renderMenu();
    });

    document.querySelectorAll('input[name="paymentMethod"]').forEach((input) => {
        input.addEventListener('change', () => {
            updatePaymentUi();
            renderCart();
        });
    });

    renderMenu();
    renderCart();
</script>
@endpush
