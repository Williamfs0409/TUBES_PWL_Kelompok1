@php
    $activeNav = $activeNav ?? '';
    $user = $user ?? session('cityzen_user', ['name' => 'CityZen User', 'email' => 'member@cityzen.local']);
    $nameParts = collect(explode(' ', trim($user['name'] ?? 'CityZen User')))->filter()->values();
    $initials = $initials ?? ($nameParts->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') ?: 'CZ');
    $handle = $handle ?? ('@'.str(str($user['email'] ?? 'member@cityzen.local')->before('@'))->replace(['.', '_', '-'], ' ')->slug('_'));
    $avatarPath = $user['avatar_path'] ?? null;
@endphp

<aside class="cz-dash-sidebar" aria-label="CityZen navigation">
    <div class="cz-dash-brand-block">
        <a class="cz-dash-brand" href="{{ url('/') }}" aria-label="CityZen landing page">
            <span class="cz-dash-brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" role="img">
                    <path d="M18.5 4.7c-6.7.6-11.9 4-13.4 8.2-1.2 3.4.8 6.4 4.2 6.4 4.2 0 7.9-4.7 9.2-14.6Z" />
                    <path d="M7.5 15.5c2.8-.5 5.3-2.2 7.4-5.1" />
                </svg>
            </span>
            <span>
                <strong>CityZen</strong>
                <small>Civic Control</small>
            </span>
        </a>
    </div>

    <nav class="cz-dash-nav" aria-label="Dashboard menu">
        <a class="cz-dash-nav-link {{ $activeNav === 'home' ? 'is-active' : '' }}" href="{{ url('/dashboard') }}">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 11 8-7 8 7v8a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-8Z" /></svg>
            <span>Home</span>
        </a>
        <a class="cz-dash-nav-link {{ $activeNav === 'explore' ? 'is-active' : '' }}" href="{{ url('/explore') }}">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15.5 8.5-2.1 5.9-5.9 2.1 2.1-5.9 5.9-2.1Z" /><circle cx="12" cy="12" r="9" /></svg>
            <span>Explore</span>
        </a>
        <a class="cz-dash-nav-link {{ $activeNav === 'notifications' ? 'is-active' : '' }}" href="{{ url('/notifications') }}">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 9a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" /><path d="M10 20a2 2 0 0 0 4 0" /></svg>
            <span>Notifications</span>
        </a>
        <a class="cz-dash-nav-link {{ $activeNav === 'bookmarks' ? 'is-active' : '' }}" href="{{ url('/bookmarks') }}">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4h12v17l-6-4-6 4V4Z" /></svg>
            <span>Bookmarks</span>
        </a>
        @if ($isAdmin ?? false)
            <a class="cz-dash-nav-link {{ $activeNav === 'admin' ? 'is-active' : '' }}" href="{{ url('/admin') }}">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4z" /><path d="M8 9h8" /><path d="M8 13h5" /><path d="M8 17h3" /></svg>
                <span>Admin</span>
            </a>
        @endif
    </nav>

    <div class="cz-dash-sidebar-bottom">
        <div class="cz-dash-user-menu" data-user-menu>
            <button class="cz-dash-user-card" type="button" data-user-menu-toggle aria-expanded="false" aria-label="Open account menu">
                <span class="cz-dash-avatar">
                    @if ($avatarPath)
                        <img src="{{ route('users.avatar', $user['id']) }}" alt="">
                    @else
                        {{ $initials }}
                    @endif
                </span>
                <span class="cz-dash-user-copy">
                    <strong>{{ $user['name'] }}</strong>
                    <small>{{ $handle }}</small>
                </span>
                <svg class="cz-dash-user-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6" /></svg>
            </button>
            <div class="cz-dash-account-panel" data-user-menu-panel>
                <a href="{{ url('/profile') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path d="M4 20c1.6-4 14.4-4 16 0" /></svg>
                    <span>Profile</span>
                </a>
                <a href="{{ url('/settings') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z" /><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2 3.4-.2-.1a1.7 1.7 0 0 0-2 .4 1.7 1.7 0 0 0-.5 1.8V23H9v-.5a1.7 1.7 0 0 0-.5-1.8 1.7 1.7 0 0 0-2-.4l-.2.1-2-3.4.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1.2H3v-4h.2a1.7 1.7 0 0 0 1.5-1.2 1.7 1.7 0 0 0-.3-1.9l-.1-.1 2-3.4.2.1a1.7 1.7 0 0 0 2-.4A1.7 1.7 0 0 0 9 1.1V1h6v.1a1.7 1.7 0 0 0 .5 1.8 1.7 1.7 0 0 0 2 .4l.2-.1 2 3.4-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.5 1.2h.2v4h-.2a1.7 1.7 0 0 0-1.4 1.2Z" /></svg>
                    <span>Settings</span>
                </a>
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8V5a1 1 0 0 0-1-1H5v16h8a1 1 0 0 0 1-1v-3" /><path d="M10 12h10" /><path d="m17 9 3 3-3 3" /></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
