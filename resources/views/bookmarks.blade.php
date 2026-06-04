<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bookmarks | CityZen</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-dashboard-page cz-list-page" data-theme="light">
    @php
        $user = session('cityzen_user', ['name' => 'CityZen User', 'email' => 'member@cityzen.local']);
        $nameParts = collect(explode(' ', trim($user['name'] ?? 'CityZen User')))->filter()->values();
        $initials = $nameParts->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') ?: 'CZ';
        $handle = '@'.str(str($user['email'] ?? 'member@cityzen.local')->before('@'))->replace(['.', '_', '-'], ' ')->slug('_');
    @endphp

    <div class="cz-dash-shell">
        <aside class="cz-dash-sidebar" aria-label="CityZen navigation">
            <div class="cz-dash-brand-block">
                <a class="cz-dash-brand" href="{{ url('/') }}" aria-label="CityZen landing page">
                    <span class="cz-dash-brand-mark" aria-hidden="true">
                        <svg viewBox="0 0 24 24" role="img"><path d="M18.5 4.7c-6.7.6-11.9 4-13.4 8.2-1.2 3.4.8 6.4 4.2 6.4 4.2 0 7.9-4.7 9.2-14.6Z" /><path d="M7.5 15.5c2.8-.5 5.3-2.2 7.4-5.1" /></svg>
                    </span>
                    <span><strong>CityZen</strong><small>Civic Control</small></span>
                </a>
            </div>

            <nav class="cz-dash-nav" aria-label="Dashboard menu">
                <a class="cz-dash-nav-link" href="{{ url('/dashboard') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 11 8-7 8 7v8a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-8Z" /></svg><span>Home</span></a>
                <a class="cz-dash-nav-link" href="{{ url('/explore') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15.5 8.5-2.1 5.9-5.9 2.1 2.1-5.9 5.9-2.1Z" /><circle cx="12" cy="12" r="9" /></svg><span>Explore</span></a>
                <a class="cz-dash-nav-link" href="{{ url('/notifications') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 9a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" /><path d="M10 20a2 2 0 0 0 4 0" /></svg><span>Notifications</span></a>
                <a class="cz-dash-nav-link is-active" href="{{ url('/bookmarks') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4h12v17l-6-4-6 4V4Z" /></svg><span>Bookmarks</span></a>
                <a class="cz-dash-nav-link" href="{{ url('/profile') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path d="M4 20c1.6-4 14.4-4 16 0" /></svg><span>Profile</span></a>
                @if ($isAdmin ?? false)
                    <a class="cz-dash-nav-link" href="{{ url('/admin/reports') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4z" /><path d="M8 9h8" /><path d="M8 13h5" /><path d="M8 17h3" /></svg><span>Admin</span></a>
                @endif
            </nav>

            <div class="cz-dash-sidebar-bottom">
                <div class="cz-dash-user-card">
                    <span class="cz-dash-avatar">{{ $initials }}</span>
                    <span class="cz-dash-user-copy"><strong>{{ $user['name'] }}</strong><small>{{ $handle }}</small></span>
                    <form method="POST" action="{{ url('/logout') }}">@csrf<button class="cz-dash-icon-button" type="submit" aria-label="Logout"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8V5a1 1 0 0 0-1-1H5v16h8a1 1 0 0 0 1-1v-3" /><path d="M10 12h10" /><path d="m17 9 3 3-3 3" /></svg></button></form>
                </div>
            </div>
        </aside>

        <main class="cz-list-main">
            <header class="cz-list-header">
                <span>Saved places</span>
                <h1>Bookmarks</h1>
                <p>Tempat yang kamu simpan dari dashboard akan tampil di sini langsung dari tabel bookmarks.</p>
            </header>

            <section class="cz-list-stack">
                @forelse ($bookmarks as $bookmark)
                    <article class="cz-list-card">
                        <span class="cz-list-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M6 4h12v17l-6-4-6 4V4Z" /></svg>
                        </span>
                        <div class="cz-list-copy">
                            <div class="cz-list-eyebrow">{{ $bookmark->category_name ?? 'Public Space' }} &middot; saved {{ $bookmark->saved_at ? \Illuminate\Support\Carbon::parse($bookmark->saved_at)->diffForHumans() : 'recently' }}</div>
                            <h2 class="cz-list-title">{{ $bookmark->name }}</h2>
                            <p class="cz-list-meta">{{ $bookmark->short_description ?: 'Belum ada deskripsi singkat.' }}</p>
                            <p class="cz-list-meta">{{ collect([$bookmark->city, $bookmark->province])->filter()->implode(', ') ?: 'Lokasi belum diisi' }} &middot; {{ number_format((float) $bookmark->average_rating, 1) }} rating &middot; {{ $bookmark->likes_count }} likes &middot; {{ $bookmark->reviews_count }} reviews</p>
                        </div>
                        <a class="cz-list-action" href="{{ url('/explore') }}">Lihat tempat</a>
                    </article>
                @empty
                    <article class="cz-list-empty">
                        <h2>Belum ada bookmark.</h2>
                        <p>Tekan tombol bookmark pada post tempat di dashboard untuk menyimpan tempat favorit.</p>
                        <a href="{{ url('/dashboard') }}">Buka dashboard</a>
                    </article>
                @endforelse
            </section>
        </main>
    </div>
</body>
</html>
