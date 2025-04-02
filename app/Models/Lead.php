<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status_id',
        'name',
        'email',
        'phone',
        'company',
        'notes',
        'description',
        'source',
        'expected_amount',
        'follow_up_date',
        'location',
        'latitude',
        'longitude',
        'additional_info'
    ];

    protected $casts = [
        'follow_up_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    protected $with = ['status'];

    /**
     * Get the user that owns the lead.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status of the lead.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(LeadStatus::class, 'status_id');
    }

    /**
     * Get the sale record associated with the lead.
     */
    public function sale()
    {
        return $this->hasOne(Sale::class);
    }

    /**
     * Get the activities for the lead.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest();
    }

    /**
     * Get the notifications for the lead.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'notifiable_id')
            ->where('notifiable_type', Lead::class);
    }

    /**
     * Check if a user can add a new lead based on their attendance.
     */
    public function canAddNewLead(): bool
    {
        return Attendance::where('user_id', $this->user_id)
            ->whereDate('date', now()->toDateString())
            ->exists();
    }

    /**
     * Share lead details with another brand.
     */
    public function shareWithOtherBrand($brandId, $notes): void
    {
        $this->update([
            'status' => 'shared',
            'additional_info' => array_merge($this->additional_info ?? [], [
                'shared_with_brand' => $brandId,
                'shared_notes' => $notes,
                'shared_at' => now()
            ])
        ]);
    }

    /**
     * Get the lead's color based on status.
     */
    public function getStatusColorAttribute(): string
    {
        return $this->status->color ?? '#3B82F6';
    }

    /**
     * Get the status label for the lead.
     */
    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    /**
     * Get the formatted expected value.
     */
    public function getFormattedExpectedValueAttribute(): string
    {
        return number_format($this->expected_amount, 2);
    }

    /**
     * Get the formatted follow up date.
     */
    public function getFollowUpDateFormattedAttribute(): ?string
    {
        return $this->follow_up_date?->format('M d, Y');
    }

    /**
     * Get the days until follow up.
     */
    public function getDaysUntilFollowUpAttribute(): ?int
    {
        if (!$this->follow_up_date) return null;
        return now()->diffInDays($this->follow_up_date, false);
    }

    /**
     * Check if the lead is overdue for follow up.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->follow_up_date && $this->follow_up_date->isPast();
    }

    /**
     * Check if the lead is converted.
     */
    public function getIsConvertedAttribute(): bool
    {
        return $this->status === 'converted';
    }

    /**
     * Check if the lead is lost.
     */
    public function getIsLostAttribute(): bool
    {
        return $this->status === 'lost';
    }

    /**
     * Check if the lead is active.
     */
    public function getIsActiveAttribute(): bool
    {
        return !$this->is_converted && !$this->is_lost;
    }

    /**
     * Create a new activity for the lead.
     */
    public function createActivity(string $type, string $description, User $user): LeadActivity
    {
        return LeadActivity::createActivity($this, $type, $description, $user);
    }

    /**
     * Update the lead status and create an activity.
     */
    public function updateStatus(string $newStatus, User $user): bool
    {
        $oldStatus = $this->status;
        $this->status = $newStatus;
        
        if ($this->save()) {
            $this->createActivity(
                'Status Changed',
                "Status changed from {$oldStatus} to {$newStatus}",
                $user
            );
            return true;
        }
        
        return false;
    }

    /**
     * Schedule a follow up and create an activity.
     */
    public function scheduleFollowUp(string $date, ?string $notes, User $user): bool
    {
        $this->follow_up_date = $date;
        $this->notes = $notes;
        
        if ($this->save()) {
            $this->createActivity(
                'Follow Up Scheduled',
                "Follow up scheduled for {$date}" . ($notes ? ": {$notes}" : ''),
                $user
            );

            // Create a notification for the follow-up
            $this->user->notifications()->create([
                'type' => 'lead_follow_up',
                'title' => 'Lead Follow-up Scheduled',
                'message' => "You have a follow-up scheduled for lead {$this->name} on " . Carbon::parse($date)->format('M d, Y'),
                'data' => [
                    'lead_id' => $this->id,
                    'follow_up_date' => $date
                ]
            ]);

            return true;
        }
        
        return false;
    }

    /**
     * Get the pending follow-ups count.
     */
    public static function getPendingFollowUpsCount(User $user): int
    {
        return static::where('user_id', $user->id)
            ->where('follow_up_date', '<=', now()->addDays(7))
            ->where('follow_up_date', '>=', now())
            ->count();
    }

    public static function getValidationRules($isUpdate = false, $leadId = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'company' => 'required|string|max:255',
            'additional_info' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'expected_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'description' => 'nullable|string',
            'status_id' => 'required|exists:lead_statuses,id',
            'follow_up_date' => 'required|date|after_or_equal:today',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180'
        ];

        if ($isUpdate && $leadId) {
            $rules['email'] .= ',email,' . $leadId;
        }

        return $rules;
    }
}
