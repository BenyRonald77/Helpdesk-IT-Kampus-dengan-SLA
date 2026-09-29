<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SlaRuleController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EscalationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::view('profile', 'profile')->name('profile');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/self-assign', [TicketController::class, 'selfAssign'])->name('tickets.self-assign');
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.update-status');
    Route::patch('/tickets/{ticket}/priority', [TicketController::class, 'updatePriority'])->name('tickets.update-priority');
    Route::post('/tickets/{ticket}/reassign', [TicketController::class, 'reassign'])->name('tickets.reassign');
    Route::post('/tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('tickets.comments.store');

    Route::middleware('role:supervisor,admin')->group(function () {
        Route::get('/eskalasi', [EscalationController::class, 'index'])->name('escalations.index');
    });

    Route::middleware('role:admin,supervisor')->group(function () {
        Route::get('/laporan', [ReportController::class, 'index'])->name('report.index');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/kategori', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/kategori', [CategoryController::class, 'store'])->name('categories.store');
        Route::patch('/kategori/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/kategori/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/aturan-sla', [SlaRuleController::class, 'index'])->name('sla-rules.index');
        Route::patch('/aturan-sla/{slaRule}', [SlaRuleController::class, 'update'])->name('sla-rules.update');

        Route::get('/tim', [TeamController::class, 'index'])->name('teams.index');
        Route::post('/tim', [TeamController::class, 'store'])->name('teams.store');
        Route::patch('/tim/{team}', [TeamController::class, 'update'])->name('teams.update');

        Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
        Route::post('/pengguna', [UserController::class, 'store'])->name('users.store');
        Route::patch('/pengguna/{user}', [UserController::class, 'update'])->name('users.update');
    });
});

require __DIR__.'/auth.php';
