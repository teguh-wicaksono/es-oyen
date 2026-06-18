@extends('layouts.wana', ['title' => 'Profil Kasir | Wana Cafe'])

@section('content')
    <section class="profile-hero">
        <div class="profile-hero-copy">
            <div class="eyebrow">Kasir</div>
            <h1>Profil Kasir</h1>
            <p class="lead">Data akun kasir digunakan sebagai identitas login, transaksi, dan komunikasi internal. Perubahan akun dilakukan oleh owner.</p>
            <div class="profile-pills">
                <span>{{ ucfirst(auth()->user()->role) }} aktif</span>
                <span>Akun owner-managed</span>
            </div>
        </div>

        <div class="profile-hero-visual" aria-hidden="true">
            <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=900&q=85" alt="">
            <div class="hero-badge">
                <span>Wana Cafe</span>
                <strong>Staff Profile</strong>
            </div>
        </div>
    </section>

    <section class="profile-layout">
        <div class="identity-card">
            <div class="identity-cover" aria-hidden="true">
                <img src="https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=1000&q=80" alt="">
            </div>

            <div class="identity-body">
                <div class="profile-summary">
                    <div class="profile-avatar" aria-label="Logo kasir Wana Cafe">
                        <svg viewBox="0 0 64 64" aria-hidden="true">
                            <rect x="12" y="17" width="34" height="30" rx="10"></rect>
                            <path d="M46 25h4a8 8 0 0 1 0 16h-4"></path>
                            <path d="M22 12v-3"></path>
                            <path d="M31 12v-3"></path>
                            <path d="M40 12v-3"></path>
                            <path d="M17 52h32"></path>
                        </svg>
                    </div>
                    <div>
                        <h2>{{ auth()->user()->name }}</h2>
                        <span>{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                </div>

                <div class="profile-fields">
                    <div class="profile-field">
                        <span>Nama</span>
                        <strong>{{ auth()->user()->name }}</strong>
                    </div>
                    <div class="profile-field">
                        <span>Username</span>
                        <strong>{{ auth()->user()->username }}</strong>
                    </div>
                    <div class="profile-field">
                        <span>No HP</span>
                        <strong>{{ auth()->user()->no_hp ?: '-' }}</strong>
                    </div>
                    <div class="profile-field wide">
                        <span>Alamat</span>
                        <strong>{{ auth()->user()->alamat ?: '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <aside class="account-panel">
            <div class="panel-title">
                <div>
                    <h2>Akses Akun</h2>
                    <p>Akun kasir dibuat dan diperbarui oleh owner.</p>
                </div>
                <span class="badge">Read Only</span>
            </div>

            <div class="account-note">
                Jika nama, username, nomor HP, atau data login perlu diubah, hubungi owner melalui chat internal.
            </div>

            <a class="btn ghost" href="{{ route('kasir.chat') }}">Hubungi Owner</a>

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="btn secondary">Logout</button>
            </form>
        </aside>
    </section>
@endsection

@push('styles')
<style>
    .profile-hero {
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
            linear-gradient(135deg, rgba(255, 250, 242, .97), rgba(245, 234, 219, .84) 52%, rgba(100, 122, 84, .18)),
            url('https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=1600&q=80') center/cover;
        box-shadow: 0 34px 80px rgba(49, 29, 15, .12);
    }

    .profile-hero::before {
        content: "";
        position: absolute;
        inset: 18px;
        border: 1px solid rgba(255, 255, 255, .62);
        border-radius: 24px;
        pointer-events: none;
    }

    .profile-hero-copy,
    .profile-hero-visual {
        position: relative;
        z-index: 1;
    }

    .profile-hero h1 {
        margin-top: 8px;
        font-size: clamp(42px, 5vw, 72px);
    }

    .profile-hero .lead {
        max-width: 720px;
        font-size: 16px;
        font-weight: 700;
    }

    .profile-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 24px;
    }

    .profile-pills span {
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

    .profile-hero-visual {
        min-height: 260px;
        display: grid;
        place-items: center;
    }

    .profile-hero-visual img {
        width: min(360px, 80vw);
        aspect-ratio: 1 / .82;
        object-fit: cover;
        border: 10px solid rgba(255, 250, 242, .96);
        border-radius: 28px;
        box-shadow: 0 34px 80px rgba(43, 22, 11, .24);
        transform: rotate(2deg);
    }

    .hero-badge {
        position: absolute;
        left: 0;
        bottom: 24px;
        display: grid;
        gap: 4px;
        min-width: 164px;
        padding: 14px 16px;
        border: 1px solid rgba(255, 250, 242, .72);
        border-radius: 18px;
        background: rgba(43, 22, 11, .82);
        color: #fff8ed;
        box-shadow: 0 22px 50px rgba(43, 22, 11, .22);
        backdrop-filter: blur(12px);
    }

    .hero-badge span {
        color: rgba(255, 248, 237, .76);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .hero-badge strong {
        font-family: "Playfair Display", Georgia, serif;
        font-size: 26px;
        line-height: 1;
    }

    .profile-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(300px, .75fr);
        gap: 24px;
        align-items: start;
    }

    .identity-card,
    .account-panel {
        overflow: hidden;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 28px;
        background: rgba(255, 253, 249, .94);
        box-shadow: 0 30px 80px rgba(49, 29, 15, .11);
    }

    .identity-cover {
        position: relative;
        height: 170px;
        overflow: hidden;
    }

    .identity-cover img {
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
    }

    .identity-cover::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(22, 12, 7, .08), rgba(22, 12, 7, .46));
    }

    .identity-body {
        padding: 28px;
    }

    .profile-summary {
        display: flex;
        align-items: center;
        gap: 16px;
        width: fit-content;
        max-width: 100%;
        margin-top: -74px;
        margin-bottom: 24px;
        padding: 12px 18px 12px 12px;
        border: 1px solid rgba(255, 250, 244, .78);
        border-radius: 26px;
        background: rgba(255, 250, 244, .94);
        box-shadow: 0 22px 48px rgba(49, 29, 15, .16);
        backdrop-filter: blur(12px);
        position: relative;
        z-index: 1;
    }

    .profile-avatar {
        display: grid;
        place-items: center;
        width: 84px;
        height: 84px;
        border: 6px solid #fffaf4;
        border-radius: 26px;
        color: #fff8ed;
        background:
            radial-gradient(circle at 28% 24%, rgba(227, 180, 103, .34), transparent 36%),
            linear-gradient(135deg, #2b160b, #8b5737);
        box-shadow: 0 18px 42px rgba(49, 29, 15, .18);
    }

    .profile-avatar svg {
        width: 48px;
        height: 48px;
        stroke: currentColor;
        stroke-width: 4;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .profile-summary h2 {
        color: var(--coffee);
        font-size: 26px;
        line-height: 1.08;
        text-shadow: 0 1px 0 rgba(255, 255, 255, .82);
    }

    .profile-summary span {
        display: block;
        margin-top: 5px;
        color: #6b4f3a;
        font-weight: 800;
    }

    .profile-fields {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .profile-field {
        min-height: 96px;
        display: grid;
        align-content: center;
        gap: 8px;
        padding: 16px;
        border: 1px solid rgba(83, 58, 38, .10);
        border-radius: 20px;
        background: #fffaf4;
    }

    .profile-field.wide {
        grid-column: 1 / -1;
    }

    .profile-field span {
        color: var(--sage);
        font-size: 12px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .profile-field strong {
        color: var(--ink);
        font-size: 17px;
        line-height: 1.4;
        word-break: break-word;
    }

    .account-panel {
        position: sticky;
        top: 94px;
        display: grid;
        gap: 18px;
        padding: 24px;
    }

    .panel-title {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
    }

    .panel-title h2 {
        color: var(--coffee);
        font-size: 24px;
    }

    .panel-title p {
        margin-top: 6px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .account-note {
        padding: 16px;
        border: 1px solid rgba(100, 122, 84, .18);
        border-radius: 18px;
        color: var(--muted);
        background: rgba(100, 122, 84, .08);
        font-size: 14px;
        line-height: 1.65;
    }

    .account-panel .btn {
        width: 100%;
        min-height: 52px;
        border-radius: 18px;
    }

    .account-panel .btn.ghost {
        color: var(--coffee);
        background: #f9f4ec;
        border: 1px solid rgba(140, 118, 98, .18);
    }

    .account-panel .logout-form {
        margin: 0;
    }

    .btn.secondary {
        color: var(--coffee);
        background: #f7e2b3;
        box-shadow: inset 0 0 0 1px rgba(110, 73, 33, .12);
    }

    @media (max-width: 1080px) {
        .profile-hero,
        .profile-layout {
            grid-template-columns: 1fr;
        }

        .account-panel {
            position: static;
        }
    }

    @media (max-width: 700px) {
        .profile-hero,
        .identity-body,
        .account-panel {
            padding: 20px;
            border-radius: 22px;
        }

        .profile-hero::before {
            inset: 10px;
            border-radius: 18px;
        }

        .profile-hero-visual img {
            width: min(300px, 78vw);
        }

        .hero-badge {
            position: relative;
            inset: auto;
            width: 100%;
            margin-top: -12px;
        }

        .profile-fields {
            grid-template-columns: 1fr;
        }

        .profile-summary {
            width: 100%;
            align-items: flex-start;
            margin-top: -66px;
            padding: 10px 12px;
        }

        .profile-avatar {
            width: 72px;
            height: 72px;
            border-radius: 22px;
        }

        .profile-avatar svg {
            width: 40px;
            height: 40px;
        }

        .profile-summary h2 {
            font-size: 22px;
        }
    }
</style>
@endpush
