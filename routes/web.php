<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertiseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubmitController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UptimeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Category browsing
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

// Link detail
Route::get('/link/{slug}', [LinkController::class, 'show'])->name('link.show');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

// Submit link (open to all, with honeypot)

Route::get('/submit', [SubmitController::class, 'create'])->name('submit.create');
Route::post('/submit', [SubmitController::class, 'store'])->name('submit.store');


// Crawl URL (AJAX-free server-side crawl for submit form)
Route::post('/crawl-url', [SubmitController::class, 'crawl'])->name('submit.crawl');

// Comment on link
Route::post('/link/{id}/comment', [LinkController::class, 'storeComment'])

    ->name('link.comment');

// Uptime check (rate limited: 3 checks per 5 minutes)
Route::post('/link/{id}/check', [UptimeController::class, 'check'])
    ->name('link.check');

// Support
Route::get('/support', [SupportController::class, 'index'])->name('support.index');


// Advertise

Route::get('/advertise', [AdvertiseController::class, 'create'])->name('advertise.create');
Route::post('/advertise', [AdvertiseController::class, 'store'])->name('advertise.store');


/*
|--------------------------------------------------------------------------
| Auth Routes (Guests Only)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// Auth Routes (Auth Only)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Auth + Admin Middleware)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // Links management (admin can only delete — no approve/reject)
        Route::get('/links', [AdminController::class, 'links'])->name('links');
        Route::post('/links/{id}/delete', [AdminController::class, 'deleteLink'])->name('links.delete');

        // Ads management
        Route::get('/ads', [AdminController::class, 'ads'])->name('ads');
        Route::get('/ads/create', [AdminController::class, 'createAd'])->name('ads.create');
        Route::post('/ads/create', [AdminController::class, 'storeAd'])->name('ads.store');
        Route::get('/ads/{id}/edit', [AdminController::class, 'editAd'])->name('ads.edit');
        Route::post('/ads/{id}/edit', [AdminController::class, 'updateAd'])->name('ads.update');
        Route::post('/ads/{id}/delete', [AdminController::class, 'deleteAd'])->name('ads.delete');
        Route::post('/ads/{id}/approve', [AdminController::class, 'approveAd'])->name('ads.approve');
        Route::post('/ads/{id}/reject', [AdminController::class, 'rejectAd'])->name('ads.reject');



        // Uptime logs
        Route::get('/uptime-logs', [AdminController::class, 'uptimeLogs'])->name('uptime-logs');

        // Blacklist
        Route::get('/blacklist', [AdminController::class, 'blacklist'])->name('blacklist');
        Route::post('/blacklist', [AdminController::class, 'addBlacklist'])->name('blacklist.add');
        Route::post('/blacklist/{id}/remove', [AdminController::class, 'removeBlacklist'])->name('blacklist.remove');
    });
