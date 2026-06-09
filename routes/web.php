<?php

use App\Http\Controllers\PlaceController;
use App\Models\Category;
use App\Models\Place;
use App\Models\Report;
use App\Models\ReportCategory;
use App\Models\ReportStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome');
});

$isAdminUser = function (?array $sessionUser): bool {
    $userId = $sessionUser['id'] ?? null;

    if (! $userId || ! Schema::hasTable('users') || ! Schema::hasTable('roles')) {
        return false;
    }

    $roleName = DB::table('users')
        ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
        ->where('users.id', $userId)
        ->value('roles.name');

    return in_array(strtolower((string) ($roleName ?? '')), ['admin', 'superadmin'], true);
};

$sessionPayload = function (User $user): array {
    $roleName = null;

    if (Schema::hasTable('roles') && Schema::hasColumn('users', 'role_id')) {
        $roleName = DB::table('roles')->where('id', $user->role_id)->value('name');
    }

    return [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $roleName ?: 'user',
    ];
};

$profileStats = function (int $userId): array {
    return [
        'watched_places' => Schema::hasTable('bookmarks') ? DB::table('bookmarks')->where('user_id', $userId)->count() : 0,
        'reports_drafted' => Schema::hasTable('reports') ? DB::table('reports')->where('user_id', $userId)->count() : 0,
        'reviews_count' => Schema::hasTable('reviews') ? DB::table('reviews')->where('user_id', $userId)->count() : 0,
        'likes_count' => Schema::hasTable('likes') ? DB::table('likes')->where('user_id', $userId)->count() : 0,
    ];
};

Route::get('/login', function (Request $request) {
    if ($request->session()->has('cityzen_user')) {
        return redirect('/dashboard');
    }

    return view('auth.cityzen', ['mode' => 'login']);
})->name('login');

Route::post('/login', function (Request $request) use ($sessionPayload) {
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

    $request->session()->put('cityzen_user', $sessionPayload($user));
    $request->session()->regenerate();

    return redirect('/dashboard');
});

Route::get('/register', function (Request $request) {
    if ($request->session()->has('cityzen_user')) {
        return redirect('/dashboard');
    }

    return view('auth.cityzen', ['mode' => 'register']);
});

Route::post('/register', function (Request $request) use ($sessionPayload) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:80'],
        'email' => ['required', 'email', 'unique:users,email'],
        'password' => ['required', 'min:4'],
    ]);

    $user = User::create($data);

    $request->session()->put('cityzen_user', $sessionPayload($user));
    $request->session()->regenerate();

    return redirect('/dashboard');
});

Route::get('/dashboard', function (Request $request) use ($isAdminUser) {
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
            Schema::hasTable('place_photos') ? 'place_photos.image_path' : DB::raw('NULL as image_path'),
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
        'isAdmin' => $isAdminUser($request->session()->get('cityzen_user')),
    ]);
});

Route::get('/explore', function (Request $request) use ($isAdminUser) {
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
            ->map(function ($report) {
                return [
                    'place_name' => $report->place_name ?: 'Unknown place',
                    'category' => $report->category_name ?: 'Report',
                    'status' => $report->status_name ?: 'Pending',
                    'description' => $report->description,
                ];
            });
    }

    return view('explore', [
        'places' => $places,
        'categories' => $categories,
        'reports' => $reports,
        'isAdmin' => $isAdminUser($request->session()->get('cityzen_user')),
    ]);
});

Route::post('/places/{place}/like', function (Request $request, Place $place) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
        return redirect('/login')->with('notice', 'Please login to like a place.');
    }

    $existing = DB::table('likes')
        ->where('user_id', $userId)
        ->where('place_id', $place->id)
        ->first();

    if ($existing) {
        DB::table('likes')->where('id', $existing->id)->delete();
    } else {
        DB::table('likes')->insert([
            'user_id' => $userId,
            'place_id' => $place->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $place->update([
        'likes_count' => DB::table('likes')->where('place_id', $place->id)->count(),
    ]);

    return back()->with('status', $existing ? 'Like removed.' : 'Place liked.');
})->name('places.like');

Route::post('/places/{place}/bookmark', function (Request $request, Place $place) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
        return redirect('/login')->with('notice', 'Please login to bookmark a place.');
    }

    $existing = DB::table('bookmarks')
        ->where('user_id', $userId)
        ->where('place_id', $place->id)
        ->first();

    if ($existing) {
        DB::table('bookmarks')->where('id', $existing->id)->delete();
    } else {
        DB::table('bookmarks')->insert([
            'user_id' => $userId,
            'place_id' => $place->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    $place->update([
        'bookmarks_count' => DB::table('bookmarks')->where('place_id', $place->id)->count(),
    ]);

    return back()->with('status', $existing ? 'Bookmark removed.' : 'Place saved.');
})->name('places.bookmark');

Route::post('/places/{place}/review', function (Request $request, Place $place) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
        return redirect('/login')->with('notice', 'Please login to review a place.');
    }

    $data = $request->validate([
        'rating' => ['required', 'integer', 'min:1', 'max:5'],
        'review' => ['nullable', 'string', 'max:500'],
    ]);

    DB::table('reviews')->updateOrInsert(
        [
            'user_id' => $userId,
            'place_id' => $place->id,
        ],
        [
            'rating' => $data['rating'],
            'review' => $data['review'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    $ratingStats = DB::table('reviews')
        ->where('place_id', $place->id)
        ->selectRaw('COUNT(*) as total, AVG(rating) as average')
        ->first();

    $place->update([
        'reviews_count' => (int) $ratingStats->total,
        'average_rating' => round((float) $ratingStats->average, 2),
    ]);

    return back()->with('status', 'Review submitted.');
})->name('places.review');

Route::get('/places/{place}/report', function (Request $request, Place $place) {
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
})->name('reports.create');

Route::post('/places/{place}/report', function (Request $request, Place $place) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
        return redirect('/login')->with('notice', 'Please login to report a place.');
    }

    $data = $request->validate([
        'report_category_id' => ['required', 'exists:report_categories,id'],
        'description' => ['required', 'string', 'max:800'],
    ]);

    $pendingStatus = ReportStatus::firstOrCreate(
        ['slug' => 'pending'],
        ['name' => 'Pending', 'description' => 'Menunggu verifikasi admin', 'is_active' => true]
    );

    Report::create([
        'user_id' => $userId,
        'place_id' => $place->id,
        'report_category_id' => $data['report_category_id'],
        'report_status_id' => $pendingStatus->id,
        'description' => $data['description'],
    ]);

    $place->update([
        'reports_count' => Report::where('place_id', $place->id)->count(),
    ]);

    return redirect('/dashboard')->with('status', 'Laporan berhasil dikirim dan menunggu verifikasi admin.');
})->name('reports.store');

Route::get('/admin/reports', function (Request $request) use ($isAdminUser) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to open admin reports.');
    }

    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    $reports = Report::with(['place', 'category', 'status', 'user'])
        ->latest()
        ->get();

    $statuses = ReportStatus::orderBy('name')->get();

    return view('admin.reports.index', compact('reports', 'statuses'));
})->name('admin.reports');

Route::post('/admin/reports/{report}/status', function (Request $request, Report $report) use ($isAdminUser) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
        return redirect('/login')->with('notice', 'Please login to moderate reports.');
    }

    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    $data = $request->validate([
        'report_status_id' => ['required', 'exists:report_statuses,id'],
        'admin_note' => ['nullable', 'string', 'max:500'],
    ]);

    $report->update([
        'report_status_id' => $data['report_status_id'],
        'admin_note' => $data['admin_note'] ?? null,
        'verified_by' => $userId,
        'verified_at' => now(),
    ]);

    return back()->with('status', 'Status laporan berhasil diperbarui.');
})->name('admin.reports.status');

Route::resource('places', PlaceController::class);

Route::get('/bookmarks', function (Request $request) use ($isAdminUser) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
        return redirect('/login')->with('notice', 'Please login to open your bookmarks.');
    }

    $bookmarks = collect();

    if (Schema::hasTable('bookmarks') && Schema::hasTable('places')) {
        $bookmarks = DB::table('bookmarks')
            ->join('places', 'places.id', '=', 'bookmarks.place_id')
            ->leftJoin('categories', 'categories.id', '=', 'places.category_id')
            ->where('bookmarks.user_id', $userId)
            ->whereNull('places.deleted_at')
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
        'isAdmin' => $isAdminUser($request->session()->get('cityzen_user')),
    ]);
})->name('bookmarks.index');

Route::get('/notifications', function (Request $request) use ($isAdminUser) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
        return redirect('/login')->with('notice', 'Please login to open notifications.');
    }

    $notifications = collect();

    if (Schema::hasTable('notifications')) {
        $notifications = DB::table('notifications')
            ->leftJoin('notification_types', 'notification_types.id', '=', 'notifications.notification_type_id')
            ->leftJoin('users as actors', 'actors.id', '=', 'notifications.actor_id')
            ->where('notifications.user_id', $userId)
            ->select([
                'notifications.title',
                'notifications.message',
                'notifications.read_at',
                'notifications.created_at',
                'notification_types.name as type_name',
                'actors.name as actor_name',
            ])
            ->orderByDesc('notifications.created_at')
            ->limit(30)
            ->get();
    }

    return view('notifications', [
        'notifications' => $notifications,
        'isAdmin' => $isAdminUser($request->session()->get('cityzen_user')),
    ]);
})->name('notifications.index');

Route::get('/profile', function (Request $request) use ($isAdminUser, $profileStats) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to open your CityZen profile.');
    }

    $userId = $request->session()->get('cityzen_user.id');

    return view('profile', [
        'isAdmin' => $isAdminUser($request->session()->get('cityzen_user')),
        'stats' => $profileStats((int) $userId),
    ]);
});

Route::post('/logout', function (Request $request) {
    $request->session()->forget('cityzen_user');
    $request->session()->forget('cityzen_last_report');
    $request->session()->regenerateToken();

    return redirect('/');
});