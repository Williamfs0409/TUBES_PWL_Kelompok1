<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Users | CityZen</title>
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
                    <span class="cz-profile-eyebrow">User governance</span>
                    <h1>Users</h1>
                    <p>Suspend akun bermasalah dan kelola role admin/superadmin sesuai kewenangan.</p>
                </div>
                <button class="cz-dash-theme-toggle" type="button" data-theme-toggle aria-pressed="false">
                    <span class="cz-theme-sun" aria-hidden="true"></span>
                    <span data-theme-label>Dark mode</span>
                </button>
            </header>

            @include('admin.partials.nav', ['activeAdmin' => 'users'])

            @if (session('status'))
                <div class="cz-form-alert cz-form-alert--success">{{ session('status') }}</div>
            @endif

            <section class="cz-admin-report-list">
                @foreach ($users as $account)
                    <article class="cz-admin-report-card">
                        <div>
                            <span>{{ $account->role_name ?? 'user' }} &middot; {{ $account->is_suspended ? 'Suspended' : 'Active' }}</span>
                            <h2>{{ $account->name }}</h2>
                            <p>{{ $account->email }}</p>
                            <small>
                                Joined {{ $account->created_at ? \Illuminate\Support\Carbon::parse($account->created_at)->diffForHumans() : 'recently' }}
                                @if ($account->suspended_at)
                                    &middot; Suspended {{ \Illuminate\Support\Carbon::parse($account->suspended_at)->diffForHumans() }}
                                @endif
                            </small>
                        </div>

                        <form method="POST" action="{{ route('admin.users.update', $account->id) }}">
                            @csrf
                            @method('PATCH')

                            @if ($isSuperAdmin)
                                <select name="role_id" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @selected($account->role_id === $role->id)>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            <label class="cz-admin-check">
                                <input name="is_suspended" type="checkbox" value="1" @checked($account->is_suspended) @disabled($currentUserId === $account->id)>
                                Suspended
                            </label>
                            <button type="submit" @disabled($currentUserId === $account->id && ! $isSuperAdmin)>Save User</button>
                        </form>
                    </article>
                @endforeach
            </section>
        </main>
    </div>
</body>
</html>
