@php
    $isRegister = ($mode ?? 'login') === 'register';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isRegister ? 'Register' : 'Login' }} | CityZen</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    <style>
        :root {
            --surface: #f8f9fa;
            --surface-low: #eef4f0;
            --ink: #191c1d;
            --muted: #566154;
            --line: #c5d2c4;
            --primary: #154212;
            --primary-2: #2d6429;
            --mint: #bcf0ae;
            --peach: #ffdbca;
            --danger: #b42318;
            --shadow: 0 10px 24px rgba(25, 28, 29, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(188, 240, 174, 0.24), transparent 32rem),
                linear-gradient(135deg, var(--surface), #f1f6f0);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            margin: 0;
            min-height: 100vh;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .page {
            display: grid;
            grid-template-columns: minmax(0, 0.95fr) minmax(320px, 460px);
            min-height: 100vh;
        }

        .intro {
            align-content: center;
            border-right: 1px solid var(--line);
            display: grid;
            gap: 28px;
            padding: clamp(32px, 7vw, 88px);
        }

        .brand {
            color: var(--primary);
            font-family: "Hanken Grotesk", Inter, sans-serif;
            font-size: 34px;
            font-weight: 800;
            width: max-content;
        }

        .intro h1 {
            font-family: "Hanken Grotesk", Inter, sans-serif;
            font-size: clamp(48px, 7vw, 92px);
            letter-spacing: 0;
            line-height: 0.9;
            margin: 0;
            max-width: 780px;
        }

        .intro h1 span {
            color: var(--primary-2);
        }

        .intro p {
            color: var(--muted);
            font-size: clamp(17px, 2vw, 21px);
            line-height: 1.7;
            margin: 0;
            max-width: 660px;
        }

        .signal-grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            max-width: 720px;
        }

        .signal {
            background: rgba(255, 255, 255, 0.68);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 18px;
        }

        .signal strong {
            display: block;
            font-size: 24px;
            font-weight: 900;
        }

        .signal small {
            color: var(--muted);
            display: block;
            font-size: 12px;
            font-weight: 800;
            line-height: 1.4;
            margin-top: 6px;
            text-transform: uppercase;
        }

        .panel-wrap {
            align-items: center;
            display: grid;
            padding: clamp(24px, 5vw, 56px);
        }

        .panel {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: clamp(24px, 5vw, 38px);
        }

        .panel h2 {
            font-family: "Hanken Grotesk", Inter, sans-serif;
            font-size: 38px;
            line-height: 1;
            margin: 0 0 10px;
        }

        .panel > p {
            color: var(--muted);
            line-height: 1.6;
            margin: 0 0 26px;
        }

        .field {
            display: grid;
            gap: 8px;
            margin-bottom: 18px;
        }

        label {
            color: var(--ink);
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
        }

        input {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 10px;
            color: var(--ink);
            font: inherit;
            min-height: 48px;
            outline: none;
            padding: 12px 14px;
        }

        input:focus {
            border-color: var(--primary-2);
            box-shadow: 0 0 0 4px rgba(188, 240, 174, 0.36);
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap input {
            padding-right: 52px;
            width: 100%;
        }

        .password-toggle {
            align-items: center;
            background: transparent;
            border: 0;
            border-radius: 999px;
            color: var(--muted);
            cursor: pointer;
            display: inline-flex;
            height: 38px;
            justify-content: center;
            padding: 0;
            position: absolute;
            right: 6px;
            top: 5px;
            transition: background-color 160ms ease, color 160ms ease, transform 160ms ease;
            width: 38px;
        }

        .password-toggle:hover,
        .password-toggle:focus-visible {
            background: rgba(188, 240, 174, 0.55);
            color: var(--primary);
            outline: none;
            transform: scale(1.04);
        }

        .password-toggle svg {
            fill: none;
            height: 21px;
            stroke: currentColor;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-width: 2;
            width: 21px;
        }

        .password-toggle .eye-off {
            display: none;
        }

        .password-toggle.is-visible .eye {
            display: none;
        }

        .password-toggle.is-visible .eye-off {
            display: block;
        }

        .error {
            color: var(--danger);
            font-size: 13px;
            font-weight: 700;
        }

        .error-list {
            background: #fff1ed;
            border: 1.5px solid var(--danger);
            border-radius: 8px;
            color: var(--danger);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.5;
            margin: 0 0 20px;
            padding: 14px 16px;
        }

        .notice {
            background: #ecfdf3;
            border: 1.5px solid var(--primary-2);
            border-radius: 8px;
            color: var(--primary);
            font-size: 14px;
            font-weight: 800;
            line-height: 1.5;
            margin: 0 0 20px;
            padding: 14px 16px;
        }

        .button {
            align-items: center;
            border: 1.5px solid var(--line);
            border-radius: 999px;
            cursor: pointer;
            display: inline-flex;
            font-weight: 900;
            justify-content: center;
            min-height: 48px;
            padding: 12px 20px;
            transition: box-shadow 160ms ease, transform 160ms ease;
        }

        .button:hover,
        .button:focus-visible {
            box-shadow: 0 8px 20px rgba(25, 28, 29, 0.14);
            transform: translateY(-1px);
        }

        .button--primary {
            background: var(--primary-2);
            border-color: var(--primary-2);
            color: #ffffff;
            width: 100%;
        }

        .switch {
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.5;
            margin: 20px 0 0;
            text-align: center;
        }

        .switch a {
            color: var(--primary);
            font-weight: 900;
            text-decoration: underline;
            text-underline-offset: 4px;
        }

        .home-link {
            color: var(--muted);
            display: inline-block;
            font-size: 14px;
            font-weight: 800;
            margin-top: 18px;
            text-align: center;
            width: 100%;
        }

        /* Visual polish: calmer auth surface while preserving the current layout. */
        body {
            background: #f6f8f2;
            color: #161d17;
        }

        .page {
            background: #f6f8f2;
        }

        .brand {
            letter-spacing: -0.02em;
        }

        .intro {
            border-right-color: rgba(30, 45, 32, 0.22);
            gap: clamp(28px, 6vw, 64px);
        }

        .intro h1 {
            letter-spacing: -0.045em;
            line-height: 0.96;
        }

        .intro p {
            color: #536052;
            line-height: 1.66;
            max-width: 680px;
        }

        .signal {
            background: rgba(255, 255, 250, 0.78);
            border-width: 1px;
            border-radius: 10px;
            box-shadow: 2px 3px 0 rgba(25, 28, 29, 0.045);
        }

        .signal strong {
            color: #1b5226;
        }

        .panel {
            background: #fffef9;
            border-width: 1px;
            border-radius: 12px;
            box-shadow: 3px 4px 0 rgba(25, 28, 29, 0.06);
        }

        .panel h2 {
            letter-spacing: -0.035em;
        }

        .panel > p {
            color: #536052;
        }

        input {
            background: #fbfcf7;
            border-width: 1px;
            border-radius: 10px;
            transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
        }

        input:focus {
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(49, 112, 58, 0.14);
        }

        .password-toggle {
            border-radius: 999px;
            color: #344337;
            transition: background 160ms ease, color 160ms ease, transform 160ms ease;
        }

        .password-toggle:hover,
        .password-toggle:focus-visible {
            background: rgba(216, 235, 207, 0.82);
            transform: translateY(-1px);
        }

        .button {
            border-width: 1px;
            box-shadow: none;
            letter-spacing: 0;
            min-height: 44px;
            transition: background 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .button:hover,
        .button:focus-visible {
            box-shadow: 0 6px 14px rgba(25, 28, 29, 0.08);
            transform: translateY(-1px);
        }

        label {
            letter-spacing: 0.075em;
        }

        .field {
            gap: 7px;
            margin-bottom: 16px;
        }

        .error-list,
        .notice {
            border-width: 1px;
            border-radius: 10px;
        }

        .home-link {
            color: #536052;
        }

        @media (max-width: 900px) {
            .page {
                grid-template-columns: 1fr;
            }

            .intro {
                border-right: 0;
                border-bottom: 1.5px solid var(--line);
                min-height: auto;
            }

            .signal-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <section class="intro" aria-label="CityZen welcome">
            <a class="brand" href="{{ url('/') }}">CityZen</a>
            <div>
                <h1>{{ $isRegister ? 'Join the urban care network.' : 'Welcome back, city builder.' }}</h1>
                <p>
                    {{ $isRegister
                        ? 'Create a CityZen account to start mapping public spaces, sharing reviews, and reporting facilities that need attention.'
                        : 'Sign in to continue contributing public space insights for a more sustainable city.' }}
                </p>
            </div>
            <div class="signal-grid" aria-label="Community signals">
                <div class="signal">
                    <strong>Map</strong>
                    <small>Discover public space context</small>
                </div>
                <div class="signal">
                    <strong>Report</strong>
                    <small>Turn concerns into civic data</small>
                </div>
                <div class="signal">
                    <strong>Improve</strong>
                    <small>Track what communities need</small>
                </div>
            </div>
        </section>

        <section class="panel-wrap" aria-label="{{ $isRegister ? 'Register' : 'Login' }} form">
            <div class="panel">
                <h2>{{ $isRegister ? 'Create account' : 'Login' }}</h2>
                <p>{{ $isRegister ? 'Start your CityZen profile with a name, email, and password.' : 'Use your email and password to enter CityZen.' }}</p>

                @if (session('notice'))
                    <div class="notice">{{ session('notice') }}</div>
                @endif

                @if ($errors->any())
                    <div class="error-list">
                        Please check the highlighted fields and try again.
                    </div>
                @endif

                <form method="POST" action="{{ $isRegister ? url('/register') : url('/login') }}">
                    @csrf

                    @if ($isRegister)
                        <div class="field">
                            <label for="name">Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required>
                            @error('name')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                        @error('email')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="password-wrap">
                            <input id="password" name="password" type="password" autocomplete="{{ $isRegister ? 'new-password' : 'current-password' }}" minlength="4" required>
                            <button class="password-toggle" type="button" data-password-toggle aria-label="Show password" aria-pressed="false">
                                <svg class="eye" viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" /><circle cx="12" cy="12" r="3" /></svg>
                                <svg class="eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="m3 3 18 18" /><path d="M10.7 5.2A10.4 10.4 0 0 1 12 5c6 0 9.5 7 9.5 7a17.6 17.6 0 0 1-3.1 4.1" /><path d="M6.5 6.8A17.6 17.6 0 0 0 2.5 12s3.5 7 9.5 7a10.4 10.4 0 0 0 4.1-.8" /><path d="M9.9 9.9A3 3 0 0 0 14.1 14.1" /></svg>
                            </button>
                        </div>
                        @error('password')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button class="button button--primary" type="submit">
                        {{ $isRegister ? 'Register' : 'Login' }}
                    </button>
                </form>

                <p class="switch">
                    {{ $isRegister ? 'Already part of CityZen?' : 'New to CityZen?' }}
                    <a href="{{ $isRegister ? url('/login') : url('/register') }}">
                        {{ $isRegister ? 'Login' : 'Register' }}
                    </a>
                </p>
                <a class="home-link" href="{{ url('/') }}">Back to landing page</a>
            </div>
        </section>
    </main>
    <script>
        document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
            const input = toggle.closest('.password-wrap')?.querySelector('input');

            toggle.addEventListener('click', () => {
                if (!input) return;

                const isVisible = input.type === 'text';
                input.type = isVisible ? 'password' : 'text';
                toggle.classList.toggle('is-visible', !isVisible);
                toggle.setAttribute('aria-pressed', String(!isVisible));
                toggle.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
                input.focus();
            });
        });
    </script>
</body>
</html>
