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
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Reports | CityZen</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=hanken-grotesk:700,800|inter:400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cz-dashboard-page" data-theme="light">
    <main class="cz-admin-page">
        <header class="cz-admin-header">
            <div>
                <a href="{{ url('/dashboard') }}">Back to dashboard</a>
                <h1>Report Moderation</h1>
                <p>Verifikasi laporan warga, beri catatan admin, lalu ubah status sesuai kondisi lapangan.</p>
            </div>
        </header>

        @if (session('status'))
            <div class="cz-form-alert cz-form-alert--success">{{ session('status') }}</div>
        @endif

        <section class="cz-admin-report-list">
            @forelse ($reports as $report)
                @php
                    $status = $report->status ?? ($report->report_status_id ? 'status #'.$report->report_status_id : 'pending');
                    $category = $report->category ?? ($report->report_category_id ? 'category #'.$report->report_category_id : 'report');
                @endphp

                <article class="cz-admin-report-card">
                    <div>
                        <span>{{ $category }} &middot; {{ $status }}</span>
                        <h2>{{ $report->place_name ?? 'Unknown place' }}</h2>
                        <p>{{ $report->description }}</p>
                        <small>Submitted by {{ $report->user_name ?? 'CityZen user' }}</small>
                    </div>

                    <form method="POST" action="{{ route('admin.reports.status', $report->id) }}">
                        @csrf
                        <select name="status" required>
                            @foreach ($statuses as $item)
                                <option value="{{ $item }}" @selected($status === $item)>{{ ucfirst($item) }}</option>
                            @endforeach
                        </select>
                        <input name="admin_note" value="{{ $report->admin_note }}" placeholder="Catatan admin">
                        <button type="submit">Update</button>
                    </form>
                </article>
            @empty
                <article class="cz-dash-empty">
                    <h2>Belum ada laporan masuk.</h2>
                    <p>Setelah user membuat laporan dari feed, laporan akan muncul di halaman ini.</p>
                </article>
            @endforelse
        </section>
    </main>
</body>
</html>
