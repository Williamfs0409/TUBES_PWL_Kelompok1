<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Analytics | CityZen</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-dashboard-page cz-admin-shell-page" data-theme="light">
    <div class="cz-dash-shell">
        @include('partials.dashboard-sidebar', ['activeNav' => 'admin', 'isAdmin' => true])

        <main class="cz-admin-main">
            <header class="cz-list-header">
                <div>
                    <span class="cz-profile-eyebrow">Admin control</span>
                    <h1>CityZen Analytics</h1>
                    <p>Ringkasan aktivitas komunitas, laporan, tempat populer, dan kategori paling aktif.</p>
                </div>
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

            @include('admin.partials.nav', ['activeAdmin' => 'dashboard'])

            <section class="cz-admin-stat-grid" aria-label="Admin statistics">
                <article><span>Users</span><strong>{{ $stats['users'] }}</strong><p>Registered accounts</p></article>
                <article><span>Places</span><strong>{{ $stats['places'] }}</strong><p>Public spaces</p></article>
                <article><span>Reports</span><strong>{{ $stats['reports'] }}</strong><p>Total reports</p></article>
                <article><span>Pending</span><strong>{{ $stats['pending_reports'] }}</strong><p>Need verification</p></article>
                <article><span>Reviews</span><strong>{{ $stats['reviews'] }}</strong><p>Community ratings</p></article>
                <article><span>Likes</span><strong>{{ $stats['likes'] }}</strong><p>Feed engagement</p></article>
                <article><span>Bookmarks</span><strong>{{ $stats['bookmarks'] }}</strong><p>Saved places</p></article>
            </section>

            <section class="cz-admin-grid-2">
                <article class="cz-admin-panel">
                    <h2>Top Places</h2>
                    @forelse ($topPlaces as $place)
                        <div class="cz-admin-row">
                            <div>
                                <strong>{{ $place->name }}</strong>
                                <small>{{ $place->category_name ?? 'Public Space' }} &middot; {{ $place->city ?: 'Unknown city' }}</small>
                            </div>
                            <span>{{ $place->likes_count }} likes &middot; {{ $place->reports_count }} reports &middot; {{ number_format((float) $place->average_rating, 1) }}</span>
                        </div>
                    @empty
                        <p>Belum ada place di database.</p>
                    @endforelse
                </article>

                <article class="cz-admin-panel">
                    <h2>Category Activity</h2>
                    @forelse ($categoryActivity as $category)
                        <div class="cz-admin-row">
                            <div>
                                <strong>{{ $category->category_label }}</strong>
                                <small>{{ $category->total }} places</small>
                            </div>
                            <span>{{ $category->total }}</span>
                        </div>
                    @empty
                        <p>Belum ada kategori aktif.</p>
                    @endforelse
                </article>
            </section>
        </main>
    </div>
</body>
</html>
