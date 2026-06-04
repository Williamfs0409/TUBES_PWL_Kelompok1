<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaceController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
            ->withErrors([
                'email' => 'Email atau password salah.',
            ])
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

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
    ]);

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

    return view('dashboard');
});

Route::resource('places', PlaceController::class);

Route::get('/profile', function (Request $request) {
    if (! $request->session()->has('cityzen_user')) {
        return redirect('/login')->with('notice', 'Please login to open your CityZen profile.');
    }

    return view('profile');
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

Route::post('/logout', function (Request $request) {
    $request->session()->forget('cityzen_user');
    $request->session()->forget('cityzen_last_report');
    $request->session()->regenerateToken();

    return redirect('/');
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
