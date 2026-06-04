<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CityZen Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="cz-dashboard-page"
    data-dashboard-awal
    data-dashboard-flash="{{ session('status') }}"
    data-report-url="{{ url('/places/create') }}"
>
    @php
        $user = session('cityzen_user', ['name' => 'CityZen User', 'email' => 'member@cityzen.local']);
        $places = [
            ['title' => 'Central Park Commons', 'location' => 'Menteng, Jakarta', 'image' => 'central-park.jpg', 'status' => 'Open space', 'score' => '94', 'reports' => '128', 'tag' => 'Low emission'],
            ['title' => 'Eco Zen Garden', 'location' => 'Ciumbuleuit, Bandung', 'image' => 'eco-zen.jpg', 'status' => 'Community garden', 'score' => '91', 'reports' => '86', 'tag' => 'Urban farming'],
            ['title' => 'Riverfront Walk', 'location' => 'Kali Code, Yogyakarta', 'image' => 'riverfront.jpg', 'status' => 'Waterfront', 'score' => '88', 'reports' => '74', 'tag' => 'Clean river'],
            ['title' => 'Skyline Greens', 'location' => 'BSD City, Tangerang', 'image' => 'skyline-greens.jpg', 'status' => 'Green corridor', 'score' => '90', 'reports' => '103', 'tag' => 'Walkable'],
            ['title' => 'Solar Loop Plaza', 'location' => 'Sudirman, Jakarta', 'image' => 'solar-loop.jpg', 'status' => 'Transit plaza', 'score' => '86', 'reports' => '64', 'tag' => 'Solar powered'],
            ['title' => 'Urban Canopy Hub', 'location' => 'Tunjungan, Surabaya', 'image' => 'urban-canopy.jpg', 'status' => 'Shade network', 'score' => '89', 'reports' => '97', 'tag' => 'Heat relief'],
        ];
    @endphp

    <aside class="cz-dash-sidebar" aria-label="Dashboard navigation">
        <a class="cz-dash-brand" href="{{ url('/') }}">CityZen</a>
        <nav>
            <a class="is-active" href="#overview">Overview</a>
            <a href="#places">Places</a>
            <a href="#reports">Reports</a>
            <a href="#trending">Trending</a>
            <a href="{{ url('/profile') }}">Profile</a>
        </nav>
        <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <button class="cz-dash-logout" type="submit">Logout</button>
        </form>
    </aside>

    <main class="cz-dash-main">
        <header class="cz-dash-header" id="overview">
            <div>
                <p>Welcome back, {{ $user['name'] }}</p>
                <h1>Your sustainable city dashboard.</h1>
            </div>
            <a class="cz-dash-new-report" href="{{ url('/places/create') }}">New Report</a>
        </header>

        <section class="cz-dash-stats" aria-label="CityZen stats">
            <article><span>Places watched</span><strong>24</strong></article>
            <article><span>Reports sent</span><strong>12</strong></article>
            <article><span>Impact score</span><strong>91%</strong></article>
        </section>

        <section class="cz-dash-toolbar" id="places">
            <div>
                <h2>Explore public spaces</h2>
                <p>Track parks, plazas, and corridors shaped by citizen reports.</p>
            </div>
            <label class="cz-dash-search">
                <span>Search</span>
                <input type="search" placeholder="Search place or city" data-dashboard-search>
            </label>
        </section>

        <section class="cz-dash-grid" aria-live="polite">
            @foreach ($places as $place)
                <article class="cz-dash-place-card" data-place-card data-title="{{ strtolower($place['title'].' '.$place['location'].' '.$place['tag']) }}">
                    <img src="{{ asset('cityzen-dashboard/'.$place['image']) }}" alt="{{ $place['title'] }}">
                    <div class="cz-dash-place-body">
                        <div>
                            <span>{{ $place['status'] }}</span>
                            <h3>{{ $place['title'] }}</h3>
                            <p>{{ $place['location'] }}</p>
                        </div>
                        <dl>
                            <div><dt>Score</dt><dd>{{ $place['score'] }}</dd></div>
                            <div><dt>Reports</dt><dd>{{ $place['reports'] }}</dd></div>
                        </dl>
                        <div class="cz-dash-card-actions">
                        <button type="button" data-like-place>Like</button>
                        <button type="button" data-bookmark-place>Bookmark</button>

                        <a href="{{ route('reports.create', 1) }}" class="cz-dash-report-btn">
                            Report
                        </a>
                    </div>
                    </div>
                </article>
            @endforeach
        </section>

        <section class="cz-dash-bottom" id="reports">
            <article class="cz-dash-panel">
                <h2>Recent report actions</h2>
                @if (session('cityzen_last_report'))
                    <p class="cz-dash-report-note">
                        Last draft: {{ session('cityzen_last_report.place_name') }} &middot;
                        {{ session('cityzen_last_report.issue') }}
                    </p>
                @endif
                <div class="cz-dash-report-list">
                    <button type="button" data-report-action="air quality">Air quality check</button>
                    <button type="button" data-report-action="broken lighting">Broken lighting</button>
                    <button type="button" data-report-action="tree canopy">Tree canopy request</button>
                </div>
            </article>
            <article class="cz-dash-trending" id="trending">
                <h2>Trending now</h2>
                <button type="button" data-trending-place="Riverfront Walk">Riverfront Walk cleanup</button>
                <button type="button" data-trending-place="Solar Loop Plaza">Solar Loop weekend market</button>
                <button type="button" data-trending-place="Urban Canopy Hub">Urban Canopy cooling route</button>
            </article>
        </section>
    </main>

    <div class="cz-dash-toast" role="status" aria-live="polite" data-dashboard-toast></div>
</body>
</html>
