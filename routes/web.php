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

    $places = \App\Models\Place::with(['category', 'user'])
    ->withCount(['likes', 'bookmarks', 'reviews'])
    ->withAvg('reviews', 'rating')
    ->latest()
    ->get();

    return view('dashboard', compact('places')  );
});

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

Route::post('/places/{place}/like', function (\App\Models\Place $place) {
    $userId = session('cityzen_user.id');

    if (! $userId) {
        return redirect('/login');
    }

    $existingLike = \App\Models\Like::where('user_id', $userId)
        ->where('place_id', $place->id)
        ->first();

    if ($existingLike) {
        $existingLike->delete();
    } else {
        \App\Models\Like::create([
            'user_id' => $userId,
            'place_id' => $place->id,
        ]);
    }

    return back();
})->name('places.like');

Route::post('/places/{place}/bookmark', function (\App\Models\Place $place) {
    $userId = session('cityzen_user.id');

    if (! $userId) {
        return redirect('/login');
    }

    $existingBookmark = \App\Models\Bookmark::where('user_id', $userId)
        ->where('place_id', $place->id)
        ->first();

    if ($existingBookmark) {
        $existingBookmark->delete();
    } else {
        \App\Models\Bookmark::create([
            'user_id' => $userId,
            'place_id' => $place->id,
        ]);
    }

    return back();
})->name('places.bookmark');

Route::post('/places/{place}/review', function (\Illuminate\Http\Request $request, \App\Models\Place $place) {
    $userId = session('cityzen_user.id');

    if (! $userId) {
        return redirect('/login');
    }

    $validated = $request->validate([
        'rating' => ['required', 'integer', 'min:1', 'max:5'],
        'review' => ['nullable', 'string', 'max:500'],
    ]);

    \App\Models\Review::updateOrCreate(
        [
            'user_id' => $userId,
            'place_id' => $place->id,
        ],
        $validated
    );

    return back();
})->name('places.review');

