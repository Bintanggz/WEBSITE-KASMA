<?php

namespace Database\Seeders;

use App\Models\CashPeriod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with initial clean KASMA data.
     * Only sets up the Bendahara account and the semester cash periods.
     */
    public function run(): void
    {
        // 1. Create Default Bendahara Account if not exists
        if (! User::where('role', 'bendahara')->exists()) {
            User::create([
                'name' => 'Bendahara TI26A3',
                'nim' => '220400',
                'email' => 'bendahara@kasma.edu',
                'password' => Hash::make('password'),
                'role' => 'bendahara',
                'phone_number' => '082198765432',
                'is_active' => true,
                'activated_at' => now(),
            ]);
        }

        // 2. Initialize 16 Weekly Cash Periods if not exist (Semester Genap 2025/2026)
        if (CashPeriod::count() === 0) {
            $baseDate = Carbon::create(2026, 7, 20);
            for ($w = 1; $w <= 16; $w++) {
                $startDate = $baseDate->copy()->addWeeks($w - 1);
                $dueDate = $startDate->copy()->addDays(4);

                CashPeriod::create([
                    'academic_year' => '2025/2026',
                    'semester' => 'genap',
                    'week_number' => $w,
                    'name' => 'Pekan ' . $w,
                    'amount' => '5000.00',
                    'start_date' => $startDate->toDateString(),
                    'due_date' => $dueDate->toDateString(),
                    'is_active' => ($w === 1), // Week 1 is default active
                ]);
            }
        }
    }
}
