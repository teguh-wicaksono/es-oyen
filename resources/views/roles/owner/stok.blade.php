@extends('layouts.wana', ['title' => 'Stok Produk | Wana Cafe'])

@section('content')
<div class="stock-dashboard">
    <section class="stock-hero">
        <div class="stock-hero-copy">
            <div class="eyebrow">Owner Inventory Center</div>
            <h1>Stok Produk</h1>
            <p class="lead">Pantau stok menu, nilai persediaan produk, dan item yang perlu segera diisi ulang.</p>
            <div class="stock-hero-actions">
                <button class="btn stock-primary" type="button" onclick="refreshStockProducts()">Refresh Data</button>
                <a class="btn stock-secondary" href="{{ route('owner.stok-bahan') }}">Lihat Stok Bahan</a>
            </div>
        </div>
        <div class="stock-hero-visual" aria-hidden="true">
            <div class="stock-console">
                <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93?auto=format&fit=crop&w=900&q=85" alt="">
                <div class="stock-float top"><span>Nilai Produk</span><strong id="heroStockValue">Rp 0</strong></div>
                <div class="stock-float bottom"><span>Produk Aktif</span><strong id="heroStockCount">0 item</strong></div>
                <div id="stockHeroBars" class="stock-bars"></div>
            </div>
        </div>
    </section>

    <section class="stock-metrics">
        <article class="stock-metric"><span>Total Nilai</span><strong id="metricValue">Rp 0</strong><p>Estimasi nilai stok produk.</p></article>
        <article class="stock-metric"><span>Produk Aktif</span><strong id="metricProducts">0</strong><p>Menu yang tampil di kasir.</p></article>
        <article class="stock-metric"><span>Stok Rendah</span><strong id="metricLow">0</strong><p>Produk dengan stok 10 atau kurang.</p></article>
        <article class="stock-metric"><span>Total Unit</span><strong id="metricUnits">0</strong><p>Akumulasi unit produk tersedia.</p></article>
    </section>

    <section class="stock-layout">
        <div class="stock-panel">
            <div class="stock-toolbar">
                <div class="stock-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m16.5 16.5 4 4"></path></svg>
                    <input id="stockSearch" type="search" placeholder="Cari produk atau kategori..." autocomplete="off">
                </div>
                <div class="stock-segment">
                    <button class="active" type="button" data-stock-filter="Semua">Semua</button>
                    <button type="button" data-stock-filter="Aman">Aman</button>
                    <button type="button" data-stock-filter="Rendah">Rendah</button>
                </div>
                <select id="stockSort" class="stock-sort" aria-label="Urutkan stok produk">
                    <option value="value">Nilai terbesar</option>
                    <option value="low">Stok terendah</option>
                    <option value="high">Stok tertinggi</option>
                    <option value="name">Nama A-Z</option>
                </select>
            </div>
            <div id="stockRows" class="stock-grid"></div>
        </div>

        <aside class="stock-side">
            <div class="stock-panel">
                <div class="stock-panel-head"><div><h2>Kategori Produk</h2><span>Komposisi menu aktif</span></div></div>
                <div id="categoryRows" class="stock-side-list"></div>
            </div>
            <div class="stock-panel">
                <div class="stock-panel-head"><div><h2>Insight Stok</h2><span>Rekomendasi cepat</span></div></div>
                <div id="stockInsights" class="stock-side-list"></div>
            </div>
        </aside>
    </section>
</div>
@endsection

@push('styles')
<style>
    .stock-dashboard { width: min(100%, 1360px); margin: 0 auto; display: grid; gap: 20px; }
    .stock-hero {
        position: relative; display: grid; grid-template-columns: minmax(0, 1.05fr) minmax(320px, 420px);
        gap: 30px; align-items: center; min-height: 340px; padding: 32px; overflow: hidden;
        border: 1px solid rgba(83,58,38,.12); border-radius: 30px;
        background: linear-gradient(110deg, rgba(43,22,11,.94), rgba(65,38,23,.86) 48%, rgba(143,99,61,.5)), url('https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=1600&q=85') center/cover;
        box-shadow: 0 34px 80px rgba(49,29,15,.12);
    }
    .stock-hero::before { content:""; position:absolute; inset:18px; border:1px solid rgba(255,255,255,.62); border-radius:24px; pointer-events:none; }
    .stock-hero-copy, .stock-hero-visual { position: relative; z-index: 1; }
    .stock-hero-copy .eyebrow { color:#f0d7a7; }
    .stock-hero-copy h1 { max-width: 780px; margin-top: 8px; color:#fff8ed; font-size: clamp(42px,5vw,72px); line-height:.98; }
    .stock-hero-copy .lead { max-width: 720px; color: rgba(255,248,237,.84); font-weight: 750; }
    .stock-hero-actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:26px; }
    .stock-hero-actions .btn { width:auto; min-height:44px; border-radius:999px; }
    .stock-primary { color:#fff8ed; background:var(--coffee); }
    .stock-secondary { color:var(--coffee); border:1px solid rgba(83,58,38,.13); background:rgba(255,250,242,.88); }
    .stock-hero-visual { min-height:276px; display:grid; place-items:center; }
    .stock-console { position:relative; width:min(390px,100%); min-height:272px; padding:14px; border:1px solid rgba(255,248,237,.42); border-radius:28px; background:rgba(255,250,242,.14); box-shadow:0 34px 80px rgba(20,10,5,.24); backdrop-filter:blur(10px); }
    .stock-console img { width:100%; height:238px; display:block; object-fit:cover; border:1px solid rgba(255,248,237,.44); border-radius:22px; }
    .stock-float { position:absolute; display:grid; gap:4px; min-width:150px; padding:14px 16px; border:1px solid rgba(255,250,242,.78); border-radius:16px; background:rgba(255,253,249,.92); box-shadow:0 20px 46px rgba(49,29,15,.13); }
    .stock-float.top { top:28px; right:-18px; } .stock-float.bottom { left:-18px; bottom:32px; }
    .stock-float span { color:var(--sage); font-size:12px; font-weight:900; letter-spacing:.1em; text-transform:uppercase; }
    .stock-float strong { font-size:20px; }
    .stock-bars { position:absolute; right:22px; bottom:22px; display:flex; align-items:end; gap:7px; width:116px; height:66px; padding:10px; border-radius:16px; background:rgba(43,22,11,.76); }
    .stock-bars i { flex:1; height:var(--h,28%); border-radius:999px; background:linear-gradient(180deg,#f3d7a7,#c8844f); transition:height .24s ease; }
    .stock-metrics { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
    .stock-metric, .stock-panel { position:relative; overflow:hidden; border:1px solid rgba(83,58,38,.1); border-radius:22px; background:rgba(255,253,249,.9); box-shadow:0 24px 58px rgba(49,29,15,.08); }
    .stock-metric { min-height:144px; padding:20px; transition:transform .18s ease, box-shadow .18s ease; }
    .stock-metric:hover { transform:translateY(-4px); box-shadow:0 30px 70px rgba(49,29,15,.13); }
    .stock-metric::after { content:""; position:absolute; right:-34px; bottom:-42px; width:128px; height:128px; border-radius:999px; background:rgba(100,122,84,.12); }
    .stock-metric span, .stock-panel-head span { color:var(--muted); font-size:12px; font-weight:900; letter-spacing:.1em; text-transform:uppercase; }
    .stock-metric strong { position:relative; z-index:1; display:block; margin-top:14px; font-size:clamp(28px,3vw,40px); line-height:1; }
    .stock-metric p { position:relative; z-index:1; margin-top:12px; color:var(--sage); font-weight:700; }
    .stock-layout { display:grid; grid-template-columns:minmax(0,1.42fr) minmax(330px,.58fr); gap:20px; align-items:start; }
    .stock-panel { padding:18px; }
    .stock-toolbar { display:grid; grid-template-columns:minmax(240px,1fr) auto minmax(160px,auto); gap:12px; align-items:center; margin-bottom:16px; }
    .stock-search { display:flex; align-items:center; gap:10px; min-height:50px; padding:0 16px; border:1px solid rgba(83,58,38,.12); border-radius:18px; background:#fffaf4; }
    .stock-search svg { width:20px; height:20px; color:var(--sage); fill:none; stroke:currentColor; stroke-width:2; }
    .stock-search input { width:100%; min-width:0; border:0; outline:0; background:transparent; font-weight:800; }
    .stock-segment { display:flex; gap:4px; padding:5px; border:1px solid rgba(83,58,38,.1); border-radius:999px; background:rgba(245,234,219,.72); }
    .stock-segment button, .stock-sort { font-size:13px; font-weight:900; }
    .stock-segment button { min-height:38px; padding:0 14px; border:0; border-radius:999px; color:var(--muted); background:transparent; }
    .stock-segment button.active { color:#fff8ed; background:var(--coffee); box-shadow:0 12px 24px rgba(49,29,15,.13); }
    .stock-sort { min-height:48px; min-width:170px; padding:0 14px; border:1px solid rgba(83,58,38,.13); border-radius:999px; color:var(--muted); background:#fbf6ef; outline:0; }
    .stock-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; max-height:640px; overflow:auto; padding-right:5px; }
    .stock-card, .stock-side-item { border:1px solid rgba(83,58,38,.09); border-radius:18px; background:rgba(255,250,242,.74); }
    .stock-card { display:grid; gap:14px; padding:16px; transition:transform .18s ease, box-shadow .18s ease; }
    .stock-card:hover { transform:translateY(-2px); box-shadow:0 22px 50px rgba(49,29,15,.1); }
    .stock-card header, .stock-card footer, .stock-side-item { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:12px; align-items:center; }
    .stock-card h3 { margin:0; font-size:18px; } .stock-card p { margin-top:6px; color:var(--muted); font-weight:700; line-height:1.45; }
    .stock-pill { display:inline-flex; align-items:center; min-height:28px; padding:0 10px; border-radius:999px; color:var(--coffee); background:rgba(245,234,219,.86); font-size:12px; font-weight:900; white-space:nowrap; }
    .stock-pill.low { color:#7a3032; background:#f8e3e4; }
    .stock-track { height:10px; overflow:hidden; border-radius:999px; background:rgba(83,58,38,.1); }
    .stock-track i { display:block; width:var(--bar,0%); height:100%; border-radius:inherit; background:linear-gradient(90deg,#647a54,#c8844f); }
    .stock-side { display:grid; gap:18px; }
    .stock-panel-head { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:12px; margin-bottom:16px; }
    .stock-panel-head h2 { margin:0; font-size:18px; }
    .stock-side-list { display:grid; gap:12px; }
    .stock-side-item { padding:14px; }
    .stock-side-item strong { display:block; font-size:14px; } .stock-side-item span { color:var(--muted); font-size:12px; font-weight:750; }
    .stock-empty { padding:28px; border:1px dashed rgba(83,58,38,.18); border-radius:18px; color:var(--muted); background:rgba(255,250,242,.62); text-align:center; font-weight:800; }
    @media (max-width:1100px){ .stock-hero,.stock-layout,.stock-toolbar{grid-template-columns:1fr}.stock-metrics,.stock-grid{grid-template-columns:repeat(2,minmax(0,1fr))} }
    @media (max-width:760px){ .stock-hero{padding:24px;border-radius:24px}.stock-hero::before{inset:12px;border-radius:18px}.stock-hero-copy h1{font-size:clamp(36px,13vw,54px)}.stock-hero-actions .btn,.stock-sort{width:100%}.stock-console{width:min(340px,100%)}.stock-float.top{right:4px}.stock-float.bottom{left:4px;bottom:22px}.stock-metrics,.stock-grid,.stock-card header,.stock-card footer,.stock-side-item{grid-template-columns:1fr}.stock-panel,.stock-metric{border-radius:18px;padding:18px}.stock-segment{width:100%;overflow-x:auto}.stock-segment button{flex:1 0 auto} }
</style>
@endpush

@push('scripts')
<script>
    let activeStockFilter = 'Semua';
    const stockEscape = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' }[char]));
    const stockValue = (product) => Number(product.stock || 0) * Number(product.price || 0);
    const stockStatus = (product) => Number(product.stock || 0) <= 10 ? 'Rendah' : 'Aman';

    function filteredStockProducts() {
        const search = String(document.getElementById('stockSearch').value || '').toLowerCase();
        const sort = document.getElementById('stockSort').value;
        return getProducts().filter((product) => {
            const status = stockStatus(product);
            const text = [product.name, product.category, status].join(' ').toLowerCase();
            return (activeStockFilter === 'Semua' || status === activeStockFilter) && (!search || text.includes(search));
        }).sort((a, b) => {
            if (sort === 'low') return Number(a.stock || 0) - Number(b.stock || 0);
            if (sort === 'high') return Number(b.stock || 0) - Number(a.stock || 0);
            if (sort === 'name') return String(a.name || '').localeCompare(String(b.name || ''));
            return stockValue(b) - stockValue(a);
        });
    }

    function renderStockProducts() {
        const allProducts = getProducts();
        const products = filteredStockProducts();
        const totalValue = products.reduce((sum, product) => sum + stockValue(product), 0);
        const allValue = allProducts.reduce((sum, product) => sum + stockValue(product), 0);
        const totalUnits = products.reduce((sum, product) => sum + Number(product.stock || 0), 0);
        const maxStock = Math.max(...allProducts.map((product) => Number(product.stock || 0)), 1);

        document.getElementById('heroStockValue').textContent = rupiah(allValue);
        document.getElementById('heroStockCount').textContent = `${allProducts.length} item`;
        document.getElementById('metricValue').textContent = rupiah(totalValue);
        document.getElementById('metricProducts').textContent = products.length;
        document.getElementById('metricLow').textContent = products.filter((product) => stockStatus(product) === 'Rendah').length;
        document.getElementById('metricUnits').textContent = totalUnits;

        document.getElementById('stockHeroBars').innerHTML = [...allProducts].sort((a, b) => stockValue(b) - stockValue(a)).slice(0, 5).map((product) => {
            const height = Math.max(22, Math.round((Number(product.stock || 0) / maxStock) * 92));
            return `<i style="--h:${height}%"></i>`;
        }).join('') || '<i></i><i></i><i></i><i></i><i></i>';

        document.getElementById('stockRows').innerHTML = products.length ? products.map((product) => {
            const status = stockStatus(product);
            const stock = Number(product.stock || 0);
            return `
                <article class="stock-card">
                    <header>
                        <div><h3>${stockEscape(product.name)}</h3><p>${stockEscape(product.category || 'Menu')}</p></div>
                        <span class="stock-pill ${status === 'Rendah' ? 'low' : ''}">${status}</span>
                    </header>
                    <div class="stock-track"><i style="--bar:${Math.min(100, Math.round((stock / maxStock) * 100))}%"></i></div>
                    <footer>
                        <div><span class="stock-pill">${stock} stok</span></div>
                        <strong>${rupiah(stockValue(product))}</strong>
                    </footer>
                </article>
            `;
        }).join('') : '<div class="stock-empty">Produk tidak ditemukan pada filter ini.</div>';

        const categories = allProducts.reduce((carry, product) => {
            const category = product.category || 'Menu';
            carry[category] = carry[category] || { count: 0, value: 0 };
            carry[category].count += 1;
            carry[category].value += stockValue(product);
            return carry;
        }, {});

        document.getElementById('categoryRows').innerHTML = Object.entries(categories).map(([category, info]) => `
            <div class="stock-side-item"><div><strong>${stockEscape(category)}</strong><span>${info.count} produk aktif</span></div><b>${rupiah(info.value)}</b></div>
        `).join('') || '<div class="stock-empty">Belum ada kategori.</div>';

        const lowProducts = allProducts.filter((product) => stockStatus(product) === 'Rendah');
        const highestValue = [...allProducts].sort((a, b) => stockValue(b) - stockValue(a))[0];
        document.getElementById('stockInsights').innerHTML = `
            <div class="stock-side-item"><div><strong>Perlu restock</strong><span>${lowProducts.length ? lowProducts.map((item) => stockEscape(item.name)).join(', ') : 'Semua produk aman.'}</span></div><b>${lowProducts.length}</b></div>
            <div class="stock-side-item"><div><strong>Nilai terbesar</strong><span>${highestValue ? stockEscape(highestValue.name) : 'Belum ada produk.'}</span></div><b>${highestValue ? rupiah(stockValue(highestValue)) : '-'}</b></div>
        `;
    }

    async function refreshStockProducts() {
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
        renderStockProducts();
    }

    document.getElementById('stockSearch').addEventListener('input', renderStockProducts);
    document.getElementById('stockSort').addEventListener('change', renderStockProducts);
    document.querySelectorAll('[data-stock-filter]').forEach((button) => button.addEventListener('click', () => {
        activeStockFilter = button.dataset.stockFilter;
        document.querySelectorAll('[data-stock-filter]').forEach((item) => item.classList.toggle('active', item === button));
        renderStockProducts();
    }));
    window.addEventListener('wana:storage', renderStockProducts);
    refreshStockProducts();
</script>
@endpush
