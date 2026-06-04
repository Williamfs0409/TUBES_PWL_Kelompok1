<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Report - CityZen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="font-family: Arial, sans-serif; background: #eef8ef; color: #101810;">
    <main style="max-width: 720px; margin: 48px auto; background: white; border: 1px solid #163b1f; border-radius: 18px; padding: 28px;">
        <a href="/dashboard" style="color: #0d5c22; font-weight: 700;">Kembali</a>

        <h1 style="font-size: 32px; margin-top: 24px;">Laporkan Kondisi Tempat</h1>
        <p style="color: #526158;">Tempat: <strong>{{ $place->name }}</strong></p>

        @if ($errors->any())
            <div style="background: #ffe8e8; border: 1px solid #d33; padding: 14px; border-radius: 12px; margin: 18px 0;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('reports.store', $place) }}" style="display: grid; gap: 18px; margin-top: 24px;">
            @csrf

            <label>
                <span style="display: block; font-weight: 700; margin-bottom: 8px;">Kategori Laporan</span>
                <select name="report_category_id" required style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #163b1f;">
                    <option value="">Pilih kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span style="display: block; font-weight: 700; margin-bottom: 8px;">Deskripsi</span>
                <textarea name="description" rows="6" required placeholder="Contoh: Ada fasilitas rusak di area taman..." style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #163b1f;"></textarea>
            </label>

            <button type="submit" style="background: #0d5c22; color: white; border: 0; border-radius: 999px; padding: 14px 22px; font-weight: 800; cursor: pointer;">
                Kirim Laporan
            </button>
        </form>
    </main>
</body>
</html>
