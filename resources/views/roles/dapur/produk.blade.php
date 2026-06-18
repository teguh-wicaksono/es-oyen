@extends('layouts.wana', ['title' => 'Produk Dapur | Wana Cafe'])

@section('content')
    @php
        $lowStockCount = collect($products)->filter(fn ($product) => ($product['stock'] ?? 0) <= 10)->count();
    @endphp

    <section class="product-hero">
        <div class="hero-copy">
            <div class="eyebrow">Role Dapur</div>
            <h1>Kelola Produk Dapur</h1>
            <p class="lead">Hanya dapur yang bisa mengubah stok, gambar, harga, dan detail produk. Tampilan dibuat seirama dengan menu kasir agar lebih familiar dan mudah dipakai.</p>
            <div class="hero-actions">
                <button class="btn hero-action" type="button" onclick="openProductModal()">Tambah Produk</button>
            </div>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <div class="coffee-orbit">
                <img src="https://images.unsplash.com/photo-1498654896293-37aacf113fd9?auto=format&fit=crop&w=1200&q=85" alt="">
            </div>
            <div class="floating-note top-note">
                <span>Menu Aktif</span>
                <strong>{{ count($products) }} item</strong>
            </div>
            <div class="floating-note bottom-note">
                <span>Stok Rendah</span>
                <strong>{{ $lowStockCount }} item</strong>
            </div>
        </div>
    </section>

    <section class="dashboard-cards">
        <article class="dashboard-card card-primary">
            <span>Total Produk</span>
            <strong>{{ count($products) }}</strong>
            <p>Semua menu yang tersimpan untuk operasional dapur.</p>
        </article>
        <article class="dashboard-card card-secondary">
            <span>Menu Aktif</span>
            <strong>{{ count($products) }}</strong>
            <p>Produk yang tampil dan bisa dipakai oleh kasir.</p>
        </article>
        <article class="dashboard-card card-tertiary">
            <span>Stok Rendah</span>
            <strong>{{ $lowStockCount }}</strong>
            <p>Menu yang perlu diperiksa ketersediaannya.</p>
        </article>
    </section>

    <section class="panel product-panel">
        <div class="panel-title">
            <div>
                <h2>Daftar Produk</h2>
                <p class="mini">Tampilkan menu dengan kartu visual yang sama seperti menu kasir.</p>
            </div>
            <div class="filter-row">
                <button class="pill active" data-filter="Semua" type="button" onclick="setProductFilter('Semua')">Semua</button>
                <button class="pill" data-filter="Minuman" type="button" onclick="setProductFilter('Minuman')">Minuman</button>
                <button class="pill" data-filter="Makanan" type="button" onclick="setProductFilter('Makanan')">Makanan</button>
                <button class="pill" data-filter="Snack" type="button" onclick="setProductFilter('Snack')">Snack</button>
            </div>
        </div>

        <div id="productCards" class="menu-grid"></div>
    </section>

    <div id="productModal" class="modal hidden" onclick="closeProductModal(event)">
        <div class="modal-panel" onclick="event.stopPropagation()">
            <div class="modal-header">
                <div class="modal-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M5 8h10v6a5 5 0 0 1-5 5 5 5 0 0 1-5-5V8Z"/><path d="M15 10h2a3 3 0 0 1 0 6h-2"/><path d="M6 3v2"/><path d="M10 3v2"/><path d="M14 3v2"/></svg>
                </div>
                <div>
                    <h2 id="productModalTitle">Tambah Produk</h2>
                    <p class="mini" id="productModalSubtitle">Isi data produk baru atau edit produk yang tersedia.</p>
                </div>
                <button class="topbar-action" type="button" onclick="closeProductModal()" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                </button>
            </div>

            <div class="modal-form">
            <div class="field field-wide">
                <label>Nama Menu</label>
                <input id="editProductName" type="text" placeholder="Ketik nama menu...">
            </div>
            <div class="field field-wide">
                <label>Gambar</label>
                <input id="editProductImage" type="text" placeholder="URL gambar produk">
                <div class="image-tools">
                    <button class="image-tool" type="button" onclick="document.getElementById('productImageFile').click()">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="m9 15 2 2 4-5"/></svg>
                        Pilih Foto
                    </button>
                    <button class="image-tool" type="button" onclick="openCameraCapture()">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8h3l2-3h6l2 3h3v11H4Z"/><circle cx="12" cy="13.5" r="3.5"/></svg>
                        Buka Kamera
                    </button>
                    <input id="productImageFile" type="file" accept="image/*" hidden>
                </div>
                <div class="image-preview" id="productImagePreview">
                    <span>Preview gambar akan tampil setelah URL diisi.</span>
                </div>
            </div>
            <div class="field">
                <label>Kategori</label>
                <select id="editProductCategory">
                    <option>Minuman</option>
                    <option>Makanan</option>
                    <option>Snack</option>
                </select>
            </div>
            <div class="field">
                <label>Stok</label>
                <input id="editProductStock" type="number" min="0" value="0">
            </div>
            <div class="field">
                <label>Harga</label>
                <input id="editProductPrice" type="number" min="0" value="0">
            </div>
            <div class="field field-wide">
                <label>Deskripsi</label>
                <textarea id="editProductDescription" placeholder="Detail produk..." rows="4"></textarea>
            </div>
            </div>

            <div class="modal-actions">
                <button class="btn secondary" type="button" onclick="resetProductForm()">Bersihkan</button>
                <button class="btn" id="saveProductButton" type="button" onclick="saveProduct()">Simpan Produk</button>
            </div>
        </div>
    </div>

    <div id="cameraModal" class="camera-modal hidden" onclick="closeCameraCapture(event)">
        <div class="camera-panel" onclick="event.stopPropagation()">
            <div class="camera-head">
                <div>
                    <h2>Kamera Produk</h2>
                    <p>Ambil foto langsung dari kamera perangkat.</p>
                </div>
                <button class="camera-close" type="button" onclick="closeCameraCapture()" aria-label="Tutup kamera">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12"></path><path d="M18 6 6 18"></path></svg>
                </button>
            </div>

            <div class="camera-view">
                <video id="productCameraVideo" autoplay playsinline muted></video>
                <div id="cameraMessage" class="camera-message">Meminta akses kamera...</div>
            </div>

            <div class="camera-actions">
                <button class="btn secondary" type="button" onclick="closeCameraCapture()">Batal</button>
                <button class="btn" type="button" onclick="captureProductPhoto()">Jepret Foto</button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .product-hero {
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
            url('https://images.unsplash.com/photo-1498654896293-37aacf113fd9?auto=format&fit=crop&w=1600&q=80') center/cover;
        box-shadow: 0 34px 80px rgba(49, 29, 15, .12);
    }

    .product-hero::before {
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

    .dashboard-card span,
    .dashboard-card strong,
    .dashboard-card p {
        position: relative;
        z-index: 1;
    }

    .dashboard-card span {
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    .dashboard-card strong {
        font-size: 32px;
        margin-top: 4px;
        line-height: 1.05;
    }

    .dashboard-card p {
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

    .product-panel {
        width: 100%;
        padding: 24px;
        border-radius: 28px;
        background: rgba(255, 253, 249, .94);
        box-shadow: 0 30px 80px rgba(49, 29, 15, .11);
    }

    .product-panel .panel-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }

    .product-panel .panel-title h2 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
    }

    .product-panel .mini {
        color: var(--muted);
        font-size: 13px;
        line-height: 1.6;
        margin-top: 4px;
    }

    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: .55rem;
    }

    .pill {
        min-height: 42px;
        padding: 0 16px;
        border-radius: 999px;
        border: 1px solid rgba(83, 58, 38, .12);
        background: #fffaf2;
        color: var(--muted);
        font-weight: 800;
    }

    .pill.active {
        color: #fff8ed;
        background: var(--coffee);
        border-color: var(--coffee);
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

    .product-foot button {
        flex: 1;
        min-height: 44px;
        padding: 0 12px;
        border-radius: 15px;
        border: none;
        font-weight: 800;
        cursor: pointer;
    }

    .product-foot .btn {
        min-height: 44px;
        border-radius: 15px;
        box-shadow: none;
    }

    .product-foot .btn:hover {
        transform: translateY(-1px);
    }

    .product-foot .secondary {
        color: var(--coffee);
        background: #f7efe3;
        border: 1px solid var(--line);
    }

    .product-foot .secondary:hover {
        background: #efe4d4;
    }

    .product-foot .warn {
        background: #f7dfdc;
        color: #8a2f2f;
    }

    .product-foot .warn:hover {
        background: #efc8c3;
    }

    .field input,
    .field select,
    .field textarea {
        width: 100%;
    }

    .modal {
        position: fixed;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background:
            radial-gradient(circle at 48% 34%, rgba(227, 180, 103, .18), transparent 32%),
            rgba(15, 11, 7, .48);
        backdrop-filter: blur(10px);
        z-index: 9999;
    }

    .modal.hidden {
        display: none !important;
    }

    .modal-panel {
        width: min(760px, 100%);
        max-height: min(92dvh, 840px);
        overflow-y: auto;
        padding: 0;
        border: 1px solid rgba(255, 250, 244, .7);
        background: linear-gradient(180deg, #fffdf9, #f8f0e7);
        border-radius: 28px;
        box-shadow: 0 34px 110px rgba(20, 10, 5, .34);
    }

    .modal-header {
        position: sticky;
        top: 0;
        z-index: 2;
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
        padding: 22px 24px;
        border-bottom: 1px solid rgba(83, 58, 38, .10);
        background: rgba(255, 253, 249, .88);
        backdrop-filter: blur(14px);
    }

    .modal-icon {
        display: grid;
        place-items: center;
        width: 52px;
        height: 52px;
        border-radius: 18px;
        color: #fff8ed;
        background:
            radial-gradient(circle at 32% 24%, rgba(255, 232, 184, .62), transparent 30%),
            linear-gradient(145deg, #261209, #8b5737);
        box-shadow: 0 16px 34px rgba(43, 22, 11, .18);
    }

    .modal-icon svg {
        width: 29px;
        height: 29px;
        stroke: currentColor;
        stroke-width: 2.2;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .modal-header h2 {
        margin: 0;
        color: var(--coffee);
        font-size: 28px;
        line-height: 1.05;
    }

    .modal-header .mini {
        max-width: 460px;
        margin-top: 5px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.55;
    }

    .modal-header button {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 14px;
        border: none;
        background: rgba(83, 58, 38, .07);
        color: var(--ink);
    }

    .modal-header button:hover {
        background: rgba(83, 58, 38, .12);
    }

    .modal-form {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        padding: 22px 24px 8px;
    }

    .modal-form .field {
        margin: 0;
    }

    .field-wide {
        grid-column: 1 / -1;
    }

    .modal-form .field label {
        color: #765f4d;
        font-size: 12px;
        letter-spacing: .08em;
    }

    .modal-form .field input,
    .modal-form .field select,
    .modal-form .field textarea {
        min-height: 54px;
        border-radius: 16px;
        border-color: rgba(83, 58, 38, .13);
        background: rgba(255, 253, 249, .9);
        font-weight: 700;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .82);
    }

    .modal-form .field textarea {
        min-height: 118px;
    }

    .image-tools {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .image-tool {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 14px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 14px;
        color: var(--coffee);
        background: #fffaf4;
        font-size: 13px;
        font-weight: 900;
        box-shadow: 0 10px 24px rgba(49, 29, 15, .06);
        transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
    }

    .image-tool:hover {
        transform: translateY(-1px);
        background: #f7efe3;
        box-shadow: 0 14px 30px rgba(49, 29, 15, .09);
    }

    .image-tool svg {
        width: 18px;
        height: 18px;
        stroke: currentColor;
        stroke-width: 2.2;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .image-preview {
        position: relative;
        display: grid;
        place-items: center;
        min-height: 132px;
        margin-top: 10px;
        overflow: hidden;
        border: 1px dashed rgba(83, 58, 38, .18);
        border-radius: 18px;
        background:
            linear-gradient(135deg, rgba(255, 250, 242, .88), rgba(245, 234, 219, .66));
        color: var(--muted);
        font-size: 13px;
        font-weight: 800;
        text-align: center;
    }

    .image-preview img {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .image-preview.has-image {
        border-style: solid;
        background: #efe5d8;
    }

    .image-preview.has-image::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent, rgba(22, 12, 7, .18));
        pointer-events: none;
    }

    .camera-modal {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgba(15, 11, 7, .58);
        backdrop-filter: blur(10px);
    }

    .camera-modal.hidden {
        display: none !important;
    }

    .camera-panel {
        width: min(680px, 100%);
        overflow: hidden;
        border: 1px solid rgba(255, 250, 244, .42);
        border-radius: 26px;
        background: #fffaf4;
        box-shadow: 0 34px 110px rgba(20, 10, 5, .34);
    }

    .camera-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 20px 22px;
        border-bottom: 1px solid rgba(83, 58, 38, .10);
    }

    .camera-head h2 {
        margin: 0;
        color: var(--coffee);
        font-size: 24px;
    }

    .camera-head p {
        margin-top: 5px;
        color: var(--muted);
        font-size: 14px;
    }

    .camera-close {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        border: 0;
        border-radius: 14px;
        color: var(--coffee);
        background: rgba(83, 58, 38, .07);
    }

    .camera-close svg {
        width: 20px;
        height: 20px;
        stroke: currentColor;
        stroke-width: 2.2;
        fill: none;
        stroke-linecap: round;
    }

    .camera-view {
        position: relative;
        display: grid;
        place-items: center;
        min-height: 360px;
        background: #160c07;
    }

    .camera-view video {
        width: 100%;
        max-height: 68vh;
        display: block;
        object-fit: cover;
        aspect-ratio: 4 / 3;
    }

    .camera-message {
        position: absolute;
        inset: 0;
        display: grid;
        place-items: center;
        padding: 24px;
        color: #fff8ed;
        background: rgba(22, 12, 7, .72);
        font-weight: 900;
        text-align: center;
    }

    .camera-message.hidden {
        display: none;
    }

    .camera-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 18px 22px 22px;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding: 16px 24px 24px;
        margin-top: 0;
    }

    .modal-actions .btn {
        min-width: 150px;
        min-height: 54px;
        border-radius: 18px;
    }

    @media (max-width: 860px) {
        .product-hero {
            grid-template-columns: 1fr;
            align-items: start;
        }

        .hero-visual {
            min-height: 240px;
        }

        .dashboard-cards {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .product-panel .panel-title {
            align-items: flex-start;
            flex-direction: column;
        }
    }

    @media (max-width: 560px) {
        .menu-grid {
            grid-template-columns: 1fr;
        }

        .product-panel {
            padding: 18px;
        }

        .product-hero {
            min-height: auto;
            gap: 18px;
            padding: 20px;
            border-radius: 22px;
        }

        .product-hero::before {
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

        .dashboard-cards {
            grid-template-columns: 1fr;
        }

        .dashboard-card {
            min-height: auto;
            padding: 18px;
        }

        .modal-actions {
            flex-direction: column;
            padding: 14px 18px 18px;
        }

        .modal-actions .btn {
            width: 100%;
        }

        .camera-actions {
            flex-direction: column;
        }

        .camera-actions .btn {
            width: 100%;
        }

        .modal {
            padding: 12px;
        }

        .modal-header {
            grid-template-columns: minmax(0, 1fr) auto;
            padding: 18px;
        }

        .modal-icon {
            display: none;
        }

        .modal-form {
            grid-template-columns: 1fr;
            padding: 18px 18px 8px;
        }

        .modal-header h2 {
            font-size: 24px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let selectedProductId = null;
    let activeProductFilter = 'Semua';
    let productCameraStream = null;

    function getFilteredProducts() {
        const products = getProducts();
        return activeProductFilter === 'Semua'
            ? products
            : products.filter((product) => product.category === activeProductFilter);
    }

    function renderProductCards() {
        const products = getFilteredProducts();
        const container = document.getElementById('productCards');

        container.innerHTML = products.length
            ? products.map((product) => `
            <article class="product-card">
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
                        <button class="btn secondary" type="button" onclick="openEditProductModal(${product.id})">Edit</button>
                        <button class="btn warn" type="button" onclick="deleteProduct(${product.id})">Hapus</button>
                    </div>
                </div>
            </article>
            `).join('')
            : '<div class="empty">Belum ada produk tersedia.</div>';
    }

    function setProductFilter(filter) {
        activeProductFilter = filter;
        document.querySelectorAll('.filter-row .pill').forEach((button) => {
            button.classList.toggle('active', button.dataset.filter === filter);
        });
        renderProductCards();
    }

    function openProductModal() {
        selectedProductId = null;
        document.getElementById('productModalTitle').textContent = 'Tambah Produk';
        document.getElementById('productModalSubtitle').textContent = 'Isi data produk baru atau edit produk yang tersedia.';
        document.getElementById('saveProductButton').textContent = 'Simpan Produk';
        resetProductForm();
        showProductModal();
    }

    function openEditProductModal(id) {
        const product = getProducts().find((item) => item.id === id);
        if (!product) return;
        selectedProductId = id;
        document.getElementById('productModalTitle').textContent = 'Edit Produk';
        document.getElementById('productModalSubtitle').textContent = `Ubah detail "${product.name}"`;
        document.getElementById('saveProductButton').textContent = 'Perbarui Produk';
        document.getElementById('editProductName').value = product.name;
        document.getElementById('editProductImage').value = product.image;
        document.getElementById('editProductCategory').value = product.category;
        document.getElementById('editProductStock').value = product.stock;
        document.getElementById('editProductPrice').value = product.price;
        document.getElementById('editProductDescription').value = product.description;
        updateProductImagePreview();
        showProductModal();
    }

    function showProductModal() {
        document.getElementById('productModal').classList.remove('hidden');
    }

    function closeProductModal(event) {
        if (event && event.target !== document.getElementById('productModal')) return;
        document.getElementById('productModal').classList.add('hidden');
    }

    function resetProductForm() {
        selectedProductId = null;
        document.getElementById('editProductName').value = '';
        document.getElementById('editProductImage').value = '';
        document.getElementById('editProductCategory').value = 'Minuman';
        document.getElementById('editProductStock').value = 0;
        document.getElementById('editProductPrice').value = 0;
        document.getElementById('editProductDescription').value = '';
        document.getElementById('saveProductButton').textContent = 'Simpan Produk';
        updateProductImagePreview();
    }

    function updateProductImagePreview() {
        const preview = document.getElementById('productImagePreview');
        const image = document.getElementById('editProductImage').value.trim();
        if (!preview) return;

        if (!image) {
            preview.classList.remove('has-image');
            preview.innerHTML = '<span>Preview gambar akan tampil setelah URL diisi.</span>';
            return;
        }

        preview.classList.add('has-image');
        preview.innerHTML = `<img src="${image}" alt="Preview produk" onerror="this.parentElement.classList.remove('has-image'); this.parentElement.innerHTML='<span>URL gambar tidak bisa dimuat.</span>'">`;
    }

    function useProductImageFile(input) {
        const file = input.files?.[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            notify('File harus berupa gambar.');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = () => {
            document.getElementById('editProductImage').value = reader.result;
            updateProductImagePreview();
            input.value = '';
        };
        reader.onerror = () => {
            notify('Gambar gagal dibaca.');
            input.value = '';
        };
        reader.readAsDataURL(file);
    }

    async function openCameraCapture() {
        const modal = document.getElementById('cameraModal');
        const video = document.getElementById('productCameraVideo');
        const message = document.getElementById('cameraMessage');

        if (!navigator.mediaDevices?.getUserMedia) {
            notify('Browser tidak mendukung akses kamera.');
            return;
        }

        modal.classList.remove('hidden');
        message.textContent = 'Meminta akses kamera...';
        message.classList.remove('hidden');

        try {
            productCameraStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'environment' },
                    width: { ideal: 1280 },
                    height: { ideal: 960 }
                },
                audio: false
            });

            video.srcObject = productCameraStream;
            await video.play();
            message.classList.add('hidden');
        } catch (error) {
            message.textContent = 'Kamera tidak bisa diakses. Izinkan akses kamera di browser atau gunakan perangkat yang memiliki kamera.';
            notify('Akses kamera gagal.');
        }
    }

    function stopProductCamera() {
        if (productCameraStream) {
            productCameraStream.getTracks().forEach((track) => track.stop());
            productCameraStream = null;
        }

        const video = document.getElementById('productCameraVideo');
        if (video) {
            video.pause();
            video.srcObject = null;
        }
    }

    function closeCameraCapture(event) {
        if (event && event.target !== document.getElementById('cameraModal')) return;
        stopProductCamera();
        document.getElementById('cameraModal').classList.add('hidden');
    }

    function captureProductPhoto() {
        const video = document.getElementById('productCameraVideo');
        if (!video || !video.videoWidth) {
            notify('Kamera belum siap.');
            return;
        }

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        document.getElementById('editProductImage').value = canvas.toDataURL('image/jpeg', 0.88);
        updateProductImagePreview();
        closeCameraCapture();
        notify('Foto produk berhasil diambil.');
    }

    async function saveProduct() {
        const name = document.getElementById('editProductName').value.trim();
        const image = document.getElementById('editProductImage').value.trim();
        const category = document.getElementById('editProductCategory').value;
        const stock = Number(document.getElementById('editProductStock').value || 0);
        const price = Number(document.getElementById('editProductPrice').value || 0);
        const description = document.getElementById('editProductDescription').value.trim();

        if (!name) return notify('Nama produk harus diisi.');
        if (!image) return notify('URL gambar harus diisi.');
        if (stock < 0 || price < 0) return notify('Stok dan harga tidak boleh negatif.');

        const products = getProducts();
        const payload = { name, image, category, stock, price, description };

        if (selectedProductId) {
            const previous = products.find((product) => product.id === selectedProductId);
            let savedProduct;
            let activity;
            try {
                ({ product: savedProduct, activity } = await wanaRequest(`/dapur/produk/${selectedProductId}`, {
                    method: 'PUT',
                    body: JSON.stringify(payload)
                }));
            } catch (error) {
                notify(error.message);
                return;
            }

            const updated = products.map((product) => product.id === selectedProductId ? savedProduct : product);
            setProducts(updated);
            if (activity) setKitchenHistory([activity, ...getKitchenHistory()].slice(0, 120));
            notify('Produk berhasil diperbarui.');
        } else {
            let savedProduct;
            let activity;
            try {
                ({ product: savedProduct, activity } = await wanaRequest('/dapur/produk', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                }));
            } catch (error) {
                notify(error.message);
                return;
            }

            setProducts([savedProduct, ...products]);
            if (activity) setKitchenHistory([activity, ...getKitchenHistory()].slice(0, 120));
            notify('Produk baru berhasil ditambahkan.');
        }

        closeProductModal();
        resetProductForm();
        renderProductCards();
    }

    async function deleteProduct(id) {
        const product = getProducts().find((item) => item.id === id);
        if (!product) return;

        if (!confirm(`Hapus produk "${product.name}" dari menu kasir?`)) {
            return;
        }

        try {
            const response = await wanaRequest(`/dapur/produk/${id}`, { method: 'DELETE' });
            if (response.activity) setKitchenHistory([response.activity, ...getKitchenHistory()].slice(0, 120));
        } catch (error) {
            notify(error.message);
            return;
        }

        setProducts(getProducts().filter((item) => item.id !== id));
        notify('Produk berhasil dihapus dari menu kasir.');
        renderProductCards();
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('editProductImage')?.addEventListener('input', updateProductImagePreview);
        document.getElementById('productImageFile')?.addEventListener('change', (event) => useProductImageFile(event.target));
        renderProductCards();
    });
</script>
@endpush
