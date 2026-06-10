<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function create(Request $request, Place $place)
    {
        if (! $request->session()->has('cityzen_user')) {
            return redirect('/login')->with('notice', 'Please login to report a place.');
        }

        $categories = ReportCategory::where('is_active', true)->orderBy('name')->get();

        if ($categories->isEmpty()) {
            collect(['Sampah', 'Kerusakan fasilitas', 'Keamanan', 'Aksesibilitas', 'Vandalisme', 'Lainnya'])
                ->each(function ($name) {
                    ReportCategory::firstOrCreate(
                        ['slug' => Str::slug($name)],
                        ['name' => $name, 'description' => null, 'is_active' => true]
                    );
                });

            $categories = ReportCategory::where('is_active', true)->orderBy('name')->get();
        }

        return view('reports.create', [
            'place' => $place,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, Place $place)
    {
        $userId = $request->session()->get('cityzen_user.id');

        if (! $userId) {
            return redirect('/login')->with('notice', 'Please login to report a place.');
        }

        $data = $request->validate([
            'report_category_id' => ['required', 'exists:report_categories,id'],
            'description' => ['required', 'string', 'max:800'],
            'photos' => ['nullable', 'array', 'max:6'],
            'photos.*' => ['image', 'max:4096'],
        ]);

        $pendingStatus = ReportStatus::firstOrCreate(
            ['slug' => 'pending'],
            ['name' => 'Pending', 'description' => 'Menunggu verifikasi admin', 'is_active' => true]
        );

        $report = Report::create([
            'user_id' => $userId,
            'place_id' => $place->id,
            'report_category_id' => $data['report_category_id'],
            'report_status_id' => $pendingStatus->id,
            'description' => $data['description'],
        ]);

        foreach ($request->file('photos', []) as $index => $photo) {
            DB::table('report_photos')->insert([
                'report_id' => $report->id,
                'image_path' => 'storage/'.$photo->store('reports', 'public'),
                'caption' => 'Report evidence '.($index + 1),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $place->update([
            'reports_count' => Report::where('place_id', $place->id)->count(),
        ]);

        return redirect('/dashboard')->with('status', 'Laporan berhasil dikirim dan menunggu verifikasi admin.');
    }
}
