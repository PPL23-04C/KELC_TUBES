<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SavingTargetController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // ===== Admin routes (prefix: admin) =====
    Route::prefix('admin')->name('admin.')->middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        Route::resource('electricity_rates', \App\Http\Controllers\Admin\ElectricityRateController::class)->except(['show']);
    });

    // ===== User routes =====
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('devices', DeviceController::class)->except(['show']);
    Route::get('/analysis/input', [AnalysisController::class, 'create'])->name('analysis.input');
    Route::post('/analysis', [AnalysisController::class, 'store'])->name('analysis.store');
    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::post('/recommendations/toggle-tip-checklist', [RecommendationController::class, 'toggleTipChecklist'])->name('recommendations.toggleTipChecklist');
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboards.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/saving-target', [SavingTargetController::class, 'index'])->name('saving-target.index');
    Route::post('/saving-target', [SavingTargetController::class, 'store'])->name('saving-target.store');
    Route::delete('/saving-target', [SavingTargetController::class, 'destroy'])->name('saving-target.destroy');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send');

    Route::get('/reminder', [ReminderController::class, 'index'])->name('reminder.index');
    Route::post('/reminder', [ReminderController::class, 'store'])->name('reminder.store');

    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');
});

Route::get('/', function () {
    return view('landing');
})->name('landing');
