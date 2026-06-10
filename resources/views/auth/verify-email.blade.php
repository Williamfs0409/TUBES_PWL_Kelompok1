<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email | CityZen</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-dashboard-page cz-verification-page" data-theme="light">
    <main class="cz-verification-shell">
        <section class="cz-verification-card">
            <a class="cz-verification-brand" href="{{ url('/') }}">
                <span class="cz-dash-brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24" role="img">
                        <path d="M18.5 4.7c-6.7.6-11.9 4-13.4 8.2-1.2 3.4.8 6.4 4.2 6.4 4.2 0 7.9-4.7 9.2-14.6Z" />
                        <path d="M7.5 15.5c2.8-.5 5.3-2.2 7.4-5.1" />
                    </svg>
                </span>
                <strong>CityZen</strong>
            </a>

            <p class="cz-verification-eyebrow">Email verification</p>
            <h1>Cek email kamu dulu.</h1>
            <p>Kami mengirim link verifikasi ke <strong>{{ $email }}</strong>. Klik link tersebut agar dashboard, upload tempat, report, dan fitur komunitas CityZen aktif.</p>

            @if (session('status'))
                <div class="cz-verification-status">{{ session('status') }}</div>
            @endif

            @if (session('notice'))
                <div class="cz-verification-notice">{{ session('notice') }}</div>
            @endif

            @if (session('mail_error'))
                <div class="cz-verification-notice">
                    <strong>SMTP detail:</strong> {{ session('mail_error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit">Kirim ulang email</button>
            </form>

            <form method="POST" action="{{ url('/logout') }}">
                @csrf
                <button class="is-secondary" type="submit">Logout</button>
            </form>
        </section>
    </main>
</body>
</html>
