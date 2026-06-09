<!DOCTYPE html>
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
                <article class="cz-admin-report-card">
                    <div>
                        <span>{{ $report->category->name ?? 'Report' }} &middot; {{ $report->status->name ?? 'Pending' }}</span>
                        <h2>{{ $report->place->name ?? 'Unknown place' }}</h2>
                        <p>{{ $report->description }}</p>
                        <small>Submitted by {{ $report->user->name ?? 'CityZen user' }}</small>
                    </div>

                    <form method="POST" action="{{ route('admin.reports.status', $report) }}">
                        @csrf
                        <select name="report_status_id" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->id }}" @selected($report->report_status_id === $status->id)>
                                    {{ $status->name }}
                                </option>
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
