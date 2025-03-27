<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'meeting_date',
        'reminder_date',
        'status', // pending, completed, cancelled
        'location',
        'attendees',
        'notes'
    ];

    protected $casts = [
        'meeting_date' => 'datetime',
        'reminder_date' => 'datetime',
        'attendees' => 'array'
    ];

    /**
     * Get the user that owns the meeting.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if meeting is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if meeting is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if meeting is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if reminder is due
     */
    public function isReminderDue(): bool
    {
        return $this->reminder_date && $this->reminder_date->isPast() && !$this->isCompleted();
    }
} 