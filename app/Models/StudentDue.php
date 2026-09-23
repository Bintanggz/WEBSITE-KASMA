<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StudentDue extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cash_period_id',
        'user_id',
        'amount',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * Check if this weekly obligation is paid.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if this weekly obligation is unpaid.
     */
    public function isUnpaid(): bool
    {
        return $this->status === 'unpaid';
    }

    /**
     * The weekly period this due belongs to.
     */
    public function cashPeriod(): BelongsTo
    {
        return $this->belongsTo(CashPeriod::class);
    }

    /**
     * The student this obligation is assigned to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * All payment attempts submitted for this due.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * The single approved payment for this due (if paid).
     */
    public function approvedPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->where('status', 'approved');
    }

    /**
     * The currently pending payment under review (if any).
     */
    public function pendingPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->where('status', 'pending');
    }

    /**
     * The most recent rejected payment (if any).
     */
    public function rejectedPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->where('status', 'rejected')->latestOfMany('id');
    }

    /**
     * The latest payment attempt for this due.
     */
    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany('id');
    }
}
