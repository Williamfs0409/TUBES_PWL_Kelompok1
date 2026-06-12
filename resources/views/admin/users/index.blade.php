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
                            <span class="cz-admin-user-avatar" aria-hidden="true">
                                @if ($account->avatar_path)
                                    <img src="{{ route('users.avatar', $account->id) }}" alt="">
                                @else
                                    {{ strtoupper(substr($account->name, 0, 1)) }}
                                @endif
                            </span>
                            <div class="cz-admin-user-identity">
                                <div class="cz-admin-user-badges">
                                    <span class="cz-admin-role-badge is-{{ $roleSlug }}">{{ $roleName }}</span>
                                    <span class="cz-admin-user-state {{ $account->is_suspended ? 'is-warning' : '' }}">{{ $account->is_suspended ? 'Suspended' : 'Active' }}</span>
                                </div>
                                <h2>{{ $account->name }}</h2>
                                <p>{{ $account->email }}</p>
                                <small>
                                    Joined {{ $account->created_at ? \Illuminate\Support\Carbon::parse($account->created_at)->diffForHumans() : 'recently' }}
                                    @if ($account->suspended_at)
                                        &middot; Suspended {{ \Illuminate\Support\Carbon::parse($account->suspended_at)->diffForHumans() }}
                                    @endif
                                </small>
                            </div>
                        </div>

                        <form class="cz-admin-user-form" method="POST" action="{{ route('admin.users.update', $account->id) }}" data-admin-user-form data-original-role="{{ $account->role_id }}" data-original-suspended="{{ $account->is_suspended ? '1' : '0' }}" data-admin-user-name="{{ $account->name }}">
                            @csrf
                            @method('PATCH')

                            @if ($isSuperAdmin)
                                <label class="cz-admin-user-field">
                                    <span>Role</span>
                                    <select name="role_id" required data-admin-role-select>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" @selected($account->role_id === $role->id)>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </label>
                            @endif

                            <label class="cz-admin-check cz-admin-suspend-check" data-admin-suspend-control>
                                <input name="is_suspended" type="checkbox" value="1" @checked($account->is_suspended) @disabled($currentUserId === $account->id) data-admin-suspend-check>
                                Suspended
                            </label>
                            <button type="submit" @disabled($currentUserId === $account->id && ! $isSuperAdmin)>Save User</button>
                        </form>
                    </article>
                @endforeach
            </section>
        </main>
    </div>

    <div class="cz-admin-modal" hidden data-admin-confirm-modal>
        <div class="cz-admin-modal__backdrop" data-admin-confirm-cancel></div>
        <section class="cz-admin-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="admin-confirm-title">
            <div class="cz-admin-modal__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M12 9v4" /><path d="M12 17h.01" /><path d="M10.3 4.6 2.8 17.5A2 2 0 0 0 4.5 20h15a2 2 0 0 0 1.7-2.5L13.7 4.6a2 2 0 0 0-3.4 0Z" /></svg>
            </div>
            <div>
                <p class="cz-admin-modal__eyebrow">Konfirmasi Akun</p>
                <h2 id="admin-confirm-title">Konfirmasi penangguhan</h2>
                <p data-admin-confirm-modal-copy>Perubahan ini akan membatasi akses user ke platform CityZen.</p>
            </div>
            <div class="cz-admin-modal__actions">
                <button class="cz-admin-modal__cancel" type="button" data-admin-confirm-cancel>Batal</button>
                <button class="cz-admin-modal__confirm" type="button" data-admin-confirm-submit>Ya, simpan</button>
            </div>
        </section>
    </div>
</body>
</html>
