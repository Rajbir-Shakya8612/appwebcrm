<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'pincode',
        'status',
        'notes',
        'expected_amount',
        'follow_up_date',
        'source',
        'location',
        'additional_info'
    ];

    protected $casts = [
        'expected_amount' => 'decimal:2',
        'follow_up_date' => 'date',
        'additional_info' => 'array'
    ];

    /**
     * Get the user who owns the lead.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
}
