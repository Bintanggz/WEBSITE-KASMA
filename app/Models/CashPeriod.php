<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CashPeriod extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'academic_year',
        'semester',
        'week_number',
        'name',
        'amount',
        'start_date',
        'due_date',
        'is_active',
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
            'week_number' => 'integer',
            'start_date' => 'date',
            'due_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Student dues obligations generated for this weekly period.
     */
    public function studentDues(): HasMany
    {
        return $this->hasMany(StudentDue::class);
    }

    /**
     * All payments associated with this weekly period.
     */
    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, StudentDue::class);
    }
}
