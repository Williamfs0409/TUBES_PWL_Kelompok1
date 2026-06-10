<?php

namespace App\Http\Controllers;

use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->session()->get('cityzen_user.id');
        $bookmarks = collect();

        if (Schema::hasTable('bookmarks') && Schema::hasTable('places')) {
            $bookmarks = DB::table('bookmarks')
                ->join('places', 'places.id', '=', 'bookmarks.place_id')
                ->leftJoin('categories', 'categories.id', '=', 'places.category_id')
                ->where('bookmarks.user_id', $userId)
                ->whereNull('places.deleted_at')
                ->when($request->filled('q'), function ($query) use ($request) {
                    $term = '%'.$request->string('q')->trim().'%';

                    $query->where(function ($inner) use ($term) {
                        $inner->where('places.name', 'like', $term)
                            ->orWhere('places.short_description', 'like', $term)
                            ->orWhere('places.city', 'like', $term)
                            ->orWhere('categories.name', 'like', $term);
                    });
                })
                ->select([
                    'bookmarks.created_at as saved_at',
                    'places.id',
                    'places.name',
                    'places.short_description',
                    'places.city',
                    'places.province',
                    'places.average_rating',
                    'places.likes_count',
                    'places.reviews_count',
                    'categories.name as category_name',
                ])
                ->orderByDesc('bookmarks.created_at')
                ->get();
        }

        return view('bookmarks', [
            'bookmarks' => $bookmarks,
            'isAdmin' => CityZenAccess::isAdmin($request->session()->get('cityzen_user')),
        ]);
    }
}
