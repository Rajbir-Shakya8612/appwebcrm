<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SalespersonDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\LocationTrackController;
use App\Http\Controllers\AdminLocationController;

// adminroute
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Dashboard Data APIs
    Route::get('/attendance/overview', [AdminDashboardController::class, 'getAttendanceOverview']);
    Route::get('/performance/overview', [AdminDashboardController::class, 'getPerformanceOverview']);
    Route::get('/activities/recent', [AdminDashboardController::class, 'getRecentActivities']);

    // Tasks Management
    Route::get('/tasks', [AdminDashboardController::class, 'tasks'])->name('tasks');
    Route::post('/tasks', [AdminDashboardController::class, 'createTask'])->name('tasks.create');
    Route::get('/tasks/{task}', [AdminDashboardController::class, 'showTask'])->name('tasks.show');
    Route::put('/tasks/{task}', [AdminDashboardController::class, 'updateTask'])->name('tasks.update');
    Route::delete('/tasks/{task}', [AdminDashboardController::class, 'deleteTask'])->name('tasks.delete');
    Route::put('/tasks/{task}/status', [AdminDashboardController::class, 'updateTaskStatus'])->name('tasks.status');

    // Attendance Management
    Route::get('/attendance', [AdminDashboardController::class, 'attendance'])->name('attendance');
    Route::get('/attendance/overview', [AdminDashboardController::class, 'getAttendanceOverview'])->name('attendance.overview');
    Route::post('/attendance/bulk-update', [AdminDashboardController::class, 'bulkUpdateAttendance'])->name('attendance.bulk-update');
    Route::get('/attendance/export', [AdminDashboardController::class, 'exportAttendance'])->name('attendance.export');
    Route::get('/attendance/{attendance}', [AdminDashboardController::class, 'showAttendance'])->name('attendance.show');
    Route::put('/attendance/{attendance}', [AdminDashboardController::class, 'updateAttendance'])->name('attendance.update');

    // Sales Management
    Route::get('/sales', [AdminDashboardController::class, 'sales'])->name('sales');
    Route::get('/sales/export', [AdminDashboardController::class, 'exportSales'])->name('sales.export');
    Route::get('/sales/analytics', [AdminDashboardController::class, 'salesAnalytics'])->name('sales.analytics');

    // Leads Management
    Route::get('/leads', [LeadController::class, 'index'])->name('leads');
    Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    Route::put('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.status');
    Route::post('/leads/{lead}/follow-up', [LeadController::class, 'scheduleFollowUp'])->name('leads.follow-up');
    Route::get('/leads/status/{status}', [LeadController::class, 'getLeadsByStatus'])->name('leads.by-status');
    Route::get('/leads/stats', [LeadController::class, 'getLeadStats'])->name('leads.stats');
    Route::get('/leads/export', [LeadController::class, 'export'])->name('leads.export');

    // User management
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
    Route::post('/users', [AdminDashboardController::class, 'createUser'])->name('users.store');
    Route::get('/users/{user}', [AdminDashboardController::class, 'showUser'])->name('users.show');
    Route::put('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminDashboardController::class, 'deleteUser'])->name('users.destroy');

    // Settings
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');

    // Locations Management
    Route::prefix('locations')->name('locations.')->group(function () {
        // Location tracking routes
        Route::get('/tracks', [AdminLocationController::class, 'getLocationTracks'])->name('tracks');
        Route::get('/stats', [AdminLocationController::class, 'getLocationStats'])->name('stats');
        Route::get('/checked-in', [AdminLocationController::class, 'getCheckedInUsers'])->name('checked-in');
        
        // CRUD routes
        Route::get('/', [AdminLocationController::class, 'index'])->name('index');
        Route::post('/', [AdminLocationController::class, 'store'])->name('store');
        Route::get('/{location}', [AdminLocationController::class, 'show'])->name('show');
        Route::put('/{location}', [AdminLocationController::class, 'update'])->name('update');
        Route::delete('/{location}', [AdminLocationController::class, 'destroy'])->name('delete');
    });
});
