<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Users | CityZen</title>
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
                    <span class="cz-profile-eyebrow">User governance</span>
                    <h1>Users</h1>
                    <p>Suspend akun bermasalah dan kelola role admin/superadmin sesuai kewenangan.</p>
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

            @include('admin.partials.nav', ['activeAdmin' => 'users'])

            @if (session('status'))
                <div class="cz-form-alert cz-form-alert--success">{{ session('status') }}</div>
            @endif

            <section class="cz-admin-report-list">
                @foreach ($users as $account)
                    @php
                        $roleName = $account->role_name ?? 'user';
                        $roleSlug = \Illuminate\Support\Str::slug($roleName);
                    @endphp
                    <article class="cz-admin-report-card cz-admin-user-card is-role-{{ $roleSlug }} {{ $account->is_suspended ? 'is-suspended' : '' }}">
                        <div class="cz-admin-user-summary">
                            <span class="cz-admin-role-badge is-{{ $roleSlug }}">{{ $roleName }}</span>
                            <span class="cz-admin-user-state {{ $account->is_suspended ? 'is-warning' : '' }}">{{ $account->is_suspended ? 'Suspended' : 'Active' }}</span>
                            <h2>{{ $account->name }}</h2>
                            <p>{{ $account->email }}</p>
                            <small>
                                Joined {{ $account->created_at ? \Illuminate\Support\Carbon::parse($account->created_at)->diffForHumans() : 'recently' }}
                                @if ($account->suspended_at)
                                    &middot; Suspended {{ \Illuminate\Support\Carbon::parse($account->suspended_at)->diffForHumans() }}
                                @endif
                            </small>
                        </div>

                        <form class="cz-admin-user-form" method="POST" action="{{ route('admin.users.update', $account->id) }}" data-admin-user-form data-original-role="{{ $account->role_id }}" data-original-suspended="{{ $account->is_suspended ? '1' : '0' }}">
                            @csrf
                            @method('PATCH')

                            @if ($isSuperAdmin)
                                <select name="role_id" required data-admin-role-select>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @selected($account->role_id === $role->id)>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            <label class="cz-admin-check cz-admin-suspend-check">
                                <input name="is_suspended" type="checkbox" value="1" @checked($account->is_suspended) @disabled($currentUserId === $account->id) data-admin-suspend-check>
                                Suspended
                            </label>
                            <button type="submit" @disabled($currentUserId === $account->id && ! $isSuperAdmin)>Save User</button>

                            <div class="cz-admin-confirm-bar" hidden data-admin-confirm-bar>
                                <strong>Konfirmasi perubahan akun</strong>
                                <span data-admin-confirm-copy>Perubahan role atau suspend akan mempengaruhi akses user.</span>
                                <label>
                                    <input type="checkbox" data-admin-confirm-checkbox>
                                    Saya yakin dengan perubahan ini.
                                </label>
                            </div>
                        </form>
                    </article>
                @endforeach
            </section>
        </main>
    </div>
</body>
</html>
