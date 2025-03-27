<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'check_in_time',
        'check_out_time',
        'check_in_location',
        'check_out_location',
        'status',
        'check_in_photo',
        'check_out_photo',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime'
    ];

    public static function isPresent($userId, $date)
    {
        return self::where('user_id', $userId)
            ->whereDate('date', $date)
            ->whereNotNull('check_in_time')
            ->exists();
    }

    public static function isLate($checkInTime)
    {
        $lateThreshold = '09:30:00';
        return $checkInTime > $lateThreshold;
    }

    public function getStatusAttribute($value)
    {
        if ($this->check_in_time && $this->isLate($this->check_in_time->format('H:i:s'))) {
            return 'late';
        }
        return $value;
    }
}
