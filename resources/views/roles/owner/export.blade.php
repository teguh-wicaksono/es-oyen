@extends('layouts.wana', ['title' => 'Export Laporan | Wana Cafe'])

@section('content')
<div class="export-dashboard">
    <section class="export-hero">
        <div class="export-hero-copy">
            <div class="eyebrow">Owner Export Center</div>
            <h1>Export Data Operasional</h1>
            <p class="lead">Siapkan laporan transaksi, stok produk, stok bahan, dan ringkasan operasional untuk dicetak atau dibuka di Excel.</p>
            <div class="export-hero-actions">
                <button class="btn export-primary" type="button" onclick="window.print()">Cetak PDF</button>
                <button class="btn export-secondary" type="button" onclick="exportSelectedCsv()">Export CSV</button>
            </div>
        </div>

        <div class="export-hero-visual" aria-hidden="true">
            <div class="export-console">
                <img src="https://images.unsplash.com/photo-1556742031-c6961e8560b0?auto=format&fit=crop&w=900&q=85" alt="">
                <div class="export-float top"><span>Dataset</span><strong id="heroDataset">Transaksi</strong></div>
                <div class="export-float bottom"><span>Record</span><strong id="heroRecordCount">0 data</strong></div>
                <div class="export-stack"><i>CSV</i><i>PDF</i><i>XLS</i></div>
            </div>
        </div>
    </section>

    <section class="export-metrics">
        <article class="export-metric"><span>Transaksi</span><strong id="metricExportOrders">0</strong><p>Data pesanan dari kasir.</p></article>
        <article class="export-metric"><span>Produk</span><strong id="metricExportProducts">0</strong><p>Menu dan stok produk.</p></article>
        <article class="export-metric"><span>Bahan</span><strong id="metricExportMaterials">0</strong><p>Stok bahan dapur.</p></article>
        <article class="export-metric"><span>Omzet</span><strong id="metricExportRevenue">Rp 0</strong><p>Total nilai transaksi.</p></article>
    </section>

    <section class="export-layout">
        <div class="export-panel">
            <div class="export-toolbar">
                <div class="export-options">
                    <button class="active" type="button" data-export-type="orders">Transaksi</button>
                    <button type="button" data-export-type="products">Stok Produk</button>
                    <button type="button" data-export-type="materials">Stok Bahan</button>
                    <button type="button" data-export-type="summary">Ringkasan</button>
                </div>
                <button class="mini-export" type="button" onclick="refreshExportData()">Refresh Data</button>
            </div>
            <div id="exportPreview" class="export-preview"></div>
        </div>

        <aside class="export-side">
            <div class="export-panel">
                <div class="export-panel-head"><div><h2>Format Export</h2><span>Pilih sesuai kebutuhan</span></div></div>
                <div class="export-cards">
                    <button type="button" onclick="window.print()"><strong>PDF Print</strong><span>Cetak halaman laporan yang sedang dibuka.</span></button>
                    <button type="button" onclick="exportSelectedCsv()"><strong>CSV Excel</strong><span>Unduh dataset aktif ke file CSV.</span></button>
                </div>
            </div>
            <div class="export-panel">
                <div class="export-panel-head"><div><h2>Insight Export</h2><span>Data siap unduh</span></div></div>
                <div id="exportInsights" class="export-side-list"></div>
            </div>
        </aside>
    </section>

    <section id="printReport" class="print-report" aria-label="Laporan cetak Wana Cafe"></section>
</div>
@endsection

@push('styles')
<style>
    .export-dashboard { width:min(100%,1360px); margin:0 auto; display:grid; gap:20px; }
    .export-hero { position:relative; display:grid; grid-template-columns:minmax(0,1.05fr) minmax(320px,420px); gap:30px; align-items:center; min-height:340px; padding:32px; overflow:hidden; border:1px solid rgba(83,58,38,.12); border-radius:30px; background:linear-gradient(110deg,rgba(43,22,11,.94),rgba(65,38,23,.86) 48%,rgba(143,99,61,.5)),url('https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=1600&q=85') center/cover; box-shadow:0 34px 80px rgba(49,29,15,.12); }
    .export-hero::before { content:""; position:absolute; inset:18px; border:1px solid rgba(255,255,255,.62); border-radius:24px; pointer-events:none; }
    .export-hero-copy,.export-hero-visual { position:relative; z-index:1; }
    .export-hero-copy .eyebrow { color:#f0d7a7; }
    .export-hero-copy h1 { max-width:820px; margin-top:8px; color:#fff8ed; font-size:clamp(42px,5vw,72px); line-height:.98; }
    .export-hero-copy .lead { max-width:720px; color:rgba(255,248,237,.84); font-weight:750; }
    .export-hero-actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:26px; }
    .export-hero-actions .btn { width:auto; min-height:44px; border-radius:999px; }
    .export-primary { color:#fff8ed; background:var(--coffee); }
    .export-secondary,.mini-export { color:var(--coffee); border:1px solid rgba(83,58,38,.13); background:rgba(255,250,242,.88); }
    .export-hero-visual { min-height:276px; display:grid; place-items:center; }
    .export-console { position:relative; width:min(390px,100%); min-height:272px; padding:14px; border:1px solid rgba(255,248,237,.42); border-radius:28px; background:rgba(255,250,242,.14); box-shadow:0 34px 80px rgba(20,10,5,.24); backdrop-filter:blur(10px); }
    .export-console img { width:100%; height:238px; display:block; object-fit:cover; border:1px solid rgba(255,248,237,.44); border-radius:22px; }
    .export-float { position:absolute; display:grid; gap:4px; min-width:150px; padding:14px 16px; border:1px solid rgba(255,250,242,.78); border-radius:16px; background:rgba(255,253,249,.92); box-shadow:0 20px 46px rgba(49,29,15,.13); }
    .export-float.top { top:28px; right:-18px; } .export-float.bottom { left:-18px; bottom:32px; }
    .export-float span { color:var(--sage); font-size:12px; font-weight:900; letter-spacing:.1em; text-transform:uppercase; }
    .export-float strong { font-size:20px; }
    .export-stack { position:absolute; right:22px; bottom:22px; display:flex; gap:7px; padding:10px; border-radius:16px; background:rgba(43,22,11,.76); }
    .export-stack i { display:grid; place-items:center; width:38px; height:46px; border-radius:12px; color:var(--coffee); background:#fff4df; font-style:normal; font-size:11px; font-weight:900; }
    .export-metrics { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
    .export-metric,.export-panel { position:relative; overflow:hidden; border:1px solid rgba(83,58,38,.1); border-radius:22px; background:rgba(255,253,249,.9); box-shadow:0 24px 58px rgba(49,29,15,.08); }
    .export-metric { min-height:144px; padding:20px; transition:transform .18s ease,box-shadow .18s ease; }
    .export-metric:hover { transform:translateY(-4px); box-shadow:0 30px 70px rgba(49,29,15,.13); }
    .export-metric::after { content:""; position:absolute; right:-34px; bottom:-42px; width:128px; height:128px; border-radius:999px; background:rgba(100,122,84,.12); }
    .export-metric span,.export-panel-head span { color:var(--muted); font-size:12px; font-weight:900; letter-spacing:.1em; text-transform:uppercase; }
    .export-metric strong { position:relative; z-index:1; display:block; margin-top:14px; font-size:clamp(28px,3vw,40px); line-height:1; }
    .export-metric p { position:relative; z-index:1; margin-top:12px; color:var(--sage); font-weight:700; }
    .export-layout { display:grid; grid-template-columns:minmax(0,1.42fr) minmax(330px,.58fr); gap:20px; align-items:start; }
    .export-panel { padding:18px; }
    .export-toolbar { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:12px; align-items:center; margin-bottom:16px; }
    .export-options { display:flex; flex-wrap:wrap; gap:6px; padding:5px; border:1px solid rgba(83,58,38,.1); border-radius:999px; background:rgba(245,234,219,.72); }
    .export-options button,.mini-export { min-height:38px; padding:0 14px; border:0; border-radius:999px; color:var(--muted); background:transparent; font-size:13px; font-weight:900; }
    .export-options button.active { color:#fff8ed; background:var(--coffee); box-shadow:0 12px 24px rgba(49,29,15,.13); }
    .mini-export { border:1px solid rgba(83,58,38,.13); color:var(--coffee); background:#fbf6ef; }
    .export-preview { display:grid; gap:12px; max-height:640px; overflow:auto; padding-right:5px; }
    .export-row,.export-side-item,.export-cards button { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:12px; align-items:center; padding:14px; border:1px solid rgba(83,58,38,.09); border-radius:18px; background:rgba(255,250,242,.74); text-align:left; }
    .export-row strong,.export-side-item strong,.export-cards strong { display:block; font-size:14px; }
    .export-row span,.export-side-item span,.export-cards span { color:var(--muted); font-size:12px; font-weight:750; }
    .export-pill { display:inline-flex; align-items:center; min-height:28px; padding:0 10px; border-radius:999px; color:var(--coffee); background:rgba(245,234,219,.86); font-size:12px; font-weight:900; white-space:nowrap; }
    .export-side { display:grid; gap:18px; }
    .export-panel-head { margin-bottom:16px; }
    .export-panel-head h2 { margin:0; font-size:18px; }
    .export-cards,.export-side-list { display:grid; gap:12px; }
    .export-cards button { grid-template-columns:1fr; cursor:pointer; }
    .export-empty { padding:28px; border:1px dashed rgba(83,58,38,.18); border-radius:18px; color:var(--muted); background:rgba(255,250,242,.62); text-align:center; font-weight:800; }
    .print-report { display:none; }
    @media print {
        @page { size:A4; margin:12mm; }
        body { background:#fff !important; color:#111 !important; font-family:Arial, sans-serif !important; }
        .topbar,.sidebar,.sidebar-overlay,.export-hero,.export-metrics,.export-layout,#toast,.live-popup { display:none !important; }
        .shell,body.sidebar-collapsed .shell { margin:0 !important; width:100% !important; max-width:none !important; }
        .export-dashboard { display:block !important; width:100% !important; }
        .print-report { display:block !important; }
        .print-cover { border-bottom:2px solid #111; margin-bottom:14px; padding-bottom:10px; }
        .print-cover h1 { margin:0 0 6px; font-family:Arial, sans-serif !important; font-size:22px; line-height:1.2; }
        .print-cover p { margin:2px 0; font-size:11px; }
        .print-section { page-break-inside:avoid; margin:0 0 18px; }
        .print-section h2 { margin:0 0 8px; font-size:15px; font-family:Arial, sans-serif !important; }
        .print-table { width:100%; border-collapse:collapse; font-size:10px; }
        .print-table th,.print-table td { border:1px solid #222; padding:5px 6px; text-align:left; vertical-align:top; }
        .print-table th { background:#efefef !important; font-weight:700; }
        .print-table td.number,.print-table th.number { text-align:right; }
    }
    @media (max-width:1100px){ .export-hero,.export-layout,.export-toolbar{grid-template-columns:1fr}.export-metrics{grid-template-columns:repeat(2,minmax(0,1fr))} }
    @media (max-width:760px){ .export-hero{padding:24px;border-radius:24px}.export-hero::before{inset:12px;border-radius:18px}.export-hero-copy h1{font-size:clamp(36px,13vw,54px)}.export-hero-actions .btn,.mini-export{width:100%}.export-console{width:min(340px,100%)}.export-float.top{right:4px}.export-float.bottom{left:4px;bottom:22px}.export-metrics,.export-row,.export-side-item{grid-template-columns:1fr}.export-panel,.export-metric{border-radius:18px;padding:18px}.export-options{border-radius:18px}.export-options button{flex:1 0 auto} }
</style>
@endpush

@push('scripts')
<script>
    let activeExportType = 'orders';
    const exportEscape = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' }[char]));
    const csvValue = (value) => `"${String(value ?? '').replace(/"/g, '""')}"`;

    function exportRows() {
        if (activeExportType === 'products') {
            return {
                label: 'Stok Produk',
                headers: ['Produk', 'Kategori', 'Stok', 'Harga', 'Nilai'],
                rows: getProducts().map((product) => [product.name, product.category, product.stock, product.price, Number(product.stock || 0) * Number(product.price || 0)])
            };
        }
        if (activeExportType === 'materials') {
            return {
                label: 'Stok Bahan',
                headers: ['Bahan', 'Kategori', 'Jumlah', 'Satuan', 'Minimum', 'Catatan'],
                rows: getMaterials().map((item) => [item.name, item.category, item.qty, item.unit, item.min, item.note])
            };
        }
        if (activeExportType === 'summary') {
            const orders = getOrders();
            const revenue = orders.reduce((sum, order) => sum + Number(order.total || 0), 0);
            return {
                label: 'Ringkasan',
                headers: ['Metrik', 'Nilai'],
                rows: [
                    ['Total transaksi', orders.length],
                    ['Total omzet', revenue],
                    ['Produk aktif', getProducts().length],
                    ['Stok bahan', getMaterials().length],
                    ['Chat tercatat', getComplaints().length],
                ]
            };
        }
        return {
            label: 'Transaksi',
            headers: ['Kode', 'Pelanggan', 'Meja', 'Kasir', 'Status', 'Waktu', 'Total'],
            rows: getOrders().map((order) => [order.id, order.customer, order.table, order.cashier, order.status, order.createdAt, order.total])
        };
    }

    function renderExportPage() {
        const orders = getOrders();
        const products = getProducts();
        const materials = getMaterials();
        const revenue = orders.reduce((sum, order) => sum + Number(order.total || 0), 0);
        const dataset = exportRows();

        document.getElementById('metricExportOrders').textContent = orders.length;
        document.getElementById('metricExportProducts').textContent = products.length;
        document.getElementById('metricExportMaterials').textContent = materials.length;
        document.getElementById('metricExportRevenue').textContent = rupiah(revenue);
        document.getElementById('heroDataset').textContent = dataset.label;
        document.getElementById('heroRecordCount').textContent = `${dataset.rows.length} data`;

        document.getElementById('exportPreview').innerHTML = dataset.rows.length ? dataset.rows.slice(0, 30).map((row) => `
            <div class="export-row">
                <div><strong>${exportEscape(row[0])}</strong><span>${exportEscape(dataset.headers.slice(1).map((header, index) => `${header}: ${row[index + 1] ?? '-'}`).join(' | '))}</span></div>
                <span class="export-pill">${dataset.label}</span>
            </div>
        `).join('') : '<div class="export-empty">Belum ada data untuk dataset ini.</div>';

        document.getElementById('exportInsights').innerHTML = `
            <div class="export-side-item"><div><strong>Dataset aktif</strong><span>${exportEscape(dataset.label)}</span></div><b>${dataset.rows.length}</b></div>
            <div class="export-side-item"><div><strong>Omzet transaksi</strong><span>Total dari database pesanan.</span></div><b>${rupiah(revenue)}</b></div>
            <div class="export-side-item"><div><strong>Waktu export</strong><span>${new Date().toLocaleString('id-ID')}</span></div><b>Live</b></div>
        `;

        renderPrintReport();
    }

    function printTable(title, headers, rows, numericIndexes = []) {
        const body = rows.length
            ? rows.map((row) => `
                <tr>
                    ${row.map((cell, index) => `<td class="${numericIndexes.includes(index) ? 'number' : ''}">${exportEscape(cell)}</td>`).join('')}
                </tr>
            `).join('')
            : `<tr><td colspan="${headers.length}">Belum ada data.</td></tr>`;

        return `
            <section class="print-section">
                <h2>${exportEscape(title)}</h2>
                <table class="print-table">
                    <thead>
                        <tr>${headers.map((header, index) => `<th class="${numericIndexes.includes(index) ? 'number' : ''}">${exportEscape(header)}</th>`).join('')}</tr>
                    </thead>
                    <tbody>${body}</tbody>
                </table>
            </section>
        `;
    }

    function renderPrintReport() {
        const orders = getOrders();
        const products = getProducts();
        const materials = getMaterials();
        const chats = getComplaints();
        const revenue = orders.reduce((sum, order) => sum + Number(order.total || 0), 0);
        const paid = orders.reduce((sum, order) => sum + Number(order.paid || 0), 0);
        const change = orders.reduce((sum, order) => sum + Number(order.change || 0), 0);
        const statusCount = (status) => orders.filter((order) => order.status === status).length;

        const summaryRows = [
            ['Total pesanan', orders.length],
            ['Pesanan masuk', statusCount('Masuk')],
            ['Pesanan diproses', statusCount('Diproses')],
            ['Pesanan selesai', statusCount('Selesai')],
            ['Total omzet', rupiah(revenue)],
            ['Total uang pelanggan', rupiah(paid)],
            ['Total kembalian', rupiah(change)],
            ['Produk aktif', products.length],
            ['Stok bahan tercatat', materials.length],
            ['Chat tercatat', chats.length],
        ];

        const orderRows = orders.map((order) => [
            order.id,
            order.createdAt || '-',
            order.customer || '-',
            order.table || '-',
            order.cashier || '-',
            order.status || '-',
            (order.items || []).map((item) => `${item.qty}x ${item.name}`).join(', '),
            rupiah(order.total || 0),
            rupiah(order.paid || 0),
            rupiah(order.change || 0),
        ]);

        const productRows = products.map((product) => [
            product.name,
            product.category,
            product.stock,
            rupiah(product.price || 0),
            rupiah(Number(product.stock || 0) * Number(product.price || 0)),
        ]);

        const materialRows = materials.map((item) => [
            item.name,
            item.category || '-',
            item.qty,
            item.unit || '-',
            item.min,
            Number(item.qty || 0) <= Number(item.min || 0) ? 'Rendah' : 'Aman',
            item.note || '-',
        ]);

        const chatRows = chats.map((chat) => [
            chat.createdAt || chat.time || '-',
            chat.sender || '-',
            chat.recipient || '-',
            chat.message || '-',
        ]);

        document.getElementById('printReport').innerHTML = `
            <div class="print-cover">
                <h1>Laporan Operasional Wana Cafe</h1>
                <p>Tanggal cetak: ${new Date().toLocaleString('id-ID')}</p>
                <p>Sumber data: database aplikasi Wana Cafe</p>
            </div>
            ${printTable('Ringkasan Omzet dan Operasional', ['Metrik', 'Nilai'], summaryRows, [1])}
            ${printTable('Laporan Pesanan', ['Kode', 'Waktu', 'Pelanggan', 'Meja', 'Kasir', 'Status', 'Detail Item', 'Total', 'Dibayar', 'Kembalian'], orderRows, [7, 8, 9])}
            ${printTable('Laporan Stok Produk', ['Produk', 'Kategori', 'Stok', 'Harga', 'Nilai Stok'], productRows, [2, 3, 4])}
            ${printTable('Laporan Stok Bahan', ['Bahan', 'Kategori', 'Jumlah', 'Satuan', 'Minimum', 'Status', 'Catatan'], materialRows, [2, 4])}
            ${printTable('Laporan Chat Tim', ['Waktu', 'Dari', 'Ke', 'Pesan'], chatRows)}
        `;
    }

    async function refreshExportData() {
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
        renderExportPage();
    }

    function exportSelectedCsv() {
        const dataset = exportRows();
        const rows = [dataset.headers, ...dataset.rows];
        const csv = rows.map((row) => row.map(csvValue).join(',')).join('\n');
        const link = document.createElement('a');
        link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
        link.download = `wana-cafe-${activeExportType}.csv`;
        link.click();
        URL.revokeObjectURL(link.href);
    }

    document.querySelectorAll('[data-export-type]').forEach((button) => button.addEventListener('click', () => {
        activeExportType = button.dataset.exportType;
        document.querySelectorAll('[data-export-type]').forEach((item) => item.classList.toggle('active', item === button));
        renderExportPage();
    }));

    window.addEventListener('wana:storage', renderExportPage);
    refreshExportData();
</script>
@endpush
