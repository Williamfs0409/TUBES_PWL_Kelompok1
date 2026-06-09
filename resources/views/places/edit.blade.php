<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Place | CityZen</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-dashboard-page cz-profile-page" data-theme="light">
    <div class="cz-dash-shell">
        @include('partials.dashboard-sidebar', ['activeNav' => 'home'])

        <main class="cz-profile-main">
            <header class="cz-list-header">
                <div class="cz-list-heading">
                    <span>Place editor</span>
                    <h1>Edit Place</h1>
                    <p>Perbarui informasi ruang publik agar feed, explore, dan report tetap akurat.</p>
                </div>
                <a class="cz-list-action" href="{{ url('/places') }}">Back</a>
            </header>

            @if ($errors->any())
                <div class="cz-form-alert">Data belum valid. Cek field yang ditandai lalu simpan ulang.</div>
            @endif

            <section class="cz-form-card">
                <form class="cz-form-grid" method="POST" action="{{ url('/places/'.$place->id) }}">
                    @csrf
                    @method('PUT')

                    <label>
                        <span>Nama Place</span>
                        <input name="name" value="{{ old('name', $place->name) }}" required>
                        @error('name') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Kategori</span>
                        <select name="category_id" required>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $place->category_id) == $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Deskripsi Singkat</span>
                        <input name="short_description" value="{{ old('short_description', $place->short_description) }}">
                        @error('short_description') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Deskripsi</span>
                        <textarea name="description" required>{{ old('description', $place->description) }}</textarea>
                        @error('description') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Alamat</span>
                        <input name="address" value="{{ old('address', $place->address) }}" required>
                        @error('address') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Kota</span>
                        <input name="city" value="{{ old('city', $place->city) }}" required>
                        @error('city') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Provinsi</span>
                        <input name="province" value="{{ old('province', $place->province) }}">
                        @error('province') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Google Maps URL</span>
                        <input name="google_maps_url" value="{{ old('google_maps_url', $place->google_maps_url) }}">
                        @error('google_maps_url') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <label>
                        <span>Image path</span>
                        <input name="image" value="{{ old('image', $place->image) }}" placeholder="images/place.jpg">
                        @error('image') <small class="cz-field-error">{{ $message }}</small> @enderror
                    </label>

                    <div class="cz-form-actions">
                        <a class="cz-profile-button cz-profile-button--secondary" href="{{ url('/places') }}">Cancel</a>
                        <button type="submit">Update Place</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
