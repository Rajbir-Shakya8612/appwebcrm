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
use App\Http\Controllers\TaskController;
use App\Http\Controllers\AdminLocationController;
use App\Http\Controllers\NotificationController;

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

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';

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

    // Location Tracking Routes
    Route::post('/location/tracks', [LocationTrackController::class, 'store'])->name('location.store');
    Route::get('/location/today-tracks', [LocationTrackController::class, 'getTodayTracks'])->name('location.today');
    Route::get('/location/monthly-timeline', [LocationTrackController::class, 'getMonthlyTimeline'])->name('location.monthly');
    Route::get('/location/stats', [LocationTrackController::class, 'getLocationStats'])->name('location.stats');

    // Notification Routes
    Route::post('notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');
});




Route::middleware(['auth', 'salesperson'])->prefix('salesperson')->group(function () {

    Route::get('/salesperson-dashboard', [SalespersonDashboardController::class, 'index'])
        ->name('salesperson.dashboard');

    Route::get('/sales', [SaleController::class, 'index'])->name('salesperson.sales');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('salesperson.attendance');
    Route::get('/performance', [PerformanceController::class, 'index'])->name('salesperson.performance');
    Route::get('/profile', [ProfileController::class, 'index'])->name('salesperson.profile');
    Route::get('/settings', [SettingsController::class, 'index'])->name('salesperson.settings');

    // Attendance
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])->name('salesperson.attendance.checkin');
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut'])->name('salesperson.attendance.checkout');
    Route::get('/attendance/status', [AttendanceController::class, 'status']);
    Route::get('/attendance/monthly-report', [AttendanceController::class, 'monthlyReport'])->name('salesperson.attendance.report');
    Route::get('/attendance/calendar-events', [AttendanceController::class, 'calendarEvents']);

    // Leads
    Route::get('/leads', [LeadController::class, 'index'])->name('salesperson.leads.index');
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

    // Task Routes
    Route::get('/tasks', [AdminDashboardController::class, 'index'])->name('salesperson.tasks');
    Route::post('/tasks', [AdminDashboardController::class, 'store'])->name('salesperson.tasks.store');
    Route::get('/tasks/{task}', [AdminDashboardController::class, 'show'])->name('salesperson.tasks.show');
    Route::put('/tasks/{task}', [AdminDashboardController::class, 'update'])->name('salesperson.tasks.update');
    Route::delete('/tasks/{task}', [AdminDashboardController::class, 'destroy'])->name('salesperson.tasks.destroy');
    Route::put('/tasks/{task}/status', [AdminDashboardController::class, 'updateStatus'])->name('salesperson.tasks.status');
});


// Common routes for all authenticated users
Route::prefix('settings')->group(function () {
    Route::get('/', [SettingsController::class, 'index']);
    Route::put('/profile', [SettingsController::class, 'updateProfile']);
    Route::put('/password', [SettingsController::class, 'updatePassword']);
    Route::put('/notifications', [SettingsController::class, 'updateNotificationPreferences']);
    Route::put('/targets', [SettingsController::class, 'updateTargets']);
});

