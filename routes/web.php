<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertiseController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BtcRateController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubmitController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\UptimeController;
use App\Http\Controllers\LeaderboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/directory', [HomeController::class, 'directory'])->name('directory');
Route::get('/offline', [HomeController::class, 'offline'])->name('offline');
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

// Static Pages
Route::view('/about', 'about')->name('about');
Route::view('/gpg', 'gpg')->name('gpg');

// Category browsing
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

// Link detail
Route::get('/link/random', [LinkController::class, 'random'])->name('link.random');
Route::get('/link/{slug}', [LinkController::class, 'show'])->name('link.show');

// Search
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/goto', [\App\Http\Controllers\SearchAnalyticsController::class, 'trackAndRedirect'])->name('search.redirect');
Route::post('/search/click', [\App\Http\Controllers\SearchAnalyticsController::class, 'trackClick'])->name('search.click');
Route::post('/search/impressions', [\App\Http\Controllers\SearchAnalyticsController::class, 'trackImpressions'])->name('search.impressions');

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

// Voting
Route::post('/link/{id}/like', [LinkController::class, 'vote'])->name('link.like');
Route::post('/link/{id}/dislike', [LinkController::class, 'vote'])->name('link.dislike');

// Support
Route::get('/support', [SupportController::class, 'index'])->name('support.index');


// Advertise
Route::get('/advertise', [AdvertiseController::class, 'create'])->name('advertise.create');
Route::post('/advertise', [AdvertiseController::class, 'store'])->name('advertise.store');
Route::put('/advertise/{id}', [AdvertiseController::class, 'update'])->name('advertise.update');

// Ad Tracking
Route::get('/ad/click/{id}', [\App\Http\Controllers\AdTrackingController::class, 'trackClick'])->name('ad.track');

// BTC Payment Gateway
Route::get('/payment/{id}', [PaymentController::class, 'show'])->name('payment.show');
Route::get('/payment/{id}/status', [PaymentController::class, 'checkStatus'])->name('payment.status');
Route::post('/payment/{id}/submit-txid', [PaymentController::class, 'submitTxid'])->name('payment.submit-txid');


// BTC live rate (cached proxy — public, no auth needed)
Route::get('/api/btc-rate', [BtcRateController::class, 'rate'])->name('api.btc-rate');


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
    Route::get('/captcha/refresh', [AuthController::class, 'refreshCaptcha'])->name('captcha.refresh');
    // Email verification (guest — not yet logged in after registration)
    Route::get('/verify/{userId}', [AuthController::class, 'verifyNotice'])->name('verify.notice');
    Route::post('/verify/{userId}/code', [AuthController::class, 'verifyCode'])->name('verify.code');
    Route::post('/verify/{userId}/resend', [AuthController::class, 'resendVerification'])->name('verify.resend');
});

// Email verification token link (public — accessed from email client)
Route::get('/verify/email/{token}', [AuthController::class, 'verifyEmail'])->name('verify.email');

// Auth Routes (Auth Only)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/advertiser', [\App\Http\Controllers\AdsDashboardController::class, 'index'])->name('dashboard.ads');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Welcome page after registration
    Route::get('/welcome/registered', fn() => view('welcome.register'))->name('welcome.register');

    // ── User Control Panel ──────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/username', [ProfileController::class, 'updateUsername'])->name('profile.username');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/email', [ProfileController::class, 'updateEmail'])->name('profile.email');
    Route::get('/profile/verify', [ProfileController::class, 'verifyNotice'])->name('profile.verify.notice');
    Route::post('/profile/verify/code', [ProfileController::class, 'verifyCode'])->name('profile.verify.code');
    Route::post('/profile/verify/resend', [ProfileController::class, 'resendVerification'])->name('profile.verify.resend');
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

        // Master Search
        Route::get('/search', [\App\Http\Controllers\Admin\SearchController::class, 'index'])->name('search');
        Route::get('/search/live', [\App\Http\Controllers\Admin\SearchController::class, 'live'])->name('search.live');

        // Links management (admin can only delete — no approve/reject)
        Route::get('/links', [AdminController::class, 'links'])->name('links');
        Route::get('/offline-links', [AdminController::class, 'offlineLinks'])->name('offline-links');
        Route::post('/links/cleanup', [AdminController::class, 'cleanupDuplicates'])->name('links.cleanup');
        Route::post('/links/normalize-urls', [AdminController::class, 'normalizeAllUrls'])->name('links.normalize-urls');
        Route::post('/links/reset-duplicates', [AdminController::class, 'resetDuplicates'])->name('links.reset-duplicates');
        Route::get('/links/{id}/edit', [AdminController::class, 'editLink'])->name('links.edit');
        Route::post('/links/{id}/edit', [AdminController::class, 'updateLink'])->name('links.update');
        Route::post('/links/bulk-enrich', [AdminController::class, 'bulkEnrichMetadata'])->name('links.bulk-enrich');
        Route::post('/links/{id}/enrich', [AdminController::class, 'enrichMetadata'])->name('links.enrich');
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

        // User Management
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}/edit', [\App\Http\Controllers\Admin\UserController::class, 'edit'])->name('users.edit');
        Route::post('/users/{id}/edit', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::post('/users/{id}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{id}/delete', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.delete');



        // Uptime logs
        Route::get('/uptime-logs', [AdminController::class, 'uptimeLogs'])->name('uptime-logs');

        // Blacklist
        Route::get('/blacklist', [AdminController::class, 'blacklist'])->name('blacklist');
        Route::post('/blacklist', [AdminController::class, 'addBlacklist'])->name('blacklist.add');
        Route::post('/blacklist/{id}/remove', [AdminController::class, 'removeBlacklist'])->name('blacklist.remove');

        // ── Email Crawler ────────────────────────────────────────────────
        Route::prefix('email-crawler')->name('email-crawler.')->group(function () {
            Route::get('/', [\App\Http\Controllers\EmailCrawlerController::class, 'index'])->name('index');
            Route::post('/scan-url', [\App\Http\Controllers\EmailCrawlerController::class, 'scanUrl'])->name('scan-url');
            Route::post('/scan-bulk', [\App\Http\Controllers\EmailCrawlerController::class, 'scanBulk'])->name('scan-bulk');
            Route::post('/manual-add', [\App\Http\Controllers\EmailCrawlerController::class, 'manualAdd'])->name('manual-add');
            Route::get('/export', [\App\Http\Controllers\EmailCrawlerController::class, 'exportCsv'])->name('export');
            Route::post('/{id}/status', [\App\Http\Controllers\EmailCrawlerController::class, 'updateStatus'])->name('update-status');
            Route::post('/{id}/delete', [\App\Http\Controllers\EmailCrawlerController::class, 'destroy'])->name('delete');
            Route::post('/bulk-delete', [\App\Http\Controllers\EmailCrawlerController::class, 'bulkDelete'])->name('bulk-delete');
            Route::post('/crawl-from-db', [\App\Http\Controllers\EmailCrawlerController::class, 'crawlFromDb'])->name('crawl-from-db');
            Route::post('/reset-exported', [\App\Http\Controllers\EmailCrawlerController::class, 'resetExported'])->name('reset-exported');
        });

        // ── Crawler Management ───────────────────────────────────────────
        Route::prefix('crawler')->name('crawler.')->group(function () {
            Route::get('/', [\App\Http\Controllers\CrawlerController::class, 'index'])->name('index');
            Route::post('/dispatch', [\App\Http\Controllers\CrawlerController::class, 'dispatch'])->name('dispatch');
            Route::post('/crawl-all', [\App\Http\Controllers\CrawlerController::class, 'crawlAll'])->name('crawl-all');
            Route::get('/logs', [\App\Http\Controllers\CrawlerController::class, 'allLogs'])->name('logs');
            Route::post('/{id}/crawl', [\App\Http\Controllers\CrawlerController::class, 'crawlSingle'])->name('crawl-single');
            Route::post('/{id}/reset', [\App\Http\Controllers\CrawlerController::class, 'resetForce'])->name('reset-force');
            Route::get('/{id}/discovered', [\App\Http\Controllers\CrawlerController::class, 'discoveredLinks'])->name('discovered');
            Route::post('/{id}/discovered/clear', [\App\Http\Controllers\CrawlerController::class, 'clearDiscovered'])->name('discovered.clear');
            Route::get('/{id}/logs', [\App\Http\Controllers\CrawlerController::class, 'crawlLogs'])->name('link-logs');
        });
    });
