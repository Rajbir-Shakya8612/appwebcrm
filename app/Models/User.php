<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone',
        'photo',
        'whatsapp_number',
        'pincode',
        'address',
        'location',
        'designation',
        'date_of_joining',
        'status',
        'settings',
        'target_amount',
        'target_leads'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_joining' => 'date',
            'settings' => 'array',
            'target_amount' => 'decimal:2',
            'target_leads' => 'integer',
            'status' => 'boolean',
        ];
    }

    /**
     * Get the user's photo URL.
     */
    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : asset('images/default-avatar.png');
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is salesperson
     */
    public function isSalesperson(): bool
    {
        return $this->role === 'salesperson';
    }

    /**
     * Check if user is dealer
     */
    public function isDealer(): bool
    {
        return $this->role === 'dealer';
    }

    /**
     * Check if user is carpenter
     */
    public function isCarpenter(): bool
    {
        return $this->role === 'carpenter';
    }

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->status;
    }

    // /**
    //  * Get user's attendances
    //  */
    // public function attendances(): HasMany
    // {
    //     return $this->hasMany(Attendance::class);
    // }

    // /**
    //  * Get user's leads
    //  */
    // public function leads(): HasMany
    // {
    //     return $this->hasMany(Lead::class);
    // }

    // /**
    //  * Get user's sales
    //  */
    // public function sales(): HasMany
    // {
    //     return $this->hasMany(Sale::class);
    // }

    // /**
    //  * Get user's location tracks
    //  */
    // public function locationTracks(): HasMany
    // {
    //     return $this->hasMany(LocationTrack::class);
    // }

    // /**
    //  * Get user's tasks
    //  */
    // public function tasks(): HasMany
    // {
    //     return $this->hasMany(Task::class);
    // }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($role): bool
    {
        return $this->role->slug === $role;
    }
}
