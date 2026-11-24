<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProgressController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::redirect('/', '/signup');
    Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [ProgressController::class, 'index'])->name('dashboard');

    Route::get('/vocab-tracker', [ProgressController::class, 'index'])->name('vocab.tracker');

    Route::post('/progress/mock-test', [ProgressController::class, 'saveMockTest'])->name('progress.mock');
    Route::post('/progress/day-completion', [ProgressController::class, 'setDayCompletion'])->name('progress.complete');
    Route::post('/progress/reset', [ProgressController::class, 'resetProgress'])->name('progress.reset');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});