<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Places | CityZen</title>
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
                    <span class="cz-profile-eyebrow">Post moderation</span>
                    <h1>Places</h1>
                    <p>Moderasi postingan ruang publik, sembunyikan konten bermasalah, atau hapus jika melanggar.</p>
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

            @include('admin.partials.nav', ['activeAdmin' => 'places'])

            @if (session('status'))
                <div class="cz-form-alert cz-form-alert--success">{{ session('status') }}</div>
            @endif

            <section class="cz-admin-report-list">
                @forelse ($places as $place)
                    <article class="cz-admin-report-card">
                        <div>
                            <span>{{ $place->category->name ?? 'Public Space' }} &middot; {{ $place->status }}</span>
                            <h2>{{ $place->name }}</h2>
                            <p>{{ $place->short_description ?: $place->description }}</p>
                            <small>By {{ $place->user->name ?? 'CityZen user' }} &middot; {{ $place->city }} &middot; {{ $place->likes_count }} likes &middot; {{ $place->reports_count }} reports</small>
                        </div>

                        <form method="POST" action="{{ route('admin.places.status', $place) }}">
                            @csrf
                            @method('PATCH')
                            <select name="status" required>
                                @foreach (['active' => 'Active', 'hidden' => 'Hidden', 'rejected' => 'Rejected'] as $value => $label)
                                    <option value="{{ $value }}" @selected($place->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit">Update Status</button>
                        </form>

                        <form method="POST" action="{{ route('admin.places.destroy', $place) }}">
                            @csrf
                            @method('DELETE')
                            <button class="cz-list-danger" type="submit" onclick="return confirm('Hapus postingan place ini?')">Delete Post</button>
                        </form>
                    </article>
                @empty
                    <article class="cz-list-empty">
                        <h2>Belum ada place.</h2>
                        <p>Post tempat publik dari user akan muncul di sini.</p>
                    </article>
                @endforelse
            </section>
        </main>
    </div>
</body>
</html>
