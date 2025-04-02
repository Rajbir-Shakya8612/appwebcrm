<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Models\LocationTrack;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminLocationController extends Controller
{
    public function index()
    {
        $date = request('date', now()->toDateString());
        
        // Get checked in users for today
        $checkedInUsers = Attendance::with(['user' => function($q) {
            $q->whereHas('role', function($q) {
                $q->where('slug', 'salesperson');
            });
        }])
        ->whereHas('user.role', function($q) {
            $q->where('slug', 'salesperson');
        })
        ->whereDate('date', $date)
        ->whereNotNull('check_in_location')
        ->get();

        return view('admin.locations.index', compact('checkedInUsers', 'date'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pincode' => 'nullable|string|max:10',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string'
        ]);

        $location = Location::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Location created successfully',
            'location' => $location
        ]);
    }

    public function show(Location $location)
    {
        $location->load('user');
        return view('admin.locations.show', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'pincode' => 'nullable|string|max:10',
            'is_active' => 'required|boolean',
            'notes' => 'nullable|string'
        ]);

        $location->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'location' => $location
        ]);
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully'
        ]);
    }

    public function getLocationTracks(Request $request)
    {
        try {
            $date = $request->date ?? now()->toDateString();
            
            $query = Attendance::with(['user' => function($q) {
                $q->whereHas('role', function($q) {
                    $q->where('slug', 'salesperson');
                });
            }])
            ->whereHas('user.role', function($q) {
                $q->where('slug', 'salesperson');
            })
            ->whereDate('date', $date)
            ->whereNotNull('check_in_location');

            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            $attendances = $query->latest()->get();

            if ($attendances->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No tracking data found for the selected criteria'
                ]);
            }

            $tracks = [];
            foreach ($attendances as $attendance) {
                if ($attendance->user) {
                    // Add check-in location
                    if ($attendance->check_in_location) {
                        $checkInLocation = json_decode($attendance->check_in_location, true);
                        if (isset($checkInLocation['latitude'], $checkInLocation['longitude'])) {
                            $tracks[] = [
                                'id' => $attendance->id . '_in',
                                'user' => $attendance->user->name,
                                'date' => $attendance->date,
                                'time' => $attendance->check_in_time,
                                'latitude' => $checkInLocation['latitude'],
                                'longitude' => $checkInLocation['longitude'],
                                'accuracy' => $checkInLocation['accuracy'] ?? null,
                                'type' => 'check_in',
                                'google_maps_url' => "https://www.google.com/maps?q={$checkInLocation['latitude']},{$checkInLocation['longitude']}"
                            ];
                        }
                    }

                    // Add check-out location if exists
                    if ($attendance->check_out_location) {
                        $checkOutLocation = json_decode($attendance->check_out_location, true);
                        if (isset($checkOutLocation['latitude'], $checkOutLocation['longitude'])) {
                            $tracks[] = [
                                'id' => $attendance->id . '_out',
                                'user' => $attendance->user->name,
                                'date' => $attendance->date,
                                'time' => $attendance->check_out_time,
                                'latitude' => $checkOutLocation['latitude'],
                                'longitude' => $checkOutLocation['longitude'],
                                'accuracy' => $checkOutLocation['accuracy'] ?? null,
                                'type' => 'check_out',
                                'google_maps_url' => "https://www.google.com/maps?q={$checkOutLocation['latitude']},{$checkOutLocation['longitude']}"
                            ];
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $tracks,
                'message' => count($tracks) > 0 ? 'Tracking data retrieved successfully' : 'No tracking points found'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getLocationTracks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching location tracks',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLocationStats(Request $request)
    {
        $query = Attendance::with(['user' => function($q) {
            $q->whereHas('role', function($q) {
                $q->where('slug', 'salesperson');
            });
        }])
        ->whereHas('user.role', function($q) {
            $q->where('slug', 'salesperson');
        })
        ->whereDate('date', $request->date ?? now()->toDateString())
        ->whereNotNull('check_in_location');

        $attendances = $query->get();

        $totalLocations = 0;
        $uniqueUsers = collect();
        $totalDistance = 0;
        $totalSpeed = 0;
        $speedCount = 0;

        foreach ($attendances as $attendance) {
            if ($attendance->check_in_location) {
                $totalLocations++;
                $uniqueUsers->push($attendance->user_id);
            }
            if ($attendance->check_out_location) {
                $totalLocations++;
            }
        }

        return response()->json([
            'total_tracks' => $totalLocations,
            'unique_users' => $uniqueUsers->unique()->count(),
            'total_distance' => $totalDistance,
            'average_speed' => $speedCount > 0 ? $totalSpeed / $speedCount : 0
        ]);
    }

    public function getCheckedInUsers(Request $request)
    {
        $query = Attendance::with(['user' => function($q) {
            $q->whereHas('role', function($q) {
                $q->where('slug', 'salesperson');
            });
        }])
        ->whereHas('user.role', function($q) {
            $q->where('slug', 'salesperson');
        })
        ->whereNotNull('check_in_location')
        ->whereNull('check_out_time')
        ->whereDate('date', $request->date ?? now()->toDateString());

        $attendances = $query->get();

        $users = $attendances->map(function ($attendance) {
            if ($attendance->user) {
                $location = json_decode($attendance->check_in_location, true);
                return [
                    'id' => $attendance->user_id,
                    'name' => $attendance->user->name,
                    'check_in_time' => $attendance->check_in_time,
                    'latitude' => $location['latitude'],
                    'longitude' => $location['longitude'],
                    'accuracy' => $location['accuracy'],
                    'status' => $attendance->status
                ];
            }
        })->filter(); // Remove any null values

        return response()->json([
            'data' => $users
        ]);
    }

    public function timeline()
    {
        // Get all salespersons
        $salespersons = User::whereHas('role', function($q) {
            $q->where('slug', 'salesperson');
        })->get();

        return view('admin.locations.timeline', compact('salespersons'));
    }
} 