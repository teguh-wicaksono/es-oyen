@extends('layouts.wana', ['title' => 'Stok Bahan | Wana Cafe'])

@section('content')
<div class="material-dashboard">
    <section class="material-hero">
        <div class="material-hero-copy">
            <div class="eyebrow">Owner Kitchen Stock</div>
            <h1>Stok Bahan Dapur</h1>
            <p class="lead">Pantau bahan mentah yang dikelola dapur, batas minimum, kategori bahan, dan catatan operasionalnya.</p>
            <div class="material-hero-actions">
                <button class="btn material-primary" type="button" onclick="refreshMaterialsOwner()">Refresh Data</button>
                <a class="btn material-secondary" href="{{ route('owner.stok') }}">Lihat Stok Produk</a>
            </div>
        </div>
        <div class="material-hero-visual" aria-hidden="true">
            <div class="material-console">
                <img src="https://images.unsplash.com/photo-1606914469633-bd39206ea739?auto=format&fit=crop&w=900&q=85" alt="">
                <div class="material-float top"><span>Total Bahan</span><strong id="heroMaterialCount">0 item</strong></div>
                <div class="material-float bottom"><span>Butuh Restock</span><strong id="heroMaterialLow">0 bahan</strong></div>
                <div id="materialHeroBars" class="material-bars"></div>
            </div>
        </div>
    </section>

    <section class="material-metrics">
        <article class="material-metric"><span>Total Bahan</span><strong id="metricMaterials">0</strong><p>Jumlah bahan dari dapur.</p></article>
        <article class="material-metric"><span>Stok Aman</span><strong id="metricSafe">0</strong><p>Bahan di atas batas minimum.</p></article>
        <article class="material-metric"><span>Stok Rendah</span><strong id="metricLowMaterial">0</strong><p>Bahan perlu perhatian.</p></article>
        <article class="material-metric"><span>Kategori</span><strong id="metricCategories">0</strong><p>Kelompok bahan aktif.</p></article>
    </section>

    <section class="material-layout">
        <div class="material-panel">
            <div class="material-toolbar">
                <div class="material-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m16.5 16.5 4 4"></path></svg>
                    <input id="materialSearch" type="search" placeholder="Cari bahan, kategori, satuan, atau catatan..." autocomplete="off">
                </div>
                <div class="material-segment">
                    <button class="active" type="button" data-material-filter="Semua">Semua</button>
                    <button type="button" data-material-filter="Aman">Aman</button>
                    <button type="button" data-material-filter="Rendah">Rendah</button>
                </div>
                <select id="materialSort" class="material-sort" aria-label="Urutkan stok bahan">
                    <option value="low">Paling kritis</option>
                    <option value="qty-high">Jumlah terbesar</option>
                    <option value="name">Nama A-Z</option>
                    <option value="category">Kategori</option>
                </select>
            </div>
            <div id="materialRows" class="material-grid"></div>
        </div>

        <aside class="material-side">
            <div class="material-panel">
                <div class="material-panel-head"><div><h2>Kategori Bahan</h2><span>Distribusi stok dapur</span></div></div>
                <div id="materialCategoryRows" class="material-side-list"></div>
            </div>
            <div class="material-panel">
                <div class="material-panel-head"><div><h2>Insight Bahan</h2><span>Kontrol cepat owner</span></div></div>
                <div id="materialInsights" class="material-side-list"></div>
            </div>
        </aside>
    </section>
</div>
@endsection

@push('styles')
<style>
    .material-dashboard { width:min(100%,1360px); margin:0 auto; display:grid; gap:20px; }
    .material-hero { position:relative; display:grid; grid-template-columns:minmax(0,1.05fr) minmax(320px,420px); gap:30px; align-items:center; min-height:340px; padding:32px; overflow:hidden; border:1px solid rgba(83,58,38,.12); border-radius:30px; background:linear-gradient(110deg,rgba(43,22,11,.94),rgba(65,38,23,.86) 48%,rgba(143,99,61,.5)),url('https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=1600&q=85') center/cover; box-shadow:0 34px 80px rgba(49,29,15,.12); }
    .material-hero::before { content:""; position:absolute; inset:18px; border:1px solid rgba(255,255,255,.62); border-radius:24px; pointer-events:none; }
    .material-hero-copy,.material-hero-visual { position:relative; z-index:1; }
    .material-hero-copy .eyebrow { color:#f0d7a7; }
    .material-hero-copy h1 { max-width:820px; margin-top:8px; color:#fff8ed; font-size:clamp(42px,5vw,72px); line-height:.98; }
    .material-hero-copy .lead { max-width:720px; color:rgba(255,248,237,.84); font-weight:750; }
    .material-hero-actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:26px; }
    .material-hero-actions .btn { width:auto; min-height:44px; border-radius:999px; }
    .material-primary { color:#fff8ed; background:var(--coffee); }
    .material-secondary { color:var(--coffee); border:1px solid rgba(83,58,38,.13); background:rgba(255,250,242,.88); }
    .material-hero-visual { min-height:276px; display:grid; place-items:center; }
    .material-console { position:relative; width:min(390px,100%); min-height:272px; padding:14px; border:1px solid rgba(255,248,237,.42); border-radius:28px; background:rgba(255,250,242,.14); box-shadow:0 34px 80px rgba(20,10,5,.24); backdrop-filter:blur(10px); }
    .material-console img { width:100%; height:238px; display:block; object-fit:cover; border:1px solid rgba(255,248,237,.44); border-radius:22px; }
    .material-float { position:absolute; display:grid; gap:4px; min-width:150px; padding:14px 16px; border:1px solid rgba(255,250,242,.78); border-radius:16px; background:rgba(255,253,249,.92); box-shadow:0 20px 46px rgba(49,29,15,.13); }
    .material-float.top { top:28px; right:-18px; } .material-float.bottom { left:-18px; bottom:32px; }
    .material-float span { color:var(--sage); font-size:12px; font-weight:900; letter-spacing:.1em; text-transform:uppercase; }
    .material-float strong { font-size:20px; }
    .material-bars { position:absolute; right:22px; bottom:22px; display:flex; align-items:end; gap:7px; width:116px; height:66px; padding:10px; border-radius:16px; background:rgba(43,22,11,.76); }
    .material-bars i { flex:1; height:var(--h,28%); border-radius:999px; background:linear-gradient(180deg,#f3d7a7,#c8844f); }
    .material-metrics { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
    .material-metric,.material-panel { position:relative; overflow:hidden; border:1px solid rgba(83,58,38,.1); border-radius:22px; background:rgba(255,253,249,.9); box-shadow:0 24px 58px rgba(49,29,15,.08); }
    .material-metric { min-height:144px; padding:20px; transition:transform .18s ease,box-shadow .18s ease; }
    .material-metric:hover { transform:translateY(-4px); box-shadow:0 30px 70px rgba(49,29,15,.13); }
    .material-metric::after { content:""; position:absolute; right:-34px; bottom:-42px; width:128px; height:128px; border-radius:999px; background:rgba(100,122,84,.12); }
    .material-metric span,.material-panel-head span { color:var(--muted); font-size:12px; font-weight:900; letter-spacing:.1em; text-transform:uppercase; }
    .material-metric strong { position:relative; z-index:1; display:block; margin-top:14px; font-size:clamp(28px,3vw,40px); line-height:1; }
    .material-metric p { position:relative; z-index:1; margin-top:12px; color:var(--sage); font-weight:700; }
    .material-layout { display:grid; grid-template-columns:minmax(0,1.42fr) minmax(330px,.58fr); gap:20px; align-items:start; }
    .material-panel { padding:18px; }
    .material-toolbar { display:grid; grid-template-columns:minmax(240px,1fr) auto minmax(160px,auto); gap:12px; align-items:center; margin-bottom:16px; }
    .material-search { display:flex; align-items:center; gap:10px; min-height:50px; padding:0 16px; border:1px solid rgba(83,58,38,.12); border-radius:18px; background:#fffaf4; }
    .material-search svg { width:20px; height:20px; color:var(--sage); fill:none; stroke:currentColor; stroke-width:2; }
    .material-search input { width:100%; min-width:0; border:0; outline:0; background:transparent; font-weight:800; }
    .material-segment { display:flex; gap:4px; padding:5px; border:1px solid rgba(83,58,38,.1); border-radius:999px; background:rgba(245,234,219,.72); }
    .material-segment button,.material-sort { font-size:13px; font-weight:900; }
    .material-segment button { min-height:38px; padding:0 14px; border:0; border-radius:999px; color:var(--muted); background:transparent; }
    .material-segment button.active { color:#fff8ed; background:var(--coffee); box-shadow:0 12px 24px rgba(49,29,15,.13); }
    .material-sort { min-height:48px; min-width:170px; padding:0 14px; border:1px solid rgba(83,58,38,.13); border-radius:999px; color:var(--muted); background:#fbf6ef; outline:0; }
    .material-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; max-height:640px; overflow:auto; padding-right:5px; }
    .material-card,.material-side-item { border:1px solid rgba(83,58,38,.09); border-radius:18px; background:rgba(255,250,242,.74); }
    .material-card { display:grid; gap:14px; padding:16px; transition:transform .18s ease,box-shadow .18s ease; }
    .material-card:hover { transform:translateY(-2px); box-shadow:0 22px 50px rgba(49,29,15,.1); }
    .material-card header,.material-card footer,.material-side-item { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:12px; align-items:center; }
    .material-card h3 { margin:0; font-size:18px; } .material-card p { margin-top:6px; color:var(--muted); font-weight:700; line-height:1.45; }
    .material-pill { display:inline-flex; align-items:center; min-height:28px; padding:0 10px; border-radius:999px; color:var(--coffee); background:rgba(245,234,219,.86); font-size:12px; font-weight:900; white-space:nowrap; }
    .material-pill.low { color:#7a3032; background:#f8e3e4; }
    .material-track { height:10px; overflow:hidden; border-radius:999px; background:rgba(83,58,38,.1); }
    .material-track i { display:block; width:var(--bar,0%); height:100%; border-radius:inherit; background:linear-gradient(90deg,#647a54,#c8844f); }
    .material-side { display:grid; gap:18px; }
    .material-panel-head { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:12px; margin-bottom:16px; }
    .material-panel-head h2 { margin:0; font-size:18px; }
    .material-side-list { display:grid; gap:12px; }
    .material-side-item { padding:14px; }
    .material-side-item strong { display:block; font-size:14px; } .material-side-item span { color:var(--muted); font-size:12px; font-weight:750; }
    .material-empty { padding:28px; border:1px dashed rgba(83,58,38,.18); border-radius:18px; color:var(--muted); background:rgba(255,250,242,.62); text-align:center; font-weight:800; }
    @media (max-width:1100px){ .material-hero,.material-layout,.material-toolbar{grid-template-columns:1fr}.material-metrics,.material-grid{grid-template-columns:repeat(2,minmax(0,1fr))} }
    @media (max-width:760px){ .material-hero{padding:24px;border-radius:24px}.material-hero::before{inset:12px;border-radius:18px}.material-hero-copy h1{font-size:clamp(36px,13vw,54px)}.material-hero-actions .btn,.material-sort{width:100%}.material-console{width:min(340px,100%)}.material-float.top{right:4px}.material-float.bottom{left:4px;bottom:22px}.material-metrics,.material-grid,.material-card header,.material-card footer,.material-side-item{grid-template-columns:1fr}.material-panel,.material-metric{border-radius:18px;padding:18px}.material-segment{width:100%;overflow-x:auto}.material-segment button{flex:1 0 auto} }
</style>
@endpush

@push('scripts')
<script>
    let activeMaterialFilter = 'Semua';
    const materialEscape = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' }[char]));
    const materialStatus = (item) => Number(item.qty || 0) <= Number(item.min || 0) ? 'Rendah' : 'Aman';

    function filteredMaterialsOwner() {
        const search = String(document.getElementById('materialSearch').value || '').toLowerCase();
        const sort = document.getElementById('materialSort').value;
        return getMaterials().filter((item) => {
            const status = materialStatus(item);
            const text = [item.name, item.category, item.unit, item.note, status].join(' ').toLowerCase();
            return (activeMaterialFilter === 'Semua' || status === activeMaterialFilter) && (!search || text.includes(search));
        }).sort((a, b) => {
            if (sort === 'qty-high') return Number(b.qty || 0) - Number(a.qty || 0);
            if (sort === 'name') return String(a.name || '').localeCompare(String(b.name || ''));
            if (sort === 'category') return String(a.category || '').localeCompare(String(b.category || ''));
            return (Number(a.qty || 0) - Number(a.min || 0)) - (Number(b.qty || 0) - Number(b.min || 0));
        });
    }

    function renderMaterialsOwner() {
        const allItems = getMaterials();
        const items = filteredMaterialsOwner();
        const lowItems = items.filter((item) => materialStatus(item) === 'Rendah');
        const safeItems = items.filter((item) => materialStatus(item) === 'Aman');
        const categories = new Set(allItems.map((item) => item.category || 'Bahan'));
        const maxQty = Math.max(...allItems.map((item) => Number(item.qty || 0)), 1);

        document.getElementById('heroMaterialCount').textContent = `${allItems.length} item`;
        document.getElementById('heroMaterialLow').textContent = `${allItems.filter((item) => materialStatus(item) === 'Rendah').length} bahan`;
        document.getElementById('metricMaterials').textContent = items.length;
        document.getElementById('metricSafe').textContent = safeItems.length;
        document.getElementById('metricLowMaterial').textContent = lowItems.length;
        document.getElementById('metricCategories').textContent = categories.size;

        document.getElementById('materialHeroBars').innerHTML = [...allItems].sort((a, b) => Number(b.qty || 0) - Number(a.qty || 0)).slice(0, 5).map((item) => {
            const height = Math.max(22, Math.round((Number(item.qty || 0) / maxQty) * 92));
            return `<i style="--h:${height}%"></i>`;
        }).join('') || '<i></i><i></i><i></i><i></i><i></i>';

        document.getElementById('materialRows').innerHTML = items.length ? items.map((item) => {
            const status = materialStatus(item);
            const qty = Number(item.qty || 0);
            return `
                <article class="material-card">
                    <header>
                        <div><h3>${materialEscape(item.name)}</h3><p>${materialEscape(item.category || 'Bahan Dapur')}</p></div>
                        <span class="material-pill ${status === 'Rendah' ? 'low' : ''}">${status}</span>
                    </header>
                    <div class="material-track"><i style="--bar:${Math.min(100, Math.round((qty / maxQty) * 100))}%"></i></div>
                    <footer>
                        <div><span class="material-pill">${qty} ${materialEscape(item.unit || '')}</span><span class="material-pill">Min ${materialEscape(item.min || 0)}</span></div>
                        <strong>${materialEscape(item.note || 'Tanpa catatan')}</strong>
                    </footer>
                </article>
            `;
        }).join('') : '<div class="material-empty">Bahan tidak ditemukan pada filter ini.</div>';

        const categoryRows = allItems.reduce((carry, item) => {
            const category = item.category || 'Bahan';
            carry[category] = carry[category] || { count: 0, low: 0 };
            carry[category].count += 1;
            if (materialStatus(item) === 'Rendah') carry[category].low += 1;
            return carry;
        }, {});

        document.getElementById('materialCategoryRows').innerHTML = Object.entries(categoryRows).map(([category, info]) => `
            <div class="material-side-item"><div><strong>${materialEscape(category)}</strong><span>${info.count} bahan, ${info.low} rendah</span></div><b>${info.count}</b></div>
        `).join('') || '<div class="material-empty">Belum ada kategori bahan.</div>';

        const mostCritical = [...allItems].sort((a, b) => (Number(a.qty || 0) - Number(a.min || 0)) - (Number(b.qty || 0) - Number(b.min || 0)))[0];
        document.getElementById('materialInsights').innerHTML = `
            <div class="material-side-item"><div><strong>Perlu restock</strong><span>${allItems.filter((item) => materialStatus(item) === 'Rendah').map((item) => materialEscape(item.name)).join(', ') || 'Semua bahan aman.'}</span></div><b>${allItems.filter((item) => materialStatus(item) === 'Rendah').length}</b></div>
            <div class="material-side-item"><div><strong>Paling kritis</strong><span>${mostCritical ? materialEscape(mostCritical.name) : 'Belum ada bahan.'}</span></div><b>${mostCritical ? `${materialEscape(mostCritical.qty)} ${materialEscape(mostCritical.unit)}` : '-'}</b></div>
        `;
    }

    async function refreshMaterialsOwner() {
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
        renderMaterialsOwner();
    }

    document.getElementById('materialSearch').addEventListener('input', renderMaterialsOwner);
    document.getElementById('materialSort').addEventListener('change', renderMaterialsOwner);
    document.querySelectorAll('[data-material-filter]').forEach((button) => button.addEventListener('click', () => {
        activeMaterialFilter = button.dataset.materialFilter;
        document.querySelectorAll('[data-material-filter]').forEach((item) => item.classList.toggle('active', item === button));
        renderMaterialsOwner();
    }));
    window.addEventListener('wana:storage', renderMaterialsOwner);
    refreshMaterialsOwner();
</script>
@endpush
