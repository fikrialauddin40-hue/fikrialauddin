<?php

use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CoupleController;
use App\Http\Controllers\ProfileController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $totalReports = \App\Models\DailyReport::count();
    $totalCouples = \App\Models\User::whereNotNull('partner_id')->count() / 2;
    $totalDays = \App\Models\User::whereNotNull('anniversary_date')->count() > 0
        ? \App\Models\User::whereNotNull('anniversary_date')->with('partner')->get()->sum(function ($u) {
            return \Carbon\Carbon::parse($u->anniversary_date)->diffInDays(now());
        })
        : 0;

    return view('welcome', compact('totalReports', 'totalCouples', 'totalDays'));
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('reports', DailyReportController::class);
    Route::delete('/reports/document/{document}', [DailyReportController::class, 'deleteDocument'])
        ->name('reports.document.delete');

    Route::prefix('couple')->name('couple.')->group(function () {
        Route::get('/', [CoupleController::class, 'index'])->name('index');
        Route::post('/generate-code', [CoupleController::class, 'generateCode'])->name('generate');
        Route::post('/regenerate-code', [CoupleController::class, 'regenerateCode'])->name('regenerate');
        Route::post('/connect', [CoupleController::class, 'connect'])->name('connect');
        Route::post('/disconnect', [CoupleController::class, 'disconnect'])->name('disconnect');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
