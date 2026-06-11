<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | CityZen</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-dashboard-page cz-profile-page" data-theme="light">
    @php
        $user = session('cityzen_user', ['name' => 'CityZen User', 'email' => 'member@cityzen.local']);
        $nameParts = collect(explode(' ', trim($user['name'] ?? 'CityZen User')))->filter()->values();
        $initials = $nameParts->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') ?: 'CZ';
        $handle = '@'.str(str($user['email'] ?? 'member@cityzen.local')->before('@'))->replace(['.', '_', '-'], ' ')->slug('_');
        $lastReport = session('cityzen_last_report');
    @endphp

    <div class="cz-dash-shell">
        @include('partials.dashboard-sidebar', ['activeNav' => 'profile'])

        <main class="cz-profile-main">
            <section class="cz-profile-hero">
                <span class="cz-profile-avatar">{{ $initials }}</span>
                <div>
                    <span class="cz-profile-eyebrow">Citizen profile</span>
                    <h1>{{ $user['name'] }}</h1>
                    <p>
                        {{ $user['email'] }}
                        @if (! empty($profile->username))
                            &middot; {{ '@'.$profile->username }}
                        @endif
                        @if (! empty($profile->city))
                            &middot; {{ $profile->city }}
                        @endif
                    </p>
                    @if (! empty($profile->bio))
                        <p>{{ $profile->bio }}</p>
                    @else
                        <p>Verified CityZen contributor. Bio bisa diisi dari menu Settings pada kartu akun.</p>
                    @endif
                    <div class="cz-profile-actions">
                        <a class="cz-profile-button cz-profile-button--secondary" href="{{ url('/dashboard') }}">Explore Dashboard</a>
                        <a class="cz-profile-button cz-profile-button--primary" href="{{ url('/settings') }}">Edit Profile</a>
                    </div>
                </div>
            </section>

            <section class="cz-profile-grid" aria-label="Impact stats">
                <article>
                    <span>Watched places</span>
                    <strong>{{ $stats['watched_places'] ?? 0 }}</strong>
                    <p>Tempat yang kamu simpan di bookmark.</p>
                </article>
                <article>
                    <span>Reports sent</span>
                    <strong>{{ $stats['reports_drafted'] ?? 0 }}</strong>
                    <p>Laporan yang sudah tersimpan di database.</p>
                </article>
                <article>
                    <span>Interactions</span>
                    <strong>{{ ($stats['reviews_count'] ?? 0) + ($stats['likes_count'] ?? 0) }}</strong>
                    <p>Total review dan like yang kamu berikan.</p>
                </article>
            </section>

            <section class="cz-profile-report-card {{ ($stats['reports_drafted'] ?? 0) > 0 ? '' : 'is-empty' }}">
                @if (($stats['reports_drafted'] ?? 0) > 0)
                    <span>Report activity</span>
                    <h2>{{ $stats['reports_drafted'] }} laporan terkirim.</h2>
                    <p>Semua laporan yang kamu kirim tersimpan dan bisa diverifikasi oleh admin.</p>
                @else
                    <span>No report yet</span>
                    <h2>Your first report can start here.</h2>
                    <p>Report hanya tersedia dari post tempat di dashboard agar laporan selalu terhubung ke data tempat yang jelas.</p>
                @endif
            </section>

            <section class="cz-profile-activity">
                <span class="cz-profile-eyebrow">Recent activity</span>
                <article>
                    <h3>Dashboard connected</h3>
                    <p>Login and register now lead directly into the protected dashboard.</p>
                </article>
                <article>
                    <h3>Profile connected</h3>
                    <p>Dashboard, report, profile, and logout now share the same session flow.</p>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
