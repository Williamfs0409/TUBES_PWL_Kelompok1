<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportStatus;
use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminReportController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.reports.index', [
            'reports' => Report::with(['place.user', 'place.category', 'category', 'status', 'user'])->latest()->get(),
            'statuses' => ReportStatus::orderBy('name')->get(),
            'isSuperAdmin' => CityZenAccess::isSuperAdmin($request->session()->get('cityzen_user')),
        ]);
    }

    public function updateStatus(Request $request, Report $report)
    {
        $data = $request->validate([
            'report_status_id' => ['required', 'exists:report_statuses,id'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $report->update([
            'report_status_id' => $data['report_status_id'],
            'admin_note' => $data['admin_note'] ?? null,
            'verified_by' => $request->session()->get('cityzen_user.id'),
            'verified_at' => now(),
        ]);

        $this->notifyReporter($report);

        return back()->with('status', 'Status laporan berhasil diperbarui.');
    }

    public function updateUploaderSuspension(Request $request, Report $report)
    {
        $data = $request->validate([
            'action' => ['required', 'in:suspend,restore'],
        ]);

        $report->loadMissing('place.user');
        $uploader = $report->place?->user;

        if (! $uploader || ! Schema::hasColumn('users', 'is_suspended')) {
            return back()->withErrors(['report' => 'Uploader postingan tidak ditemukan atau tabel user belum mendukung suspend.']);
        }

        if ((int) $uploader->id === (int) $request->session()->get('cityzen_user.id')) {
            return back()->withErrors(['report' => 'Admin tidak bisa mensuspend akun sendiri dari report queue.']);
        }

        $shouldSuspend = $data['action'] === 'suspend';

        $uploader->forceFill([
            'is_suspended' => $shouldSuspend,
            'suspended_at' => $shouldSuspend ? now() : null,
        ])->save();

        if ($shouldSuspend && $report->place && Schema::hasColumn('places', 'status')) {
            $report->place->update(['status' => 'hidden']);
        }

        return back()->with('status', $shouldSuspend
            ? 'Uploader berhasil disuspend dan postingan disembunyikan.'
            : 'Suspend uploader berhasil dicabut.');
    }

    private function notifyReporter(Report $report): void
    {
        if (! Schema::hasTable('notifications') || ! Schema::hasTable('notification_types')) {
            return;
        }

        $typeId = DB::table('notification_types')
            ->where('slug', 'report')
            ->orWhere('name', 'Report')
            ->value('id');

        if (! $typeId) {
            $payload = [
                'name' => 'Report',
                'description' => 'Report moderation updates',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('notification_types', 'slug')) {
                $payload['slug'] = Str::slug($payload['name']);
            }

            $typeId = DB::table('notification_types')->insertGetId($payload);
        }

        $statusName = $report->status?->name ?: 'Updated';

        DB::table('notifications')->insert([
            'user_id' => $report->user_id,
            'actor_id' => $report->verified_by,
            'notification_type_id' => $typeId,
            'title' => 'Status laporan diperbarui',
            'message' => 'Laporan kamu sekarang berstatus '.$statusName.'.',
            'related_table' => 'reports',
            'related_id' => $report->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
