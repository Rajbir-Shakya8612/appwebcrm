<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SalespersonDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::resource('roles', RoleController::class)->names([
        'index' => 'web.roles.index',
        'create' => 'web.roles.create',
        'store' => 'web.roles.store',
        'show' => 'web.roles.show',
        'edit' => 'web.roles.edit',
        'update' => 'web.roles.update',
        'destroy' => 'web.roles.destroy',
    ]);
    Route::resource('users', UserController::class)->names([
        'index' => 'web.users.index',
        'create' => 'web.users.create',
        'store' => 'web.users.store',
        'show' => 'web.users.show',
        'edit' => 'web.users.edit',
        'update' => 'web.users.update',
        'destroy' => 'web.users.destroy',
    ]);
    // Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])
    //     ->name('admin.dashboard')->middleware('role:admin');

    Route::get('/salesperson-dashboard', [SalespersonDashboardController::class, 'index'])
        ->name('salesperson.dashboard')->middleware('role:salesperson');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // Dashboard Data APIs
    Route::get('/attendance/overview', [AdminDashboardController::class, 'getAttendanceOverview']);
    Route::get('/performance/overview', [AdminDashboardController::class, 'getPerformanceOverview']);
    Route::get('/activities/recent', [AdminDashboardController::class, 'getRecentActivities']);

    // Tasks Management
    Route::get('/tasks', [AdminDashboardController::class, 'tasks'])->name('admin.tasks');
    Route::post('/tasks', [AdminDashboardController::class, 'createTask'])->name('admin.tasks.create');
    Route::get('/tasks/{task}', [AdminDashboardController::class, 'showTask'])->name('admin.tasks.show');
    Route::put('/tasks/{task}', [AdminDashboardController::class, 'updateTask'])->name('admin.tasks.update');
    Route::delete('/tasks/{task}', [AdminDashboardController::class, 'deleteTask'])->name('admin.tasks.delete');
    Route::put('/tasks/{task}/status', [AdminDashboardController::class, 'updateTaskStatus'])->name('admin.tasks.status');

    // Attendance Management
    Route::get('/attendance', [AdminDashboardController::class, 'attendance'])->name('admin.attendance');
    Route::get('/attendance/export', [AdminDashboardController::class, 'exportAttendance'])->name('admin.attendance.export');
    Route::post('/attendance/bulk', [AdminDashboardController::class, 'bulkUpdateAttendance'])->name('admin.attendance.bulk');

    // Sales Management
    Route::get('/sales', [AdminDashboardController::class, 'sales'])->name('admin.sales');
    Route::get('/sales/export', [AdminDashboardController::class, 'exportSales'])->name('admin.sales.export');
    Route::get('/sales/analytics', [AdminDashboardController::class, 'salesAnalytics'])->name('admin.sales.analytics');

    // Leads Management
    Route::get('/leads', [AdminDashboardController::class, 'leads'])->name('admin.leads');
    Route::get('/leads/export', [AdminDashboardController::class, 'exportLeads'])->name('admin.leads.export');
    Route::get('/leads/analytics', [AdminDashboardController::class, 'leadsAnalytics'])->name('admin.leads.analytics');

    // User management
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('admin.users');
    Route::post('/users', [AdminDashboardController::class, 'createUser'])->name('admin.users.create');
    Route::get('/users/{user}', [AdminDashboardController::class, 'showUser'])->name('admin.users.show');
    Route::put('/users/{user}', [AdminDashboardController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminDashboardController::class, 'deleteUser'])->name('admin.users.delete');

    // Settings
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
    Route::put('/settings', [AdminDashboardController::class, 'updateSettings'])->name('admin.settings.update');

    // Locations Management
    Route::get('/locations', [AdminDashboardController::class, 'locations'])->name('admin.locations');
    Route::post('/locations', [AdminDashboardController::class, 'createLocation'])->name('admin.locations.create');
    Route::get('/locations/{location}', [AdminDashboardController::class, 'showLocation'])->name('admin.locations.show');
    Route::put('/locations/{location}', [AdminDashboardController::class, 'updateLocation'])->name('admin.locations.update');
    Route::delete('/locations/{location}', [AdminDashboardController::class, 'deleteLocation'])->name('admin.locations.delete');
});