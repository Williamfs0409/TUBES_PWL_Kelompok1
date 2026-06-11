<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Reports | CityZen</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
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
                    <span class="cz-profile-eyebrow">Moderation queue</span>
                    <h1>Report Moderation</h1>
                    <p>Verifikasi laporan warga, beri catatan admin, lalu ubah status sesuai kondisi lapangan.</p>
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

            @include('admin.partials.nav', ['activeAdmin' => 'reports'])

            @if (session('status'))
                <div class="cz-form-alert cz-form-alert--success">{{ session('status') }}</div>
            @endif

            <nav class="cz-admin-report-filters" aria-label="Filter laporan">
                @foreach (['all' => 'Semua', 'pending' => 'Pending', 'verified' => 'Verified', 'rejected' => 'Rejected'] as $filterKey => $filterLabel)
                    <a class="{{ $statusFilter === $filterKey ? 'is-active' : '' }}" href="{{ route('admin.reports', $filterKey === 'all' ? [] : ['status' => $filterKey]) }}">
                        <span>{{ $filterLabel }}</span>
                        <strong>{{ $statusCounts[$filterKey] ?? 0 }}</strong>
                    </a>
                @endforeach
            </nav>

            <section class="cz-admin-report-list">
                @forelse ($reports as $report)
                    @php
                        $statusKey = \Illuminate\Support\Str::slug($report->status->slug ?: ($report->status->name ?? 'pending'));
                        $statusName = $report->status->name ?? 'Pending';
                        $placeName = $report->place->name ?? 'Unknown place';
                        $uploader = $report->place?->user;
                    @endphp
                    <article class="cz-admin-report-card">
                        <div class="cz-admin-report-topline">
                            <div>
                                <span>{{ $report->category->name ?? 'Report' }} &middot; {{ $statusName }}</span>
                                <h2>{{ $placeName }}</h2>
                                <div class="cz-admin-report-description">
                                    <span>Deskripsi laporan</span>
                                    <p>{{ $report->description }}</p>
                                </div>
                                <small>Submitted by {{ $report->user->name ?? 'CityZen user' }} &middot; {{ optional($report->created_at)->diffForHumans() }}</small>
                            </div>
                            <span class="cz-admin-status-pill is-{{ $statusKey }}">
                                <i aria-hidden="true"></i>
                                {{ $statusName }}
                            </span>
                        </div>

                        <div class="cz-admin-report-context">
                            <span>Post: <strong>{{ $placeName }}</strong></span>
                            <span>{{ $report->place->category->name ?? 'Public Space' }} &middot; {{ $report->place->status ?? 'active' }}</span>
                            <span>Uploader: <strong>{{ $uploader->name ?? 'Unknown user' }}</strong> &middot; {{ $uploader->email ?? 'No email' }} &middot; {{ $uploader?->is_suspended ? 'Suspended' : 'Active' }}</span>
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

                        @if ($report->place?->user)
                            <form class="cz-admin-suspend-form" method="POST" action="{{ route('admin.reports.uploader-suspension', $report) }}">
                                @csrf
                                @if ($report->place->user->is_suspended)
                                    <input type="hidden" name="action" value="restore">
                                    <button class="cz-admin-secondary-button" type="submit">Restore uploader</button>
                                @else
                                    <input type="hidden" name="action" value="suspend">
                                    <button class="cz-list-danger" type="submit" onclick="return confirm('Suspend uploader postingan ini dan sembunyikan post terkait?')">Suspend uploader</button>
                                @endif
                            </form>
                        @endif
                    </article>
                @empty
                    <article class="cz-list-empty">
                        <h2>Belum ada laporan masuk.</h2>
                        <p>Setelah user membuat laporan dari feed, laporan akan muncul di halaman ini.</p>
                    </article>
                @endforelse
            </section>
        </main>
    </div>
</body>
</html>
