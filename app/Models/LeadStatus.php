<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeadStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'description'
    ];

    /**
     * Get the leads for this status.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'status_id');
    }
} 