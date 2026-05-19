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

Route::post('/logout', function (Request $request) {
    $request->session()->forget('cityzen_user');
    $request->session()->regenerateToken();

    return redirect('/');
});
