<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bookmarks | CityZen</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
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
        @include('partials.dashboard-sidebar', ['activeNav' => 'bookmarks'])

        <main class="cz-list-main cz-list-main--focused">
            <header class="cz-list-topbar">
                <h1>Bookmarks</h1>
                <label class="cz-dash-theme-toggle switch" aria-label="Toggle dark mode">
                    <input class="switch__input" type="checkbox" role="switch" data-theme-toggle aria-pressed="false">
                    <span class="switch__icon" aria-hidden="true">
                        <span class="switch__icon-part switch__icon-part--1"></span>
                        <span class="switch__icon-part switch__icon-part--2"></span>
                        <span class="switch__icon-part switch__icon-part--3"></span>
                        <span class="switch__icon-part switch__icon-part--4"></span>
                        <span class="switch__icon-part switch__icon-part--5"></span>
                        <span class="switch__icon-part switch__icon-part--6"></span>
                        <span class="switch__icon-part switch__icon-part--7"></span>
                        <span class="switch__icon-part switch__icon-part--8"></span>
                        <span class="switch__icon-part switch__icon-part--9"></span>
                        <span class="switch__icon-part switch__icon-part--10"></span>
                        <span class="switch__icon-part switch__icon-part--11"></span>
                    </span>
                    <span class="switch__sr" data-theme-label>Dark Mode</span>
                </label>
            </header>

            <form class="cz-bookmark-search" action="{{ url('/bookmarks') }}" method="GET">
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" /><path d="m20 20-3.5-3.5" /></svg>
                <input name="q" value="{{ request('q') }}" placeholder="Search bookmarks" aria-label="Search bookmarks">
            </form>

            <section class="cz-bookmark-feed">
                @forelse ($bookmarks as $bookmark)
                    <article class="cz-bookmark-post">
                        <div class="cz-bookmark-avatar">{{ strtoupper(substr($bookmark->name, 0, 1)) }}</div>
                        <div class="cz-bookmark-body">
                            <header>
                                <div>
                                    <strong>{{ $bookmark->name }}</strong>
                                    <span>{{ $bookmark->category_name ?? 'Public Space' }} &middot; saved {{ $bookmark->saved_at ? \Illuminate\Support\Carbon::parse($bookmark->saved_at)->diffForHumans() : 'recently' }}</span>
                                </div>
                                <button class="cz-icon-dots" type="button" aria-label="Bookmark options">
                                    <svg viewBox="0 0 24 24"><circle cx="5" cy="12" r="1" /><circle cx="12" cy="12" r="1" /><circle cx="19" cy="12" r="1" /></svg>
                                </button>
                            </header>
                            <p>{{ $bookmark->short_description ?: 'Belum ada deskripsi singkat untuk tempat ini.' }}</p>
                            <div class="cz-bookmark-place-meta">
                                <span>{{ collect([$bookmark->city, $bookmark->province])->filter()->implode(', ') ?: 'Lokasi belum diisi' }}</span>
                                <span>{{ number_format((float) $bookmark->average_rating, 1) }} rating</span>
                            </div>
                            <footer>
                                <span>{{ $bookmark->likes_count }} likes</span>
                                <span>{{ $bookmark->reviews_count }} reviews</span>
                                <a href="{{ url('/explore') }}">Open</a>
                                <span class="cz-bookmark-saved" aria-label="Saved bookmark">
                                    <svg viewBox="0 0 24 24"><path d="M6 4h12v17l-6-4-6 4V4Z" /></svg>
                                </span>
                            </footer>
                        </div>
                    </article>
                @empty
                    <article class="cz-notification-empty">
                        <h2>Belum ada bookmark.</h2>
                        <p>Tekan tombol bookmark pada post tempat di dashboard untuk menyimpan tempat favorit.</p>
                        <a href="{{ url('/dashboard') }}">Buka dashboard</a>
                    </article>
                @endforelse
            </section>
        </main>

        <aside class="cz-dash-right-rail" aria-label="Bookmark summary">
            <section class="cz-list-side-card">
                <h2>Saved</h2>
                <p>{{ $bookmarks->count() }} tempat tersimpan dari database.</p>
                <a href="{{ url('/dashboard') }}">Cari tempat</a>
            </section>

            <footer class="cz-dash-rail-footer">
                <a href="{{ url('/') }}">CityZen Charter</a>
                <a href="{{ url('/notifications') }}">Notifications</a>
                <span>&copy; {{ date('Y') }} CityZen Corp.</span>
            </footer>
        </aside>
    </div>
</body>
</html>
