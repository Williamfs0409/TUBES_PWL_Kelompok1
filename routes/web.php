<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\PlaceController;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function (Request $request) {
    if ($request->session()->has('cityzen_user')) {
        return redirect('/dashboard');
    }

    return view('auth.cityzen', ['mode' => 'login']);
})->name('login');

Route::post('/places/{place}/report', function (
    \Illuminate\Http\Request $request,
    \App\Models\Place $place
) {
    if (! session('cityzen_user')) {
        return redirect('/login');
    }

    $validated = $request->validate([
        'report_category_id' => ['required', 'exists:report_categories,id'],
        'description' => ['required', 'string', 'max:800'],
    ]);

    $pendingStatus = \App\Models\ReportStatus::firstOrCreate(
        ['slug' => 'pending'],
        [
            'name' => 'Pending',
            'description' => 'Menunggu verifikasi admin',
        ]
    );

    \App\Models\Report::create([
        'user_id' => session('cityzen_user.id'),
        'place_id' => $place->id,
        'report_category_id' => $validated['report_category_id'],
        'report_status_id' => $pendingStatus->id,
        'description' => $validated['description'],
    ]);

    return redirect('/dashboard')
        ->with('success', 'Laporan berhasil dikirim.');
})->name('reports.store');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'min:4'],
    ]);

    $user = User::where('email', $credentials['email'])->first();

    if (! $user || ! Hash::check($credentials['password'], $user->password)) {
        return back()
            ->withErrors(['email' => 'Email atau password CityZen belum sesuai.'])
            ->onlyInput('email');
    }

    $request->session()->put('cityzen_user', [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
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
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'min:4'],
    ]);

    $user = User::create($data);

    $request->session()->put('cityzen_user', [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
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
    $userId = $request->session()->get('cityzen_user.id');
    $likedPlaceIds = collect();
    $bookmarkedPlaceIds = collect();

    if ($userId && Schema::hasTable('likes')) {
        $likedPlaceIds = DB::table('likes')->where('user_id', $userId)->pluck('place_id');
    }

    if ($userId && Schema::hasTable('bookmarks')) {
        $bookmarkedPlaceIds = DB::table('bookmarks')->where('user_id', $userId)->pluck('place_id');
    }

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
            ->map(function ($place) use ($compactNumber, $initials, $likedPlaceIds, $bookmarkedPlaceIds) {
                $author = $place->user_name ?: 'CityZen Citizen';
                $location = collect([$place->city, $place->province])->filter()->implode(', ');
                $description = $place->short_description ?: $place->description;

                return [
                    'id' => $place->id,
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
                    'liked' => $likedPlaceIds->contains($place->id),
                    'bookmarked' => $bookmarkedPlaceIds->contains($place->id),
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
Route::get('/places/{place}/report', function (\App\Models\Place $place) {
    if (! session('cityzen_user')) {
        return redirect('/login');
    }

    $categories = \App\Models\Category::where('is_active', true)
    ->orderBy('sort_order')
    ->get();

    return view('places.create', compact('categories'));
})->name('reports.create');

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

Route::get('/admin/reports', function () {
    if (! session('cityzen_user')) {
        return redirect('/login');
    }

    $reports = \App\Models\Report::with(['place', 'category', 'status'])
        ->latest()
        ->get();

    $statuses = \App\Models\ReportStatus::orderBy('name')->get();

    return view('admin.reports.index', compact('reports', 'statuses'));
})->name('admin.reports');

Route::post('/admin/reports/{report}/status', function (\Illuminate\Http\Request $request, \App\Models\Report $report) {
    if (! session('cityzen_user')) {
        return redirect('/login');
    }
Route::post('/places/{place}/like', function (\App\Models\Place $place) {
    $userId = session('cityzen_user.id');

Route::post('/places/{place}/like', function (Request $request, int $place) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
        return redirect('/login')->with('notice', 'Please login to like a place.');
    }

    if (! Schema::hasTable('likes') || ! Schema::hasTable('places')) {
        return back()->with('status', 'Tabel likes belum tersedia.');
    }

    $existing = DB::table('likes')
        ->where('user_id', $userId)
        ->where('place_id', $place)
        ->first();

    if ($existing) {
        DB::table('likes')->where('id', $existing->id)->delete();
    } else {
        DB::table('likes')->insert([
            'user_id' => $userId,
            'place_id' => $place,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
})->name('places.like');

    DB::table('places')
        ->where('id', $place)
        ->update([
            'likes_count' => DB::table('likes')->where('place_id', $place)->count(),
            'updated_at' => now(),
        ]);

    return back()->with('status', $existing ? 'Like removed.' : 'Place liked.');
})->name('places.like');

Route::post('/places/{place}/bookmark', function (Request $request, int $place) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
        return redirect('/login')->with('notice', 'Please login to bookmark a place.');
    }

    if (! Schema::hasTable('bookmarks') || ! Schema::hasTable('places')) {
        return back()->with('status', 'Tabel bookmarks belum tersedia.');
    }

    $existing = DB::table('bookmarks')
        ->where('user_id', $userId)
        ->where('place_id', $place)
        ->first();

    if ($existing) {
        DB::table('bookmarks')->where('id', $existing->id)->delete();
    } else {
        DB::table('bookmarks')->insert([
            'user_id' => $userId,
            'place_id' => $place,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    DB::table('places')
        ->where('id', $place)
        ->update([
            'bookmarks_count' => DB::table('bookmarks')->where('place_id', $place)->count(),
            'updated_at' => now(),
        ]);

    return back()->with('status', $existing ? 'Bookmark removed.' : 'Place saved.');
})->name('places.bookmark');

Route::post('/places/{place}/review', function (Request $request, int $place) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
      
        return redirect('/login');
    }

    $validated = $request->validate([
        'report_status_id' => ['required', 'exists:report_statuses,id'],
        'admin_note' => ['nullable', 'string', 'max:500'],
    ]);

    $report->update([
        'report_status_id' => $validated['report_status_id'],
        'admin_note' => $validated['admin_note'] ?? null,
        'verified_by' => session('cityzen_user.id'),
        'verified_at' => now(),
    ]);

    return back()->with('success', 'Status laporan berhasil diperbarui.');
})->name('admin.reports.status');
$userId = session('cityzen_user.id');

if (!$userId) {
    return redirect('/login')->with('notice', 'Please login to review a place.');
}

$validated = $request->validate([
        'rating' => ['required', 'integer', 'min:1', 'max:5'],
        'review' => ['nullable', 'string', 'max:500'],
    ]);

    if (! Schema::hasTable('reviews') || ! Schema::hasTable('places')) {
        return back()->with('status', 'Tabel reviews belum tersedia.');
    }

    $existing = DB::table('reviews')
        ->where('user_id', $userId)
        ->where('place_id', $place)
        ->first();

    $payload = [
        'rating' => $data['rating'],
        'review' => $data['review'] ?? null,
        'updated_at' => now(),
    ];

    if ($existing) {
        DB::table('reviews')->where('id', $existing->id)->update($payload);
    } else {
        DB::table('reviews')->insert($payload + [
            'user_id' => $userId,
            'place_id' => $place,
            'created_at' => now(),
        ]);
    }

    $ratingStats = DB::table('reviews')
        ->where('place_id', $place)
        ->selectRaw('COUNT(*) as total, AVG(rating) as average')
        ->first();

    DB::table('places')
        ->where('id', $place)
        ->update([
            'reviews_count' => (int) $ratingStats->total,
            'average_rating' => round((float) $ratingStats->average, 2),
            'updated_at' => now(),
        ]);

    return back()->with('status', $existing ? 'Review updated.' : 'Review submitted.');
})->name('places.review');

Route::get('/places/{place}/report', function (Request $request, int $place) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to report a place.');
    }

    $placeData = Schema::hasTable('places')
        ? DB::table('places')->where('id', $place)->first()
        : null;

    abort_unless($placeData, 404);

    $categories = [
        'Sampah',
        'Kerusakan fasilitas',
        'Keamanan',
        'Aksesibilitas',
        'Vandalisme',
        'Lainnya',
    ];

    return view('reports.create', [
        'place' => $placeData,
        'categories' => $categories,
    ]);
})->name('reports.create');

Route::post('/places/{place}/report', function (Request $request, int $place) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
        return redirect('/login')->with('notice', 'Please login to report a place.');
    }

    $data = $request->validate([
        'category' => ['required', 'string', 'max:80'],
        'description' => ['required', 'string', 'max:800'],
    ]);

    if (! Schema::hasTable('reports') || ! Schema::hasTable('places')) {
        return back()->with('status', 'Tabel reports belum tersedia.');
    }

    $reportPayload = [
        'user_id' => $userId,
        'place_id' => $place,
        'description' => $data['description'],
        'admin_note' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ];

    if (Schema::hasColumn('reports', 'category')) {
        $reportPayload['category'] = $data['category'];
    } elseif (Schema::hasColumn('reports', 'report_category_id')) {
        $reportPayload['report_category_id'] = null;
    }

    if (Schema::hasColumn('reports', 'status')) {
        $reportPayload['status'] = 'pending';
    } elseif (Schema::hasColumn('reports', 'report_status_id')) {
        $reportPayload['report_status_id'] = null;
    }

    DB::table('reports')->insert($reportPayload);

    DB::table('places')
        ->where('id', $place)
        ->update([
            'reports_count' => DB::table('reports')->where('place_id', $place)->count(),
            'updated_at' => now(),
        ]);

    return redirect('/dashboard')->with('status', 'Laporan berhasil dikirim dan menunggu verifikasi admin.');
})->name('reports.store');

Route::get('/admin/reports', function (Request $request) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to open admin reports.');
    }

    $reports = collect();

    if (Schema::hasTable('reports')) {
        $reports = DB::table('reports')
            ->leftJoin('places', 'places.id', '=', 'reports.place_id')
            ->leftJoin('users', 'users.id', '=', 'reports.user_id')
            ->select([
                'reports.*',
                'places.name as place_name',
                'users.name as user_name',
            ])
            ->orderByDesc('reports.created_at')
            ->get();
    }

    return view('admin.reports.index', [
        'reports' => $reports,
        'statuses' => ['pending', 'verified', 'rejected', 'resolved'],
    ]);
})->name('admin.reports');

Route::post('/admin/reports/{report}/status', function (Request $request, int $report) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
        return redirect('/login')->with('notice', 'Please login to moderate reports.');
    }

    $data = $request->validate([
        'status' => ['required', 'in:pending,verified,rejected,resolved'],
        'admin_note' => ['nullable', 'string', 'max:500'],
    ]);

    $payload = [
        'admin_note' => $data['admin_note'] ?? null,
        'verified_by' => $userId,
        'verified_at' => now(),
        'updated_at' => now(),
    ];

    if (Schema::hasColumn('reports', 'status')) {
        $payload['status'] = $data['status'];
    }

    DB::table('reports')->where('id', $report)->update($payload);

    return back()->with('status', 'Status laporan berhasil diperbarui.');
})->name('admin.reports.status');

Route::resource('places', PlaceController::class);

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
