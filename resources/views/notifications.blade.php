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

            <nav class="cz-notification-filters" aria-label="Notification filters">
                <a class="is-active" href="{{ url('/notifications') }}">All</a>
                <a href="{{ url('/notifications') }}">Unread</a>
                <a href="{{ url('/notifications') }}">Reports</a>
                <a href="{{ url('/notifications') }}">System</a>
            </nav>

            <section class="cz-notification-list">
                @forelse ($notifications as $notification)
                    @php
                        $title = $notification->title ?? 'CityZen notification';
                        $isRepost = str($title)->lower()->contains('reposted');
                        $isLike = str($title)->lower()->contains('liked');
                        $actorName = $notification->actor_name ?: 'CityZen';
                        $actorInitial = strtoupper(substr($actorName, 0, 1));
                        $placeUrl = ($notification->related_table === 'places' && $notification->related_id) ? route('places.show', $notification->related_id) : null;
                        $time = $notification->updated_at ?: $notification->created_at;
                    @endphp
                    <article class="cz-notification-card cz-social-notification-card {{ $notification->read_at ? '' : 'is-unread' }}">
                        <span class="cz-notification-icon cz-social-notification-icon {{ $isRepost ? 'is-repost' : ($isLike ? 'is-like' : '') }}" aria-hidden="true">
                            @if ($isRepost)
                                <svg viewBox="0 0 24 24"><path d="M17 1l4 4-4 4" /><path d="M3 11V9a4 4 0 0 1 4-4h14" /><path d="M7 23l-4-4 4-4" /><path d="M21 13v2a4 4 0 0 1-4 4H3" /></svg>
                            @elseif ($isLike)
                                <svg viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z" /></svg>
                            @else
                                <svg viewBox="0 0 24 24"><path d="M4 5h16v14H4z" /><path d="M8 9h8" /><path d="M8 13h5" /></svg>
                            @endif
                        </span>
                        <div class="cz-social-notification-body">
                            <a class="cz-social-notification-avatar" href="{{ $placeUrl ?? url('/notifications') }}" aria-label="Open related notification">
                                @if ($notification->actor_id && $notification->actor_avatar_path)
                                    <img src="{{ route('users.avatar', $notification->actor_id) }}" alt="">
                                @else
                                    <span>{{ $actorInitial }}</span>
                                @endif
                            </a>
                            <div class="cz-notification-copy">
                                <div class="cz-notification-row">
                                    <h2>
                                        <strong>{{ $actorName }}</strong>
                                        {{ $isRepost ? 'reposted your post' : ($isLike ? 'liked your post' : $title) }}
                                    </h2>
                                    <span>{{ $time ? \Illuminate\Support\Carbon::parse($time)->diffForHumans() : 'recently' }}</span>
                                </div>
                                <p>{{ $notification->message ?: 'Ada aktivitas baru di CityZen.' }}</p>
                                <small>{{ $notification->place_name ?: ($notification->type_name ?? 'CityZen system') }}</small>
                            </div>
                        </div>
                        @if ($placeUrl)
                            <a class="cz-social-notification-thumb" href="{{ $placeUrl }}" aria-label="Open post">
                                <img src="{{ route('places.image', $notification->related_id) }}" alt="">
                            </a>
                        @endif
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
