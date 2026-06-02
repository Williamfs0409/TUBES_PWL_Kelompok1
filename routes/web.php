<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlaceController;

Route::resource('places', PlaceController::class);
Route::get('/', function () {
    return view('welcome');
});
