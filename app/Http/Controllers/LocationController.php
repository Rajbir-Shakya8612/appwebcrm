<?php

namespace App\Http\Controllers;

use App\Models\LocationTrack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        
        // Validate request
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'address' => 'nullable|string',
            'accuracy' => 'nullable|numeric'
        ]);
        
        // Create location track
        $locationTrack = LocationTrack::create([
            'user_id' => $user->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'address' => $request->address,
            'accuracy' => $request->accuracy,
            'timestamp' => $now
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'location' => $locationTrack
        ]);
    }
    
    public function getTodayTracks()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        $tracks = LocationTrack::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->orderBy('created_at')
            ->get();
            
        return response()->json([
            'success' => true,
            'tracks' => $tracks
        ]);
    }
    
    public function getMonthlyTracks()
    {
        $user = Auth::user();
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        
        $tracks = LocationTrack::where('user_id', $user->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->orderBy('created_at')
            ->get();
            
        return response()->json([
            'success' => true,
            'tracks' => $tracks
        ]);
    }
    
    public function getTracksByDate(Request $request)
    {
        $user = Auth::user();
        $date = Carbon::parse($request->date);
        
        $tracks = LocationTrack::where('user_id', $user->id)
            ->whereDate('created_at', $date)
            ->orderBy('created_at')
            ->get();
            
        return response()->json([
            'success' => true,
            'tracks' => $tracks
        ]);
    }
} 