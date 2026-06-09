<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Settings | CityZen</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-dashboard-page cz-profile-page" data-theme="light">
    @php
        $user = session('cityzen_user', ['name' => 'CityZen User', 'email' => 'member@cityzen.local']);
        $nameParts = collect(explode(' ', trim($user['name'] ?? 'CityZen User')))->filter()->values();
        $initials = $nameParts->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') ?: 'CZ';
        $fallbackUsername = str(str($account->email ?? $user['email'] ?? 'member@cityzen.local')->before('@'))->replace(['.', '-'], '_')->slug('_');
    @endphp

    <div class="cz-dash-shell">
        @include('partials.dashboard-sidebar', ['activeNav' => 'settings'])

        <main class="cz-profile-main">
            <header class="cz-list-header">
                <div class="cz-list-heading">
                    <span>Account center</span>
                    <h1>Settings</h1>
                    <p>Kelola identitas akun, profil publik, dan password CityZen kamu dari satu tempat.</p>
                </div>
                <button class="cz-dash-theme-toggle" type="button" data-theme-toggle aria-pressed="false">
                    <span class="cz-theme-sun" aria-hidden="true"></span>
                    <span data-theme-label>Dark mode</span>
                </button>
            </header>

            @if (session('status'))
                <div class="cz-form-alert cz-form-alert--success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="cz-form-alert">
                    Data belum valid. Cek field yang ditandai lalu simpan ulang.
                </div>
            @endif

            <section class="cz-form-card">
                <div class="cz-form-heading">
                    <span>Profile details</span>
                    <h1>Public identity</h1>
                    <p>Data ini dipakai di dashboard, kontribusi, bookmark, dan aktivitas report.</p>
                </div>

                <form class="cz-form-grid" method="POST" action="{{ route('settings.update') }}">
                    @csrf

                    <label>
                        <span>Name</span>
                        <input name="name" value="{{ old('name', $account->name) }}" required maxlength="80">
                        @error('name') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Email</span>
                        <input name="email" type="email" value="{{ old('email', $account->email) }}" required>
                        @error('email') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Username</span>
                        <input name="username" value="{{ old('username', $profile->username ?? $fallbackUsername) }}" required maxlength="40">
                        @error('username') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>City</span>
                        <input name="city" value="{{ old('city', $profile->city ?? '') }}" maxlength="100" placeholder="Contoh: Medan">
                        @error('city') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Bio</span>
                        <textarea name="bio" maxlength="500" placeholder="Tulis deskripsi singkat tentang kamu sebagai warga CityZen.">{{ old('bio', $profile->bio ?? '') }}</textarea>
                        @error('bio') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>New password</span>
                        <input name="password" type="password" minlength="4" placeholder="Kosongkan jika tidak diganti">
                        @error('password') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <div class="cz-form-actions">
                        <a class="cz-profile-button cz-profile-button--secondary" href="{{ url('/profile') }}">Cancel</a>
                        <button type="submit">Save settings</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
