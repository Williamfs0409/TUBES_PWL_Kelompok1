@php
    $cityzenUser = session('cityzen_user');
    $protectedUrl = fn (string $path) => $cityzenUser ? url($path) : url('/login');
    $isLandingPage = request()->path() === '/';
    $isAuthPage = request()->is('login') || request()->is('register');
@endphp

<header class="cz-topbar">
    <a class="cz-brand" href="{{ url('/') }}" aria-label="CityZen home">CityZen</a>
    @unless ($isLandingPage || $isAuthPage)
        <nav class="cz-nav" aria-label="Main navigation">
            <a class="{{ request()->is('/') ? 'is-active' : '' }}" href="{{ url('/') }}">Home</a>
            <a class="{{ request()->is('dashboard') ? 'is-active' : '' }}" href="{{ $protectedUrl('/dashboard') }}">Discover</a>
            <a class="{{ request()->is('places/create') ? 'is-active' : '' }}" href="{{ $protectedUrl('/places/create') }}">Report</a>
            <a class="{{ request()->is('profile') ? 'is-active' : '' }}" href="{{ $protectedUrl('/profile') }}">Profile</a>
            <a class="{{ request()->is('admin') ? 'is-active' : '' }}" href="{{ $protectedUrl('/admin') }}">Admin</a>
        </nav>
    @endunless
    <div class="cz-actions">
        @if ($cityzenUser)
            <button class="cz-icon-button" type="button" data-public-search aria-label="Search">?</button>
            <button class="cz-icon-button" type="button" data-public-notify aria-label="Notifications">!</button>
            <a class="cz-avatar" href="{{ url('/profile') }}" aria-label="Open profile">
                {{ collect(explode(' ', $cityzenUser['name']))->filter()->take(2)->map(fn ($part) => $part[0])->join('') ?: 'CZ' }}
            </a>
            <form action="{{ url('/logout') }}" method="POST">
                @csrf
                <button class="button button--secondary cz-logout" type="submit">Logout</button>
            </form>
        @else
            <a class="button button--secondary" href="{{ url('/login') }}">Login</a>
            <a class="button button--primary" href="{{ url('/register') }}">Register</a>
        @endif
    </div>
</header>

@unless ($isLandingPage || $isAuthPage)
    <nav class="cz-mobile-nav" aria-label="Mobile navigation">
        <a class="{{ request()->is('/') ? 'is-active' : '' }}" href="{{ url('/') }}"><span>H</span>Home</a>
        <a class="{{ request()->is('dashboard') ? 'is-active' : '' }}" href="{{ $protectedUrl('/dashboard') }}"><span>D</span>Discover</a>
        <a class="{{ request()->is('places/create') ? 'is-active' : '' }}" href="{{ $protectedUrl('/places/create') }}"><span>+</span>Report</a>
        <a class="{{ request()->is('profile') ? 'is-active' : '' }}" href="{{ $protectedUrl('/profile') }}"><span>P</span>Profile</a>
    </nav>
@endunless
