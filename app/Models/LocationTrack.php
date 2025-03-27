<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'tracked_at' => 'datetime'
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
}
