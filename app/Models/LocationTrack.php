<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LocationTrack extends Model
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'address',
        'speed',
        'accuracy',
        'tracked_at'
    ];

    protected $casts = [
        'latitude' => 'decimal:10,8',
        'longitude' => 'decimal:11,8',
        'tracked_at' => 'datetime',
        'speed' => 'decimal:2',
        'accuracy' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // 📍 किसी भी यूजर का आज का पूरा लोकेशन टाइमलाइन निकालने के लिए
    public static function getDailyTimeline($userId, $date)
    {
        return self::where('user_id', $userId)
            ->whereDate('tracked_at', $date)
            ->orderBy('tracked_at')
            ->get();
    }

    // 📍 किसी भी यूजर का सबसे नया लोकेशन डेटा (Latest location) निकालने के लिए
    public static function getCurrentLocation($userId)
    {
        return self::where('user_id', $userId)
            ->latest('tracked_at')
            ->first();
    }

    public static function recordLocation($userId, $latitude, $longitude, $address = null, $speed = null, $accuracy = null)
    {
        return self::create([
            'user_id' => $userId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'address' => $address,
            'speed' => $speed,
            'accuracy' => $accuracy,
            'tracked_at' => Carbon::now()
        ]);
    }

    public static function getMonthlyTimeline($userId, $month, $year)
    {
        return self::where('user_id', $userId)
            ->whereMonth('tracked_at', $month)
            ->whereYear('tracked_at', $year)
            ->orderBy('tracked_at')
            ->get()
            ->groupBy(function ($track) {
                return $track->tracked_at->format('Y-m-d');
            });
    }

    public static function getLocationStats($userId, $date)
    {
        $tracks = self::where('user_id', $userId)
            ->whereDate('tracked_at', $date)
            ->get();

        if ($tracks->isEmpty()) {
            return null;
        }

        $firstTrack = $tracks->first();
        $lastTrack = $tracks->last();

        return [
            'start_time' => $firstTrack->tracked_at,
            'end_time' => $lastTrack->tracked_at,
            'total_points' => $tracks->count(),
            'average_speed' => $tracks->avg('speed'),
            'average_accuracy' => $tracks->avg('accuracy'),
            'distance' => self::calculateTotalDistance($tracks),
            'locations' => $tracks->map(function ($track) {
                return [
                    'latitude' => $track->latitude,
                    'longitude' => $track->longitude,
                    'time' => $track->tracked_at->format('H:i:s'),
                    'address' => $track->address
                ];
            })
        ];
    }

    private static function calculateTotalDistance($tracks)
    {
        $totalDistance = 0;
        $tracks = $tracks->values();

        for ($i = 1; $i < $tracks->count(); $i++) {
            $totalDistance += self::calculateDistance(
                $tracks[$i - 1]->latitude,
                $tracks[$i - 1]->longitude,
                $tracks[$i]->latitude,
                $tracks[$i]->longitude
            );
        }

        return round($totalDistance, 2); // Distance in kilometers
    }

    private static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat/2) * sin($deltaLat/2) +
             cos($lat1) * cos($lat2) *
             sin($deltaLon/2) * sin($deltaLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return $distance;
    }
}
