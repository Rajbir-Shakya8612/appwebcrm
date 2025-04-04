<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalespersonDashboardController;

// Task Management Routes
Route::prefix('tasks')->group(function () {
    Route::get('/', [SalespersonDashboardController::class, 'tasks'])->name('salesperson.tasks.index');
    Route::get('/{task}', [SalespersonDashboardController::class, 'showTask'])->name('salesperson.tasks.show');
    Route::patch('/{task}/status', [SalespersonDashboardController::class, 'updateTaskStatus'])->name('salesperson.tasks.status');
}); 