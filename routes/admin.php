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

// adminroute
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
Route::get('/leads', [LeadController::class, 'index'])->name('admin.leads');
Route::post('/leads', [LeadController::class, 'store'])->name('admin.leads.store');
Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('admin.leads.show');
Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('admin.leads.update');
Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('admin.leads.destroy');
Route::put('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('admin.leads.status');
Route::post('/leads/{lead}/follow-up', [LeadController::class, 'scheduleFollowUp'])->name('admin.leads.follow-up');
Route::get('/leads/status/{status}', [LeadController::class, 'getLeadsByStatus'])->name('admin.leads.by-status');
Route::get('/leads/stats', [LeadController::class, 'getLeadStats'])->name('admin.leads.stats');
Route::get('/leads/export', [LeadController::class, 'export'])->name('admin.leads.export');

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
Route::post('/locations', [AdminDashboardController::class, 'createLocation'])->name('admin.locations.store');
Route::get('/locations/{location}', [AdminDashboardController::class, 'showLocation'])->name('admin.locations.show');
Route::put('/locations/{location}', [AdminDashboardController::class, 'updateLocation'])->name('admin.locations.update');
Route::delete('/locations/{location}', [AdminDashboardController::class, 'deleteLocation'])->name('admin.locations.delete');

// Location Tracking
Route::get('/location-tracks', [LocationController::class, 'getTracksByDate'])->name('admin.location.tracks');
Route::get('/attendance/timeline', [AttendanceController::class, 'timeline'])->name('admin.attendance.timeline');
});