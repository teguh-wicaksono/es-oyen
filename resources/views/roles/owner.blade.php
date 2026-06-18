<?php $__env->startSection('content'); ?>
<div class="owner-dashboard">
    <section class="owner-hero">
        <div class="owner-hero-copy">
            <div class="eyebrow">Owner Command Center</div>
            <h1>Ringkasan Bisnis Wana Cafe</h1>
            <p class="lead">Pantau omzet, performa menu, stok kritis, status order, dan komunikasi tim dalam satu layar yang siap dipakai mengambil keputusan.</p>
            <div class="owner-hero-actions">
                <a class="btn owner-primary" href="<?php echo e(route('owner.penjualan')); ?>">Lihat Penjualan</a>
                <a class="btn owner-secondary" href="<?php echo e(route('owner.export')); ?>">Export Laporan</a>
                <button class="btn owner-ghost" type="button" onclick="refreshOwnerDashboardFromDatabase()">Refresh</button>
            </div>
        </div>

        <div class="owner-hero-visual" aria-hidden="true">
            <div class="owner-console">
                <div class="console-photo">
                    <img src="https://images.unsplash.com/photo-1559925393-8be0ec4767c8?auto=format&fit=crop&w=900&q=85" alt="">
                </div>
                <div class="console-card sales">
                    <span>Omzet Hari Ini</span>
                    <strong id="heroSales">Rp 0</strong>
                </div>
                <div class="console-card active">
                    <span>Order Aktif</span>
                    <strong id="heroActive">0 order</strong>
                </div>
                <div class="console-chart">
                    <i style="height:42%"></i>
                    <i style="height:68%"></i>
                    <i style="height:54%"></i>
                    <i style="height:88%"></i>
                    <i style="height:72%"></i>
                </div>
            </div>
        </div>
    </section>

    <section class="owner-metrics">
        <article class="owner-metric metric-sales" data-metric="sales">
            <span>Total Penjualan</span>
            <strong id="salesMetric">Rp 0</strong>
            <p>Akumulasi nilai transaksi yang masuk.</p>
        </article>
        <article class="owner-metric metric-orders" data-metric="orders">
            <span>Total Transaksi</span>
            <strong id="orderMetric">0</strong>
            <p>Jumlah order dari kasir.</p>
        </article>
        <article class="owner-metric metric-products" data-metric="products">
            <span>Produk Aktif</span>
            <strong id="productMetric">0</strong>
            <p>Menu yang tersedia untuk dijual.</p>
        </article>
        <article class="owner-metric metric-alert" data-metric="stock">
            <span>Stok Rendah</span>
            <strong id="lowStockMetric">0</strong>
            <p>Item yang perlu dipantau segera.</p>
        </article>
    </section>

    <section class="owner-grid">
        <div class="owner-main">
            <div class="owner-panel performance-panel">
                <div class="panel-title">
                    <div>
                        <h2>Performa Operasional</h2>
                        <span class="panel-subtitle">Komposisi order dan kesehatan stok hari ini.</span>
                    </div>
                    <button class="btn ghost compact-btn" type="button" onclick="refreshOwnerDashboardFromDatabase()">Refresh Data</button>
                </div>
                <div class="performance-layout">
                    <div class="owner-ring">
                        <svg viewBox="0 0 120 120" aria-hidden="true">
                            <circle cx="60" cy="60" r="48"></circle>
                            <circle id="completionRing" cx="60" cy="60" r="48"></circle>
                        </svg>
                        <div>
                            <strong id="completionMetric">0%</strong>
                            <span>Order selesai</span>
                        </div>
                    </div>
                    <div class="owner-progress-list">
                        <div class="progress-row">
                            <div><span>Masuk</span><strong id="incomingCount">0</strong></div>
                            <div class="progress-track"><i id="incomingBar"></i></div>
                        </div>
                        <div class="progress-row">
                            <div><span>Diproses</span><strong id="processCount">0</strong></div>
                            <div class="progress-track"><i id="processBar"></i></div>
                        </div>
                        <div class="progress-row">
                            <div><span>Selesai</span><strong id="doneCount">0</strong></div>
                            <div class="progress-track"><i id="doneBar"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="owner-panel">
                <div class="panel-title owner-filter-head">
                    <div>
                        <h2>Laporan Penjualan</h2>
                        <span class="panel-subtitle">Klik filter untuk melihat status order tertentu.</span>
                    </div>
                    <div class="owner-segment" role="group" aria-label="Filter laporan penjualan">
                        <button type="button" class="active" data-order-filter="all">Semua</button>
                        <button type="button" data-order-filter="Masuk">Masuk</button>
                        <button type="button" data-order-filter="Diproses">Proses</button>
                        <button type="button" data-order-filter="Selesai">Selesai</button>
                    </div>
                </div>
                <div id="salesRows" class="owner-order-list"></div>
            </div>

            <div class="owner-panel stock-panel">
                <div class="panel-title">
                    <div>
                        <h2>Laporan Stok</h2>
                        <span class="panel-subtitle">Nilai stok dihitung dari harga produk dan jumlah tersedia.</span>
                    </div>
                    <a class="btn ghost compact-btn" href="<?php echo e(route('owner.stok')); ?>">Detail Stok</a>
                </div>
                <div id="stockRows" class="owner-stock-grid"></div>
            </div>
        </div>

        <aside class="owner-side">
            <div class="owner-panel quick-panel">
                <div class="panel-title">
                    <h2>Aksi Cepat</h2>
                    <span class="badge">Owner</span>
                </div>
                <div class="quick-actions">
                    <a href="<?php echo e(route('owner.penjualan')); ?>">
                        <span>Penjualan</span>
                        <strong>Laporan omzet</strong>
                    </a>
                    <a href="<?php echo e(route('owner.stok')); ?>">
                        <span>Stok</span>
                        <strong>Kontrol bahan</strong>
                    </a>
                    <a href="<?php echo e(route('owner.karyawan')); ?>">
                        <span>Karyawan</span>
                        <strong>Kelola tim</strong>
                    </a>
                    <a href="<?php echo e(route('owner.chat')); ?>">
                        <span>Chat</span>
                        <strong>Koordinasi tim</strong>
                    </a>
                </div>
            </div>

            <div class="owner-panel">
                <div class="panel-title">
                    <h2>Menu Bernilai</h2>
                    <span class="badge">Top 5</span>
                </div>
                <div id="topProductRows" class="top-products"></div>
            </div>

            <div class="owner-panel">
                <div class="panel-title">
                    <h2>Insight Hari Ini</h2>
                    <span class="badge">Live</span>
                </div>
                <div id="insightList" class="owner-insights"></div>
            </div>

            <div class="owner-panel owner-chat-panel">
                <div class="panel-title">
                    <div>
                        <h2>Pengaduan Kasir</h2>
                        <span class="panel-subtitle">Balasan owner akan masuk ke chat kasir.</span>
                    </div>
                    <span class="badge">Chat</span>
                </div>
                <div id="ownerChat" class="owner-chat-list"></div>
                <div class="field">
                    <label for="ownerReply">Balasan owner</label>
                    <textarea id="ownerReply" placeholder="Tulis balasan untuk kasir..."></textarea>
                </div>
                <button class="btn owner-primary" type="button" onclick="sendOwnerReply()">Kirim Balasan</button>
            </div>
        </aside>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .owner-dashboard {
        width: min(100%, 1360px);
        margin: 0 auto;
        display: grid;
        gap: 20px;
    }

    .owner-hero {
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
            linear-gradient(110deg, rgba(43, 22, 11, .94) 0%, rgba(65, 38, 23, .86) 46%, rgba(143, 99, 61, .48) 100%),
            url('https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=1600&q=85') center/cover;
        box-shadow: 0 34px 80px rgba(49, 29, 15, .12);
    }

    .owner-hero::before {
        content: "";
        position: absolute;
        inset: 18px;
        border: 1px solid rgba(255, 255, 255, .62);
        border-radius: 24px;
        pointer-events: none;
    }

    .owner-hero-copy,
    .owner-hero-visual {
        position: relative;
        z-index: 1;
    }

    .owner-hero-copy h1 {
        max-width: 760px;
        margin-top: 8px;
        color: #fff8ed;
        font-size: clamp(42px, 5vw, 72px);
        line-height: .98;
    }

    .owner-hero-copy .lead {
        max-width: 720px;
        margin-top: 16px;
        color: rgba(255, 248, 237, .84);
        font-size: 16px;
        font-weight: 750;
    }

    .owner-hero-copy .eyebrow {
        color: #f0d7a7;
    }

    .owner-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 26px;
    }

    .owner-hero-actions .btn,
    .compact-btn {
        width: auto;
        min-height: 46px;
        border-radius: 999px;
    }

    .owner-primary {
        background: var(--coffee);
        color: #fff8ed;
        box-shadow: 0 16px 34px rgba(43, 22, 11, .16);
    }

    .owner-secondary,
    .owner-ghost {
        color: var(--coffee);
        border: 1px solid rgba(83, 58, 38, .13);
        background: rgba(255, 250, 242, .88);
    }

    .owner-ghost {
        background: rgba(255, 255, 255, .66);
    }

    .owner-hero-visual {
        min-height: 276px;
        display: grid;
        place-items: center;
    }

    .owner-console {
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

    .console-photo {
        height: 238px;
        overflow: hidden;
        border-radius: 22px;
        border: 1px solid rgba(255, 248, 237, .44);
    }

    .console-photo img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .console-card {
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

    .console-card.sales {
        top: 28px;
        right: -18px;
    }

    .console-card.active {
        left: -18px;
        bottom: 32px;
    }

    .console-card span {
        color: var(--leaf);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .console-card strong {
        font-size: 20px;
    }

    .console-chart {
        position: absolute;
        right: 22px;
        bottom: 22px;
        display: flex;
        align-items: end;
        gap: 7px;
        width: 112px;
        height: 64px;
        padding: 10px;
        border-radius: 16px;
        background: rgba(43, 22, 11, .76);
        box-shadow: 0 16px 34px rgba(20, 10, 5, .22);
    }

    .console-chart i {
        flex: 1;
        min-width: 0;
        border-radius: 999px;
        background: linear-gradient(180deg, #f3d7a7, #c8844f);
    }

    .owner-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .owner-metric,
    .owner-panel {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(83, 58, 38, .1);
        border-radius: 22px;
        background: rgba(255, 253, 249, .88);
        box-shadow: 0 24px 58px rgba(49, 29, 15, .08);
    }

    .owner-metric {
        min-height: 144px;
        padding: 20px;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .owner-metric:hover {
        transform: translateY(-4px);
        box-shadow: 0 30px 70px rgba(49, 29, 15, .13);
    }

    .owner-metric::after {
        content: "";
        position: absolute;
        right: -34px;
        bottom: -42px;
        width: 128px;
        height: 128px;
        border-radius: 999px;
        background: rgba(100, 122, 84, .12);
    }

    .metric-sales::after { background: rgba(198, 122, 64, .14); }
    .metric-orders::after { background: rgba(100, 122, 84, .14); }
    .metric-products::after { background: rgba(227, 180, 103, .18); }
    .metric-alert::after { background: rgba(176, 80, 82, .14); }

    .owner-metric span,
    .panel-subtitle {
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .owner-metric strong {
        position: relative;
        z-index: 1;
        display: block;
        margin-top: 14px;
        font-size: clamp(28px, 3vw, 40px);
        line-height: 1;
    }

    .owner-metric p {
        position: relative;
        z-index: 1;
        margin-top: 12px;
        color: var(--leaf);
        font-weight: 700;
    }

    .owner-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.42fr) minmax(330px, .58fr);
        gap: 20px;
        align-items: start;
    }

    .owner-main,
    .owner-side {
        display: grid;
        gap: 18px;
    }

    .owner-panel {
        padding: 18px;
    }

    .owner-panel .badge {
        flex: 0 0 auto;
        min-width: max-content;
        min-height: 32px;
        padding: 7px 14px;
        white-space: nowrap;
        line-height: 1;
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .01em;
    }

    .owner-filter-head {
        gap: 14px;
        align-items: flex-start;
    }

    .owner-segment {
        display: flex;
        gap: 4px;
        padding: 5px;
        border: 1px solid rgba(83, 58, 38, .1);
        border-radius: 999px;
        background: rgba(245, 234, 219, .72);
    }

    .owner-segment button {
        min-height: 38px;
        padding: 0 14px;
        border: 0;
        border-radius: 999px;
        color: var(--muted);
        background: transparent;
        font-weight: 900;
        cursor: pointer;
    }

    .owner-segment button.active {
        color: #fff8ed;
        background: var(--coffee);
        box-shadow: 0 12px 24px rgba(49, 29, 15, .13);
    }

    .performance-layout {
        display: grid;
        grid-template-columns: 190px minmax(0, 1fr);
        gap: 20px;
        align-items: center;
        margin-top: 14px;
    }

    .owner-ring {
        position: relative;
        display: grid;
        place-items: center;
        min-height: 190px;
    }

    .owner-ring svg {
        width: 166px;
        height: 166px;
        transform: rotate(-90deg);
    }

    .owner-ring circle {
        fill: none;
        stroke-width: 12;
        stroke: rgba(83, 58, 38, .1);
    }

    .owner-ring #completionRing {
        stroke: #647a54;
        stroke-linecap: round;
        stroke-dasharray: 301.59;
        stroke-dashoffset: 301.59;
        transition: stroke-dashoffset .35s ease;
    }

    .owner-ring div {
        position: absolute;
        display: grid;
        gap: 6px;
        text-align: center;
    }

    .owner-ring strong {
        font-size: 38px;
        line-height: 1;
    }

    .owner-ring span {
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .owner-progress-list {
        display: grid;
        gap: 16px;
    }

    .progress-row {
        display: grid;
        gap: 8px;
    }

    .progress-row > div:first-child {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        font-weight: 900;
    }

    .progress-row span {
        color: var(--muted);
        font-size: 13px;
    }

    .progress-track {
        height: 11px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(83, 58, 38, .1);
    }

    .progress-track i {
        display: block;
        width: 0;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #647a54, #c8844f);
        transition: width .3s ease;
    }

    .owner-order-list {
        display: grid;
        gap: 10px;
        margin-top: 16px;
        max-height: 390px;
        overflow: auto;
        padding-right: 5px;
    }

    .owner-order-card {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 14px;
        align-items: center;
        padding: 14px 16px;
        border: 1px solid rgba(83, 58, 38, .09);
        border-radius: 16px;
        background: rgba(255, 250, 242, .72);
    }

    .owner-order-card strong {
        display: block;
        font-size: 16px;
    }

    .owner-order-card p {
        margin-top: 4px;
        color: var(--muted);
        font-weight: 700;
    }

    .owner-order-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .owner-pill {
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

    .owner-order-total {
        text-align: right;
    }

    .owner-order-total b {
        display: block;
        font-size: 18px;
    }

    .quick-actions,
    .top-products,
    .owner-insights,
    .owner-chat-list,
    .owner-stock-grid {
        display: grid;
        gap: 12px;
        margin-top: 16px;
    }

    .owner-order-list,
    .top-products,
    .owner-insights,
    .owner-chat-list,
    .owner-stock-grid {
        scrollbar-width: thin;
        scrollbar-color: rgba(83, 58, 38, .38) rgba(245, 234, 219, .64);
    }

    .owner-order-list::-webkit-scrollbar,
    .top-products::-webkit-scrollbar,
    .owner-insights::-webkit-scrollbar,
    .owner-chat-list::-webkit-scrollbar,
    .owner-stock-grid::-webkit-scrollbar {
        width: 8px;
    }

    .owner-order-list::-webkit-scrollbar-track,
    .top-products::-webkit-scrollbar-track,
    .owner-insights::-webkit-scrollbar-track,
    .owner-chat-list::-webkit-scrollbar-track,
    .owner-stock-grid::-webkit-scrollbar-track {
        border-radius: 999px;
        background: rgba(245, 234, 219, .64);
    }

    .owner-order-list::-webkit-scrollbar-thumb,
    .top-products::-webkit-scrollbar-thumb,
    .owner-insights::-webkit-scrollbar-thumb,
    .owner-chat-list::-webkit-scrollbar-thumb,
    .owner-stock-grid::-webkit-scrollbar-thumb {
        border: 2px solid rgba(245, 234, 219, .64);
        border-radius: 999px;
        background: rgba(83, 58, 38, .5);
    }

    .quick-actions a,
    .top-product-item,
    .owner-insight,
    .owner-chat-item,
    .owner-stock-card {
        border: 1px solid rgba(83, 58, 38, .09);
        border-radius: 16px;
        background: rgba(255, 250, 242, .74);
    }

    .quick-actions {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .quick-actions a {
        display: grid;
        gap: 6px;
        min-height: 86px;
        padding: 14px;
        color: inherit;
        text-decoration: none;
        transition: transform .18s ease, border-color .18s ease;
    }

    .quick-actions a:hover {
        transform: translateY(-3px);
        border-color: rgba(83, 58, 38, .2);
    }

    .quick-actions span {
        color: var(--leaf);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .quick-actions strong {
        font-size: 15px;
    }

    .top-product-item,
    .owner-insight,
    .owner-chat-item {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 12px;
        align-items: center;
        padding: 14px;
    }

    .top-products {
        max-height: 360px;
        overflow: auto;
        padding-right: 5px;
    }

    .owner-insights {
        max-height: 260px;
        overflow: auto;
        padding-right: 5px;
    }

    .top-product-item strong,
    .owner-insight strong,
    .owner-chat-item strong {
        display: block;
        font-size: 14px;
    }

    .top-product-item span,
    .owner-insight span,
    .owner-chat-item span {
        color: var(--muted);
        font-size: 12px;
        font-weight: 750;
    }

    .top-product-bar {
        grid-column: 1 / -1;
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: rgba(83, 58, 38, .1);
    }

    .top-product-bar i {
        display: block;
        width: var(--bar, 0%);
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #c8844f, #647a54);
    }

    .owner-stock-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        max-height: 390px;
        overflow: auto;
        padding-right: 4px;
    }

    .owner-stock-card {
        padding: 14px;
    }

    .owner-stock-card header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .owner-stock-card h3 {
        margin: 0;
        font-size: 17px;
    }

    .owner-stock-card p {
        margin-top: 6px;
        color: var(--muted);
        font-weight: 700;
    }

    .stock-value {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 16px;
        color: var(--leaf);
        font-weight: 900;
    }

    .owner-chat-panel textarea {
        min-height: 94px;
    }

    .owner-chat-list {
        max-height: 238px;
        overflow: auto;
        padding-right: 5px;
    }

    .owner-empty {
        padding: 18px;
        border: 1px dashed rgba(83, 58, 38, .18);
        border-radius: 16px;
        color: var(--muted);
        background: rgba(255, 250, 242, .62);
        text-align: center;
        font-weight: 800;
    }

    @media (max-width: 1100px) {
        .owner-hero,
        .owner-grid {
            grid-template-columns: 1fr;
        }

        .owner-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (min-width: 1600px) {
        .owner-dashboard {
            width: min(100%, 1320px);
        }

        .owner-grid {
            grid-template-columns: minmax(0, 1.38fr) 350px;
        }

    }

    @media (max-width: 760px) {
        .owner-hero {
            min-height: auto;
            padding: 24px;
            border-radius: 24px;
        }

        .owner-hero::before {
            inset: 12px;
            border-radius: 18px;
        }

        .owner-hero-copy h1 {
            font-size: clamp(36px, 13vw, 54px);
        }

        .owner-hero-actions .btn,
        .compact-btn {
            width: 100%;
        }

        .owner-hero-visual {
            min-height: 240px;
        }

        .owner-console {
            width: min(340px, 100%);
            min-height: 250px;
        }

        .console-photo {
            height: 220px;
        }

        .console-card {
            min-width: 142px;
            padding: 12px;
        }

        .console-card.sales {
            right: 4px;
        }

        .console-card.active {
            left: 4px;
            bottom: 22px;
        }

        .console-chart {
            right: 12px;
            bottom: 12px;
        }

        .owner-metrics,
        .performance-layout,
        .owner-stock-grid,
        .quick-actions {
            grid-template-columns: 1fr;
        }

        .owner-panel,
        .owner-metric {
            border-radius: 18px;
            padding: 18px;
        }

        .owner-filter-head,
        .owner-order-card,
        .top-product-item,
        .owner-insight,
        .owner-chat-item {
            grid-template-columns: 1fr;
        }

        .owner-segment {
            width: 100%;
            overflow-x: auto;
        }

        .owner-segment button {
            flex: 1 0 auto;
        }

        .owner-order-total {
            text-align: left;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let ownerOrderFilter = 'all';

    function ownerEscape(value) {
        return String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[char]));
    }

    function ownerOrderItems(order) {
        const items = Array.isArray(order.items) ? order.items : [];
        return items.length
            ? items.map((item) => `${ownerEscape(item.qty || 1)}x ${ownerEscape(item.name)}`).join(', ')
            : 'Belum ada detail item.';
    }

    function ownerStatusCount(orders, status) {
        return orders.filter((order) => order.status === status).length;
    }

    function setOwnerProgress(id, value, total) {
        const bar = document.getElementById(id);
        if (!bar) return;
        const percent = total > 0 ? Math.round((value / total) * 100) : 0;
        bar.style.width = `${Math.min(percent, 100)}%`;
    }

    function renderOwnerDashboard() {
        const orders = getOrders();
        const products = getProducts();
        const complaints = getComplaints();
        const totalSales = orders.reduce((sum, order) => sum + (Number(order.total) || 0), 0);
        const lowStock = products.filter((product) => Number(product.stock) <= 10);
        const incoming = ownerStatusCount(orders, 'Masuk');
        const processing = ownerStatusCount(orders, 'Diproses');
        const done = ownerStatusCount(orders, 'Selesai');
        const active = orders.length - done;
        const completion = orders.length ? Math.round((done / orders.length) * 100) : 0;
        const filteredOrders = ownerOrderFilter === 'all'
            ? orders
            : orders.filter((order) => order.status === ownerOrderFilter);

        document.getElementById('salesMetric').textContent = rupiah(totalSales);
        document.getElementById('orderMetric').textContent = orders.length;
        document.getElementById('productMetric').textContent = products.length;
        document.getElementById('lowStockMetric').textContent = lowStock.length;
        document.getElementById('heroSales').textContent = rupiah(totalSales);
        document.getElementById('heroActive').textContent = `${active} order`;
        document.getElementById('incomingCount').textContent = incoming;
        document.getElementById('processCount').textContent = processing;
        document.getElementById('doneCount').textContent = done;
        document.getElementById('completionMetric').textContent = `${completion}%`;

        const ring = document.getElementById('completionRing');
        if (ring) {
            const circumference = 301.59;
            ring.style.strokeDashoffset = circumference - (circumference * completion / 100);
        }

        setOwnerProgress('incomingBar', incoming, orders.length);
        setOwnerProgress('processBar', processing, orders.length);
        setOwnerProgress('doneBar', done, orders.length);

        document.getElementById('salesRows').innerHTML = filteredOrders.length
            ? filteredOrders.map((order) => `
                <article class="owner-order-card">
                    <div>
                        <strong>${ownerEscape(order.id)} - ${ownerEscape(order.customer || 'Pelanggan')}</strong>
                        <p>${ownerOrderItems(order)}</p>
                        <div class="owner-order-meta">
                            <span class="owner-pill">${ownerEscape(order.status || 'Masuk')}</span>
                            <span class="owner-pill">${ownerEscape(order.table || 'Meja -')}</span>
                            <span class="owner-pill">${ownerEscape(order.createdAt || order.time || 'Hari ini')}</span>
                        </div>
                    </div>
                    <div class="owner-order-total">
                        <b>${rupiah(order.total || 0)}</b>
                        <span class="owner-pill">${ownerEscape(order.cashier || 'Kasir')}</span>
                    </div>
                </article>
            `).join('')
            : '<div class="owner-empty">Belum ada transaksi pada filter ini.</div>';

        const sortedProducts = [...products].sort((a, b) => (Number(b.stock) * Number(b.price)) - (Number(a.stock) * Number(a.price)));
        const topMax = Math.max(...sortedProducts.map((product) => Number(product.stock) * Number(product.price)), 1);
        document.getElementById('topProductRows').innerHTML = sortedProducts.slice(0, 5).map((product) => {
            const value = Number(product.stock) * Number(product.price);
            const width = Math.max(8, Math.round((value / topMax) * 100));
            return `
                <div class="top-product-item">
                    <div>
                        <strong>${ownerEscape(product.name)}</strong>
                        <span>${ownerEscape(product.category)} - ${ownerEscape(product.stock)} stok</span>
                    </div>
                    <b>${rupiah(value)}</b>
                    <div class="top-product-bar"><i style="--bar:${width}%"></i></div>
                </div>
            `;
        }).join('') || '<div class="owner-empty">Belum ada produk aktif.</div>';

        document.getElementById('stockRows').innerHTML = products.map((product) => {
            const stock = Number(product.stock) || 0;
            const status = stock <= 10 ? 'Restock' : 'Aman';
            return `
                <article class="owner-stock-card">
                    <header>
                        <div>
                            <h3>${ownerEscape(product.name)}</h3>
                            <p>${ownerEscape(product.category)}</p>
                        </div>
                        <span class="owner-pill">${status}</span>
                    </header>
                    <div class="stock-value">
                        <span>${stock} stok</span>
                        <strong>${rupiah(stock * (Number(product.price) || 0))}</strong>
                    </div>
                </article>
            `;
        }).join('') || '<div class="owner-empty">Belum ada data stok.</div>';

        const ownerChats = complaints.filter((chat) => chat.sender === 'Owner' || chat.recipient === 'Owner');
        document.getElementById('ownerChat').innerHTML = ownerChats.map((chat) => `
            <div class="owner-chat-item">
                <div>
                    <strong>${ownerEscape(chat.sender)} ke ${ownerEscape(chat.recipient)}</strong>
                    <span>${ownerEscape(chat.message)}</span>
                </div>
                <span>${ownerEscape(chat.time)}</span>
            </div>
        `).join('') || '<div class="owner-empty">Belum ada pesan untuk Owner.</div>';

        const bestOrder = [...orders].sort((a, b) => (Number(b.total) || 0) - (Number(a.total) || 0))[0];
        const bestProduct = sortedProducts[0];
        document.getElementById('insightList').innerHTML = `
            <div class="owner-insight">
                <div>
                    <strong>Produk perlu perhatian</strong>
                    <span>${lowStock.length ? lowStock.map((product) => ownerEscape(product.name)).join(', ') : 'Semua stok aman.'}</span>
                </div>
                <b>${lowStock.length}</b>
            </div>
            <div class="owner-insight">
                <div>
                    <strong>Order terbesar</strong>
                    <span>${bestOrder ? `${ownerEscape(bestOrder.id)} - ${ownerEscape(bestOrder.customer || 'Pelanggan')}` : 'Belum ada order.'}</span>
                </div>
                <b>${bestOrder ? rupiah(bestOrder.total || 0) : '-'}</b>
            </div>
            <div class="owner-insight">
                <div>
                    <strong>Menu paling bernilai</strong>
                    <span>${bestProduct ? ownerEscape(bestProduct.name) : 'Belum ada produk.'}</span>
                </div>
                <b>${bestProduct ? rupiah((Number(bestProduct.stock) || 0) * (Number(bestProduct.price) || 0)) : '-'}</b>
            </div>
        `;
    }

    async function sendOwnerReply() {
        const input = document.getElementById('ownerReply');
        if (!input.value.trim()) {
            notify('Isi balasan owner dulu.');
            return;
        }

        try {
            await saveChatMessage('Kasir', input.value.trim());
        } catch (error) {
            notify(error.message);
            return;
        }
        input.value = '';
        renderOwnerDashboard();
        notify('Balasan owner terkirim ke kasir.');
    }

    async function refreshOwnerDashboardFromDatabase() {
        try {
            const payload = await wanaRequest('<?php echo e(route('owner.dashboard.feed')); ?>', { method: 'GET' });
            setProducts(payload.products || []);
            setOrders(payload.orders || []);
            setComplaints(payload.chats || []);
            if (typeof setMaterialsStore === 'function') {
                setMaterialsStore(payload.materials || []);
            }
            setKitchenHistory(payload.activities || []);
        } catch (error) {
            notify(error.message);
            return;
        }

        renderOwnerDashboard();
    }

    document.querySelectorAll('[data-order-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            ownerOrderFilter = button.dataset.orderFilter;
            document.querySelectorAll('[data-order-filter]').forEach((item) => item.classList.remove('active'));
            button.classList.add('active');
            renderOwnerDashboard();
        });
    });

    window.addEventListener('storage', renderOwnerDashboard);
    window.addEventListener('wana:storage', renderOwnerDashboard);
    refreshOwnerDashboardFromDatabase();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.wana', ['title' => 'Owner | Wana Cafe'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\wanacafe\resources\views/roles/owner.blade.php ENDPATH**/ ?>
