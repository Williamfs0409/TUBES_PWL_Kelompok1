<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminPlaceController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/healthz', fn () => response('ok', 200)->header('Content-Type', 'text/plain'));

Route::view('/', 'welcome');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('cityzen.auth');

Route::middleware('cityzen.auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
});

Route::middleware(['cityzen.auth', 'cityzen.verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/explore', ExploreController::class)->name('explore');

    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/profile', ProfileController::class)->name('profile');
    Route::get('/settings', [SettingController::class, 'edit'])->name('settings');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('/users/{user}/avatar', [SettingController::class, 'avatar'])->name('users.avatar');

    Route::get('/places/{place}/image', [PlaceController::class, 'image'])->name('places.image');
    Route::get('/places/{place}/photos/{photo}', [PlaceController::class, 'photo'])->name('places.photos.show');
    Route::resource('places', PlaceController::class);
    Route::post('/places/{place}/like', [InteractionController::class, 'like'])->name('places.like');
    Route::post('/places/{place}/bookmark', [InteractionController::class, 'bookmark'])->name('places.bookmark');
    Route::post('/places/{place}/repost', [InteractionController::class, 'repost'])->name('places.repost');
    Route::post('/places/{place}/review', [InteractionController::class, 'review'])->name('places.review');
    Route::get('/places/{place}/report', [ReportController::class, 'create'])->name('reports.create');
    Route::post('/places/{place}/report', [ReportController::class, 'store'])->name('reports.store');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['cityzen.admin', 'cityzen.verified'])
    ->group(function () {
        Route::get('/', AdminDashboardController::class)->name('dashboard');

        Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
        Route::post('/reports/{report}/status', [AdminReportController::class, 'updateStatus'])->name('reports.status');
        Route::post('/reports/{report}/uploader-suspension', [AdminReportController::class, 'updateUploaderSuspension'])->name('reports.uploader-suspension');

        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::patch('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/places', [AdminPlaceController::class, 'index'])->name('places');
        Route::patch('/places/{place}/status', [AdminPlaceController::class, 'updateStatus'])->name('places.status');
        Route::delete('/places/{place}', [AdminPlaceController::class, 'destroy'])->name('places.destroy');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    });
