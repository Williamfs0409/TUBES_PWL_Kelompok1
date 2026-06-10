<?php

namespace App\Http\Controllers;

use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $pendingReports = 0;

        if (Schema::hasTable('reports')) {
            if (Schema::hasTable('report_statuses') && Schema::hasColumn('report_statuses', 'slug')) {
                $pendingReports = DB::table('reports')
                    ->leftJoin('report_statuses', 'report_statuses.id', '=', 'reports.report_status_id')
                    ->where('report_statuses.slug', 'pending')
                    ->count();
            } elseif (Schema::hasTable('report_statuses') && Schema::hasColumn('report_statuses', 'name')) {
                $pendingReports = DB::table('reports')
                    ->leftJoin('report_statuses', 'report_statuses.id', '=', 'reports.report_status_id')
                    ->whereRaw('LOWER(report_statuses.name) = ?', ['pending'])
                    ->count();
            } elseif (Schema::hasColumn('reports', 'status')) {
                $pendingReports = DB::table('reports')->where('status', 'pending')->count();
            }
        }

        $stats = [
            'users' => Schema::hasTable('users') ? DB::table('users')->count() : 0,
            'places' => Schema::hasTable('places') ? DB::table('places')->whereNull('deleted_at')->count() : 0,
            'reports' => Schema::hasTable('reports') ? DB::table('reports')->count() : 0,
            'pending_reports' => $pendingReports,
            'reviews' => Schema::hasTable('reviews') ? DB::table('reviews')->count() : 0,
            'likes' => Schema::hasTable('likes') ? DB::table('likes')->count() : 0,
            'bookmarks' => Schema::hasTable('bookmarks') ? DB::table('bookmarks')->count() : 0,
        ];

        $topPlaces = Schema::hasTable('places')
            ? DB::table('places')
                ->leftJoin('categories', 'categories.id', '=', 'places.category_id')
                ->whereNull('places.deleted_at')
                ->select('places.name', 'places.city', 'places.likes_count', 'places.reports_count', 'places.average_rating', 'categories.name as category_name')
                ->orderByRaw('(places.likes_count + places.reports_count + places.reviews_count) DESC')
                ->limit(5)
                ->get()
            : collect();

        $categoryActivity = Schema::hasTable('places')
            ? DB::table('places')
                ->leftJoin('categories', 'categories.id', '=', 'places.category_id')
                ->whereNull('places.deleted_at')
                ->selectRaw('COALESCE(categories.name, "Uncategorized") as category_label, COUNT(places.id) as total')
                ->groupByRaw('COALESCE(categories.name, "Uncategorized")')
                ->orderByDesc('total')
                ->limit(6)
                ->get()
            : collect();

        return view('admin.dashboard', [
            'stats' => $stats,
            'topPlaces' => $topPlaces,
            'categoryActivity' => $categoryActivity,
            'isSuperAdmin' => CityZenAccess::isSuperAdmin($request->session()->get('cityzen_user')),
        ]);
    }
}
