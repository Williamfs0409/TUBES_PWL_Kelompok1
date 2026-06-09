<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Notifications | CityZen</title>
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
        @include('partials.dashboard-sidebar', ['activeNav' => 'notifications'])

        <main class="cz-list-main cz-list-main--focused">
            <header class="cz-list-topbar">
                <h1>Notifications</h1>
                <button class="cz-dash-theme-toggle" type="button" data-theme-toggle aria-pressed="false">
                    <span class="cz-theme-sun" aria-hidden="true"></span>
                    <span data-theme-label>Dark mode</span>
                </button>
            </header>

            <nav class="cz-notification-filters" aria-label="Notification filters">
                <a class="is-active" href="{{ url('/notifications') }}">All</a>
                <a href="{{ url('/notifications') }}">Unread</a>
                <a href="{{ url('/notifications') }}">Reports</a>
                <a href="{{ url('/notifications') }}">System</a>
            </nav>

            <section class="cz-notification-list">
                @forelse ($notifications as $notification)
                    <article class="cz-notification-card {{ $notification->read_at ? '' : 'is-unread' }}">
                        <span class="cz-notification-icon" aria-hidden="true">
                            @if (str($notification->type_name ?? '')->lower()->contains('report'))
                                <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-5" /><circle cx="12" cy="12" r="9" /></svg>
                            @elseif (str($notification->type_name ?? '')->lower()->contains('badge'))
                                <svg viewBox="0 0 24 24"><path d="M8 4h8v7a4 4 0 0 1-8 0V4Z" /><path d="m10 15-1 5 3-2 3 2-1-5" /></svg>
                            @else
                                <svg viewBox="0 0 24 24"><path d="M4 5h16v14H4z" /><path d="M8 9h8" /><path d="M8 13h5" /></svg>
                            @endif
                        </span>
                        <div class="cz-notification-copy">
                            <div class="cz-notification-row">
                                <h2>{{ $notification->title }}</h2>
                                <span>{{ $notification->created_at ? \Illuminate\Support\Carbon::parse($notification->created_at)->diffForHumans() : 'recently' }}</span>
                            </div>
                            <p>{{ $notification->message ?: 'Tidak ada detail tambahan.' }}</p>
                            <small>{{ $notification->actor_name ? 'From '.$notification->actor_name : ($notification->type_name ?? 'CityZen system') }}</small>
                        </div>
                        @unless ($notification->read_at)
                            <span class="cz-notification-badge">Unread</span>
                        @endunless
                        <button class="cz-icon-dots" type="button" aria-label="Notification options">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="5" r="1" /><circle cx="12" cy="12" r="1" /><circle cx="12" cy="19" r="1" /></svg>
                        </button>
                    </article>
                @empty
                    <article class="cz-notification-empty">
                        <h2>Belum ada notifikasi.</h2>
                        <p>Notifikasi akan muncul setelah ada aktivitas seperti laporan diverifikasi, badge baru, atau interaksi warga.</p>
                        <a href="{{ url('/dashboard') }}">Kembali ke dashboard</a>
                    </article>
                @endforelse
            </section>
        </main>

        <aside class="cz-dash-right-rail" aria-label="Notification summary">
            <section class="cz-list-side-card">
                <h2>Inbox</h2>
                <p>{{ $notifications->whereNull('read_at')->count() }} unread dari {{ $notifications->count() }} notifikasi.</p>
                <a href="{{ url('/dashboard') }}">Dashboard</a>
            </section>

            <footer class="cz-dash-rail-footer">
                <a href="{{ url('/') }}">CityZen Charter</a>
                <a href="{{ url('/bookmarks') }}">Bookmarks</a>
                <span>&copy; {{ date('Y') }} CityZen Corp.</span>
            </footer>
        </aside>
    </div>
</body>
</html>
