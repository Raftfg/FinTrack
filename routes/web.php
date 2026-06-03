<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Export routes
    Route::get('/transactions/export/pdf', [\App\Http\Controllers\TransactionController::class, 'exportPdf'])->name('transactions.export.pdf');
    Route::get('/transactions/export/excel', [\App\Http\Controllers\TransactionController::class, 'exportExcel'])->name('transactions.export.excel');

    // Throttled write routes (max 30 submissions per minute)
    Route::middleware('throttle:30,1')->group(function () {
        Route::post('/transactions', [\App\Http\Controllers\TransactionController::class, 'store'])->name('transactions.store');
        Route::patch('/transactions/{transaction}', [\App\Http\Controllers\TransactionController::class, 'update'])->name('transactions.update');
        Route::delete('/transactions/{transaction}', [\App\Http\Controllers\TransactionController::class, 'destroy'])->name('transactions.destroy');
        
        Route::post('/loans', [\App\Http\Controllers\LoanController::class, 'store'])->name('loans.store');
        Route::patch('/loans/{loan}', [\App\Http\Controllers\LoanController::class, 'update'])->name('loans.update');
        Route::delete('/loans/{loan}', [\App\Http\Controllers\LoanController::class, 'destroy'])->name('loans.destroy');
        
        Route::post('/goals', [\App\Http\Controllers\GoalController::class, 'store'])->name('goals.store');
        Route::patch('/goals/{goal}', [\App\Http\Controllers\GoalController::class, 'update'])->name('goals.update');
        Route::delete('/goals/{goal}', [\App\Http\Controllers\GoalController::class, 'destroy'])->name('goals.destroy');
    });

    // Read routes (resource views only)
    Route::resource('transactions', \App\Http\Controllers\TransactionController::class)->except(['store', 'update', 'destroy']);
    Route::resource('loans', \App\Http\Controllers\LoanController::class)->except(['store', 'update', 'destroy']);
    Route::resource('goals', \App\Http\Controllers\GoalController::class)->except(['store', 'update', 'destroy']);

    // Notifications routes
    Route::patch('/notifications/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
});

require __DIR__.'/auth.php';
