<?php

namespace App\Http\Controllers;

use App\Models\LocationTrack;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LocationTrackController extends Controller
{
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();
            
            // Check if user is checked in
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', $now->toDateString())
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->first();
                
            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is not checked in'
                ]);
            }
            
            // Record location
            $locationTrack = LocationTrack::recordLocation(
                $user->id,
                $request->latitude,
                $request->longitude,
                $request->address,
                $request->speed,
                $request->accuracy
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Location recorded successfully',
                'data' => $locationTrack
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error recording location: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getTodayTracks()
    {
        try {
            $user = Auth::user();
            $today = Carbon::today();
            
            $tracks = LocationTrack::getDailyTimeline($user->id, $today);
            
            return response()->json([
                'success' => true,
                'data' => $tracks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching location tracks: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getMonthlyTimeline(Request $request)
    {
        try {
            $user = Auth::user();
            $month = $request->input('month', Carbon::now()->month);
            $year = $request->input('year', Carbon::now()->year);
            
            $timeline = LocationTrack::getMonthlyTimeline($user->id, $month, $year);
            
            return response()->json([
                'success' => true,
                'data' => $timeline
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching monthly timeline: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getLocationStats(Request $request)
    {
        try {
            $user = Auth::user();
            $date = $request->input('date', Carbon::today()->toDateString());
            
            $stats = LocationTrack::getLocationStats($user->id, $date);
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching location stats: ' . $e->getMessage()
            ], 500);
        }
    }
} 