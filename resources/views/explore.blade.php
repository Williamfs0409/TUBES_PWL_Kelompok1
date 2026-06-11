<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CityZen Explore</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-dashboard-page cz-explore-page" data-explore-page data-theme="light">
    @php
        $user = session('cityzen_user', ['name' => 'CityZen User', 'email' => 'member@cityzen.local']);
        $nameParts = collect(explode(' ', trim($user['name'] ?? 'CityZen User')))->filter()->values();
        $initials = $nameParts->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') ?: 'CZ';
        $handle = '@'.str(str($user['email'] ?? 'member@cityzen.local')->before('@'))->replace(['.', '_', '-'], ' ')->slug('_');
    @endphp

    <div class="cz-dash-shell">
        @include('partials.dashboard-sidebar', ['activeNav' => 'explore'])

        <main class="cz-explore-main">
            <header class="cz-explore-searchbar">
                <label class="cz-dash-search">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7" /><path d="m16 16 4 4" /></svg>
                    <input type="search" placeholder="Search public spaces" data-explore-search>
                </label>
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

            <nav class="cz-explore-tabs" aria-label="Explore categories">
                <button class="is-active" type="button">For You</button>
                @foreach ($categories as $category)
                    <button type="button" data-explore-chip="{{ strtolower($category) }}">{{ $category }}</button>
                @endforeach
            </nav>

            <section class="cz-explore-section" aria-label="Explore CityZen places">
                <h1>Explore Public Spaces</h1>

                @forelse ($places as $place)
                    <article
                        class="cz-explore-item"
                        data-explore-item
                        data-title="{{ strtolower($place['category'].' '.$place['title'].' '.$place['description'].' '.$place['location']) }}"
                    >
                        <span>{{ $place['category'] }} &middot; Trending</span>
                        <h2>{{ $place['title'] }}</h2>
                        <p>{{ $place['description'] }}</p>
                        <small>{{ $place['location'] }} &middot; {{ $place['meta'] }}</small>
                    </article>
                @empty
                    <article class="cz-dash-empty" data-explore-item data-title="">
                        <h2>Belum ada data explore.</h2>
                        <p>Halaman Explore akan menampilkan ruang publik, kategori, dan aktivitas setelah database terisi.</p>
                        <a href="{{ url('/places/create') }}">Tambah kontribusi</a>
                    </article>
                @endforelse
            </section>
        </main>

        <aside class="cz-explore-rail" aria-label="Explore side panel">
            <section class="cz-explore-card">
                <h2>Recent Reports</h2>
                @forelse ($reports as $report)
                    <article>
                        <strong>{{ $report['place_name'] }}</strong>
                        <span>{{ $report['category'] }} &middot; {{ $report['status'] }}</span>
                    </article>
                @empty
                    <p>Belum ada laporan terbaru.</p>
                @endforelse
            </section>

            <section class="cz-explore-card">
                <h2>Recommended</h2>
                @forelse ($places->take(3) as $place)
                    <article>
                        <strong>{{ $place['title'] }}</strong>
                        <span>{{ $place['category'] }} &middot; {{ $place['meta'] }}</span>
                    </article>
                @empty
                    <p>Rekomendasi akan muncul setelah ada data tempat.</p>
                @endforelse
            </section>
        </aside>
    </div>

    <div class="cz-dash-toast" role="status" aria-live="polite" data-dashboard-toast></div>
</body>
</html>
