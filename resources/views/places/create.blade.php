
@php
    $user = session('cityzen_user', ['name' => 'CityZen User', 'email' => 'member@cityzen.local']);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Place | CityZen</title>
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
            --coral: #ffe0d2;
            --shadow: 10px 10px 0 rgba(23, 32, 26, 0.1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background:
                linear-gradient(135deg, rgba(201, 242, 127, 0.2), transparent 28rem),
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

        .topbar {
            align-items: center;
            background: rgba(247, 250, 246, 0.92);
            backdrop-filter: blur(12px);
            border-bottom: 1.5px solid var(--line);
            display: flex;
            gap: 18px;
            justify-content: space-between;
            min-height: 64px;
            padding: 0 clamp(20px, 5vw, 54px);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .brand {
            color: var(--green);
            font-family: "Hanken Grotesk", Inter, sans-serif;
            font-size: 30px;
            font-weight: 800;
        }

        .topbar nav {
            display: flex;
            gap: 10px;
        }

        .button,
        .topbar nav a {
            align-items: center;
            border: 1.5px solid var(--line);
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            font: inherit;
            font-weight: 900;
            justify-content: center;
            min-height: 42px;
            padding: 10px 16px;
        }

        .button--primary {
            background: var(--lime);
            color: #ffffff;
            box-shadow: 6px 6px 0 rgba(23, 32, 26, 0.12);
        }

        .button--secondary,
        .topbar nav a {
            background: #ffffff;
        }

        main {
            display: grid;
            gap: 28px;
            grid-template-columns: minmax(0, 0.78fr) minmax(340px, 1fr);
            padding: clamp(28px, 5vw, 62px);
        }

        .intro,
        .form-card,
        .map-card,
        .preview-card {
            background: #ffffff;
            border: 1.5px solid var(--line);
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .intro {
            align-self: start;
            display: grid;
            gap: 22px;
            padding: clamp(24px, 5vw, 42px);
            position: sticky;
            top: 92px;
        }

        .eyebrow {
            color: var(--green);
            font-size: 12px;
            font-weight: 900;
            letter-spacing: 0.14em;
            margin: 0;
            text-transform: uppercase;
        }

        h1,
        h2 {
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
            margin: 0;
        }

        .steps {
            display: grid;
            gap: 12px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .steps li {
            background: var(--mint);
            border: 1.5px solid var(--line);
            border-radius: 8px;
            font-weight: 900;
            padding: 14px;
        }

        .form-card {
            padding: clamp(24px, 5vw, 42px);
        }

        form {
            display: grid;
            gap: 18px;
            margin-top: 24px;
        }

        .field {
            display: grid;
            gap: 8px;
        }

        label,
        .field span {
            font-size: 13px;
            font-weight: 900;
            text-transform: uppercase;
        }

        input,
        select,
        textarea {
            background: #fbfdfb;
            border: 1.5px solid var(--line);
            border-radius: 8px;
            color: var(--ink);
            font: inherit;
            outline: 0;
            padding: 13px 14px;
            width: 100%;
        }

        textarea {
            min-height: 136px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            box-shadow: 0 0 0 4px rgba(201, 242, 127, 0.45);
        }

        .error {
            color: #b42318;
            font-size: 13px;
            font-weight: 800;
        }

        .upload {
            background: var(--mint);
            border: 1.5px dashed var(--line);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
        }

        .side-stack {
            display: grid;
            gap: 20px;
        }

        .map-card,
        .preview-card {
            padding: 22px;
        }

        .map-art {
            background:
                linear-gradient(90deg, transparent 48%, rgba(23, 32, 26, 0.14) 49%, transparent 50%),
                linear-gradient(0deg, transparent 48%, rgba(23, 32, 26, 0.14) 49%, transparent 50%),
                linear-gradient(135deg, var(--mint), var(--coral));
            border: 1.5px solid var(--line);
            border-radius: 8px;
            min-height: 260px;
            position: relative;
        }

        .map-art::after {
            background: var(--lime);
            border: 1.5px solid var(--line);
            border-radius: 999px;
            content: "";
            height: 30px;
            left: 54%;
            position: absolute;
            top: 42%;
            width: 30px;
        }

        .preview-card ul {
            color: var(--muted);
            display: grid;
            gap: 10px;
            line-height: 1.5;
            margin: 16px 0 0;
            padding-left: 20px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: flex-end;
        }

        @media (max-width: 980px) {
            main {
                grid-template-columns: 1fr;
            }

            .intro {
                position: static;
            }
        }

        @media (max-width: 680px) {
            .topbar,
            .topbar nav,
            .actions {
                align-items: stretch;
                flex-direction: column;
            }

            .topbar {
                padding-bottom: 14px;
                padding-top: 14px;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <a class="brand" href="{{ url('/') }}">CityZen</a>
        <nav aria-label="Report navigation">
            <a href="{{ url('/dashboard') }}">Dashboard</a>
            <a href="{{ url('/bookmarks') }}">Bookmarks</a>
        </nav>
    </header>

    <main>
        <aside class="intro">
            <p class="eyebrow">Public place</p>
            <h1>Add a public space into CityZen.</h1>
            <p>Hi {{ $user['name'] }}, use this form to add a place that can later receive reviews, bookmarks, likes, and reports.</p>
            <ol class="steps">
                <li>1. Describe the place and category</li>
                <li>2. Add location and context</li>
                <li>3. Publish it into dashboard and explore</li>
            </ol>
        </aside>

        <section class="side-stack">
        <article class="form-card">
            <p class="eyebrow">New place</p>
            <h2>Place details</h2>
            <p>Isi data tempat publik yang ingin ditambahkan ke CityZen.</p>

            <form method="POST" action="{{ url('/places') }}">
            @csrf

            <div class="field">
            <label for="name">Nama Place</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Example: Lapangan Merdeka" required>
            @error('name')
                <span class="error">{{ $message }}</span>
            @enderror
            </div>

            <div class="field">
            <label for="category_id">Kategori</label>
            <select id="category_id" name="category_id" required>
            <option value="">Pilih kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                {{ $category->name }}
                </option>
            @endforeach
            </select>
            @error('category_id')
                <span class="error">{{ $message }}</span>
            @enderror
            </div>

            <div class="field">
                <label for="short_description">Deskripsi Singkat</label>
                <input id="short_description" name="short_description" type="text" value="{{ old('short_description') }}" placeholder="Contoh: Taman kota dengan area duduk dan jalur pejalan kaki">
                @error('short_description')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" placeholder="Jelaskan kondisi tempat, fasilitas, dan alasan tempat ini penting." required>{{ old('description') }}</textarea>
                @error('description')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
            <label for="address">Alamat</label>
            <input id="address" name="address" type="text" value="{{ old('address') }}" placeholder="Contoh: Jl. Merdeka No. 10" required>
            @error('address')
                <span class="error">{{ $message }}</span>
            @enderror
            </div>

            <div class="field">
            <label for="city">Kota</label>
            <input id="city" name="city" type="text" value="{{ old('city') }}" placeholder="Contoh: Medan" required>
            @error('city')
                <span class="error">{{ $message }}</span>
            @enderror
            </div>

            <div class="field">
            <label for="province">Provinsi</label>
            <input id="province" name="province" type="text" value="{{ old('province') }}" placeholder="Contoh: Sumatera Utara">
            @error('province')
                <span class="error">{{ $message }}</span>
            @enderror
            </div>

            <div class="field">
            <label for="google_maps_url">Google Maps URL</label>
            <input id="google_maps_url" name="google_maps_url" type="text" value="{{ old('google_maps_url') }}" placeholder="https://maps.google.com/...">
            @error('google_maps_url')
                <span class="error">{{ $message }}</span>
            @enderror
            </div>

            <div class="actions">
            <a class="button button--secondary" href="{{ url('/dashboard') }}">Cancel</a>
            <button class="button button--primary" type="submit">Simpan Place</button>
            </div>
            </form>
        </article>

            <article class="map-card" aria-label="Location preview">
                <p class="eyebrow">Location preview</p>
                <div class="map-art"></div>
            </article>

            <article class="preview-card">
                <p class="eyebrow">What makes a good report</p>
                <ul>
                    <li>Use a recognizable place name.</li>
                    <li>Describe one issue clearly.</li>
                    <li>Mention who is affected and when it happens.</li>
                </ul>
            </article>
        </section>
    </main>
</body>
</html>
