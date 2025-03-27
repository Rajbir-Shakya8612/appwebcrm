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

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }

    $user = Auth::user();
    $user->load('role'); // Ensure role is loaded

    // If user is admin, redirect to admin dashboard
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    // If user is salesperson, redirect to salesperson dashboard
    if ($user->hasRole('salesperson')) {
        return redirect()->route('salesperson.dashboard');
    }

    // For other users, show the regular dashboard
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

    // Location Tracking
    Route::get('/location-tracks', [LocationController::class, 'getTracksByDate'])->name('admin.location.tracks');
    Route::get('/attendance/timeline', [AttendanceController::class, 'timeline'])->name('admin.attendance.timeline');


    //new routes add
    Route::get('/admin/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::post('/admin/leads', [LeadController::class, 'store'])->name('leads.store');
    Route::get('/admin/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
    Route::put('/admin/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
    Route::delete('/admin/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
    Route::put('/admin/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.status');
    Route::post('/admin/leads/{lead}/follow-up', [LeadController::class, 'scheduleFollowUp'])->name('leads.follow-up');
    Route::get('/admin/leads/status/{status}', [LeadController::class, 'getLeadsByStatus'])->name('leads.by-status');
    Route::get('/admin/leads/stats', [LeadController::class, 'getLeadStats'])->name('leads.stats');
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

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'salesperson'])->prefix('admin')->group(function () {

    Route::get('/salesperson-dashboard', [SalespersonDashboardController::class, 'index'])
        ->name('salesperson.dashboard');

    Route::get('/leads', [LeadController::class, 'index'])->name('salesperson.leads');
    Route::get('/sales', [SaleController::class, 'index'])->name('salesperson.sales');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('salesperson.attendance');
    Route::get('/performance', [PerformanceController::class, 'index'])->name('salesperson.performance');
    Route::get('/profile', [ProfileController::class, 'index'])->name('salesperson.profile');
    Route::get('/settings', [SettingsController::class, 'index'])->name('salesperson.settings');

    // Attendance
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut']);
    Route::get('/attendance/status', [AttendanceController::class, 'status']);
    Route::get('/attendance/monthly-report', [AttendanceController::class, 'monthlyReport'])->name('salesperson.attendance.report');
    Route::get('/attendance/calendar-events', [AttendanceController::class, 'calendarEvents']);

    // Leads
    Route::post('/leads', [LeadController::class, 'store'])->name('salesperson.leads.store');
    Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('salesperson.leads.show');
    Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('salesperson.leads.update');
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('salesperson.leads.destroy');
    Route::put('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('salesperson.leads.status');
    Route::get('/leads/status/{status}', [LeadController::class, 'getLeadsByStatus'])->name('salesperson.leads.by-status');
    Route::get('/leads/stats', [LeadController::class, 'getLeadStats'])->name('salesperson.leads.stats');

    // Sales
    Route::post('/sales', [SaleController::class, 'store']);
    Route::get('/sales/{sale}', [SaleController::class, 'show']);
    Route::put('/sales/{sale}', [SaleController::class, 'update']);

    // Location Tracking Routes
    Route::post('/location/update', [LocationController::class, 'update']);
    Route::get('/location/today-tracks', [LocationController::class, 'getTodayTracks']);
    Route::get('/location/monthly-tracks', [LocationController::class, 'getMonthlyTracks']);
    Route::get('/location/tracks-by-date', [LocationController::class, 'getTracksByDate']);

    // Meeting Routes
    Route::get('/meetings', [MeetingController::class, 'index'])->name('salesperson.meetings');
    Route::post('/meetings', [MeetingController::class, 'store'])->name('salesperson.meetings.store');
    Route::get('/meetings/{meeting}', [MeetingController::class, 'show'])->name('salesperson.meetings.show');
    Route::put('/meetings/{meeting}', [MeetingController::class, 'update'])->name('salesperson.meetings.update');
    Route::delete('/meetings/{meeting}', [MeetingController::class, 'destroy'])->name('salesperson.meetings.destroy');
    Route::get('/meetings/pending-reminders', [MeetingController::class, 'getPendingReminders'])->name('salesperson.meetings.reminders');
    Route::put('/meetings/{meeting}/complete', [MeetingController::class, 'markAsCompleted'])->name('salesperson.meetings.complete');

    // Plan Routes
    Route::get('/plans', [PlanController::class, 'index'])->name('salesperson.plans');
    Route::post('/plans', [PlanController::class, 'store'])->name('salesperson.plans.store');
    Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('salesperson.plans.show');
    Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('salesperson.plans.update');
    Route::get('/plans/current-month', [PlanController::class, 'getCurrentMonthPlan'])->name('salesperson.plans.current');
    Route::get('/plans/quarterly', [PlanController::class, 'getQuarterlyPlan'])->name('salesperson.plans.quarterly');
    Route::get('/plans/yearly', [PlanController::class, 'getYearlyPlan'])->name('salesperson.plans.yearly');
});


// Common routes for all authenticated users
Route::prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class, 'index']);
    Route::put('/profile', [SettingsController::class, 'updateProfile']);
    Route::put('/password', [SettingsController::class, 'updatePassword']);
    Route::put('/notifications', [SettingsController::class, 'updateNotificationPreferences']);
    Route::put('/targets', [SettingsController::class, 'updateTargets']);
});

