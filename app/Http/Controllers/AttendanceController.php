<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LocationTrack;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
            
        $monthlyAttendance = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->get();
            
        $locationTracks = LocationTrack::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->get();
            
        return view('dashboard.salesperson.attendance.index', compact('attendance', 'monthlyAttendance', 'locationTracks'));
    }

    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $today = Carbon::today();
        
        // Check if already checked in
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
            
        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in for today'
            ]);
        }
        
        // Check if late (after 9:30 AM)
        $isLate = $now->hour > 9 || ($now->hour == 9 && $now->minute > 30);
        
        // Create attendance record
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $today,
            'check_in' => $now,
            'check_in_location' => $request->location,
            'status' => $isLate ? 'late' : 'present',
            'late_reason' => $isLate ? 'Arrived after 9:30 AM' : null
        ]);
        
        // If late, send WhatsApp notification
        if ($isLate) {
            $this->sendLateNotification($user);
        }
        
        return response()->json([
            'success' => true,
            'message' => $isLate ? 'Checked in (Late)' : 'Checked in successfully',
            'attendance' => $attendance
        ]);
    }

    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $today = Carbon::today();
        
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
            
        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'No check-in found for today'
            ]);
        }
        
        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked out for today'
            ]);
        }
        
        $attendance->update([
            'check_out' => $now,
            'check_out_location' => $request->location,
            'total_hours' => $now->diffInHours($attendance->check_in)
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Checked out successfully',
            'attendance' => $attendance
        ]);
    }

    public function status()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
            
        return response()->json([
            'attendance' => $attendance,
            'canCheckIn' => !$attendance || !$attendance->check_in,
            'canCheckOut' => $attendance && $attendance->check_in && !$attendance->check_out
        ]);
    }

    public function timeline()
    {
        $user = Auth::user();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        
        $locationTracks = LocationTrack::where('user_id', $user->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->orderBy('created_at')
            ->get();
            
        return view('dashboard.salesperson.attendance.timeline', compact('locationTracks'));
    }

    private function sendLateNotification($user)
    {
        // Implement WhatsApp notification logic here
        // You can use a free WhatsApp API or SMS gateway
        $message = "Dear {$user->name}, you are late for work today. Please ensure punctuality.";
        
        // Send notification logic here
        // This is a placeholder for the actual implementation
    }

    public function monthlyReport()
    {
        $user = Auth::user();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        
        $attendance = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();
            
        $stats = [
            'total_days' => $attendance->count(),
            'present' => $attendance->where('status', 'present')->count(),
            'late' => $attendance->where('status', 'late')->count(),
            'absent' => $attendance->where('status', 'absent')->count(),
            'leave' => $attendance->where('status', 'leave')->count()
        ];
        
        return view('dashboard.salesperson.attendance.monthly-report', compact('attendance', 'stats'));
    }
} 