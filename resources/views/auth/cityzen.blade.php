@php
    $isRegister = ($mode ?? 'login') === 'register';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $isRegister ? 'Register' : 'Login' }} | CityZen</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    <style>
        :root {
            --surface: #f8f9fa;
            --surface-low: #eef4f0;
            --ink: #191c1d;
            --muted: #566154;
            --line: #191c1d;
            --primary: #154212;
            --primary-2: #2d6429;
            --mint: #bcf0ae;
            --peach: #ffdbca;
            --danger: #b42318;
            --shadow: 12px 12px 0 rgba(25, 28, 29, 0.1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(188, 240, 174, 0.5), transparent 34rem),
                linear-gradient(135deg, var(--surface), #eef4f0);
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
            border-right: 1.5px solid var(--line);
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
            border: 1.5px solid var(--line);
            border-radius: 8px;
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
            border: 1.5px solid var(--line);
            border-radius: 8px;
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
            border: 1.5px solid var(--line);
            border-radius: 8px;
            color: var(--ink);
            font: inherit;
            min-height: 48px;
            outline: none;
            padding: 12px 14px;
        }

        input:focus {
            box-shadow: 0 0 0 4px rgba(188, 240, 174, 0.55);
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
                    <strong>SDG 11</strong>
                    <small>Sustainable cities focus</small>
                </div>
                <div class="signal">
                    <strong>4.2</strong>
                    <small>Community space score</small>
                </div>
                <div class="signal">
                    <strong>20+</strong>
                    <small>Spaces verified monthly</small>
                </div>
            </div>
        </section>

        <section class="panel-wrap" aria-label="{{ $isRegister ? 'Register' : 'Login' }} form">
            <div class="panel">
                <h2>{{ $isRegister ? 'Create account' : 'Login' }}</h2>
                <p>{{ $isRegister ? 'Start your CityZen profile with a name, email, and password.' : 'Use your email and password to enter CityZen.' }}</p>

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
                        <input id="password" name="password" type="password" autocomplete="{{ $isRegister ? 'new-password' : 'current-password' }}" minlength="4" required>
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
            </div>
        </section>
    </main>
</body>
</html>
