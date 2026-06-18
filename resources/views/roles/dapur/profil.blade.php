@extends('layouts.wana', ['title' => 'Profil Dapur | Wana Cafe'])

@section('content')
    <section class="page-head profile-head">
        <div>
            <div class="eyebrow">Dapur</div>
            <h1>Profil Dapur</h1>
            <p class="lead">Data akun dapur hanya sebagai identitas login. Perubahan akun dan pendaftaran karyawan dilakukan oleh owner.</p>
        </div>
    </section>

    <div class="profile-shell">
        <div class="panel profile-card simple-profile">
            <div class="profile-summary">
                <div class="profile-avatar">
                    {{ collect(explode(' ', auth()->user()->name))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') }}
                </div>
                <div>
                    <h2>{{ auth()->user()->name }}</h2>
                    <span>{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>

            <div class="field">
                <label>Nama</label>
                <input value="{{ auth()->user()->name }}" readonly>
            </div>
            <div class="field">
                <label>Username</label>
                <input value="{{ auth()->user()->username }}" readonly>
            </div>
            <div class="field">
                <label>No HP</label>
                <input value="{{ auth()->user()->no_hp ?: '-' }}" readonly>
            </div>

            <div class="account-note">
                Jika data akun perlu diubah, hubungi owner melalui menu chat internal.
            </div>

            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="btn secondary">Logout</button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .profile-head {
        max-width: 680px;
        margin: 0 auto 1.5rem;
        text-align: left;
    }

    .profile-head h1 {
        font-size: clamp(2.4rem, 4vw, 4rem);
        line-height: 1;
        margin-bottom: .7rem;
    }

    .profile-head .lead {
        max-width: 620px;
        color: var(--muted);
        font-size: 1rem;
        line-height: 1.75;
    }

    .simple-profile {
        width: min(560px, 100%);
        padding: 30px;
        border-radius: 18px;
    }

    .profile-summary {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 22px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--line);
    }

    .profile-avatar {
        display: grid;
        place-items: center;
        width: 58px;
        height: 58px;
        border-radius: 50%;
        color: #fff8ed;
        background: linear-gradient(135deg, var(--coffee), #9a6b47);
        font-weight: 900;
    }

    .profile-summary h2 {
        margin: 0;
        font-size: 22px;
    }

    .profile-summary span {
        display: block;
        margin-top: 4px;
        color: var(--muted);
        font-weight: 800;
    }

    .simple-profile .field {
        margin-bottom: 14px;
    }

    .simple-profile input[readonly] {
        color: var(--ink);
        background: #faf5ee;
        cursor: default;
    }

    .account-note {
        margin: 18px 0;
        padding: 13px 14px;
        border: 1px solid rgba(100, 122, 84, .18);
        border-radius: 10px;
        color: var(--muted);
        background: rgba(100, 122, 84, .08);
        font-size: 14px;
        line-height: 1.55;
    }

    .simple-profile .logout-form {
        margin: 0;
    }

    .simple-profile .btn {
        width: 100%;
    }

    .btn.secondary {
        color: var(--coffee);
        background: #f7e2b3;
        box-shadow: inset 0 0 0 1px rgba(110, 73, 33, .12);
    }
</style>
@endpush
