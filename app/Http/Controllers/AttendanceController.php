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
        
        // Get today's attendance status
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
            
        $isCheckedIn = $todayAttendance && $todayAttendance->check_in;
        
        // Get monthly attendance stats
        $monthlyAttendance = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->count();
            
        $monthlyPresent = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->where('status', 'present')
            ->count();
            
        $monthlyAbsent = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->where('status', 'absent')
            ->count();

        // Calculate total working hours for the month
        // $totalWorkingHours = Attendance::where('user_id', $user->id)
        //     ->whereMonth('date', $today->month)
        //     ->whereYear('date', $today->year)
        //     ->sum('working_hours');

        $totalWorkingHours = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->get()
            ->sum(function ($attendance) {
                if ($attendance->check_in && $attendance->check_out) {
                    return Carbon::parse($attendance->check_out)->diffInHours(Carbon::parse($attendance->check_in));
                }
                return 0;
            });


        // Get attendance history
        $attendanceHistory = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();
            
        // Get calendar events
        $calendarEvents = Attendance::where('user_id', $user->id)
            ->get()
            ->map(function ($attendance) {
                return [
                    'title' => $attendance->status === 'present' ? 'Present' : 'Absent',
                    'start' => $attendance->date,
                    'backgroundColor' => $attendance->status === 'present' ? '#28a745' : '#dc3545'
                ];
            });
            
        return view('dashboard.salesperson.attendance.index', compact(
            'isCheckedIn',
            'monthlyAttendance',
            'monthlyPresent',
            'monthlyAbsent',
            'totalWorkingHours',
            'attendanceHistory',
            'calendarEvents'
        ));
    }

    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Check if already checked in
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
            
        if ($existingAttendance && $existingAttendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in'
            ]);
        }
        
        // Create or update attendance record
        $attendance = $existingAttendance ?? new Attendance();
        $attendance->user_id = $user->id;
        $attendance->date = $today;
        $attendance->check_in = Carbon::now();
        $attendance->status = 'present';
        $attendance->location = json_encode($request->all());
        $attendance->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Checked in successfully'
        ]);
    }

    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Get today's attendance
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
            
        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Not checked in'
            ]);
        }
        
        // Update attendance record
        $attendance->check_out = Carbon::now();
        $attendance->working_hours = $attendance->check_in->diffInHours($attendance->check_out);
        $attendance->location = json_encode($request->all());
        $attendance->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Checked out successfully'
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

    public function export()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();
            
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance.csv"',
        ];
        
        $callback = function() use ($attendance) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Check In', 'Check Out', 'Working Hours', 'Status']);
            
            foreach ($attendance as $record) {
                fputcsv($file, [
                    $record->date->format('Y-m-d'),
                    $record->check_in ? $record->check_in->format('H:i:s') : '',
                    $record->check_out ? $record->check_out->format('H:i:s') : '',
                    $record->working_hours,
                    $record->status
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
} 