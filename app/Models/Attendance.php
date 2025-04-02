<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

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
        'working_hours',
        'late_reason'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'working_hours' => 'decimal:2'
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
        $lateThreshold = Carbon::parse('09:30:00');
        return $checkInTime > $lateThreshold;
    }

    public function getStatusAttribute($value)
    {
        if ($this->check_in_time && $this->isLate($this->check_in_time)) {
            return 'late';
        }
        return $value;
    }

    public function calculateWorkingHours()
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return 0; // Default value if check-in or check-out is missing
        }

        $checkIn = Carbon::parse($this->check_in_time);
        $checkOut = Carbon::parse($this->check_out_time);

        // Ensure check-out time is AFTER check-in time
        if ($checkOut->lessThan($checkIn)) {
            return 0; // Prevent negative values
        }

        return round($checkIn->diffInSeconds($checkOut) / 3600, 2); // Convert to decimal
    }


    public function isWorkingHoursComplete()
    {
        return $this->working_hours >= 10; // 10 hours working time (9:30 AM to 7:30 PM)
    }

    public function getLocationHistory()
    {
        return LocationTrack::where('user_id', $this->user_id)
            ->whereDate('tracked_at', $this->date)
            ->orderBy('tracked_at')
            ->get();
    }

    public function canAddNewLead()
    {
        return $this->status === 'present' || $this->status === 'late';
    }

    public function canAddNewExpense()
    {
        return $this->status === 'present' || $this->status === 'late';
    }

    public function getCheckInAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function getCheckOutAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function setCheckInAttribute($value)
    {
        $this->attributes['check_in'] = $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function setCheckOutAttribute($value)
    {
        $this->attributes['check_out'] = $value ? Carbon::parse($value)->format('H:i:s') : null;
    }

    public function getWorkingHoursAttribute()
    {
        if (!$this->check_in || !$this->check_out) {
            return null;
        }

        $checkIn = Carbon::parse($this->check_in);
        $checkOut = Carbon::parse($this->check_out);

        if ($checkOut <= $checkIn) {
            return null;
        }

        $diffInMinutes = $checkIn->diffInMinutes($checkOut);
        $hours = floor($diffInMinutes / 60);
        $minutes = $diffInMinutes % 60;

        return sprintf('%d:%02d', $hours, $minutes);
    }
}
