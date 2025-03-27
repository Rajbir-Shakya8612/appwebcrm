<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'month',
        'year',
        'type', // monthly, quarterly, yearly
        'lead_target',
        'sales_target',
        'description',
        'status', // draft, active, completed
        'achievements',
        'notes'
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'lead_target' => 'integer',
        'sales_target' => 'decimal:2',
        'achievements' => 'array'
    ];

    /**
     * Get the user that owns the plan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the achievement percentage for leads
     */
    public function getLeadAchievementPercentage(): float
    {
        $achieved = $this->achievements['leads'] ?? 0;
        return $this->lead_target > 0 ? ($achieved / $this->lead_target) * 100 : 0;
    }

    /**
     * Get the achievement percentage for sales
     */
    public function getSalesAchievementPercentage(): float
    {
        $achieved = $this->achievements['sales'] ?? 0;
        return $this->sales_target > 0 ? ($achieved / $this->sales_target) * 100 : 0;
    }

    /**
     * Update achievements
     */
    public function updateAchievements(int $leads, float $sales): void
    {
        $this->achievements = [
            'leads' => $leads,
            'sales' => $sales,
            'updated_at' => now()
        ];
        $this->save();
    }
} 