<?php

use App\Http\Controllers\AddLinkController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class,'index'])->name('welcome');

// add link get
Route::get('/add-link', [AddLinkController::class,'index'])->name('links.index');
Route::post('/add-link', [AddLinkController::class,'store'])->name('links.store');


// register route
Route::middleware('guest')->group(function () {
    Route::get('/register', [WelcomeController::class,'index'])->name('register.index');
    Route::post('/register', [WelcomeController::class,'store'])->name('register.store');
});
