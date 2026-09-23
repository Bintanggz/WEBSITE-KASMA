<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nim',
        'email',
        'password',
        'role',
        'phone_number',
        'is_active',
        'activation_token',
        'activation_expires_at',
        'activated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
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
            'is_active' => 'boolean',
            'activation_expires_at' => 'datetime',
            'activated_at' => 'datetime',
        ];
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (! empty($user->password) && $user->activated_at === null) {
                $user->activated_at = now();
            }
        });
    }

    /**
     * Determine if the user has activated their account.
     */
    public function isActivated(): bool
    {
        return $this->activated_at !== null || ! empty($this->password);
    }

    /**
     * Determine if the user has an activation pending.
     */
    public function hasPendingActivation(): bool
    {
        return empty($this->password) && $this->activated_at === null && $this->activation_token !== null;
    }

    /**
     * Determine if the user's activation link has expired.
     */
    public function isActivationExpired(): bool
    {
        return $this->hasPendingActivation() 
            && $this->activation_expires_at !== null 
            && $this->activation_expires_at->isPast();
    }

    /**
     * Determine if the user is a student.
     */
    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    /**
     * Determine if the user is a class treasurer.
     */
    public function isBendahara(): bool
    {
        return $this->role === 'bendahara';
    }

    /**
     * Weekly dues obligations assigned to the student.
     */
    public function studentDues(): HasMany
    {
        return $this->hasMany(StudentDue::class);
    }

    /**
     * Payments submitted by the student (through their assigned dues).
     */
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, StudentDue::class);
    }

    /**
     * Payments verified by this treasurer.
     */
    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    /**
     * Financial ledger transactions created by this treasurer.
     */
    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'created_by');
    }
}
