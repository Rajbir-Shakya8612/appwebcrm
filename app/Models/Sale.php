<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lead_id',
        'amount',
        'payment_status',
        'payment_method',
        'notes',
        'product_details',
        'sale_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'product_details' => 'array',
        'sale_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Set payment_status attribute with enum-like validation.
     */
    protected function paymentStatus(): Attribute
    {
        return Attribute::make(
            get: fn($value) => ucfirst($value),
            set: fn($value) => strtolower($value)
        );
    }
}
