<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    protected $fillable = [
        'lead_id',
        'type',
        'description',
        'created_by'
    ];

    /**
     * Get the lead that owns the activity.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Get the user who created the activity.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Create a new activity for a lead.
     */
    public static function createActivity(Lead $lead, string $type, string $description, User $user): self
    {
        return self::create([
            'lead_id' => $lead->id,
            'type' => $type,
            'description' => $description,
            'created_by' => $user->id
        ]);
    }

    /**
     * Get the formatted date of the activity.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->created_at->format('M d, Y H:i');
    }
} 