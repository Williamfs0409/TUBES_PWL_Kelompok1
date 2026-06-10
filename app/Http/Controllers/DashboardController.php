<?php

namespace App\Http\Controllers;

use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $compactNumber = function (int $number): string {
            if ($number >= 1000) {
                return rtrim(rtrim(number_format($number / 1000, 1), '0'), '.').'k';
            }

            return (string) $number;
        };

        $initials = function (?string $name): string {
            $parts = collect(explode(' ', trim($name ?: 'CityZen User')))->filter()->values();

            return $parts->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') ?: 'CZ';
        };

        $feedPosts = collect();
        $trends = collect();
        $userId = $request->session()->get('cityzen_user.id');
        $likedPlaceIds = Schema::hasTable('likes') ? DB::table('likes')->where('user_id', $userId)->pluck('place_id') : collect();
        $bookmarkedPlaceIds = Schema::hasTable('bookmarks') ? DB::table('bookmarks')->where('user_id', $userId)->pluck('place_id') : collect();
        $repostedPlaceIds = Schema::hasTable('reposts') ? DB::table('reposts')->where('user_id', $userId)->pluck('place_id') : collect();

        if (Schema::hasTable('places')) {
            $placesQuery = DB::table('places')
                ->leftJoin('categories', 'categories.id', '=', 'places.category_id')
                ->leftJoin('users', 'users.id', '=', 'places.user_id')
                ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
                ->where('places.status', 'active')
                ->whereNull('places.deleted_at');

            if (Schema::hasTable('place_photos')) {
                $placesQuery->leftJoin('place_photos', function ($join) {
                    $join->on('place_photos.place_id', '=', 'places.id')
                        ->where('place_photos.is_cover', '=', true);
                });
            }

            $placesQuery->select([
                'places.id',
                'places.user_id',
                'places.name',
                'places.short_description',
                'places.description',
                'places.city',
                'places.province',
                'places.average_rating',
                'places.reviews_count',
                'places.likes_count',
                'places.bookmarks_count',
                'places.reports_count',
                'places.created_at',
                'categories.name as category_name',
                'users.name as user_name',
                'profiles.avatar_path as avatar_path',
                Schema::hasTable('reposts') ? DB::raw('(select count(*) from reposts where reposts.place_id = places.id) as reposts_count') : DB::raw('0 as reposts_count'),
                Schema::hasTable('place_photos') ? 'place_photos.image_path' : DB::raw('NULL as image_path'),
            ]);

            $feedPosts = (clone $placesQuery)
                ->orderByDesc('places.created_at')
                ->limit(15)
                ->get()
                ->map(function ($place) use ($compactNumber, $initials, $likedPlaceIds, $bookmarkedPlaceIds, $repostedPlaceIds, $userId) {
                    $author = $place->user_name ?: 'CityZen Citizen';
                    $location = collect([$place->city, $place->province])->filter()->implode(', ');
                    $description = $place->short_description ?: $place->description;

                    return [
                        'id' => $place->id,
                        'author_id' => $place->user_id,
                        'author' => $author,
                        'handle' => '@'.str($author)->slug('_'),
                        'time' => $place->created_at ? Carbon::parse($place->created_at)->diffForHumans(null, true).' ago' : 'baru',
                        'avatar' => $initials($author),
                        'avatar_image' => $place->avatar_path,
                        'verified' => false,
                        'lead' => $place->name,
                        'text' => trim($description.' '.($location ? 'Lokasi: '.$location.'.' : '')),
                        'image' => $place->image_path,
                        'image_alt' => $place->name,
                        'badge' => str($place->category_name ?: 'Public Space')->studly()->toString(),
                        'comments' => $compactNumber((int) $place->reviews_count),
                        'reposts' => $compactNumber((int) $place->reposts_count),
                        'likes' => $compactNumber((int) $place->likes_count),
                        'bookmarks' => $compactNumber((int) $place->bookmarks_count),
                        'rating' => number_format((float) $place->average_rating, 1),
                        'liked' => $likedPlaceIds->contains($place->id),
                        'bookmarked' => $bookmarkedPlaceIds->contains($place->id),
                        'reposted' => $repostedPlaceIds->contains($place->id),
                        'owned' => (int) $place->user_id === (int) $userId,
                    ];
                });

            $trends = (clone $placesQuery)
                ->orderByRaw('(places.likes_count + places.reviews_count + places.reports_count) DESC')
                ->orderByDesc('places.average_rating')
                ->limit(4)
                ->get()
                ->map(fn ($place) => [
                    'topic' => $place->category_name ?: 'Public Space',
                    'title' => $place->name,
                    'meta' => ((int) $place->likes_count).' likes - '.((int) $place->reports_count).' reports - '.$place->average_rating.' rating',
                ]);
        }

        return view('dashboard', [
            'feedPosts' => $feedPosts,
            'trends' => $trends,
            'isAdmin' => CityZenAccess::isAdmin($request->session()->get('cityzen_user')),
        ]);
    }
}
