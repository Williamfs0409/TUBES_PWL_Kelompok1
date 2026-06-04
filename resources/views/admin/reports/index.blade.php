<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Reports - CityZen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="font-family: Arial, sans-serif; background: #f4faf4; color: #101810;">
    <main style="max-width: 1100px; margin: 40px auto; padding: 0 20px;">
        <a href="/dashboard" style="color: #0d5c22; font-weight: 700;">Kembali ke Dashboard</a>

        <h1 style="font-size: 36px; margin: 24px 0;">Moderasi Laporan</h1>

        @if (session('success'))
            <div style="background: #dff7df; border: 1px solid #0d5c22; padding: 14px; border-radius: 12px; margin-bottom: 18px;">
                {{ session('success') }}
            </div>
        @endif

        <div style="display: grid; gap: 16px;">
            @forelse ($reports as $report)
                <article style="background: white; border: 1px solid #163b1f; border-radius: 16px; padding: 20px;">
                    <strong>{{ $report->place->name ?? 'Tempat tidak ditemukan' }}</strong>
                    <p style="color: #526158;">
                        Kategori: {{ $report->category->name ?? '-' }} |
                        Status: {{ $report->status->name ?? '-' }}
                    </p>

                    <p>{{ $report->description }}</p>

                    <form method="POST" action="{{ route('admin.reports.status', $report) }}" style="display: grid; grid-template-columns: 180px 1fr auto; gap: 10px; margin-top: 16px;">
                        @csrf

                        <select name="report_status_id" required style="padding: 10px; border-radius: 10px;">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" @selected($report->report_status_id === $status->id)>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>

                        <input name="admin_note" value="{{ $report->admin_note }}" placeholder="Catatan admin..." style="padding: 10px; border-radius: 10px; border: 1px solid #aaa;">

                        <button type="submit" style="background: #0d5c22; color: white; border: 0; border-radius: 999px; padding: 10px 18px; font-weight: 800;">
                            Update
                        </button>
                    </form>
                </article>
            @empty
                <div style="background: white; border: 1px solid #163b1f; border-radius: 16px; padding: 28px;">
                    Belum ada laporan masuk.
                </div>
            @endforelse
        </div>
    </main>
</body>
</html>