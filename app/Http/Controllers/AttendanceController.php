<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LocationTrack;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
            
        $isCheckedIn = $todayAttendance && $todayAttendance->check_in_time;
        
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
            
        $monthlyLate = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->where('status', 'late')
            ->count();
            
        $monthlyAbsent = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->where('status', 'absent')
            ->count();

        // Calculate total working hours for the month
        $totalWorkingHours = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $today->month)
            ->whereYear('date', $today->year)
            ->sum('working_hours');

        // Get attendance history
        $attendanceHistory = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();
            
        // Get calendar events
        $calendarEvents = Attendance::where('user_id', $user->id)
            ->get()
            ->map(function ($attendance) {
                return [
                    'title' => $attendance->status === 'present' ? 'Present' : ($attendance->status === 'late' ? 'Late' : 'Absent'),
                    'start' => $attendance->date,
                    'backgroundColor' => $attendance->status === 'present' ? '#28a745' : ($attendance->status === 'late' ? '#ffc107' : '#dc3545')
                ];
            });
            
        return view('dashboard.salesperson.attendance.index', compact(
            'isCheckedIn',
            'monthlyAttendance',
            'monthlyPresent',
            'monthlyLate',
            'monthlyAbsent',
            'totalWorkingHours',
            'attendanceHistory',
            'calendarEvents'
        ));
    }

    public function checkIn(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $today = $now->toDateString();
        
        // Check if already checked in
        $existingAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
            
        if ($existingAttendance && $existingAttendance->check_in_time) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in'
            ]);
        }
        
        // Create or update attendance record
        $attendance = $existingAttendance ?? new Attendance();
        $attendance->user_id = $user->id;
        $attendance->date = $today;
        $attendance->check_in_time = $now;
        $attendance->check_in_location = json_encode([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address
        ]);
        
        // Check if late
        if (Attendance::isLate($now)) {
            $attendance->status = 'late';
            $this->sendLateNotification($user);
        } else {
            $attendance->status = 'present';
        }
        
        $attendance->save();
        
        // Record activity
        Activity::create([
            'user_id' => $user->id,
            'type' => 'attendance',
            'description' => "Checked in " . ($attendance->status === 'late' ? 'late' : 'on time'),
            'details' => json_encode([
                'time' => $now->format('h:i A'),
                'location' => $request->address
            ])
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Checked in successfully',
            'time' => $now->format('h:i A'),
            'status' => $attendance->status
        ]);
    }

    public function checkOut(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        $today = $now->toDateString();
        
        // Get today's attendance
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();
            
        if (!$attendance || !$attendance->check_in_time) {
            return response()->json([
                'success' => false,
                'message' => 'Not checked in'
            ]);
        }
        
        // Update attendance record
        $attendance->check_out_time = $now;
        $attendance->check_out_location = json_encode([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address
        ]);
        
        // Calculate working hours
        $attendance->working_hours = $attendance->calculateWorkingHours();
        $attendance->save();
        
        // Record activity
        Activity::create([
            'user_id' => $user->id,
            'type' => 'attendance',
            'description' => 'Checked out',
            'details' => json_encode([
                'time' => $now->format('h:i A'),
                'working_hours' => $attendance->working_hours,
                'location' => $request->address
            ])
        ]);
        
        // Send WhatsApp notification
        $this->sendCheckOutNotification($user, $attendance);
        
        return response()->json([
            'success' => true,
            'message' => 'Checked out successfully',
            'time' => $now->format('h:i A'),
            'working_hours' => $attendance->working_hours
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
            'canCheckIn' => !$attendance || !$attendance->check_in_time,
            'canCheckOut' => $attendance && $attendance->check_in_time && !$attendance->check_out_time
        ]);
    }

    public function timeline()
    {
        $user = Auth::user();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        
        $locationTracks = LocationTrack::where('user_id', $user->id)
            ->whereMonth('tracked_at', $month)
            ->whereYear('tracked_at', $year)
            ->orderBy('tracked_at')
            ->get();
            
        return view('dashboard.salesperson.attendance.timeline', compact('locationTracks'));
    }

    private function sendLateNotification($user)
    {
        $message = "Dear {$user->name}, you are late for work today. Please ensure punctuality.";
        
        // Send WhatsApp notification
        if ($user->whatsapp_number) {
            $this->sendWhatsAppMessage($user->whatsapp_number, $message);
        }
        
        // Record notification activity
        Activity::create([
            'user_id' => $user->id,
            'type' => 'notification',
            'description' => 'Late attendance notification sent',
            'details' => json_encode([
                'message' => $message,
                'time' => now()->format('h:i A')
            ])
        ]);
    }

    private function sendCheckOutNotification($user, $attendance)
    {
        $message = "Dear {$user->name}, you have checked out for the day.\n";
        $message .= "Check-in time: {$attendance->check_in_time->format('h:i A')}\n";
        $message .= "Check-out time: {$attendance->check_out_time->format('h:i A')}\n";
        $message .= "Working hours: {$attendance->working_hours} hours";
        
        // Send WhatsApp notification
        if ($user->whatsapp_number) {
            $this->sendWhatsAppMessage($user->whatsapp_number, $message);
        }
        
        // Record notification activity
        Activity::create([
            'user_id' => $user->id,
            'type' => 'notification',
            'description' => 'Check-out notification sent',
            'details' => json_encode([
                'message' => $message,
                'time' => now()->format('h:i A')
            ])
        ]);
    }

    private function sendWhatsAppMessage($phone, $message)
    {
        // Implement WhatsApp API integration here
        // This is a placeholder for the actual implementation
        // You can use services like Twilio, MessageBird, or any other WhatsApp API provider
        
        // Example using a hypothetical WhatsApp API:
        /*
        Http::post('your-whatsapp-api-endpoint', [
            'phone' => $phone,
            'message' => $message,
            'api_key' => config('services.whatsapp.api_key')
        ]);
        */
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
            'total_working_hours' => $attendance->sum('working_hours')
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
                    $record->check_in_time ? $record->check_in_time->format('H:i:s') : '',
                    $record->check_out_time ? $record->check_out_time->format('H:i:s') : '',
                    $record->working_hours,
                    $record->status
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
} 