<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Categories | CityZen</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-dashboard-page cz-admin-shell-page" data-theme="light">
    <div class="cz-dash-shell">
        @include('partials.dashboard-sidebar', ['activeNav' => 'admin', 'isAdmin' => true])

        <main class="cz-admin-main">
            <header class="cz-list-header cz-admin-category-hero">
                <div>
                    <span class="cz-profile-eyebrow">Administrasi data</span>
                    <h1>Categories</h1>
                    <p>Kelola kategori fasilitas dan lokasi publik dalam ekosistem CityZen. Tambahkan, urutkan, atau nonaktifkan kategori sesuai kebutuhan tata kota.</p>
                </div>
                <div class="cz-admin-category-hero-actions">
                    <span class="cz-admin-category-total"><strong>{{ str_pad((string) $categories->count(), 2, '0', STR_PAD_LEFT) }}</strong><small>Total</small></span>
                    <a class="cz-admin-category-add-link" href="#add-category">
                        <span aria-hidden="true">+</span>
                        Tambah Kategori
                    </a>
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

            @include('admin.partials.nav', ['activeAdmin' => 'categories'])

            @if (session('status'))
                <div class="cz-form-alert cz-form-alert--success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="cz-form-alert">{{ $errors->first() }}</div>
            @endif

            <section class="cz-admin-panel cz-admin-category-create" id="add-category">
                <div class="cz-admin-category-heading">
                    <span>New taxonomy</span>
                    <h2>Tambah kategori baru</h2>
                    <p>Buat kategori yang langsung bisa dipakai warga saat menambahkan tempat publik.</p>
                </div>
                <form class="cz-admin-category-create-form" method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <label>
                        <span>Nama kategori</span>
                        <input name="name" placeholder="Contoh: Taman Kota" required>
                    </label>
                    <label>
                        <span>Deskripsi singkat</span>
                        <input name="description" placeholder="Ringkasan fungsi kategori">
                    </label>
                    <button type="submit">Tambah Kategori</button>
                </form>
            </section>

            <section class="cz-admin-report-list cz-admin-category-list">
                @foreach ($categories as $category)
                    <article class="cz-admin-report-card cz-admin-category-card">
                        <div class="cz-admin-category-card-header">
                            <div>
                                <span>ID: CAT-{{ str_pad((string) $category->id, 3, '0', STR_PAD_LEFT) }} &middot; <strong>Urutan: {{ $category->sort_order }}</strong></span>
                                <h2>{{ $category->name }}</h2>
                                <p>{{ $category->description ?: 'Belum ada deskripsi.' }}</p>
                            </div>
                            <div class="cz-admin-category-meta">
                                <small>Slug</small>
                                <strong>{{ $category->slug }}</strong>
                            </div>
                        </div>

                        <form class="cz-admin-category-edit-form" method="POST" action="{{ route('admin.categories.update', $category) }}">
                            @csrf
                            @method('PATCH')
                            <label>
                                <span>Nama</span>
                                <input name="name" value="{{ $category->name }}" required>
                            </label>
                            <label>
                                <span>Deskripsi</span>
                                <input name="description" value="{{ $category->description }}" placeholder="Deskripsi">
                            </label>
                            <label>
                                <span>Sort</span>
                                <input name="sort_order" type="number" value="{{ $category->sort_order }}" min="0">
                            </label>
                            <label class="cz-admin-check"><input name="is_active" type="checkbox" value="1" @checked($category->is_active)> Active</label>
                            <button type="submit">Save</button>
                        </form>

                        <form class="cz-admin-category-delete-form" method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                            @csrf
                            @method('DELETE')
                            <button class="cz-admin-danger-button cz-admin-delete-small" type="submit" onclick="return confirm('Hapus kategori ini?')">
                                <span aria-hidden="true">⌫</span>
                                Hapus Kategori
                            </button>
                        </form>
                    </article>
                @endforeach
            </section>
        </main>
    </div>
</body>
</html>
