<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Volt;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\VoterController;
use App\Http\Controllers\VoterAuthController;
use App\Http\Controllers\VoterDashboardController;
use App\Http\Controllers\VoterProfileController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Admin2FAController;
use App\Http\Controllers\Admin2FAAdminController;
use App\Http\Controllers\PartylistController;
use App\Http\Controllers\VoterExportController;
use App\Http\Controllers\CandidateImportController;
use App\Http\Controllers\BackupController;

// 1. PUBLIC ROUTES
Route::view('/', 'welcome')->name('welcome');

// 2. STUDENT LOGIN (Guest only)
Route::middleware('guest:voter')->group(function () {
    Route::get('vote/login', [VoterAuthController::class, 'create'])->name('voter.login');
    Route::post('vote/login', [VoterAuthController::class, 'store'])->middleware('throttle:5,5');
});

// 2a. ADMIN 2FA VERIFICATION
Route::middleware(['auth'])->prefix('admin/2fa')->name('admin.2fa.')->group(function () {
    Route::get('/verify', [Admin2FAAdminController::class, 'index'])->name('index');
    Route::post('/verify', [Admin2FAAdminController::class, 'verify'])->name('verify');
    Route::post('/resend', [Admin2FAAdminController::class, 'resend'])->name('resend');
});

// 2b. ADMIN LOGOUT
Route::post('/admin/logout', function () {
    Auth::guard('web')->logout();
    session()->forget(['admin_2fa_verified']);
    session()->regenerateToken();
    return redirect()->route('login');
})->name('admin.logout')->middleware('auth');

// 2c. ADMIN REGISTRATION (Secret page — guest only)
Route::middleware('guest')->group(function () {
    Volt::route('admin/register', 'pages.auth.admin-register')
        ->name('admin.register');
});

// 3. DASHBOARD REDIRECTOR
Route::get('/dashboard', function () {
    if (Auth::guard('web')->check()) {
        return redirect()->route('admin.index');
    }
    if (Auth::guard('voter')->check()) {
        return redirect()->route('voter.dashboard');
    }
    return redirect()->route('login');
})->name('dashboard.index');

// 4. STUDENT 2FA VERIFICATION
Route::middleware(['auth:voter'])->prefix('vote/security')->name('voter.2fa.')->group(function () {
    Route::get('/verify', [Admin2FAController::class, 'index'])->name('index');
    Route::post('/verify', [Admin2FAController::class, 'verify'])->name('verify');
    Route::post('/resend', [Admin2FAController::class, 'resend'])->name('resend');
});

// 5. VOTER LOGOUT
Route::post('vote/logout', [VoterAuthController::class, 'destroy'])->name('voter.logout');
Route::get('vote/logout', function () {
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    Auth::guard('voter')->logout();
    return redirect()->route('voter.login');
});

// 6. STUDENT DASHBOARD (Protected)
Route::middleware(['auth:voter', 'voter.timeout', 'admin.2fa'])->group(function () {
    Route::get('/voting-dashboard', [VoterDashboardController::class, 'index'])->name('voter.dashboard');
    Route::get('/election/{election}/vote', [VoterDashboardController::class, 'show'])->name('voter.show');
    Route::post('/vote/submit/{election}', [VoterDashboardController::class, 'store'])->name('voter.vote.store');
    Route::get('/election/{election}/results', [VoterDashboardController::class, 'results'])->name('voter.results');
    Route::get('/election/{election}/results-data', [VoterDashboardController::class, 'resultsData'])->name('voter.results.data');

    Route::get('/voter/profile', [VoterProfileController::class, 'index'])->name('voter.profile');
    Route::patch('/voter/profile/info', [VoterProfileController::class, 'updateInfo'])->name('voter.profile.update-info');
    Route::patch('/voter/profile/password', [VoterProfileController::class, 'updatePassword'])->name('voter.profile.update-password');
});

// 7. ADMIN ROUTES (Prefix: dashboard)
Route::middleware(['auth', 'verified', 'admin2fa'])->prefix('dashboard')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('index');
    Route::get('/live-stats', [AdminDashboardController::class, 'getLiveStats'])->name('live-stats');
    Route::get('/votes-chart', [AdminDashboardController::class, 'votesChart'])->name('votes.chart');

    Route::post('/elections/deactivate-finished', [ElectionController::class, 'deactivateFinished'])->name('elections.deactivate-finished');
    Route::resource('elections', ElectionController::class);

    Route::get('/voters/template', [VoterController::class, 'downloadTemplate'])->name('voters.template');
    Route::post('/voters/import', [VoterController::class, 'import'])->name('voters.import');
    Route::get('/voters/sections/{gradeLevelId}', [VoterController::class, 'getSections'])->name('voters.sections');
    Route::get('/voters/export', [VoterExportController::class, 'export'])->name('voters.export');
    Route::resource('voters', VoterController::class);

    Route::post('/voters/{voter}/reset-password', [VoterController::class, 'resetPassword'])
        ->name('voters.reset-password');

    Route::resource('partylists', PartylistController::class);

    Route::get('/tally-results/{election}', [ResultController::class, 'show'])->name('results.show');
    Route::get('/results', [ResultController::class, 'index'])->name('results.index');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/candidates/import', [CandidateImportController::class, 'showForm'])->name('candidates.import');
    Route::post('/candidates/import', [CandidateImportController::class, 'import'])->name('candidates.import.store');

    // Database Backup
    Route::get('/backup/download', [BackupController::class, 'download'])->name('backup.download');
});

// 8. USER PROFILE & AUTH
Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

require __DIR__.'/auth.php';