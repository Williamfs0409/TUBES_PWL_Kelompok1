<?php

namespace App\Http\Controllers;

use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExploreController extends Controller
{
    public function __invoke(Request $request)
    {
        $compactNumber = function (int $number): string {
            if ($number >= 1000) {
                return rtrim(rtrim(number_format($number / 1000, 1), '0'), '.').'k';
            }

            return (string) $number;
        };

        $places = collect();
        $categories = collect();
        $reports = collect();

        if (Schema::hasTable('places')) {
            $places = DB::table('places')
                ->leftJoin('categories', 'categories.id', '=', 'places.category_id')
                ->where('places.status', 'active')
                ->whereNull('places.deleted_at')
                ->select([
                    'places.name',
                    'places.short_description',
                    'places.city',
                    'places.province',
                    'places.average_rating',
                    'places.reviews_count',
                    'places.likes_count',
                    'places.reports_count',
                    'categories.name as category_name',
                ])
                ->orderByRaw('(places.likes_count + places.reviews_count + places.reports_count) DESC')
                ->orderByDesc('places.average_rating')
                ->limit(12)
                ->get()
                ->map(function ($place) use ($compactNumber) {
                    $location = collect([$place->city, $place->province])->filter()->implode(', ');

                    return [
                        'category' => $place->category_name ?: 'Public Space',
                        'title' => $place->name,
                        'description' => $place->short_description ?: 'Ruang publik CityZen.',
                        'location' => $location ?: 'Lokasi belum diisi',
                        'meta' => $compactNumber((int) $place->likes_count).' likes - '.$compactNumber((int) $place->reports_count).' reports - '.number_format((float) $place->average_rating, 1).' rating',
                    ];
                });
        }

        if (Schema::hasTable('categories')) {
            $categories = DB::table('categories')
                ->where('is_active', true)
                ->orderBy('name')
                ->limit(5)
                ->pluck('name');
        }

        if (Schema::hasTable('reports')) {
            $reports = DB::table('reports')
                ->leftJoin('places', 'places.id', '=', 'reports.place_id')
                ->leftJoin('report_categories', 'report_categories.id', '=', 'reports.report_category_id')
                ->leftJoin('report_statuses', 'report_statuses.id', '=', 'reports.report_status_id')
                ->select([
                    'reports.description',
                    'places.name as place_name',
                    'report_categories.name as category_name',
                    'report_statuses.name as status_name',
                ])
                ->orderByDesc('reports.created_at')
                ->limit(4)
                ->get()
                ->map(fn ($report) => [
                    'place_name' => $report->place_name ?: 'Unknown place',
                    'category' => $report->category_name ?: 'Report',
                    'status' => $report->status_name ?: 'Pending',
                    'description' => $report->description,
                ]);
        }

        return view('explore', [
            'places' => $places,
            'categories' => $categories,
            'reports' => $reports,
            'isAdmin' => CityZenAccess::isAdmin($request->session()->get('cityzen_user')),
        ]);
    }
}
