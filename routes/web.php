<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\VoterController;
use App\Http\Controllers\VoterAuthController;
use App\Http\Controllers\VoterDashboardController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SettingsController;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {

    Route::get('/', [AdminDashboardController::class, 'index'])->name('index');
    Route::get('/votes-chart', [AdminDashboardController::class, 'votesChart'])->name('votes.chart');

    Route::resource('elections', ElectionController::class);
    Route::resource('voters', VoterController::class);
    Route::resource('results', ResultController::class);

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

Route::middleware('guest:voter')->group(function () {
    Route::get('vote/login', [VoterAuthController::class, 'create'])->name('voter.login');
    Route::post('vote/login', [VoterAuthController::class, 'store']);
});

Route::middleware(['auth:voter', 'voter.timeout'])->group(function () {
    
    Route::get('/voting-dashboard', [VoterDashboardController::class, 'index'])->name('voter.dashboard');

    Route::get('/election/{id}', [VoterDashboardController::class, 'show'])->name('voter.election.show');

    Route::get('/election/{id}/results', [VoterDashboardController::class, 'results'])->name('voter.election.result');

    Route::post('/vote/submit', [VoterDashboardController::class, 'store'])->name('voter.vote.store');

    Route::post('vote/logout', [VoterAuthController::class, 'destroy'])->name('voter.logout');
});

Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

require __DIR__.'/auth.php';