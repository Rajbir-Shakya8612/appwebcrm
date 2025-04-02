<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'address',
        'pincode',
        'status', 
        'notes',
        'expected_amount',
        'follow_up_date',
        'source',
        'location',
        'additional_info',
    ];

    protected $casts = [
        'next_follow_up' => 'date',
        'expected_value' => 'decimal:2'
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
     * Get the activities for the lead.
     */
    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest();
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
        return number_format($this->expected_value, 2);
    }

    /**
     * Get the formatted next follow up date.
     */
    public function getNextFollowUpFormattedAttribute(): ?string
    {
        return $this->next_follow_up?->format('M d, Y');
    }

    /**
     * Get the days in pipeline.
     */
    public function getDaysInPipelineAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    /**
     * Check if the lead is overdue for follow up.
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->next_follow_up && $this->next_follow_up->isPast();
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
        $this->next_follow_up = $date;
        $this->notes = $notes;
        
        if ($this->save()) {
            $this->createActivity(
                'Follow Up Scheduled',
                "Follow up scheduled for {$date}" . ($notes ? ": {$notes}" : ''),
                $user
            );
            return true;
        }
        
        return false;
    }
}
