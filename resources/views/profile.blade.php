<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | CityZen</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-dashboard-page cz-profile-page" data-theme="light">
    @php
        $user = session('cityzen_user', ['name' => 'CityZen User', 'email' => 'member@cityzen.local']);
        $nameParts = collect(explode(' ', trim($user['name'] ?? 'CityZen User')))->filter()->values();
        $initials = $nameParts->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') ?: 'CZ';
        $handle = '@'.str(str($user['email'] ?? 'member@cityzen.local')->before('@'))->replace(['.', '_', '-'], ' ')->slug('_');
        $lastReport = session('cityzen_last_report');
    @endphp

    <div class="cz-dash-shell">
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
                <a class="cz-dash-nav-link" href="{{ url('/dashboard') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m4 11 8-7 8 7v8a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-8Z" /></svg>
                    <span>Home</span>
                </a>
                <a class="cz-dash-nav-link" href="{{ url('/explore') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15.5 8.5-2.1 5.9-5.9 2.1 2.1-5.9 5.9-2.1Z" /><circle cx="12" cy="12" r="9" /></svg>
                    <span>Explore</span>
                </a>
                <a class="cz-dash-nav-link" href="{{ url('/dashboard#notifications') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 9a6 6 0 1 0-12 0c0 7-3 7-3 7h18s-3 0-3-7Z" /><path d="M10 20a2 2 0 0 0 4 0" /></svg>
                    <span>Notifications</span>
                </a>
                <a class="cz-dash-nav-link" href="{{ url('/dashboard#bookmarks') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 4h12v17l-6-4-6 4V4Z" /></svg>
                    <span>Bookmarks</span>
                </a>
                <a class="cz-dash-nav-link is-active" href="{{ url('/profile') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path d="M4 20c1.6-4 14.4-4 16 0" /></svg>
                    <span>Profile</span>
                </a>
                <a class="cz-dash-nav-link" href="{{ url('/admin/reports') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4z" /><path d="M8 9h8" /><path d="M8 13h5" /><path d="M8 17h3" /></svg>
                    <span>Admin</span>
                </a>
            </nav>

            <div class="cz-dash-sidebar-bottom">
                <a class="cz-dash-report-button" href="{{ url('/places/create') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 11v3a2 2 0 0 0 2 2h2l4 4v-4h3l7 3V6l-7 3H5a2 2 0 0 0-2 2Z" /><path d="M14 9v7" /></svg>
                    <span>Report</span>
                </a>

                <div class="cz-dash-user-card">
                    <span class="cz-dash-avatar">{{ $initials }}</span>
                    <span class="cz-dash-user-copy">
                        <strong>{{ $user['name'] }}</strong>
                        <small>{{ $handle }}</small>
                    </span>
                    <form method="POST" action="{{ url('/logout') }}">
                        @csrf
                        <button class="cz-dash-icon-button" type="submit" aria-label="Logout">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8V5a1 1 0 0 0-1-1H5v16h8a1 1 0 0 0 1-1v-3" /><path d="M10 12h10" /><path d="m17 9 3 3-3 3" /></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="cz-profile-main">
            <section class="cz-profile-hero">
                <span class="cz-profile-avatar">{{ $initials }}</span>
                <div>
                    <span class="cz-profile-eyebrow">Citizen profile</span>
                    <h1>{{ $user['name'] }}</h1>
                    <p>{{ $user['email'] }} &middot; Verified CityZen contributor</p>
                    <div class="cz-profile-actions">
                        <a class="cz-profile-button cz-profile-button--primary" href="{{ url('/places/create') }}">Start Report</a>
                        <a class="cz-profile-button cz-profile-button--secondary" href="{{ url('/dashboard') }}">Explore Dashboard</a>
                    </div>
                </div>
            </section>

            <section class="cz-profile-grid" aria-label="Impact stats">
                <article>
                    <span>Watched places</span>
                    <strong>24</strong>
                    <p>Places you keep an eye on from the dashboard.</p>
                </article>
                <article>
                    <span>Reports drafted</span>
                    <strong>{{ $lastReport ? '1' : '0' }}</strong>
                    <p>Drafted reports currently stored in your session.</p>
                </article>
                <article>
                    <span>Impact score</span>
                    <strong>91</strong>
                    <p>A starting civic participation score for this prototype.</p>
                </article>
            </section>

            <section class="cz-profile-report-card {{ $lastReport ? '' : 'is-empty' }}">
                @if ($lastReport)
                    <span>Latest draft</span>
                    <h2>{{ $lastReport['place_name'] }}</h2>
                    <p>{{ $lastReport['category'] }} &middot; {{ $lastReport['issue'] }}</p>
                    <p>{{ $lastReport['description'] }}</p>
                @else
                    <span>No report yet</span>
                    <h2>Your first report can start here.</h2>
                    <p>Use the report flow to save a public space issue and see it reflected across dashboard/profile.</p>
                    <div class="cz-profile-actions">
                        <a class="cz-profile-button cz-profile-button--primary" href="{{ url('/places/create') }}">Create Report</a>
                    </div>
                @endif
            </section>

            <section class="cz-profile-activity">
                <span class="cz-profile-eyebrow">Recent activity</span>
                <article>
                    <h3>Dashboard connected</h3>
                    <p>Login and register now lead directly into the protected dashboard.</p>
                </article>
                <article>
                    <h3>Profile connected</h3>
                    <p>Dashboard, report, profile, and logout now share the same session flow.</p>
                </article>
            </section>
        </main>
    </div>
</body>
</html>
