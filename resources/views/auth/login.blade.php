@extends('layouts.wana', ['title' => 'Login | Wana Cafe'])

@section('content')
    <section class="auth-wrap">
        <div class="auth-grid">
            <div class="auth-image" aria-label="Suasana cafe Wana Cafe">
                <div class="auth-image-shine" aria-hidden="true"></div>
                <div class="auth-photo-copy">
                    <span class="auth-photo-kicker">Fresh brew daily</span>
                    <h2>Ruang kerja cafe yang hangat untuk tim Wana.</h2>
                </div>
                <div class="auth-floating-card">
                    <span>Today's Mood</span>
                    <strong>Cozy</strong>
                </div>
            </div>

            <div class="auth-card">
                <div class="auth-head">
                    <div class="eyebrow">Wana Cafe</div>
                    <h1>Selamat Datang</h1>
                    <p class="lead">Masuk untuk melanjutkan ke sistem operasional cafe.</p>
                </div>

                <div class="auth-body">
                    @if (session('status'))
                        <div class="alert success">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf
                        <div class="field">
                            <label for="username">USERNAME</label>
                            <input id="username" name="username" type="text" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
                        </div>

                        <div class="field">
                            <label for="password">PASSWORD</label>
                            <div class="password-wrap">
                                <input id="password" name="password" type="password" placeholder="********" required>
                                <button id="passwordToggle" class="password-toggle" type="button" aria-label="Lihat kata sandi" aria-pressed="false">
                                    <svg class="eye-open" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-closed" viewBox="0 0 24 24" aria-hidden="true">
                                        <path d="m3 3 18 18"></path>
                                        <path d="M10.6 10.6A3 3 0 0 0 14 14"></path>
                                        <path d="M7.1 7.5C4.2 9.2 2.5 12 2.5 12s3.5 6 9.5 6c1.6 0 3-.4 4.2-1"></path>
                                        <path d="M12 6c6 0 9.5 6 9.5 6a15 15 0 0 1-2.2 2.8"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="auth-options">
                            <label class="remember-check">
                                <input type="checkbox" name="remember" value="1"> Ingat saya
                            </label>
                            <button class="forgot-link" type="button" onclick="openForgotModal()">Lupa Kata Sandi?</button>
                        </div>

                        <button class="btn auth-submit" type="submit">
                            <span>LOGIN</span>
                        </button>
                    </form>

                </div>
            </div>
        </div>

        <div id="forgotModal" class="forgot-modal" aria-hidden="true">
            <div class="forgot-dialog" role="dialog" aria-modal="true" aria-labelledby="forgotTitle">
                <button class="forgot-close" type="button" onclick="closeForgotModal()" aria-label="Tutup popup">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12"></path><path d="M18 6 6 18"></path></svg>
                </button>
                <div class="forgot-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 17h.01"></path><path d="M9.1 9a3 3 0 1 1 5.6 1.5c-.9 1.2-2.2 1.5-2.6 3.5"></path><circle cx="12" cy="12" r="9"></circle></svg>
                </div>
                <h2 id="forgotTitle">Lupa Kata Sandi?</h2>
                <p>Silakan hubungi owner untuk reset atau perubahan kata sandi akun.</p>
                <button class="btn" type="button" onclick="closeForgotModal()">Mengerti</button>
            </div>
        </div>
    </section>
@endsection

@push('styles')
<style>
    body.auth-page {
        background:
            radial-gradient(circle at 10% 12%, rgba(227, 180, 103, .22), transparent 28%),
            radial-gradient(circle at 86% 76%, rgba(100, 122, 84, .16), transparent 30%),
            linear-gradient(135deg, #fffaf3 0%, #f1e4d4 48%, #fbf6ef 100%);
    }

    .auth-wrap {
        width: 100%;
        max-width: 100vw;
        min-height: 100vh;
        display: grid;
        place-items: center;
        padding: clamp(18px, 4vw, 54px);
        overflow: hidden;
    }

    .auth-grid {
        position: relative;
        width: min(1120px, 100%);
        max-width: 100%;
        display: grid;
        grid-template-columns: minmax(0, 1.08fr) minmax(360px, .92fr);
        align-items: stretch;
        min-height: min(680px, calc(100vh - 96px));
        border: 1px solid rgba(83, 58, 38, .14);
        border-radius: 28px;
        background: rgba(255, 250, 244, .72);
        box-shadow: 0 34px 110px rgba(49, 29, 15, .18);
        overflow: hidden;
        isolation: isolate;
    }

    .auth-image {
        position: relative;
        min-height: 620px;
        background:
            linear-gradient(180deg, rgba(18, 10, 5, .06), rgba(18, 10, 5, .72)),
            url('https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=1400&q=82');
        background-size: 108%;
        background-position: center;
        overflow: hidden;
        transform-style: preserve-3d;
        transition: background-position .24s ease-out, background-size .24s ease-out;
    }

    .auth-image::after {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(90deg, transparent 58%, rgba(22, 12, 6, .28)),
            radial-gradient(circle at var(--hero-x, 42%) var(--hero-y, 38%), rgba(255, 244, 219, .22), transparent 24%);
        mix-blend-mode: screen;
        opacity: .86;
        pointer-events: none;
    }

    .auth-image-shine {
        position: absolute;
        inset: -20%;
        background: linear-gradient(115deg, transparent 24%, rgba(255, 255, 255, .22) 44%, transparent 58%);
        transform: translateX(-42%) rotate(4deg);
        animation: authShine 7s ease-in-out infinite;
        pointer-events: none;
    }

    .auth-photo-copy {
        position: absolute;
        left: clamp(24px, 5vw, 54px);
        right: clamp(24px, 5vw, 58px);
        bottom: clamp(28px, 5vw, 58px);
        z-index: 2;
        color: #fffaf2;
        text-shadow: 0 14px 34px rgba(0, 0, 0, .28);
    }

    .auth-photo-kicker {
        display: inline-flex;
        align-items: center;
        min-height: 28px;
        padding: 0 12px;
        border: 1px solid rgba(255, 250, 242, .34);
        border-radius: 999px;
        background: rgba(255, 250, 242, .16);
        backdrop-filter: blur(12px);
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .auth-photo-copy h2 {
        max-width: 520px;
        margin-top: 14px;
        font-family: "Playfair Display", Georgia, serif;
        font-size: clamp(34px, 4vw, 56px);
        line-height: 1;
    }

    .auth-floating-card {
        position: absolute;
        top: 34px;
        right: 34px;
        z-index: 2;
        display: grid;
        gap: 4px;
        min-width: 150px;
        padding: 15px 18px;
        border: 1px solid rgba(255, 250, 242, .30);
        border-radius: 18px;
        color: #fffaf2;
        background: rgba(35, 19, 10, .42);
        backdrop-filter: blur(16px);
        box-shadow: 0 18px 46px rgba(0, 0, 0, .18);
        animation: authFloat 5.8s ease-in-out infinite;
    }

    .auth-floating-card span {
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .08em;
        text-transform: uppercase;
        opacity: .78;
    }

    .auth-floating-card strong {
        font-size: 24px;
        line-height: 1;
    }

    .auth-card {
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-width: 0;
        padding: clamp(26px, 4vw, 48px);
        background:
            linear-gradient(180deg, rgba(255, 252, 247, .96), rgba(255, 247, 238, .93)),
            radial-gradient(circle at 100% 0%, rgba(200, 132, 79, .16), transparent 34%);
        backdrop-filter: blur(20px);
        overflow: hidden;
    }

    .auth-head,
    .auth-body {
        position: relative;
        z-index: 1;
        min-width: 0;
    }

    .auth-body form {
        min-width: 0;
    }

    .auth-head {
        margin-bottom: 28px;
    }

    .auth-head h1 {
        margin-top: 8px;
        font-size: clamp(38px, 4vw, 52px);
        line-height: .98;
    }

    .auth-head .lead {
        margin-top: 13px;
        font-size: 15px;
        line-height: 1.6;
    }

    .field input {
        min-height: 58px;
        border-radius: 16px;
        border-color: rgba(93, 66, 44, .16);
        background: rgba(255, 253, 249, .88);
        padding: 15px 16px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, .75), 0 12px 30px rgba(49, 29, 15, .04);
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease, background .18s ease;
    }

    .field input:focus {
        border-color: rgba(200, 132, 79, .55);
        background: #fffdf9;
        box-shadow: 0 0 0 4px rgba(200, 132, 79, .13), 0 18px 36px rgba(49, 29, 15, .08);
        transform: translateY(-1px);
    }

    .auth-options {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 8px 0 22px;
        color: var(--muted);
        font-size: 13px;
        font-weight: 800;
    }

    .remember-check {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
        font-weight: 800;
    }

    .remember-check input {
        width: 18px;
        height: 18px;
        accent-color: var(--caramel);
    }

    .auth-submit {
        position: relative;
        width: 100%;
        min-height: 58px;
        border-radius: 18px;
        background: var(--coffee);
        box-shadow: 0 22px 44px rgba(140, 85, 50, .26);
        overflow: hidden;
    }

    .auth-submit::before {
        display: none;
    }

    .auth-submit:hover::before {
        transform: none;
    }

    .auth-submit span {
        position: relative;
        z-index: 1;
    }

    .forgot-link {
        padding: 0;
        border: 0;
        color: var(--muted);
        background: transparent;
        font-size: 13px;
        font-weight: 800;
    }

    .forgot-link:hover {
        color: var(--coffee);
        text-decoration: underline;
    }

    .forgot-modal {
        position: fixed;
        inset: 0;
        z-index: 100000;
        display: grid;
        place-items: center;
        padding: 20px;
        background: rgba(24, 16, 10, .42);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity .18s ease, visibility .18s ease;
    }

    .forgot-modal.show {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .forgot-dialog {
        position: relative;
        display: grid;
        justify-items: center;
        gap: 13px;
        width: min(360px, 100%);
        padding: 28px 24px 24px;
        border: 1px solid rgba(83, 58, 38, .12);
        border-radius: 22px;
        background: #fffaf4;
        box-shadow: 0 28px 80px rgba(20, 10, 5, .24);
        text-align: center;
    }

    .forgot-close {
        position: absolute;
        top: 12px;
        right: 12px;
        display: grid;
        place-items: center;
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 10px;
        color: var(--muted);
        background: transparent;
    }

    .forgot-close:hover {
        color: var(--coffee);
        background: rgba(200, 132, 79, .12);
    }

    .forgot-close svg,
    .forgot-icon svg {
        stroke: currentColor;
        stroke-width: 2.2;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .forgot-close svg {
        width: 20px;
        height: 20px;
    }

    .forgot-icon {
        display: grid;
        place-items: center;
        width: 58px;
        height: 58px;
        border-radius: 18px;
        color: #fff8ed;
        background: linear-gradient(135deg, #2b160b, #8b5737);
        box-shadow: 0 16px 34px rgba(43, 22, 11, .18);
    }

    .forgot-icon svg {
        width: 32px;
        height: 32px;
    }

    .forgot-dialog h2 {
        margin: 0;
        color: var(--coffee);
        font-size: 24px;
    }

    .forgot-dialog p {
        margin: 0;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .forgot-dialog .btn {
        width: 100%;
        margin-top: 4px;
        border-radius: 16px;
    }

    .password-wrap {
        position: relative;
    }

    .password-wrap input {
        width: 100%;
        padding-right: 56px;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        place-items: center;
        width: 34px;
        height: 34px;
        transform: translateY(-50%);
        padding: 0;
        border: 0;
        border-radius: 12px;
        color: var(--muted);
        background: transparent;
        line-height: 0;
    }

    .password-toggle:hover {
        color: var(--coffee);
        background: rgba(200, 132, 79, .12);
    }

    .password-toggle svg {
        display: block;
        width: 20px;
        height: 20px;
        stroke: currentColor;
        stroke-width: 2.25;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .password-toggle .eye-closed,
    .password-toggle.showing .eye-open {
        display: none;
    }

    .password-toggle.showing .eye-closed {
        display: block;
    }

    @media (max-width: 1080px) {
        .auth-wrap {
            padding: 14px;
            place-items: start center;
        }

        .auth-grid {
            width: 100%;
            grid-template-columns: 1fr;
            min-height: 0;
            border-radius: 24px;
        }

        .auth-image {
            min-height: 330px;
            background-size: cover;
        }

        .auth-photo-copy {
            max-width: calc(100% - 36px);
        }

        .auth-floating-card {
            top: 22px;
            right: 22px;
        }

        .auth-card {
            width: 100%;
            padding: 28px 22px 24px;
        }

        .auth-head {
            margin-bottom: 22px;
        }
    }

    @media (max-width: 560px) {
        .auth-image {
            min-height: 300px;
        }

        .auth-photo-copy {
            left: 18px;
            right: 18px;
            bottom: 18px;
            display: block;
        }

        .auth-photo-kicker {
            min-height: 24px;
            padding: 0 10px;
            font-size: 9px;
        }

        .auth-photo-copy h2 {
            max-width: 270px;
            margin-top: 10px;
            font-size: clamp(27px, 10vw, 36px);
            line-height: .98;
        }

        .auth-floating-card {
            top: 16px;
            right: 16px;
            min-width: 120px;
            padding: 11px 13px;
            border-radius: 15px;
        }

        .auth-floating-card strong {
            font-size: 20px;
        }

        .auth-options {
            align-items: center;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 8px 12px;
            margin: 4px 0 18px;
            font-size: 11px;
            line-height: 1.2;
        }

        .remember-check {
            gap: 7px;
            font-size: 11px;
        }

        .remember-check input {
            width: 15px;
            height: 15px;
        }

        .forgot-link {
            font-size: 11px;
            line-height: 1.2;
        }

        .auth-submit {
            min-height: 52px;
            border-radius: 16px;
        }
    }

    @keyframes authFloat {
        0%, 100% { transform: translate3d(0, 0, 0); }
        50% { transform: translate3d(0, -8px, 0); }
    }

    @keyframes authShine {
        0%, 42% { transform: translateX(-52%) rotate(4deg); opacity: 0; }
        58% { opacity: .85; }
        100% { transform: translateX(52%) rotate(4deg); opacity: 0; }
    }
</style>
@endpush

@push('scripts')
<script>
    function openForgotModal() {
        const modal = document.getElementById('forgotModal');
        if (!modal) return;
        modal.classList.add('show');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeForgotModal() {
        const modal = document.getElementById('forgotModal');
        if (!modal) return;
        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');
    }

    document.getElementById('forgotModal')?.addEventListener('click', (event) => {
        if (event.target.id === 'forgotModal') {
            closeForgotModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeForgotModal();
        }
    });

    (function initPasswordToggle() {
        const input = document.getElementById('password');
        const toggle = document.getElementById('passwordToggle');
        if (!input || !toggle) return;

        toggle.addEventListener('click', () => {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            toggle.classList.toggle('showing', isHidden);
            toggle.setAttribute('aria-pressed', String(isHidden));
            toggle.setAttribute('aria-label', isHidden ? 'Sembunyikan kata sandi' : 'Lihat kata sandi');
        });
    })();

    (function initLoginHeroMotion() {
        const hero = document.querySelector('.auth-image');
        if (!hero) return;

        hero.addEventListener('pointermove', (event) => {
            const rect = hero.getBoundingClientRect();
            const x = ((event.clientX - rect.left) / rect.width) * 100;
            const y = ((event.clientY - rect.top) / rect.height) * 100;

            hero.style.setProperty('--hero-x', `${x}%`);
            hero.style.setProperty('--hero-y', `${y}%`);
            hero.style.backgroundPosition = `${50 + (x - 50) * .035}% ${50 + (y - 50) * .035}%`;
            hero.style.backgroundSize = '112%';
        });

        hero.addEventListener('pointerleave', () => {
            hero.style.removeProperty('--hero-x');
            hero.style.removeProperty('--hero-y');
            hero.style.backgroundPosition = 'center';
            hero.style.backgroundSize = '108%';
        });
    })();
</script>
@endpush
