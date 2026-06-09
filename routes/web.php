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

$isSuperAdminUser = function (?array $sessionUser): bool {
    $userId = $sessionUser['id'] ?? null;

    if (! $userId || ! Schema::hasTable('users') || ! Schema::hasTable('roles')) {
        return false;
    }

    $roleName = DB::table('users')
        ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
        ->where('users.id', $userId)
        ->value('roles.name');

    return strtolower((string) ($roleName ?? '')) === 'superadmin';
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

    if (Schema::hasColumn('users', 'is_suspended') && $user->is_suspended) {
        return back()
            ->withErrors(['email' => 'Akun CityZen ini sedang disuspend oleh admin.'])
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

    if (Schema::hasTable('roles') && Schema::hasColumn('users', 'role_id')) {
        $data['role_id'] = DB::table('roles')->where('slug', 'user')->value('id');
    }

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

Route::get('/admin', function (Request $request) use ($isAdminUser, $isSuperAdminUser) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to open admin panel.');
    }

    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

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
        'isSuperAdmin' => $isSuperAdminUser($request->session()->get('cityzen_user')),
    ]);
})->name('admin.dashboard');

Route::get('/admin/reports', function (Request $request) use ($isAdminUser, $isSuperAdminUser) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to open admin reports.');
    }

    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    $reports = Report::with(['place.user', 'place.category', 'category', 'status', 'user'])
        ->latest()
        ->get();

    $statuses = ReportStatus::orderBy('name')->get();

    return view('admin.reports.index', [
        'reports' => $reports,
        'statuses' => $statuses,
        'isSuperAdmin' => $isSuperAdminUser($request->session()->get('cityzen_user')),
    ]);
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

Route::post('/admin/reports/{report}/uploader-suspension', function (Request $request, Report $report) use ($isAdminUser) {
    $currentUserId = $request->session()->get('cityzen_user.id');

    if (! $currentUserId) {
        return redirect('/login')->with('notice', 'Please login to moderate reports.');
    }

    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    $data = $request->validate([
        'action' => ['required', 'in:suspend,restore'],
    ]);

    $report->loadMissing('place.user');
    $uploader = $report->place?->user;

    if (! $uploader || ! Schema::hasColumn('users', 'is_suspended')) {
        return back()->withErrors(['report' => 'Uploader postingan tidak ditemukan atau tabel user belum mendukung suspend.']);
    }

    if ((int) $uploader->id === (int) $currentUserId) {
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
})->name('admin.reports.uploader-suspension');

Route::get('/admin/categories', function (Request $request) use ($isAdminUser, $isSuperAdminUser) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to open category manager.');
    }

    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    return view('admin.categories.index', [
        'categories' => Category::orderBy('sort_order')->orderBy('name')->get(),
        'isSuperAdmin' => $isSuperAdminUser($request->session()->get('cityzen_user')),
    ]);
})->name('admin.categories');

Route::post('/admin/categories', function (Request $request) use ($isAdminUser) {
    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    $data = $request->validate([
        'name' => ['required', 'string', 'max:80', 'unique:categories,name'],
        'description' => ['nullable', 'string', 'max:255'],
        'icon' => ['nullable', 'string', 'max:80'],
    ]);

    Category::create([
        'name' => $data['name'],
        'slug' => Str::slug($data['name']),
        'description' => $data['description'] ?? null,
        'icon' => $data['icon'] ?? null,
        'is_active' => true,
    ]);

    return back()->with('status', 'Kategori berhasil ditambahkan.');
})->name('admin.categories.store');

Route::patch('/admin/categories/{category}', function (Request $request, Category $category) use ($isAdminUser) {
    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    $data = $request->validate([
        'name' => ['required', 'string', 'max:80', 'unique:categories,name,'.$category->id],
        'description' => ['nullable', 'string', 'max:255'],
        'icon' => ['nullable', 'string', 'max:80'],
        'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        'is_active' => ['nullable', 'boolean'],
    ]);

    $category->update([
        'name' => $data['name'],
        'slug' => Str::slug($data['name']),
        'description' => $data['description'] ?? null,
        'icon' => $data['icon'] ?? null,
        'sort_order' => $data['sort_order'] ?? 0,
        'is_active' => (bool) ($data['is_active'] ?? false),
    ]);

    return back()->with('status', 'Kategori berhasil diperbarui.');
})->name('admin.categories.update');

Route::delete('/admin/categories/{category}', function (Request $request, Category $category) use ($isAdminUser) {
    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    if (Place::where('category_id', $category->id)->exists()) {
        return back()->withErrors(['category' => 'Kategori masih dipakai oleh places. Nonaktifkan saja jika belum bisa dihapus.']);
    }

    $category->delete();

    return back()->with('status', 'Kategori berhasil dihapus.');
})->name('admin.categories.destroy');

Route::get('/admin/places', function (Request $request) use ($isAdminUser, $isSuperAdminUser) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to open place moderation.');
    }

    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    return view('admin.places.index', [
        'places' => Place::with(['category', 'user'])->latest()->get(),
        'isSuperAdmin' => $isSuperAdminUser($request->session()->get('cityzen_user')),
    ]);
})->name('admin.places');

Route::patch('/admin/places/{place}/status', function (Request $request, Place $place) use ($isAdminUser) {
    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    $data = $request->validate([
        'status' => ['required', 'in:active,hidden,rejected'],
    ]);

    $place->update(['status' => $data['status']]);

    return back()->with('status', 'Status place berhasil diperbarui.');
})->name('admin.places.status');

Route::delete('/admin/places/{place}', function (Request $request, Place $place) use ($isAdminUser) {
    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    $place->delete();

    return back()->with('status', 'Postingan place berhasil dihapus.');
})->name('admin.places.destroy');

Route::get('/admin/users', function (Request $request) use ($isAdminUser, $isSuperAdminUser) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to open user manager.');
    }

    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    $users = User::query()
        ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
        ->select('users.*', 'roles.name as role_name')
        ->orderBy('users.name')
        ->get();

    return view('admin.users.index', [
        'users' => $users,
        'roles' => Schema::hasTable('roles') ? DB::table('roles')->orderBy('id')->get() : collect(),
        'isSuperAdmin' => $isSuperAdminUser($request->session()->get('cityzen_user')),
        'currentUserId' => $request->session()->get('cityzen_user.id'),
    ]);
})->name('admin.users');

Route::patch('/admin/users/{user}', function (Request $request, User $user) use ($isAdminUser, $isSuperAdminUser) {
    abort_unless($isAdminUser($request->session()->get('cityzen_user')), 403);

    $currentUserId = $request->session()->get('cityzen_user.id');

    $data = $request->validate([
        'is_suspended' => ['nullable', 'boolean'],
        'role_id' => ['nullable', 'exists:roles,id'],
    ]);

    if ((int) $currentUserId !== (int) $user->id && Schema::hasColumn('users', 'is_suspended')) {
        $shouldSuspend = (bool) ($data['is_suspended'] ?? false);
        $user->is_suspended = $shouldSuspend;
        $user->suspended_at = $shouldSuspend ? now() : null;
    }

    if ($isSuperAdminUser($request->session()->get('cityzen_user')) && Schema::hasColumn('users', 'role_id') && isset($data['role_id'])) {
        $user->role_id = $data['role_id'];
    }

    $user->save();

    return back()->with('status', 'User berhasil diperbarui.');
})->name('admin.users.update');

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
    $profile = Schema::hasTable('profiles')
        ? DB::table('profiles')->where('user_id', $userId)->first()
        : null;

    return view('profile', [
        'isAdmin' => $isAdminUser($request->session()->get('cityzen_user')),
        'stats' => $profileStats((int) $userId),
        'profile' => $profile,
    ]);
});

Route::get('/settings', function (Request $request) use ($isAdminUser) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to open settings.');
    }

    $userId = $request->session()->get('cityzen_user.id');
    $account = User::findOrFail($userId);
    $profile = Schema::hasTable('profiles')
        ? DB::table('profiles')->where('user_id', $userId)->first()
        : null;

    return view('settings', [
        'account' => $account,
        'profile' => $profile,
        'isAdmin' => $isAdminUser($request->session()->get('cityzen_user')),
    ]);
})->name('settings');

Route::post('/settings', function (Request $request) use ($sessionPayload, $isAdminUser) {
    $userId = $request->session()->get('cityzen_user.id');

    if (! $userId) {
        return redirect('/login')->with('notice', 'Please login to update settings.');
    }

    $data = $request->validate([
        'name' => ['required', 'string', 'max:80'],
        'email' => ['required', 'email', 'unique:users,email,'.$userId],
        'username' => ['required', 'string', 'max:40', 'alpha_dash', 'unique:profiles,username,'.$userId.',user_id'],
        'city' => ['nullable', 'string', 'max:100'],
        'bio' => ['nullable', 'string', 'max:500'],
        'password' => ['nullable', 'string', 'min:4'],
    ]);

    $account = User::findOrFail($userId);
    $account->fill([
        'name' => $data['name'],
        'email' => $data['email'],
    ]);

    if (! empty($data['password'])) {
        $account->password = Hash::make($data['password']);
    }

    $account->save();

    if (Schema::hasTable('profiles')) {
        DB::table('profiles')->updateOrInsert(
            ['user_id' => $userId],
            [
                'username' => $data['username'],
                'city' => $data['city'] ?? null,
                'bio' => $data['bio'] ?? null,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    $request->session()->put('cityzen_user', $sessionPayload($account));

    return redirect('/settings')->with('status', 'Settings berhasil diperbarui.');
})->name('settings.update');

Route::post('/logout', function (Request $request) {
    $request->session()->forget('cityzen_user');
    $request->session()->forget('cityzen_last_report');
    $request->session()->regenerateToken();

    return redirect('/');
});
