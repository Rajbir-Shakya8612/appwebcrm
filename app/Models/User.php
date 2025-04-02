<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'target_leads',
        'monthly_sales_target',
        'monthly_leads_target',
        'quarterly_sales_target',
        'quarterly_leads_target',
        'yearly_sales_target',
        'yearly_leads_target'
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
            'monthly_sales_target' => 'decimal:2',
            'quarterly_sales_target' => 'decimal:2',
            'yearly_sales_target' => 'decimal:2'
        ];
    }

    protected $with = ['role'];
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

    /**
     * Get user's attendances
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get user's leads
     */
    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    /**
     * Get user's sales
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Get user's location tracks
     */
    public function locationTracks(): HasMany
    {
        return $this->hasMany(LocationTrack::class);
    }

    /**
     * Get user's tasks
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function hasRole($role): bool
    {
        return $this->role && $this->role->slug === $role;
    }

    public function getCurrentAttendance()
    {
        return $this->attendances()
            ->whereDate('date', now())
            ->first();
    }

    public function isPresent(): bool
    {
        $attendance = $this->getCurrentAttendance();
        return $attendance && $attendance->status === 'present';
    }

    public function isLate(): bool
    {
        $attendance = $this->getCurrentAttendance();
        return $attendance && $attendance->status === 'late';
    }

    public function isAbsent(): bool
    {
        $attendance = $this->getCurrentAttendance();
        return !$attendance || $attendance->status === 'absent';
    }

    public function getMonthlyPerformance()
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        return [
            'sales' => $this->sales()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount'),
            'leads' => $this->leads()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count(),
            'converted_leads' => $this->leads()
                ->where('status', 'converted')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count()
        ];
    }

    public function getDailySummary()
    {
        $today = now()->startOfDay();
        $tomorrow = now()->addDay()->startOfDay();

        return [
            'sales' => $this->sales()
                ->whereBetween('created_at', [$today, $tomorrow])
                ->sum('amount'),
            'leads' => $this->leads()
                ->whereBetween('created_at', [$today, $tomorrow])
                ->count(),
            'attendance' => $this->getCurrentAttendance()
        ];
    }

    /**
     * Get the role that owns the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get user's meetings
     */
    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    /**
     * Get user's leaves
     */
    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    /**
     * Get user's plans
     */
    public function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    /**
     * Get current month's plan
     */
    public function getCurrentMonthPlan()
    {
        return $this->plans()
            ->where('type', 'monthly')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * Get current quarter's plan
     */
    public function getCurrentQuarterPlan()
    {
        return $this->plans()
            ->where('type', 'quarterly')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * Get current year's plan
     */
    public function getCurrentYearPlan()
    {
        return $this->plans()
            ->where('type', 'yearly')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    /**
     * Get user's pending meetings
     */
    public function getPendingMeetings()
    {
        return $this->meetings()
            ->where('status', 'pending')
            ->where('meeting_date', '>=', now())
            ->orderBy('meeting_date')
            ->get();
    }

    /**
     * Get user's pending meeting reminders
     */
    public function getPendingReminders()
    {
        return $this->meetings()
            ->where('status', 'pending')
            ->where('reminder_date', '<=', now())
            ->where('meeting_date', '>', now())
            ->get();
    }

    /**
     * Get the expenses for the user.
     */
    // public function expenses(): HasMany
    // {
    //     return $this->hasMany(Expense::class);
    // }

    /**
     * Get the location records for the user.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

  
}
