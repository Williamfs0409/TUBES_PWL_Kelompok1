<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Categories | CityZen</title>
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
                    <span class="cz-profile-eyebrow">Taxonomy</span>
                    <h1>Categories</h1>
                    <p>Kelola kategori ruang publik yang dipakai untuk feed, explore, dan form kontribusi.</p>
                </div>
                <button class="cz-dash-theme-toggle" type="button" data-theme-toggle aria-pressed="false">
                    <span class="cz-theme-sun" aria-hidden="true"></span>
                    <span data-theme-label>Dark mode</span>
                </button>
            </header>

            @include('admin.partials.nav', ['activeAdmin' => 'categories'])

            @if (session('status'))
                <div class="cz-form-alert cz-form-alert--success">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="cz-form-alert">{{ $errors->first() }}</div>
            @endif

            <section class="cz-admin-panel">
                <h2>Add Category</h2>
                <form class="cz-admin-inline-form" method="POST" action="{{ route('admin.categories.store') }}">
                    @csrf
                    <input name="name" placeholder="Nama kategori" required>
                    <input name="icon" placeholder="Icon label">
                    <input name="description" placeholder="Deskripsi singkat">
                    <button type="submit">Add</button>
                </form>
            </section>

            <section class="cz-admin-report-list">
                @foreach ($categories as $category)
                    <article class="cz-admin-report-card">
                        <div>
                            <span>{{ $category->is_active ? 'Active' : 'Inactive' }} &middot; Sort {{ $category->sort_order }}</span>
                            <h2>{{ $category->name }}</h2>
                            <p>{{ $category->description ?: 'Belum ada deskripsi.' }}</p>
                            <small>Slug: {{ $category->slug }} @if ($category->icon) &middot; Icon: {{ $category->icon }} @endif</small>
                        </div>

                        <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                            @csrf
                            @method('PATCH')
                            <input name="name" value="{{ $category->name }}" required>
                            <input name="description" value="{{ $category->description }}" placeholder="Deskripsi">
                            <input name="icon" value="{{ $category->icon }}" placeholder="Icon">
                            <input name="sort_order" type="number" value="{{ $category->sort_order }}" min="0">
                            <label class="cz-admin-check"><input name="is_active" type="checkbox" value="1" @checked($category->is_active)> Active</label>
                            <button type="submit">Save</button>
                        </form>

                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                            @csrf
                            @method('DELETE')
                            <button class="cz-list-danger" type="submit" onclick="return confirm('Hapus kategori ini?')">Delete</button>
                        </form>
                    </article>
                @endforeach
            </section>
        </main>
    </div>
</body>
</html>
