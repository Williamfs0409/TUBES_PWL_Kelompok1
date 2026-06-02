<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function (Request $request) {
    if ($request->session()->has('cityzen_user')) {
        return redirect('/dashboard');
    }

    return view('auth.cityzen', ['mode' => 'login']);
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'min:4'],
    ]);

    $request->session()->put('cityzen_user', [
        'name' => str($credentials['email'])->before('@')->headline()->toString(),
        'email' => $credentials['email'],
    ]);

    $request->session()->regenerate();

    return redirect('/dashboard');
});

Route::get('/register', function (Request $request) {
    if ($request->session()->has('cityzen_user')) {
        return redirect('/dashboard');
    }

    return view('auth.cityzen', ['mode' => 'register']);
});

Route::post('/register', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:80'],
        'email' => ['required', 'email'],
        'password' => ['required', 'min:4'],
    ]);

    $request->session()->put('cityzen_user', [
        'name' => $data['name'],
        'email' => $data['email'],
    ]);

    $request->session()->regenerate();

    return redirect('/dashboard');
});

Route::get('/dashboard', function (Request $request) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to open the CityZen dashboard.');
    }

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

    if (Schema::hasTable('places')) {
        $placesQuery = DB::table('places')
            ->leftJoin('categories', 'categories.id', '=', 'places.category_id')
            ->leftJoin('users', 'users.id', '=', 'places.user_id')
            ->leftJoin('place_photos', function ($join) {
                $join->on('place_photos.place_id', '=', 'places.id')
                    ->where('place_photos.is_cover', '=', true);
            })
            ->where('places.status', 'active')
            ->whereNull('places.deleted_at')
            ->select([
                'places.id',
                'places.name',
                'places.short_description',
                'places.description',
                'places.city',
                'places.province',
                'places.average_rating',
                'places.reviews_count',
                'places.likes_count',
                'places.reports_count',
                'places.created_at',
                'categories.name as category_name',
                'users.name as user_name',
                'place_photos.image_path',
            ]);

        $feedPosts = (clone $placesQuery)
            ->orderByDesc('places.created_at')
            ->limit(10)
            ->get()
            ->map(function ($place) use ($compactNumber, $initials) {
                $author = $place->user_name ?: 'CityZen Citizen';
                $location = collect([$place->city, $place->province])->filter()->implode(', ');
                $description = $place->short_description ?: $place->description;

                return [
                    'author' => $author,
                    'handle' => '@'.str($author)->slug('_'),
                    'time' => $place->created_at ? \Illuminate\Support\Carbon::parse($place->created_at)->diffForHumans(null, true).' ago' : 'baru',
                    'avatar' => $initials($author),
                    'verified' => false,
                    'lead' => $place->name,
                    'text' => trim($description.' '.($location ? 'Lokasi: '.$location.'.' : '')),
                    'image' => $place->image_path,
                    'image_alt' => $place->name,
                    'badge' => str($place->category_name ?: 'Public Space')->studly()->toString(),
                    'comments' => $compactNumber((int) $place->reviews_count),
                    'reposts' => $compactNumber((int) $place->reports_count),
                    'likes' => $compactNumber((int) $place->likes_count),
                    'rating' => number_format((float) $place->average_rating, 1),
                ];
            });

        $trends = (clone $placesQuery)
            ->orderByRaw('(places.likes_count + places.reviews_count + places.reports_count) DESC')
            ->orderByDesc('places.average_rating')
            ->limit(4)
            ->get()
            ->map(function ($place) {
                return [
                    'topic' => $place->category_name ?: 'Public Space',
                    'title' => $place->name,
                    'meta' => ((int) $place->likes_count).' likes · '.((int) $place->reports_count).' reports · '.$place->average_rating.' rating',
                ];
            });
    }

    return view('dashboard', [
        'feedPosts' => $feedPosts,
        'trends' => $trends,
    ]);
});

Route::get('/explore', function (Request $request) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to explore CityZen spaces.');
    }

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
                    'meta' => $compactNumber((int) $place->likes_count).' likes · '.$compactNumber((int) $place->reports_count).' reports · '.number_format((float) $place->average_rating, 1).' rating',
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
            ->select('reports.report_category_id', 'reports.report_status_id', 'reports.description', 'places.name as place_name')
            ->orderByDesc('reports.created_at')
            ->limit(4)
            ->get()
            ->map(function ($report) {
                return [
                    'place_name' => $report->place_name ?: 'Unknown place',
                    'category' => $report->report_category_id ? 'Category #'.$report->report_category_id : 'Report',
                    'status' => $report->report_status_id ? 'Status #'.$report->report_status_id : 'Pending',
                    'description' => $report->description,
                ];
            });
    }

    return view('explore', [
        'places' => $places,
        'categories' => $categories,
        'reports' => $reports,
    ]);
});

Route::get('/places/create', function (Request $request) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login before starting a report.');
    }

    return view('places.create');
});

Route::post('/places', function (Request $request) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login before starting a report.');
    }

    $data = $request->validate([
        'place_name' => ['required', 'string', 'max:100'],
        'category' => ['required', 'string', 'max:60'],
        'issue' => ['required', 'string', 'max:120'],
        'description' => ['required', 'string', 'max:500'],
    ]);

    $request->session()->put('cityzen_last_report', $data);

    return redirect('/dashboard')->with('status', 'Report draft saved for '.$data['place_name'].'.');
});

Route::get('/profile', function (Request $request) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to open your CityZen profile.');
    }

    return view('profile');
});

Route::post('/logout', function (Request $request) {
    $request->session()->forget('cityzen_user');
    $request->session()->forget('cityzen_last_report');
    $request->session()->regenerateToken();

    return redirect('/');
});
