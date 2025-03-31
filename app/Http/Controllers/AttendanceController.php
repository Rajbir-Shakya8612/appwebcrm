<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Activity;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Jobs\CheckInActivity;
use App\Models\LocationTrack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Dispatch;

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



    // public function checkOut(Request $request)
    // {
    //     try {
    //         $user = Auth::user();
    //         $now = Carbon::now();
    //         $today = $now->toDateString();

    //         // Get today's attendance
    //         $attendance = Attendance::where('user_id', $user->id)
    //             ->whereDate('date', $today)
    //             ->first();

    //         if (!$attendance || !$attendance->check_in_time) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Not checked in'
    //             ]);
    //         }

    //         // Update attendance record
    //         $attendance->check_out_time = $now;
    //         $attendance->check_out_location = json_encode([
    //             'latitude' => $request->latitude,
    //             'longitude' => $request->longitude,
    //             'address' => $request->address
    //         ]);

    //         // Calculate working hours
    //         $attendance->working_hours = $attendance->calculateWorkingHours();
    //         $attendance->save();

    //         // Record activity in background
    //         dispatch(function () use ($user, $now, $request, $attendance) {
    //             Activity::create([
    //                 'user_id' => $user->id,
    //                 'type' => 'attendance',
    //                 'description' => 'Checked out',
    //                 'details' => json_encode([
    //                     'time' => $now->format('h:i A'),
    //                     'working_hours' => $attendance->working_hours,
    //                     'location' => $request->address
    //                 ])
    //             ]);
    //         })->afterResponse();

    //         // Send notification in background
    //         dispatch(function () use ($user, $attendance) {
    //             $this->sendCheckOutNotification($user, $attendance);
    //         })->afterResponse();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Checked out successfully',
    //             'time' => $now->format('h:i A'),
    //             'working_hours' => $attendance->working_hours
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error checking out: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    // public function status()
    // {
    //     $user = Auth::user();
    //     $today = Carbon::today();

    //     $attendance = Attendance::where('user_id', $user->id)
    //         ->whereDate('date', $today)
    //         ->first();

    //     return response()->json([
    //         'attendance' => $attendance,
    //         'canCheckIn' => !$attendance || !$attendance->check_in_time,
    //         'canCheckOut' => $attendance && $attendance->check_in_time && !$attendance->check_out_time
    //     ]);
    // }
    // ======================= old code =================
    public function checkIn(Request $request)
    {
        // dd($request);
        try {
            $user = Auth::user();
            $now = Carbon::now();
            $today = $now->toDateString();
            // JSON string ko array me convert karein
            $location = json_decode($request->input('check_in_location'), true);

            // Check karein ki location valid hai ya nahi
            if (!is_array($location) || !isset($location['latitude'], $location['longitude'], $location['accuracy'])) {
                return response()->json(['success' => false, 'message' => 'Invalid location data']);
            }

            $attendance = Attendance::firstOrNew([
                'user_id' => $user->id,
                'date' => $today
            ]);

            if ($attendance->check_in_time) {
                return response()->json(['success' => false, 'message' => 'Already checked in']);
            }

            $location_address = $location['latitude'] . ', ' . $location['longitude'];
            // Location data ko handle karna
            $attendance->check_in_time = $now;
            $attendance->check_in_location = json_encode([
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'address' => $location_address,
            ]);
        
            if (Attendance::isLate($now)) {
                $attendance->status = 'late';
                // Send notification in background
                dispatch(function () use ($user) {
                    $this->sendLateNotification($user);
                })->afterResponse();
            } else {
                $attendance->status = 'present';
            }

            $attendance->save();

            $check_in_time = $now->format('h:i A');

            // Record activity in background (Without $request)
            dispatch(function () use ($user, $check_in_time, $location_address, $attendance) {
                Activity::create([
                    'user_id' => $user->id,
                    'type' => 'attendance',
                    'description' => "Checked in " . ($attendance->status === 'late' ? 'late' : 'on time'),
                    'details' => json_encode([
                        'time' => $check_in_time,
                        'location' => $location_address
                    ])
                ]);
            })->afterResponse();

            return response()->json([
                'success' => true,
                'message' => 'Check-in successful',
                'check_in_time' => $attendance->check_in_time->format('H:i:s'),
                'status' => $attendance->status
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function checkOut(Request $request)
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();
            $today = $now->toDateString();

            $attendance = Attendance::where('user_id', $user->id)->whereDate('date', $today)->first();

            if (!$attendance || !$attendance->check_in_time) {
                return response()->json(['success' => false, 'message' => 'Check-in required first']);
            }

            // JSON string ko array me convert karein
            $location = json_decode($request->input('check_out_location'), true);

            // Check karein ki location valid hai ya nahi
            if (!is_array($location) || !isset($location['latitude'], $location['longitude'], $location['accuracy'])) {
                return response()->json(['success' => false, 'message' => 'Invalid location data']);
            }

            $location_address = $location['latitude'] . ', ' . $location['longitude'];

            $attendance->check_out_time = $now;
            $attendance->check_out_location = json_encode([
                'latitude' => $location['latitude'] ?? null,
                'longitude' => $location['longitude'] ?? null,
                'address' => $location_address
            ]);

                 // Calculate working hours
            $attendance->working_hours = $attendance->calculateWorkingHours();
            $attendance->save();

            // ✅ Activity log background me store karein
            $check_out_time = $now->format('h:i A');
            dispatch(function () use ($user, $check_out_time, $attendance, $location_address) {
                Activity::create([
                    'user_id' => $user->id,
                    'type' => 'attendance',
                    'description' => 'Checked out',
                    'details' => json_encode([
                        'time' => $check_out_time,
                        'working_hours' => $attendance->working_hours,
                        'location' => $location_address
                    ])
                ]);
            })->afterResponse();

            // Send notification in background
            dispatch(function () use ($user, $attendance) {
                $this->sendCheckOutNotification($user, $attendance);
            })->afterResponse();

            return response()->json([
                'success' => true,
                'message' => 'Checked out successfully',
                'time' => $now->format('h:i A'),
                'working_hours' => $attendance->working_hours
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
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