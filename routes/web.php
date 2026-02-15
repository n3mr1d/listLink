<?php

use App\Http\Controllers\AddLinkController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class,'index'])->name('welcome');

// add link get
Route::get('/add-link', [AddLinkController::class,'index'])->name('links.index');
Route::post('/add-link', [AddLinkController::class,'store'])->name('links.store');
