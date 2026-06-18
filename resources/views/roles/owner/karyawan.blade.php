@extends('layouts.wana', ['title' => 'Manajemen Karyawan | Wana Cafe'])

@php
    $users = \App\Models\User::query()->latest()->get()->map(fn ($user) => [
        'id' => $user->id,
        'name' => $user->name,
        'username' => $user->username,
        'email' => $user->email,
        'role' => $user->role,
        'no_hp' => $user->no_hp ?? null,
        'alamat' => $user->alamat ?? null,
        'createdAt' => optional($user->created_at)->timezone(config('app.timezone'))->format('d/m/Y H:i'),
    ])->values();
@endphp

@section('content')
<div class="staff-dashboard">
    <section class="staff-hero">
        <div class="staff-hero-copy">
            <div class="eyebrow">Owner Team Center</div>
            <h1>Manajemen Karyawan</h1>
            <p class="lead">Pantau akun owner, kasir, dan dapur dalam tampilan yang lebih rapi untuk mengawasi akses operasional.</p>
            <div class="staff-hero-actions">
                <button class="btn staff-primary" type="button" onclick="openStaffModal()">Tambah Karyawan</button>
                <a class="btn staff-primary" href="{{ route('owner.chat') }}">Hubungi Tim</a>
            </div>
        </div>

        <div class="staff-hero-visual" aria-hidden="true">
            <div class="staff-console">
                <img src="https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=900&q=85" alt="">
                <div class="staff-float top"><span>Total Akun</span><strong id="heroStaffCount">0 user</strong></div>
                <div class="staff-float bottom"><span>Role Aktif</span><strong id="heroRoleCount">0 role</strong></div>
                <div class="staff-avatar-stack"><i>OW</i><i>KS</i><i>DP</i></div>
            </div>
        </div>
    </section>

    <section class="staff-metrics">
        <article class="staff-metric"><span>Total Akun</span><strong id="metricStaff">0</strong><p>Semua akun yang terdaftar.</p></article>
        <article class="staff-metric"><span>Kasir</span><strong id="metricKasir">0</strong><p>Akun yang melayani transaksi.</p></article>
        <article class="staff-metric"><span>Dapur</span><strong id="metricDapur">0</strong><p>Akun pengelola pesanan dan stok.</p></article>
        <article class="staff-metric"><span>Owner</span><strong id="metricOwner">0</strong><p>Akun pengelola laporan.</p></article>
    </section>

    <section class="staff-layout">
        <div class="staff-panel">
            <div class="staff-toolbar">
                <div class="staff-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="m16.5 16.5 4 4"></path></svg>
                    <input id="staffSearch" type="search" placeholder="Cari nama, username, email, atau role..." autocomplete="off">
                </div>
                <div class="staff-segment">
                    <button class="active" type="button" data-staff-role="Semua">Semua</button>
                    <button type="button" data-staff-role="owner">Owner</button>
                    <button type="button" data-staff-role="kasir">Kasir</button>
                    <button type="button" data-staff-role="dapur">Dapur</button>
                </div>
                <select id="staffSort" class="staff-sort" aria-label="Urutkan karyawan">
                    <option value="newest">Terbaru</option>
                    <option value="name">Nama A-Z</option>
                    <option value="role">Role</option>
                </select>
            </div>

            <div id="staffRows" class="staff-grid"></div>
        </div>

        <aside class="staff-side">
            <div class="staff-panel">
                <div class="staff-panel-head"><div><h2>Komposisi Role</h2><span>Distribusi akun</span></div></div>
                <div id="roleRows" class="staff-side-list"></div>
            </div>
            <div class="staff-panel">
                <div class="staff-panel-head"><div><h2>Insight Tim</h2><span>Kontrol akses</span></div></div>
                <div id="staffInsights" class="staff-side-list"></div>
            </div>
        </aside>
    </section>

    <div id="staffModal" class="staff-modal" aria-hidden="true">
        <form class="staff-dialog" method="POST" action="{{ route('owner.karyawan.store') }}">
            @csrf
            <button class="staff-modal-close" type="button" onclick="closeStaffModal()" aria-label="Tutup form tambah karyawan">x</button>
            <div>
                <span class="staff-modal-label">Akun Baru</span>
                <h2 id="staffModalTitle">Tambah Karyawan</h2>
            </div>

            @if ($errors->any())
                <div class="staff-form-alert">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="staff-form-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="staff-form-grid">
                <label>Nama
                    <input name="name" value="{{ old('name') }}" required maxlength="100" autocomplete="name">
                </label>
                <label>Role
                    <input id="staffRoleInput" type="hidden" name="role" value="{{ old('role', 'kasir') }}">
                    <div class="staff-role-select" id="staffRoleSelect">
                        <button class="staff-role-trigger" type="button" aria-expanded="false" aria-haspopup="listbox">
                            <span id="staffRoleLabel">Kasir</span>
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
                        </button>
                        <div class="staff-role-menu" role="listbox" aria-label="Pilih role karyawan">
                            <button type="button" role="option" data-role-value="kasir">
                                <strong>Kasir</strong>
                                <span>Mengelola transaksi dan pembayaran.</span>
                            </button>
                            <button type="button" role="option" data-role-value="dapur">
                                <strong>Dapur</strong>
                                <span>Mengelola pesanan dan stok.</span>
                            </button>
                        </div>
                    </div>
                </label>
                <label>Username
                    <input name="username" value="{{ old('username') }}" required maxlength="50" autocomplete="username">
                </label>
                <label>Email
                    <input name="email" value="{{ old('email') }}" type="email" required maxlength="150" autocomplete="email">
                </label>
                <label>Password
                    <input name="password" type="password" required minlength="6" autocomplete="new-password">
                </label>
                <label>Konfirmasi Password
                    <input name="password_confirmation" type="password" required minlength="6" autocomplete="new-password">
                </label>
                <label>No HP
                    <input name="no_hp" value="{{ old('no_hp') }}" maxlength="20" autocomplete="tel">
                </label>
                <label>Alamat
                    <input name="alamat" value="{{ old('alamat') }}" autocomplete="street-address">
                </label>
            </div>
            <button class="btn staff-primary" type="submit">Simpan Akun</button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .staff-dashboard { width:min(100%,1360px); margin:0 auto; display:grid; gap:20px; }
    .staff-hero { position:relative; display:grid; grid-template-columns:minmax(0,1.05fr) minmax(320px,420px); gap:30px; align-items:center; min-height:340px; padding:32px; overflow:hidden; border:1px solid rgba(83,58,38,.12); border-radius:30px; background:linear-gradient(110deg,rgba(43,22,11,.94),rgba(65,38,23,.86) 48%,rgba(143,99,61,.5)),url('https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=1600&q=85') center/cover; box-shadow:0 34px 80px rgba(49,29,15,.12); }
    .staff-hero::before { content:""; position:absolute; inset:18px; border:1px solid rgba(255,255,255,.62); border-radius:24px; pointer-events:none; }
    .staff-hero-copy,.staff-hero-visual { position:relative; z-index:1; }
    .staff-hero-copy .eyebrow { color:#f0d7a7; }
    .staff-hero-copy h1 { max-width:820px; margin-top:8px; color:#fff8ed; font-size:clamp(42px,5vw,72px); line-height:.98; }
    .staff-hero-copy .lead { max-width:720px; color:rgba(255,248,237,.84); font-weight:750; }
    .staff-hero-actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:26px; }
    .staff-hero-actions .btn { width:auto; min-height:44px; border-radius:999px; }
    .staff-primary { color:#fff8ed; background:var(--coffee); }
    .staff-secondary { color:var(--coffee); border:1px solid rgba(83,58,38,.13); background:rgba(255,250,242,.88); }
    .staff-hero-visual { min-height:276px; display:grid; place-items:center; }
    .staff-console { position:relative; width:min(390px,100%); min-height:272px; padding:14px; border:1px solid rgba(255,248,237,.42); border-radius:28px; background:rgba(255,250,242,.14); box-shadow:0 34px 80px rgba(20,10,5,.24); backdrop-filter:blur(10px); }
    .staff-console img { width:100%; height:238px; display:block; object-fit:cover; border:1px solid rgba(255,248,237,.44); border-radius:22px; }
    .staff-float { position:absolute; display:grid; gap:4px; min-width:150px; padding:14px 16px; border:1px solid rgba(255,250,242,.78); border-radius:16px; background:rgba(255,253,249,.92); box-shadow:0 20px 46px rgba(49,29,15,.13); }
    .staff-float.top { top:28px; right:-18px; } .staff-float.bottom { left:-18px; bottom:32px; }
    .staff-float span { color:var(--sage); font-size:12px; font-weight:900; letter-spacing:.1em; text-transform:uppercase; }
    .staff-float strong { font-size:20px; }
    .staff-avatar-stack { position:absolute; right:22px; bottom:22px; display:flex; align-items:center; padding:10px; border-radius:999px; background:rgba(43,22,11,.76); }
    .staff-avatar-stack i { display:grid; place-items:center; width:38px; height:38px; margin-left:-7px; border:2px solid #fff8ed; border-radius:50%; color:var(--coffee); background:#fff4df; font-style:normal; font-size:11px; font-weight:900; }
    .staff-avatar-stack i:first-child { margin-left:0; }
    .staff-metrics { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
    .staff-metric,.staff-panel { position:relative; overflow:hidden; border:1px solid rgba(83,58,38,.1); border-radius:22px; background:rgba(255,253,249,.9); box-shadow:0 24px 58px rgba(49,29,15,.08); }
    .staff-metric { min-height:144px; padding:20px; transition:transform .18s ease,box-shadow .18s ease; }
    .staff-metric:hover { transform:translateY(-4px); box-shadow:0 30px 70px rgba(49,29,15,.13); }
    .staff-metric::after { content:""; position:absolute; right:-34px; bottom:-42px; width:128px; height:128px; border-radius:999px; background:rgba(100,122,84,.12); }
    .staff-metric span,.staff-panel-head span { color:var(--muted); font-size:12px; font-weight:900; letter-spacing:.1em; text-transform:uppercase; }
    .staff-metric strong { position:relative; z-index:1; display:block; margin-top:14px; font-size:clamp(28px,3vw,40px); line-height:1; }
    .staff-metric p { position:relative; z-index:1; margin-top:12px; color:var(--sage); font-weight:700; }
    .staff-layout { display:grid; grid-template-columns:minmax(0,1.42fr) minmax(330px,.58fr); gap:20px; align-items:start; }
    .staff-panel { padding:18px; }
    .staff-toolbar { display:grid; grid-template-columns:minmax(240px,1fr) auto minmax(160px,auto); gap:12px; align-items:center; margin-bottom:16px; }
    .staff-search { display:flex; align-items:center; gap:10px; min-height:50px; padding:0 16px; border:1px solid rgba(83,58,38,.12); border-radius:18px; background:#fffaf4; }
    .staff-search svg { width:20px; height:20px; color:var(--sage); fill:none; stroke:currentColor; stroke-width:2; }
    .staff-search input { width:100%; min-width:0; border:0; outline:0; background:transparent; font-weight:800; }
    .staff-segment { display:flex; gap:4px; padding:5px; border:1px solid rgba(83,58,38,.1); border-radius:999px; background:rgba(245,234,219,.72); }
    .staff-segment button,.staff-sort { font-size:13px; font-weight:900; }
    .staff-segment button { min-height:38px; padding:0 14px; border:0; border-radius:999px; color:var(--muted); background:transparent; }
    .staff-segment button.active { color:#fff8ed; background:var(--coffee); box-shadow:0 12px 24px rgba(49,29,15,.13); }
    .staff-sort { min-height:48px; min-width:170px; padding:0 14px; border:1px solid rgba(83,58,38,.13); border-radius:999px; color:var(--muted); background:#fbf6ef; outline:0; }
    .staff-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; max-height:640px; overflow:auto; padding-right:5px; }
    .staff-card,.staff-side-item { border:1px solid rgba(83,58,38,.09); border-radius:18px; background:rgba(255,250,242,.74); }
    .staff-card { display:grid; grid-template-columns:auto minmax(0,1fr); gap:14px; align-items:center; padding:16px; transition:transform .18s ease,box-shadow .18s ease; }
    .staff-card:hover { transform:translateY(-2px); box-shadow:0 22px 50px rgba(49,29,15,.1); }
    .staff-avatar { display:grid; place-items:center; width:58px; height:58px; border-radius:18px; color:#fff8ed; background:linear-gradient(135deg,var(--coffee),#9a603a); font-weight:900; }
    .staff-card h3 { margin:0; font-size:18px; } .staff-card p { margin-top:6px; color:var(--muted); font-weight:700; line-height:1.45; }
    .staff-meta { display:flex; flex-wrap:wrap; gap:8px; margin-top:12px; }
    .staff-pill { display:inline-flex; align-items:center; min-height:28px; padding:0 10px; border-radius:999px; color:var(--coffee); background:rgba(245,234,219,.86); font-size:12px; font-weight:900; white-space:nowrap; }
    .staff-side { display:grid; gap:18px; }
    .staff-panel-head { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:12px; margin-bottom:16px; }
    .staff-panel-head h2 { margin:0; font-size:18px; }
    .staff-side-list { display:grid; gap:12px; }
    .staff-side-item { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:12px; align-items:center; padding:14px; }
    .staff-side-item strong { display:block; font-size:14px; } .staff-side-item span { color:var(--muted); font-size:12px; font-weight:750; }
    .staff-empty { padding:28px; border:1px dashed rgba(83,58,38,.18); border-radius:18px; color:var(--muted); background:rgba(255,250,242,.62); text-align:center; font-weight:800; }
    .staff-modal { position:fixed; inset:0; z-index:100002; display:grid; place-items:center; padding:18px; background:rgba(24,16,10,.52); opacity:0; visibility:hidden; pointer-events:none; transition:opacity .18s ease, visibility .18s ease; }
    .staff-modal.show { opacity:1; visibility:visible; pointer-events:auto; }
    .staff-dialog { position:relative; display:grid; gap:16px; width:min(680px,100%); max-height:calc(100vh - 36px); overflow:auto; padding:24px; border:1px solid rgba(83,58,38,.14); border-radius:24px; background:#fffaf4; box-shadow:0 34px 100px rgba(20,10,5,.28); }
    .staff-modal-close { position:absolute; top:12px; right:12px; display:grid; place-items:center; width:36px; height:36px; border:0; border-radius:12px; color:var(--muted); background:rgba(245,234,219,.7); font-weight:900; }
    .staff-modal-label { color:var(--sage); font-size:11px; font-weight:900; letter-spacing:.12em; text-transform:uppercase; }
    .staff-dialog h2 { margin:5px 0 0; color:var(--coffee); font-size:26px; }
    .staff-form-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
    .staff-form-grid label { display:grid; gap:8px; color:var(--muted); font-size:12px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .staff-form-grid input,.staff-form-grid select { width:100%; min-height:46px; border:1px solid rgba(83,58,38,.14); border-radius:14px; background:#fffdf9; padding:0 13px; color:var(--ink); outline:0; font-weight:800; }
    .staff-role-select { position:relative; }
    .staff-role-trigger { display:flex; align-items:center; justify-content:space-between; gap:12px; width:100%; min-height:46px; padding:0 13px; border:1px solid rgba(83,58,38,.14); border-radius:14px; color:var(--ink); background:#fffdf9; font-weight:900; }
    .staff-role-trigger svg { width:18px; height:18px; flex:0 0 auto; fill:none; stroke:currentColor; stroke-width:2.6; stroke-linecap:round; stroke-linejoin:round; transition:transform .18s ease; }
    .staff-role-select.show .staff-role-trigger { border-color:rgba(83,58,38,.32); box-shadow:0 12px 28px rgba(49,29,15,.1); }
    .staff-role-select.show .staff-role-trigger svg { transform:rotate(180deg); }
    .staff-role-menu { position:absolute; left:0; right:0; top:calc(100% + 8px); z-index:4; display:grid; gap:6px; padding:8px; border:1px solid rgba(83,58,38,.12); border-radius:18px; background:rgba(255,253,249,.98); box-shadow:0 22px 54px rgba(20,10,5,.18); opacity:0; visibility:hidden; pointer-events:none; transform:translateY(-6px); transition:opacity .18s ease, transform .18s ease, visibility .18s ease; }
    .staff-role-select.show .staff-role-menu { opacity:1; visibility:visible; pointer-events:auto; transform:translateY(0); }
    .staff-role-menu button { display:grid; gap:4px; width:100%; padding:12px 13px; border:0; border-radius:12px; background:transparent; color:var(--ink); text-align:left; transition:background .18s ease, transform .18s ease; }
    .staff-role-menu button:hover,.staff-role-menu button.active { background:rgba(245,234,219,.86); transform:translateY(-1px); }
    .staff-role-menu strong { color:var(--coffee); font-size:14px; }
    .staff-role-menu span { color:var(--muted); font-size:12px; font-weight:800; line-height:1.35; letter-spacing:0; text-transform:none; }
    .staff-form-alert,.staff-form-success { padding:12px 14px; border-radius:14px; font-weight:800; }
    .staff-form-alert { color:#7a3032; background:#f8e3e4; }
    .staff-form-success { color:#334525; background:#e7efdf; }
    @media (max-width:1100px){ .staff-hero,.staff-layout,.staff-toolbar{grid-template-columns:1fr}.staff-metrics,.staff-grid{grid-template-columns:repeat(2,minmax(0,1fr))} }
    @media (max-width:760px){ .staff-hero{padding:24px;border-radius:24px}.staff-hero::before{inset:12px;border-radius:18px}.staff-hero-copy h1{font-size:clamp(36px,13vw,54px)}.staff-hero-actions .btn,.staff-sort{width:100%}.staff-console{width:min(340px,100%)}.staff-float.top{right:4px}.staff-float.bottom{left:4px;bottom:22px}.staff-metrics,.staff-grid,.staff-card,.staff-side-item,.staff-form-grid{grid-template-columns:1fr}.staff-panel,.staff-metric{border-radius:18px;padding:18px}.staff-segment{width:100%;overflow-x:auto}.staff-segment button{flex:1 0 auto} }
</style>
@endpush

@push('scripts')
<script>
    const STAFF_USERS = @json($users);
    const STAFF_CSRF = @json(csrf_token());
    let activeStaffRole = 'Semua';
    const STAFF_ROLE_LABELS = { kasir: 'Kasir', dapur: 'Dapur' };
    const staffEscape = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;' }[char]));
    const staffInitials = (name) => String(name || 'WN').split(/\s+/).map((part) => part[0] || '').join('').slice(0, 2).toUpperCase();

    function filteredStaffUsers() {
        const search = String(document.getElementById('staffSearch').value || '').toLowerCase();
        const sort = document.getElementById('staffSort').value;
        return STAFF_USERS.filter((user) => {
            const text = [user.name, user.username, user.email, user.role].join(' ').toLowerCase();
            return (activeStaffRole === 'Semua' || user.role === activeStaffRole) && (!search || text.includes(search));
        }).sort((a, b) => {
            if (sort === 'name') return String(a.name || '').localeCompare(String(b.name || ''));
            if (sort === 'role') return String(a.role || '').localeCompare(String(b.role || ''));
            return String(b.createdAt || '').localeCompare(String(a.createdAt || ''));
        });
    }

    function roleCount(role) {
        return STAFF_USERS.filter((user) => user.role === role).length;
    }

    function renderStaffPage() {
        const users = filteredStaffUsers();
        const roles = [...new Set(STAFF_USERS.map((user) => user.role))];

        document.getElementById('heroStaffCount').textContent = `${STAFF_USERS.length} user`;
        document.getElementById('heroRoleCount').textContent = `${roles.length} role`;
        document.getElementById('metricStaff').textContent = users.length;
        document.getElementById('metricKasir').textContent = roleCount('kasir');
        document.getElementById('metricDapur').textContent = roleCount('dapur');
        document.getElementById('metricOwner').textContent = roleCount('owner');

        document.getElementById('staffRows').innerHTML = users.length ? users.map((user) => `
            <article class="staff-card">
                <div class="staff-avatar">${staffInitials(user.name)}</div>
                <div>
                    <h3>${staffEscape(user.name)}</h3>
                    <p>${staffEscape(user.email || 'Email belum diisi')}</p>
                    <div class="staff-meta">
                        <span class="staff-pill">${staffEscape(user.role)}</span>
                        <span class="staff-pill">@${staffEscape(user.username || '-')}</span>
                        <span class="staff-pill">${staffEscape(user.createdAt || 'Waktu tidak tercatat')}</span>
                    </div>
                    <div style="margin-top:12px">
                        <button class="btn staff-secondary" type="button" onclick="openStaffModal('edit', ${user.id})">Edit</button>
                        <form method="POST" action="/owner/karyawan/${user.id}" onsubmit="return confirm('Hapus akun karyawan ini?')" style="display:inline-block;margin-left:8px">
                            <input type="hidden" name="_token" value="${STAFF_CSRF}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn staff-secondary" type="submit">Hapus</button>
                        </form>
                    </div>
                </div>
            </article>
        `).join('') : '<div class="staff-empty">Karyawan tidak ditemukan pada filter ini.</div>';

        document.getElementById('roleRows').innerHTML = roles.map((role) => `
            <div class="staff-side-item"><div><strong>${staffEscape(role)}</strong><span>Akun role ${staffEscape(role)}</span></div><b>${roleCount(role)}</b></div>
        `).join('') || '<div class="staff-empty">Belum ada akun.</div>';

        document.getElementById('staffInsights').innerHTML = `
            <div class="staff-side-item"><div><strong>Akun kasir</strong><span>Role yang membuat transaksi.</span></div><b>${roleCount('kasir')}</b></div>
            <div class="staff-side-item"><div><strong>Akun dapur</strong><span>Role yang memproses pesanan.</span></div><b>${roleCount('dapur')}</b></div>
            <div class="staff-side-item"><div><strong>Akun owner</strong><span>Role akses laporan dan export.</span></div><b>${roleCount('owner')}</b></div>
        `;
    }

    function openStaffModal(modeOrRole = 'kasir', maybeId) {
        const modal = document.getElementById('staffModal');
        const form = modal.querySelector('form.staff-dialog');

        if (modeOrRole === 'edit') {
            const id = maybeId;
            const user = STAFF_USERS.find((u) => u.id === id);
            if (!user) return alert('Data user tidak ditemukan');

            form.action = `/owner/karyawan/${id}`;
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            document.getElementById('staffModalTitle').textContent = 'Edit Karyawan';
            form.querySelector('button[type="submit"]').textContent = 'Perbarui Akun';

            // populate fields
            form.querySelector('input[name="name"]').value = user.name || '';
            form.querySelector('input[name="username"]').value = user.username || '';
            form.querySelector('input[name="email"]').value = user.email || '';
            form.querySelector('input[name="no_hp"]').value = user.no_hp || '';
            form.querySelector('input[name="alamat"]').value = user.alamat || '';
            setStaffRole(user.role || 'kasir');

            // password optional on edit
            form.querySelector('input[name="password"]').required = false;
            form.querySelector('input[name="password_confirmation"]').required = false;
        } else {
            // create mode
            setStaffRole(modeOrRole);
            form.action = '{{ route('owner.karyawan.store') }}';
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();
            document.getElementById('staffModalTitle').textContent = 'Tambah Karyawan';
            form.querySelector('button[type="submit"]').textContent = 'Simpan Akun';

            // clear fields
            form.querySelector('input[name="name"]').value = '';
            form.querySelector('input[name="username"]').value = '';
            form.querySelector('input[name="email"]').value = '';
            form.querySelector('input[name="password"]').value = '';
            form.querySelector('input[name="password_confirmation"]').value = '';
            form.querySelector('input[name="no_hp"]').value = '';
            form.querySelector('input[name="alamat"]').value = '';

            form.querySelector('input[name="password"]').required = true;
            form.querySelector('input[name="password_confirmation"]').required = true;
        }

        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeStaffModal() {
        const modal = document.getElementById('staffModal');
        const form = modal.querySelector('form.staff-dialog');
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');

        // reset form to create mode to avoid stale PUT method
        form.action = '{{ route('owner.karyawan.store') }}';
        const methodInput = form.querySelector('input[name="_method"]');
        if (methodInput) methodInput.remove();
        form.querySelector('button[type="submit"]').textContent = 'Simpan Akun';
        form.querySelector('input[name="password"]').required = true;
        form.querySelector('input[name="password_confirmation"]').required = true;
    }

    function setStaffRole(role = 'kasir') {
        const selectedRole = STAFF_ROLE_LABELS[role] ? role : 'kasir';
        document.getElementById('staffRoleInput').value = selectedRole;
        document.getElementById('staffRoleLabel').textContent = STAFF_ROLE_LABELS[selectedRole];
        document.querySelectorAll('[data-role-value]').forEach((button) => {
            const isActive = button.dataset.roleValue === selectedRole;
            button.classList.toggle('active', isActive);
            button.setAttribute('aria-selected', String(isActive));
        });
    }

    function closeStaffRoleSelect() {
        document.getElementById('staffRoleSelect')?.classList.remove('show');
        document.querySelector('.staff-role-trigger')?.setAttribute('aria-expanded', 'false');
    }

    document.getElementById('staffSearch').addEventListener('input', renderStaffPage);
    document.getElementById('staffSort').addEventListener('change', renderStaffPage);
    document.querySelectorAll('[data-staff-role]').forEach((button) => button.addEventListener('click', () => {
        activeStaffRole = button.dataset.staffRole;
        document.querySelectorAll('[data-staff-role]').forEach((item) => item.classList.toggle('active', item === button));
        renderStaffPage();
    }));
    document.querySelector('.staff-role-trigger')?.addEventListener('click', (event) => {
        event.stopPropagation();
        const roleSelect = event.currentTarget.closest('.staff-role-select');
        const isOpen = roleSelect.classList.toggle('show');
        event.currentTarget.setAttribute('aria-expanded', String(isOpen));
    });
    document.querySelectorAll('[data-role-value]').forEach((button) => button.addEventListener('click', () => {
        setStaffRole(button.dataset.roleValue);
        closeStaffRoleSelect();
    }));
    document.addEventListener('click', (event) => {
        const roleSelect = document.getElementById('staffRoleSelect');
        if (roleSelect && !roleSelect.contains(event.target)) {
            closeStaffRoleSelect();
        }
    });
    document.getElementById('staffModal')?.addEventListener('click', (event) => {
        if (event.target.id === 'staffModal') closeStaffModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeStaffRoleSelect();
            closeStaffModal();
        }
    });
    @if ($errors->any())
        openStaffModal(@json(old('role', 'kasir')));
    @endif
    setStaffRole(document.getElementById('staffRoleInput').value);
    renderStaffPage();
</script>
@endpush
