<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\GameLibraryController;
use App\Http\Controllers\Dashboard\GameManagementController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\ScheduleController;
use App\Http\Controllers\Dashboard\ServiceController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\ReviewController;
use App\Http\Controllers\Dashboard\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\BrowseController;
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
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/player', fn() => view('dashboards.player'))->name('player.dashboard');
    Route::get('/ebuddy/dashboard', [DashboardController::class, 'ebuddyDashboard'])->name('ebuddy.dashboard');
    
    // Status Pages
    Route::get('/ebuddy/pending', fn() => view('dashboards.ebuddy-pending'))->name('ebuddy.pending');
    Route::get('/suspended', fn() => view('auth.suspended'))->name('suspended');

    // Restricted Routes (No Admins Allowed)
    Route::middleware(['no.admin'])->group(function () {
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

        // Unified Orders Management
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::post('/orders/{service}/store', [OrderController::class, 'store'])->name('browse.order');
        Route::post('/orders/{order}/accept', [OrderController::class, 'accept'])->name('orders.accept');
        Route::post('/orders/{order}/refuse', [OrderController::class, 'refuse'])->name('orders.refuse');
        Route::post('/orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::post('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
        Route::post('/orders/{order}/review', [ReviewController::class, 'store'])->name('orders.review');

        // Browse E-Buddies
        Route::get('/browse', [BrowseController::class, 'index'])->name('browse.index');
        Route::get('/browse/{ebuddy}', [BrowseController::class, 'show'])->name('browse.show');
        Route::get('/my-orders', fn() => redirect()->route('orders', ['type' => 'outgoing']))->name('browse.my-orders');

        // Player dashboard
        Route::get('/player', [BrowseController::class, 'index'])->name('player.dashboard');

        // Chat
        Route::get('/chat/{roomId?}', [ChatController::class, 'index'])->name('chat');
        Route::get('/chat/start/{userId}', [ChatController::class, 'start'])->name('chat.start');

        // Reporting
        Route::post('/report', [ReportController::class, 'store'])->name('report.store');
    });

    // Admin Features
    Route::middleware(['auth', 'is.admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/overview', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'indexUsers'])->name('users.index');
        Route::get('/reports', [AdminController::class, 'indexReports'])->name('reports.index');
        Route::get('/reports/{report}', [AdminController::class, 'showReport'])->name('reports.show');
        Route::post('/reports/{report}/dismiss', [AdminController::class, 'dismissReport'])->name('reports.dismiss');
        Route::post('/users/{user}/suspend', [AdminController::class, 'toggleSuspension'])->name('users.suspend');
        
        Route::get('/ebuddies', [AdminController::class, 'ebuddyApplications'])->name('ebuddies.index');
        Route::post('/ebuddies/{ebuddy}/approve', [AdminController::class, 'approveEBuddy'])->name('ebuddies.approve');
        Route::get('/ebuddies/{ebuddy}/reject', [AdminController::class, 'rejectEBuddy'])->name('ebuddies.reject');

        // Game Management
        Route::get('/games', [GameManagementController::class, 'index'])->name('games.index');
        Route::get('/games/create', [GameManagementController::class, 'create'])->name('games.create');
        Route::post('/games', [GameManagementController::class, 'store'])->name('games.store');
        Route::get('/games/{game}/edit', [GameManagementController::class, 'edit'])->name('games.edit');
        Route::post('/games/{game}', [GameManagementController::class, 'update'])->name('games.update');
        Route::delete('/games/{game}', [GameManagementController::class, 'destroy'])->name('games.destroy');
        Route::delete('/ranks/{rank}', [GameManagementController::class, 'deleteRank'])->name('ranks.destroy');
    });


    // Player dashboard
    Route::get('/player', [BrowseController::class, 'index'])->name('player.dashboard');

    // Chat
    Route::get('/chat/{roomId?}', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/start/{userId}', [ChatController::class, 'start'])->name('chat.start');
});

