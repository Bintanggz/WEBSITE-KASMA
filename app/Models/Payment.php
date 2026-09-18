<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'student_due_id',
        'amount',
        'payment_method',
        'proof_file_path',
        'payment_date',
        'status',
        'rejection_reason',
        'verified_by',
        'verified_at',
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
            'payment_date' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Check if payment is pending review.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment has been approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if payment has been rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if payment was made in direct cash.
     */
    public function isCash(): bool
    {
        return $this->payment_method === 'cash';
    }

    /**
     * The student due obligation this payment is paying for.
     */
    public function studentDue(): BelongsTo
    {
        return $this->belongsTo(StudentDue::class);
    }

    /**
     * The treasurer who verified or recorded this payment.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * The corresponding financial income ledger transaction (if approved).
     */
    public function financialTransaction(): HasOne
    {
        return $this->hasOne(FinancialTransaction::class);
    }

    /**
     * Get the student who owns this payment through student due.
     */
    public function getStudentAttribute(): ?User
    {
        return $this->studentDue?->user;
    }
}
