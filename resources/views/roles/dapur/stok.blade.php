@extends('layouts.wana', ['title' => 'Stok Bahan Dapur | Wana Cafe'])

@section('content')
    <section class="stock-hero">
        <div class="hero-copy">
            <div class="eyebrow">Dapur</div>
            <h1>Stok Bahan Mentah</h1>
            <p class="lead">Kelola bahan baku dapur, pantau stok kritis, dan catat pergerakan restock atau pemakaian dalam satu layar.</p>
            <div class="hero-actions">
                <button class="btn hero-action" type="button" onclick="openMaterialModal()">Tambah Bahan</button>
            </div>
        </div>

        <div class="hero-visual" aria-hidden="true">
            <div class="coffee-orbit">
                <img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1200&q=85" alt="">
            </div>
            <div class="floating-note top-note">
                <span>Total Bahan</span>
                <strong id="heroTotalMaterials">0 item</strong>
            </div>
            <div class="floating-note bottom-note">
                <span>Perlu Restock</span>
                <strong id="heroLowMaterials">0 item</strong>
            </div>
        </div>
    </section>

    <section class="stock-metrics" aria-label="Ringkasan stok bahan">
        <div class="stock-metric">
            <span>Total Bahan</span>
            <strong id="totalMaterials">0</strong>
        </div>
        <div class="stock-metric">
            <span>Stok Aman</span>
            <strong id="safeMaterials">0</strong>
        </div>
        <div class="stock-metric urgent">
            <span>Perlu Restock</span>
            <strong id="lowMaterials">0</strong>
        </div>
        <div class="stock-metric">
            <span>Kategori</span>
            <strong id="categoryCount">0</strong>
        </div>
    </section>

    <section class="stock-board">
        <div class="stock-toolbar">
            <div class="stock-search">
                <span class="search-icon" aria-hidden="true"></span>
                <input id="materialSearch" type="search" placeholder="Cari bahan, kategori, atau satuan..." oninput="renderMaterials()">
            </div>
            <div class="stock-tabs" aria-label="Filter stok">
                <button class="active" data-filter="Semua" type="button" onclick="setMaterialFilter('Semua')">Semua</button>
                <button data-filter="Aman" type="button" onclick="setMaterialFilter('Aman')">Aman</button>
                <button data-filter="Restock" type="button" onclick="setMaterialFilter('Restock')">Restock</button>
            </div>
        </div>

        <div id="materialList" class="material-grid"></div>
    </section>

    <div id="materialModal" class="material-modal hidden" onclick="closeMaterialModal(event)">
        <div class="material-dialog" onclick="event.stopPropagation()">
            <div class="modal-head">
                <div>
                    <div class="eyebrow">Bahan Mentah</div>
                    <h2 id="materialModalTitle">Tambah Bahan</h2>
                </div>
                <button class="icon-button" type="button" onclick="closeMaterialModal()" aria-label="Tutup">x</button>
            </div>

            <div class="field">
                <label for="materialName">Nama Bahan</label>
                <input id="materialName" type="text" placeholder="Contoh: Susu Full Cream">
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="materialCategory">Kategori</label>
                    <select id="materialCategory">
                        <option>Bahan Minuman</option>
                        <option>Bahan Makanan</option>
                        <option>Rempah & Bumbu</option>
                        <option>Kemasan</option>
                    </select>
                </div>
                <div class="field">
                    <label for="materialUnit">Satuan</label>
                    <input id="materialUnit" type="text" placeholder="kg, ltr, pcs">
                </div>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label for="materialQty">Jumlah Saat Ini</label>
                    <input id="materialQty" type="number" min="0" step="0.1" value="1">
                </div>
                <div class="field">
                    <label for="materialMin">Batas Minimum</label>
                    <input id="materialMin" type="number" min="0" step="0.1" value="5">
                </div>
            </div>

            <div class="field">
                <label for="materialNote">Catatan</label>
                <textarea id="materialNote" rows="3" placeholder="Contoh: supplier utama, kualitas, atau lokasi penyimpanan."></textarea>
            </div>

            <div class="modal-actions">
                <button class="btn secondary" type="button" onclick="resetMaterialForm()">Bersihkan</button>
                <button class="btn" id="saveMaterialButton" type="button" onclick="saveMaterial()">Simpan Bahan</button>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .stock-hero {
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
            url('https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1600&q=80') center/cover;
        box-shadow: 0 34px 80px rgba(49, 29, 15, .12);
    }

    .stock-hero::before {
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
        max-width: 780px;
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

    .stock-metrics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stock-metric {
        position: relative;
        min-height: 176px;
        overflow: hidden;
        padding: 24px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 24px;
        background: linear-gradient(180deg, #fffdf9, #f9f1e7);
        box-shadow: 0 22px 48px rgba(49, 29, 15, .08);
        display: grid;
        gap: 12px;
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .stock-metric::after {
        content: "";
        position: absolute;
        right: -24px;
        bottom: -28px;
        width: 112px;
        height: 112px;
        border-radius: 50%;
        background: rgba(200, 132, 79, .12);
    }

    .stock-metric:hover {
        transform: translateY(-3px);
        box-shadow: 0 28px 60px rgba(49, 29, 15, .12);
    }

    .stock-metric span {
        position: relative;
        z-index: 1;
        display: block;
        color: var(--muted);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .stock-metric strong {
        position: relative;
        z-index: 1;
        display: block;
        margin-top: 4px;
        color: var(--coffee);
        font-size: 34px;
        line-height: 1;
    }

    .stock-metric.urgent {
        background: linear-gradient(180deg, #fff8f6, #f8e5df);
    }

    .stock-metric.urgent::after {
        background: rgba(166, 79, 83, .12);
    }

    .stock-board {
        padding: 22px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 8px;
        background: rgba(255, 253, 249, .92);
        box-shadow: 0 22px 52px rgba(49, 29, 15, .08);
    }

    .stock-toolbar {
        display: grid;
        grid-template-columns: minmax(280px, 1fr) auto;
        gap: 14px;
        align-items: center;
        margin-bottom: 18px;
    }

    .stock-search {
        display: flex;
        align-items: center;
        gap: 10px;
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

    .stock-search input {
        width: 100%;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--ink);
    }

    .stock-tabs {
        display: inline-flex;
        gap: 6px;
        padding: 5px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 999px;
        background: #f6efe7;
    }

    .stock-tabs button {
        min-height: 38px;
        padding: 0 15px;
        border: 0;
        border-radius: 999px;
        background: transparent;
        color: var(--muted);
        font-weight: 900;
    }

    .stock-tabs button.active {
        color: #fff8ed;
        background: var(--coffee);
    }

    .material-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .material-card {
        display: grid;
        gap: 14px;
        padding: 16px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 12px 28px rgba(49, 29, 15, .05);
        transition: transform .18s ease, box-shadow .18s ease;
    }

    .material-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 42px rgba(49, 29, 15, .08);
    }

    .material-top {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
    }

    .material-top h3 {
        margin: 0;
        font-size: 18px;
        line-height: 1.25;
    }

    .material-category {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-size: 12px;
        font-weight: 800;
    }

    .status-chip {
        flex: 0 0 auto;
        padding: 7px 10px;
        border-radius: 999px;
        color: #fff8ed;
        background: var(--sage);
        font-size: 11px;
        font-weight: 900;
    }

    .status-chip.low {
        background: var(--berry);
    }

    .stock-number {
        display: flex;
        align-items: baseline;
        gap: 8px;
    }

    .stock-number strong {
        font-size: 34px;
        line-height: 1;
        color: var(--coffee);
    }

    .stock-number span {
        color: var(--muted);
        font-weight: 900;
    }

    .stock-progress {
        height: 9px;
        overflow: hidden;
        border-radius: 999px;
        background: #f0e4d7;
    }

    .stock-progress span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, var(--sage), #9dac72);
    }

    .stock-progress span.low {
        background: linear-gradient(90deg, var(--berry), #dc8a68);
    }

    .material-note {
        min-height: 38px;
        color: var(--muted);
        font-size: 13px;
        line-height: 1.45;
    }

    .quick-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .quick-row button,
    .card-actions button {
        min-height: 40px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 8px;
        background: #fff7ea;
        color: var(--coffee);
        font-weight: 900;
    }

    .card-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .card-actions .delete {
        color: #8a2f2f;
        background: #f7dfdc;
    }

    .material-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgba(24, 16, 10, .42);
        backdrop-filter: blur(6px);
    }

    .material-modal.hidden {
        display: none;
    }

    .material-dialog {
        width: min(660px, 100%);
        max-height: 92vh;
        overflow: auto;
        padding: 24px;
        border-radius: 8px;
        background: #fffdf9;
        box-shadow: 0 30px 90px rgba(24, 16, 10, .24);
    }

    .modal-head {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .modal-head h2 {
        margin-top: 6px;
        font-size: 28px;
    }

    .icon-button {
        width: 40px;
        height: 40px;
        border: 0;
        border-radius: 50%;
        background: #f2e7dc;
        color: var(--coffee);
        font-size: 28px;
        line-height: 1;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 12px;
    }

    @media (max-width: 1120px) {
        .stock-hero {
            grid-template-columns: 1fr;
        }

        .hero-visual {
            min-height: 240px;
        }

        .material-grid,
        .stock-metrics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .stock-hero,
        .stock-toolbar,
        .form-grid {
            grid-template-columns: 1fr;
        }

        .stock-hero {
            min-height: auto;
            gap: 18px;
            padding: 20px;
            border-radius: 22px;
        }

        .stock-hero::before {
            inset: 10px;
            border-radius: 18px;
        }

        .hero-copy h1 {
            font-size: clamp(34px, 12vw, 44px);
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

        .stock-hero .btn,
        .modal-actions .btn {
            width: 100%;
        }

        .stock-tabs {
            width: 100%;
            justify-content: space-between;
        }

        .material-grid,
        .stock-metrics {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let activeMaterialFilter = 'Semua';
    let editingMaterialId = null;

    function normalizeMaterial(item, index) {
        return {
            id: item.id || index + 1,
            name: item.name || 'Bahan',
            qty: Number(item.qty || 0),
            unit: item.unit || 'pcs',
            min: Number(item.min || 5),
            category: item.category || 'Bahan Minuman',
            note: item.note || ''
        };
    }

    function materials() {
        const dbMaterials = typeof getMaterials === 'function' ? getMaterials() : [];
        return dbMaterials.map(normalizeMaterial);
    }

    function setMaterials(items) {
        if (typeof setMaterialsStore === 'function') {
            setMaterialsStore(items);
            return;
        }

        store.set('wana_materials', items);
    }

    function isLow(item) {
        return Number(item.qty) <= Number(item.min);
    }

    function getFilteredMaterials() {
        const search = document.getElementById('materialSearch')?.value.trim().toLowerCase() || '';
        return materials().filter((item) => {
            const matchesFilter = activeMaterialFilter === 'Semua'
                || (activeMaterialFilter === 'Restock' && isLow(item))
                || (activeMaterialFilter === 'Aman' && !isLow(item));
            const matchesSearch = !search
                || item.name.toLowerCase().includes(search)
                || item.category.toLowerCase().includes(search)
                || item.unit.toLowerCase().includes(search);
            return matchesFilter && matchesSearch;
        });
    }

    function renderMetrics(items) {
        const low = items.filter(isLow).length;
        const categories = new Set(items.map((item) => item.category)).size;
        document.getElementById('totalMaterials').textContent = items.length;
        document.getElementById('safeMaterials').textContent = items.length - low;
        document.getElementById('lowMaterials').textContent = low;
        document.getElementById('categoryCount').textContent = categories;
        document.getElementById('heroTotalMaterials').textContent = `${items.length} item`;
        document.getElementById('heroLowMaterials').textContent = `${low} item`;
    }

    function renderMaterials() {
        const allItems = materials();
        const filtered = getFilteredMaterials();
        const list = document.getElementById('materialList');
        renderMetrics(allItems);

        list.innerHTML = filtered.length ? filtered.map((item) => {
            const low = isLow(item);
            const percentage = Math.max(6, Math.min(100, Math.round((Number(item.qty) / Math.max(Number(item.min) * 2, 1)) * 100)));
            return `
                <article class="material-card">
                    <div class="material-top">
                        <div>
                            <h3>${item.name}</h3>
                            <span class="material-category">${item.category}</span>
                        </div>
                        <span class="status-chip ${low ? 'low' : ''}">${low ? 'Restock' : 'Aman'}</span>
                    </div>
                    <div class="stock-number">
                        <strong>${Number(item.qty).toLocaleString('id-ID')}</strong>
                        <span>${item.unit}</span>
                    </div>
                    <div class="stock-progress" aria-hidden="true">
                        <span class="${low ? 'low' : ''}" style="width:${percentage}%"></span>
                    </div>
                    <p class="material-note">${item.note || `Batas minimum ${item.min} ${item.unit}.`}</p>
                    <div class="quick-row">
                        <button type="button" onclick="adjustMaterial(${item.id}, -1)">Pakai -1</button>
                        <button type="button" onclick="adjustMaterial(${item.id}, 1)">Restock +1</button>
                    </div>
                    <div class="card-actions">
                        <button type="button" onclick="openMaterialModal(${item.id})">Edit</button>
                        <button class="delete" type="button" onclick="deleteMaterial(${item.id})">Hapus</button>
                    </div>
                </article>
            `;
        }).join('') : '<div class="empty">Bahan tidak ditemukan. Coba ubah pencarian atau filter.</div>';
    }

    function setMaterialFilter(filter) {
        activeMaterialFilter = filter;
        document.querySelectorAll('.stock-tabs button').forEach((button) => {
            button.classList.toggle('active', button.dataset.filter === filter);
        });
        renderMaterials();
    }

    function openMaterialModal(id = null) {
        editingMaterialId = id;
        resetMaterialForm(false);

        if (id) {
            const item = materials().find((material) => material.id === id);
            if (!item) return;
            document.getElementById('materialModalTitle').textContent = 'Edit Bahan';
            document.getElementById('saveMaterialButton').textContent = 'Perbarui Bahan';
            document.getElementById('materialName').value = item.name;
            document.getElementById('materialCategory').value = item.category;
            document.getElementById('materialQty').value = item.qty;
            document.getElementById('materialUnit').value = item.unit;
            document.getElementById('materialMin').value = item.min;
            document.getElementById('materialNote').value = item.note;
        } else {
            document.getElementById('materialModalTitle').textContent = 'Tambah Bahan';
            document.getElementById('saveMaterialButton').textContent = 'Simpan Bahan';
        }

        document.getElementById('materialModal').classList.remove('hidden');
        document.getElementById('materialName').focus();
    }

    function closeMaterialModal(event) {
        if (event && event.target !== document.getElementById('materialModal')) return;
        document.getElementById('materialModal').classList.add('hidden');
    }

    function resetMaterialForm(clearEditing = true) {
        if (clearEditing) editingMaterialId = null;
        document.getElementById('materialName').value = '';
        document.getElementById('materialCategory').value = 'Bahan Minuman';
        document.getElementById('materialQty').value = 1;
        document.getElementById('materialUnit').value = 'pcs';
        document.getElementById('materialMin').value = 5;
        document.getElementById('materialNote').value = '';
        document.getElementById('saveMaterialButton').textContent = 'Simpan Bahan';
    }

    async function saveMaterial() {
        const name = document.getElementById('materialName').value.trim();
        const category = document.getElementById('materialCategory').value;
        const qty = Number(document.getElementById('materialQty').value || 0);
        const unit = document.getElementById('materialUnit').value.trim();
        const min = Number(document.getElementById('materialMin').value || 0);
        const note = document.getElementById('materialNote').value.trim();

        if (!name) return notify('Nama bahan wajib diisi.');
        if (!unit) return notify('Satuan wajib diisi.');
        if (qty < 0 || min < 0) return notify('Jumlah dan batas minimum tidak boleh negatif.');

        const items = materials();
        const payload = { id: editingMaterialId || Date.now(), name, category, qty, unit, min, note };
        const previous = editingMaterialId ? items.find((item) => item.id === editingMaterialId) : null;

        let savedMaterial = payload;
        try {
            const response = await wanaRequest(editingMaterialId ? `/dapur/stok-bahan/${editingMaterialId}` : '/dapur/stok-bahan', {
                method: editingMaterialId ? 'PUT' : 'POST',
                body: JSON.stringify(payload)
            });
            savedMaterial = response.material;
            if (response.activity) setKitchenHistory([response.activity, ...getKitchenHistory()].slice(0, 120));
        } catch (error) {
            notify(error.message);
            return;
        }

        setMaterials(editingMaterialId
            ? items.map((item) => item.id === editingMaterialId ? savedMaterial : item)
            : [savedMaterial, ...items]
        );

        notify(editingMaterialId ? 'Bahan berhasil diperbarui.' : 'Bahan baru ditambahkan.');
        closeMaterialModal();
        resetMaterialForm();
        renderMaterials();
    }

    async function adjustMaterial(id, delta) {
        const previous = materials().find((item) => item.id === id);
        let updatedMaterial;
        try {
            const response = await wanaRequest(`/dapur/stok-bahan/${id}/adjust`, {
                method: 'PATCH',
                body: JSON.stringify({ delta })
            });
            updatedMaterial = response.material;
            if (response.activity) setKitchenHistory([response.activity, ...getKitchenHistory()].slice(0, 120));
        } catch (error) {
            notify(error.message);
            return;
        }

        const items = materials().map((item) => item.id === id ? updatedMaterial : item);
        setMaterials(items);

        renderMaterials();
    }

    async function deleteMaterial(id) {
        const item = materials().find((material) => material.id === id);
        if (!item) return;
        if (!confirm(`Hapus bahan "${item.name}" dari stok dapur?`)) return;

        try {
            const response = await wanaRequest(`/dapur/stok-bahan/${id}`, { method: 'DELETE' });
            if (response.activity) setKitchenHistory([response.activity, ...getKitchenHistory()].slice(0, 120));
        } catch (error) {
            notify(error.message);
            return;
        }

        setMaterials(materials().filter((material) => material.id !== id));
        notify('Bahan dihapus dari stok dapur.');
        renderMaterials();
    }

    renderMaterials();
</script>
@endpush
