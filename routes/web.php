<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\GameLibraryController;
use App\Http\Controllers\EBuddyOrderController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // New Role-Specific Dashboards
    Route::get('/admin', fn() => view('dashboards.admin'))->name('admin.dashboard');
    Route::get('/player', fn() => view('dashboards.player'))->name('player.dashboard');
    Route::get('/ebuddy/dashboard', [DashboardController::class, 'ebuddyDashboard'])->name('ebuddy.dashboard');
    
    // Status Pages
    Route::get('/ebuddy/pending', fn() => view('dashboards.ebuddy-pending'))->name('ebuddy.pending');
    Route::get('/suspended', fn() => view('auth.suspended'))->name('suspended');

    // Profile Management (Shared by Player & E-Buddy)
    Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'editProfile'])->name('profile.edit');
    Route::post('/profile/edit', [ProfileController::class, 'updateProfile'])->name('profile.update');
    
    // Game Library Management (Shared)
    Route::get('/profile/add-game', [GameLibraryController::class, 'addGame'])->name('profile.add-game');
    Route::post('/profile/add-game', [GameLibraryController::class, 'storeGame'])->name('profile.store-game');
    Route::delete('/profile/game/{profile}', [GameLibraryController::class, 'removeGame'])->name('profile.remove-game');

    // E-Buddy Services Management
    Route::get('/ebuddy/services', [ServiceController::class, 'index'])->name('ebuddy.services');
    Route::post('/ebuddy/services', [ServiceController::class, 'store'])->name('ebuddy.services.store');
    Route::delete('/ebuddy/services/{service}', [ServiceController::class, 'destroy'])->name('ebuddy.services.destroy');

    // E-Buddy Schedule Management
    Route::get('/ebuddy/schedule', [ScheduleController::class, 'index'])->name('ebuddy.schedule');
    Route::post('/ebuddy/schedule', [ScheduleController::class, 'storeSchedule'])->name('ebuddy.schedule.store');
    Route::delete('/ebuddy/schedule/{schedual}', [ScheduleController::class, 'destroySchedule'])->name('ebuddy.schedule.destroy');
    Route::post('/ebuddy/unavailability', [ScheduleController::class, 'storeUnavailability'])->name('ebuddy.unavailability.store');
    Route::delete('/ebuddy/unavailability/{unavailability}', [ScheduleController::class, 'destroyUnavailability'])->name('ebuddy.unavailability.destroy');

    // Reporting
    Route::post('/report', [\App\Http\Controllers\ReportController::class, 'store'])->name('report.store');

    // Unified Orders Management
    Route::get('/orders', [\App\Http\Controllers\Dashboard\OrderController::class, 'index'])->name('orders');
    Route::post('/orders/{order}/accept', [\App\Http\Controllers\Dashboard\OrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{order}/refuse', [\App\Http\Controllers\Dashboard\OrderController::class, 'refuse'])->name('orders.refuse');
    Route::post('/orders/{order}/pay', [\App\Http\Controllers\Dashboard\OrderController::class, 'pay'])->name('orders.pay');
    Route::post('/orders/{order}/cancel', [\App\Http\Controllers\Dashboard\OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/complete', [\App\Http\Controllers\Dashboard\OrderController::class, 'complete'])->name('orders.complete');
    Route::post('/orders/{order}/review', [\App\Http\Controllers\Dashboard\ReviewController::class, 'store'])->name('orders.review');

    // Browse E-Buddies (shared — both Player and E-Buddy can use these)
    Route::get('/browse', [\App\Http\Controllers\BrowseController::class, 'index'])->name('browse.index');
    Route::get('/browse/{ebuddy}', [\App\Http\Controllers\BrowseController::class, 'show'])->name('browse.show');
    Route::post('/browse/order/{service}', [\App\Http\Controllers\BrowseController::class, 'order'])->name('browse.order');
    Route::get('/my-orders', fn() => redirect()->route('orders', ['type' => 'outgoing']))->name('browse.my-orders');

    // Player dashboard
    Route::get('/player', [\App\Http\Controllers\BrowseController::class, 'index'])->name('player.dashboard');

    // Chat
    Route::get('/chat/{roomId?}', [ChatController::class, 'index'])->name('chat');
    Route::post('/chat/start/{userId}', [ChatController::class, 'start'])->name('chat.start');
});

