
@php
    $user = session('cityzen_user', ['name' => 'CityZen User', 'email' => 'member@cityzen.local']);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Place | CityZen</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    <style>
        :root {
            --surface: #f7faf6;
            --ink: #17201a;
            --muted: #5d6c61;
            --line: #c4d3c2;
            --green: #174d2e;
            --lime: #1f6b38;
            --mint: #e8f5ee;
            --coral: #ffe0d2;
            --shadow: 0 10px 22px rgba(23, 32, 26, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background:
                linear-gradient(135deg, rgba(201, 242, 127, 0.12), transparent 28rem),
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
            border-bottom: 1px solid var(--line);
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
            border: 1px solid var(--line);
            border-radius: 10px;
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
            box-shadow: none;
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
            border: 1px solid var(--line);
            border-radius: 14px;
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
            border: 1px solid var(--line);
            border-radius: 12px;
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
            border: 1px solid var(--line);
            border-radius: 10px;
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
            border-color: var(--green);
            box-shadow: 0 0 0 4px rgba(31, 107, 56, 0.12);
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

        .photo-uploader {
            background: #fbfdfb;
            border: 1px dashed color-mix(in srgb, var(--green) 32%, var(--line));
            border-radius: 14px;
            display: grid;
            gap: 12px;
            padding: 14px;
        }

        .photo-uploader__input {
            height: 1px;
            opacity: 0;
            overflow: hidden;
            position: absolute;
            width: 1px;
        }

        .photo-uploader__bar {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .photo-uploader__button,
        .photo-uploader__add {
            align-items: center;
            border: 1px solid var(--line);
            border-radius: 999px;
            cursor: pointer;
            display: inline-flex;
            font-weight: 900;
            justify-content: center;
            min-height: 40px;
            transition: background 160ms ease, border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .photo-uploader__button {
            background: var(--green);
            color: #ffffff;
            padding: 0 16px;
        }

        .photo-uploader__add {
            background: var(--mint);
            color: var(--green);
            font-size: 24px;
            line-height: 1;
            width: 42px;
        }

        .photo-uploader__button:hover,
        .photo-uploader__add:hover {
            border-color: var(--green);
            box-shadow: 0 7px 16px rgba(23, 77, 46, 0.12);
            transform: translateY(-1px);
        }

        .photo-uploader__add[disabled] {
            cursor: not-allowed;
            opacity: 0.46;
            transform: none;
        }

        .photo-uploader__status {
            color: var(--muted);
            font-size: 14px;
            font-weight: 700;
        }

        .photo-uploader__list {
            display: grid;
            gap: 10px;
            grid-template-columns: repeat(auto-fit, minmax(148px, 1fr));
        }

        .photo-uploader__item {
            align-items: center;
            background: #ffffff;
            border: 1px solid color-mix(in srgb, var(--line) 84%, transparent);
            border-radius: 12px;
            display: grid;
            gap: 10px;
            grid-template-columns: 56px minmax(0, 1fr) auto;
            min-width: 0;
            padding: 8px;
        }

        .photo-uploader__thumb {
            aspect-ratio: 1;
            background: var(--mint);
            border: 1px solid color-mix(in srgb, var(--line) 80%, transparent);
            border-radius: 10px;
            overflow: hidden;
        }

        .photo-uploader__thumb img {
            display: block;
            height: 100%;
            object-fit: cover;
            width: 100%;
        }

        .photo-uploader__meta {
            min-width: 0;
        }

        .photo-uploader__meta strong,
        .photo-uploader__meta small {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .photo-uploader__meta strong {
            font-size: 14px;
            text-transform: none;
        }

        .photo-uploader__meta small {
            color: var(--muted);
            font-size: 12px;
            margin-top: 2px;
        }

        .photo-uploader__remove {
            background: color-mix(in srgb, #b42318 9%, #ffffff);
            border: 1px solid color-mix(in srgb, #b42318 34%, var(--line));
            border-radius: 999px;
            color: #9f2018;
            cursor: pointer;
            font-weight: 900;
            height: 32px;
            width: 32px;
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

            <form method="POST" action="{{ url('/places') }}" enctype="multipart/form-data">
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

            <div class="field">
                <label for="photos">Foto tempat</label>
                <div class="photo-uploader" data-photo-uploader>
                    <input id="photos" class="photo-uploader__input" name="photos[]" type="file" accept="image/*" multiple data-photo-input>
                    <div class="photo-uploader__bar">
                        <label class="photo-uploader__button" for="photos">Pilih foto</label>
                        <button class="photo-uploader__add" type="button" data-photo-add aria-label="Tambah foto" hidden>+</button>
                        <span class="photo-uploader__status" data-photo-status>Belum ada foto dipilih. Maksimal 6 foto.</span>
                    </div>
                    <div class="photo-uploader__list" data-photo-list aria-live="polite"></div>
                </div>
                <small>Upload sampai 6 foto. Foto pertama otomatis menjadi cover feed.</small>
                @error('photos')
                    <span class="error">{{ $message }}</span>
                @enderror
                @error('photos.*')
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
    <script>
        (() => {
            const uploader = document.querySelector('[data-photo-uploader]');
            if (!uploader || typeof DataTransfer === 'undefined') return;

            const input = uploader.querySelector('[data-photo-input]');
            const addButton = uploader.querySelector('[data-photo-add]');
            const list = uploader.querySelector('[data-photo-list]');
            const status = uploader.querySelector('[data-photo-status]');
            const maxPhotos = 6;
            let selectedPhotos = [];

            const formatSize = (bytes) => {
                if (bytes >= 1024 * 1024) return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
                return `${Math.max(1, Math.round(bytes / 1024))} KB`;
            };

            const syncInput = () => {
                const transfer = new DataTransfer();
                selectedPhotos.forEach((file) => transfer.items.add(file));
                input.files = transfer.files;
            };

            const render = () => {
                syncInput();
                list.innerHTML = '';
                addButton.hidden = selectedPhotos.length === 0;
                addButton.disabled = selectedPhotos.length >= maxPhotos;
                status.textContent = selectedPhotos.length
                    ? `${selectedPhotos.length} dari ${maxPhotos} foto siap diupload.`
                    : 'Belum ada foto dipilih. Maksimal 6 foto.';

                selectedPhotos.forEach((file, index) => {
                    const item = document.createElement('article');
                    item.className = 'photo-uploader__item';

                    const thumb = document.createElement('div');
                    thumb.className = 'photo-uploader__thumb';
                    const image = document.createElement('img');
                    image.alt = file.name;
                    image.src = URL.createObjectURL(file);
                    image.addEventListener('load', () => URL.revokeObjectURL(image.src), { once: true });
                    thumb.append(image);

                    const meta = document.createElement('div');
                    meta.className = 'photo-uploader__meta';
                    const name = document.createElement('strong');
                    name.textContent = file.name;
                    const detail = document.createElement('small');
                    detail.textContent = `${index === 0 ? 'Cover feed' : 'Foto tambahan'} · ${formatSize(file.size)}`;
                    meta.append(name, detail);

                    const remove = document.createElement('button');
                    remove.className = 'photo-uploader__remove';
                    remove.type = 'button';
                    remove.setAttribute('aria-label', `Hapus ${file.name}`);
                    remove.textContent = '×';
                    remove.addEventListener('click', () => {
                        selectedPhotos = selectedPhotos.filter((_, photoIndex) => photoIndex !== index);
                        render();
                    });

                    item.append(thumb, meta, remove);
                    list.append(item);
                });
            };

            input.addEventListener('change', () => {
                const nextPhotos = Array.from(input.files || []).filter((file) => file.type.startsWith('image/'));
                selectedPhotos = [...selectedPhotos, ...nextPhotos].slice(0, maxPhotos);
                render();
            });

            addButton.addEventListener('click', () => input.click());
        })();
    </script>
</body>
</html>
