<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

    return view('dashboard');
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
