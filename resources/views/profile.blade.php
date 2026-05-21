@php
    $user = session('cityzen_user', ['name' => 'CityZen User', 'email' => 'member@cityzen.local']);
    $initials = collect(explode(' ', $user['name']))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->join('') ?: 'CZ';
    $lastReport = session('cityzen_last_report');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile | CityZen</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    <style>
        :root {
            --surface: #f7faf6;
            --ink: #17201a;
            --muted: #5d6c61;
            --line: #17201a;
            --green: #174d2e;
            --lime: #c9f27f;
            --mint: #e8f5ee;
            --blue: #dfefff;
            --coral: #ffe0d2;
            --shadow: 10px 10px 0 rgba(23, 32, 26, 0.1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background:
                radial-gradient(circle at 86% 12%, rgba(223, 239, 255, 0.8), transparent 24rem),
                var(--surface);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            margin: 0;
            min-width: 320px;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            min-height: 100vh;
        }

        aside {
            background: #ffffff;
            border-right: 1.5px solid var(--line);
            display: grid;
            gap: 24px;
            padding: 28px;
        }

        .brand {
            color: var(--green);
            font-family: "Hanken Grotesk", Inter, sans-serif;
            font-size: 30px;
            font-weight: 800;
        }

        nav {
            display: grid;
            gap: 10px;
        }

        nav a,
        .button {
            border: 1.5px solid var(--line);
            border-radius: 8px;
            display: inline-flex;
            font-weight: 900;
            justify-content: center;
            min-height: 42px;
            padding: 11px 14px;
        }

        nav a {
            background: var(--mint);
            justify-content: flex-start;
        }

        .button--primary {
            background: var(--lime);
            box-shadow: 6px 6px 0 rgba(23, 32, 26, 0.12);
        }

        .button--secondary {
            background: #ffffff;
        }

        form {
            margin: 0;
        }

        button.button {
            cursor: pointer;
            font: inherit;
            width: 100%;
        }

        main {
            display: grid;
            gap: 24px;
            padding: clamp(28px, 5vw, 62px);
        }

        .hero,
        .card,
        .report-card {
            background: #ffffff;
            border: 1.5px solid var(--line);
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .hero {
            align-items: center;
            display: grid;
            gap: 24px;
            grid-template-columns: auto minmax(0, 1fr) auto;
            padding: clamp(24px, 5vw, 44px);
        }

        .avatar {
            align-items: center;
            background: linear-gradient(135deg, var(--green), #6d8d68);
            border: 1.5px solid var(--line);
            border-radius: 999px;
            color: #ffffff;
            display: inline-flex;
            font-size: 30px;
            font-weight: 900;
            height: 108px;
            justify-content: center;
            width: 108px;
        }

        .eyebrow {
            color: var(--green);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.14em;
            margin: 0 0 10px;
            text-transform: uppercase;
        }

        h1,
        h2,
        h3 {
            font-family: "Hanken Grotesk", Inter, sans-serif;
            margin: 0;
        }

        h1 {
            font-size: clamp(42px, 6vw, 72px);
            line-height: 0.95;
        }

        h2 {
            font-size: 30px;
        }

        p {
            color: var(--muted);
            line-height: 1.6;
            margin: 8px 0 0;
        }

        .grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .card,
        .report-card {
            padding: 24px;
        }

        .card strong {
            display: block;
            font-family: "Hanken Grotesk", Inter, sans-serif;
            font-size: 46px;
            line-height: 1;
        }

        .card span,
        .report-card span {
            color: var(--muted);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .report-card {
            background: var(--mint);
        }

        .report-card.empty {
            background: var(--coral);
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
        }

        .activity {
            display: grid;
            gap: 14px;
        }

        .activity article {
            background: #ffffff;
            border: 1.5px solid var(--line);
            border-radius: 8px;
            display: grid;
            gap: 6px;
            padding: 18px;
        }

        @media (max-width: 980px) {
            .shell,
            .hero,
            .grid {
                grid-template-columns: 1fr;
            }

            aside {
                border-right: 0;
                border-bottom: 1.5px solid var(--line);
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <aside>
            <a class="brand" href="{{ url('/') }}">CityZen</a>
            <nav aria-label="Profile navigation">
                <a href="{{ url('/dashboard') }}">Dashboard</a>
                <a href="{{ url('/places/create') }}">New Report</a>
                <a href="#impact">Impact</a>
                <a href="#activity">Activity</a>
            </nav>
            <form method="POST" action="{{ url('/logout') }}">
                @csrf
                <button class="button button--secondary" type="submit">Logout</button>
            </form>
        </aside>

        <main>
            <section class="hero">
                <span class="avatar">{{ $initials }}</span>
                <div>
                    <p class="eyebrow">Citizen profile</p>
                    <h1>{{ $user['name'] }}</h1>
                    <p>{{ $user['email'] }} &middot; Verified CityZen contributor</p>
                    <div class="actions">
                        <a class="button button--primary" href="{{ url('/places/create') }}">Start Report</a>
                        <a class="button button--secondary" href="{{ url('/dashboard') }}">Explore Dashboard</a>
                    </div>
                </div>
            </section>

            <section class="grid" id="impact" aria-label="Impact stats">
                <article class="card">
                    <span>Watched places</span>
                    <strong>24</strong>
                    <p>Places you keep an eye on from the dashboard.</p>
                </article>
                <article class="card">
                    <span>Reports drafted</span>
                    <strong>{{ $lastReport ? '1' : '0' }}</strong>
                    <p>Drafted reports currently stored in your session.</p>
                </article>
                <article class="card">
                    <span>Impact score</span>
                    <strong>91</strong>
                    <p>A starting civic participation score for this prototype.</p>
                </article>
            </section>

            @if ($lastReport)
                <section class="report-card">
                    <span>Latest draft</span>
                    <h2>{{ $lastReport['place_name'] }}</h2>
                    <p>{{ $lastReport['category'] }} &middot; {{ $lastReport['issue'] }}</p>
                    <p>{{ $lastReport['description'] }}</p>
                </section>
            @else
                <section class="report-card empty">
                    <span>No report yet</span>
                    <h2>Your first report can start here.</h2>
                    <p>Use the report flow to save a public space issue and see it reflected across dashboard/profile.</p>
                    <div class="actions">
                        <a class="button button--primary" href="{{ url('/places/create') }}">Create Report</a>
                    </div>
                </section>
            @endif

            <section id="activity">
                <p class="eyebrow">Recent activity</p>
                <div class="activity">
                    <article>
                        <h3>Dashboard connected</h3>
                        <p>Login and register now lead directly into the protected dashboard.</p>
                    </article>
                    <article>
                        <h3>Profile connected</h3>
                        <p>Dashboard, report, profile, and logout now share the same session flow.</p>
                    </article>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
