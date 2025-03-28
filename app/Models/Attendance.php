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
        if ($this->check_in_time && $this->check_out_time) {
            return $this->check_in_time->diffInHours($this->check_out_time, false);
        }
        return 0;
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
}
