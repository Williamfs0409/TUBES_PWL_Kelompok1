<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Report {{ $place->name }} | CityZen</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-dashboard-page" data-theme="light">
    <main class="cz-form-page">
        <section class="cz-form-card">
            <a class="cz-form-back" href="{{ url('/dashboard') }}">Back to dashboard</a>

            <div class="cz-form-heading">
                <span>Citizen Report</span>
                <h1>{{ $place->name }}</h1>
                <p>Laporkan kondisi ruang publik dengan detail yang jelas agar admin bisa memverifikasi lebih cepat.</p>
            </div>

            @if ($errors->any())
                <div class="cz-form-alert">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('reports.store', $place) }}" class="cz-form-grid">
                @csrf

                <label>
                    <span>Kategori</span>
                    <select name="report_category_id" required>
                        <option value="">Pilih kategori laporan</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('report_category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label>
                    <span>Deskripsi</span>
                    <textarea name="description" rows="6" placeholder="Contoh: Lampu taman mati di dekat pintu masuk barat." required>{{ old('description') }}</textarea>
                </label>

                <button type="submit">Kirim laporan</button>
            </form>
        </section>
    </main>
</body>
</html>
