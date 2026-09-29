<?php

namespace Database\Seeders;

use App\Models\CashPeriod;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\StudentDue;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with sample/dummy data for development simulation.
     */
    public function run(): void
    {
        $bendahara = User::where('role', 'bendahara')->first();
        if (! $bendahara) {
            $bendahara = User::create([
                'name' => 'Nadya Putri',
                'nim' => '220400',
                'email' => 'bendahara@kasma.edu',
                'password' => Hash::make('password'),
                'role' => 'bendahara',
                'phone_number' => '082198765432',
                'is_active' => true,
                'activated_at' => now(),
            ]);
        }

        // Primary Student (Hafizh Al-Fatih)
        $primaryStudent = User::create([
            'name' => 'Hafizh Al-Fatih',
            'nim' => '220401',
            'email' => 'hafizh@kasma.edu',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'phone_number' => '081234567890',
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $students = collect([$primaryStudent]);
        for ($i = 2; $i <= 32; $i++) {
            $nim = sprintf('2204%02d', $i);
            $name = match($i) {
                12 => 'Farhan Pratama',
                25 => 'Siti Nurhaliza',
                default => 'Mahasiswa ' . $i,
            };
            $email = match($i) {
                12 => 'farhan@kasma.edu',
                25 => 'siti@kasma.edu',
                default => 'mhs' . $i . '@kasma.edu',
            };

            $students->push(User::create([
                'name' => $name,
                'nim' => $nim,
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'phone_number' => '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'is_active' => true,
                'activated_at' => now(),
            ]));
        }

        $periods = CashPeriod::all();
        if ($periods->isEmpty()) {
            $baseDate = Carbon::create(2026, 7, 20);
            for ($w = 1; $w <= 16; $w++) {
                $startDate = $baseDate->copy()->addWeeks($w - 1);
                $dueDate = $startDate->copy()->addDays(4);

                $periods->push(CashPeriod::create([
                    'academic_year' => '2025/2026',
                    'semester' => 'genap',
                    'week_number' => $w,
                    'name' => 'Pekan ' . $w,
                    'amount' => '10000.00',
                    'start_date' => $startDate->toDateString(),
                    'due_date' => $dueDate->toDateString(),
                    'is_active' => ($w === 9),
                ]));
            }
        }

        foreach ($periods as $period) {
            foreach ($students as $student) {
                StudentDue::firstOrCreate([
                    'cash_period_id' => $period->id,
                    'user_id' => $student->id,
                ], [
                    'amount' => $period->amount,
                    'status' => 'unpaid',
                ]);
            }
        }
    }
}
