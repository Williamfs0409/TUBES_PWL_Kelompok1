<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Places | CityZen</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-dashboard-page cz-list-page" data-theme="light">
    <div class="cz-dash-shell">
        @include('partials.dashboard-sidebar', ['activeNav' => 'home'])

        <main class="cz-list-main">
            <header class="cz-list-header">
                <div class="cz-list-heading">
                    <span>Place database</span>
                    <h1>Places</h1>
                    <p>Kelola ruang publik yang tersimpan di database CityZen.</p>
                </div>
                <a class="cz-list-action" href="{{ url('/places/create') }}">Tambah Place</a>
            </header>

            @if (session('status'))
                <div class="cz-form-alert cz-form-alert--success">{{ session('status') }}</div>
            @endif

            <section class="cz-list-stack">
                @forelse ($places as $place)
                    <article class="cz-list-card">
                        <span class="cz-list-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M12 21s7-5.2 7-12A7 7 0 1 0 5 9c0 6.8 7 12 7 12Z" /><circle cx="12" cy="9" r="2.2" /></svg>
                        </span>
                        <div class="cz-list-copy">
                            <div class="cz-list-eyebrow">{{ $place->category->name ?? 'Public Space' }} &middot; {{ $place->status }}</div>
                            <h2 class="cz-list-title">{{ $place->name }}</h2>
                            <p class="cz-list-meta">{{ $place->short_description ?: $place->description }}</p>
                            <p class="cz-list-meta">{{ collect([$place->city, $place->province])->filter()->implode(', ') ?: 'Lokasi belum diisi' }} &middot; {{ number_format((float) $place->average_rating, 1) }} rating &middot; {{ $place->likes_count }} likes</p>
                        </div>
                        <div class="cz-list-card-actions">
                            <a class="cz-list-action" href="{{ url('/places/'.$place->id.'/edit') }}">Edit</a>
                            <form method="POST" action="{{ url('/places/'.$place->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="cz-list-danger" type="submit" onclick="return confirm('Yakin ingin menghapus place ini?')">Hapus</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <article class="cz-list-empty">
                        <h2>Belum ada place.</h2>
                        <p>Tambahkan ruang publik pertama agar feed dashboard dan explore mulai terisi dari database.</p>
                        <a href="{{ url('/places/create') }}">Tambah place pertama</a>
                    </article>
                @endforelse
            </section>
        </main>
    </div>
</body>
</html>
