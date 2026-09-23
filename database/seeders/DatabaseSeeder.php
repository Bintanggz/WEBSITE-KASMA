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

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with initial KASMA data.
     */
    public function run(): void
    {
        // Prevent duplicate seeding if data already exists
        if (User::where('email', 'bendahara@kasma.edu')->exists()) {
            return;
        }

        // 1. Create Bendahara
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

        // 2. Create Primary Student (Hafizh Al-Fatih)
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

        // 3. Create 31 other students
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

        // 4. Initialize 16 Weekly Cash Periods (Semester Genap 2025/2026)
        $baseDate = Carbon::create(2026, 7, 20); // Semester start: Monday, July 20, 2026
        $periods = collect();

        for ($w = 1; $w <= 16; $w++) {
            $startDate = $baseDate->copy()->addWeeks($w - 1);
            $dueDate = $startDate->copy()->addDays(4); // Friday

            $periods->push(CashPeriod::create([
                'academic_year' => '2025/2026',
                'semester' => 'genap',
                'week_number' => $w,
                'name' => 'Pekan ' . $w,
                'amount' => '10000.00',
                'start_date' => $startDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'is_active' => ($w === 9), // Week 9 is current active week
            ]));
        }

        // 5. Generate Student Dues for all active students across all 16 periods
        foreach ($periods as $period) {
            foreach ($students as $student) {
                StudentDue::create([
                    'cash_period_id' => $period->id,
                    'user_id' => $student->id,
                    'amount' => $period->amount,
                    'status' => 'unpaid',
                ]);
            }
        }

        // 6. Simulate Weeks 1 to 8 payments (Fully paid for all 32 students)
        for ($w = 1; $w <= 8; $w++) {
            $period = $periods[$w - 1];
            $dues = StudentDue::where('cash_period_id', $period->id)->get();

            foreach ($dues as $due) {
                $due->update(['status' => 'paid']);

                $payment = Payment::create([
                    'student_due_id' => $due->id,
                    'amount' => $due->amount,
                    'payment_method' => ($due->user_id % 2 === 0) ? 'bank_transfer' : 'qris',
                    'proof_file_path' => 'proofs/sample_p' . $w . '.jpg',
                    'payment_date' => $period->due_date->subDays(1),
                    'status' => 'approved',
                    'verified_by' => $bendahara->id,
                    'verified_at' => $period->due_date,
                ]);

                // Create ledger income entry
                FinancialTransaction::create([
                    'type' => 'income',
                    'amount' => $payment->amount,
                    'transaction_date' => $period->due_date,
                    'category' => 'iuran_kas',
                    'description' => 'Iuran ' . $period->name . ' - ' . $due->user->name . ' (' . $due->user->nim . ')',
                    'payment_id' => $payment->id,
                    'created_by' => $bendahara->id,
                ]);
            }
        }

        // 7. Simulate Week 9 (Current Week: 28 students paid, 3 pending verification, 1 unpaid)
        $week9 = $periods[8];
        $week9Dues = StudentDue::where('cash_period_id', $week9->id)->get();

        // First 25 students paid
        foreach ($week9Dues->take(25) as $due) {
            $due->update(['status' => 'paid']);

            $payment = Payment::create([
                'student_due_id' => $due->id,
                'amount' => $due->amount,
                'payment_method' => 'bank_transfer',
                'proof_file_path' => 'proofs/w9_paid.jpg',
                'payment_date' => Carbon::now()->subDays(2),
                'status' => 'approved',
                'verified_by' => $bendahara->id,
                'verified_at' => Carbon::now()->subDays(1),
            ]);

            FinancialTransaction::create([
                'type' => 'income',
                'amount' => $payment->amount,
                'transaction_date' => Carbon::now()->subDays(1)->toDateString(),
                'category' => 'iuran_kas',
                'description' => 'Iuran ' . $week9->name . ' - ' . $due->user->name . ' (' . $due->user->nim . ')',
                'payment_id' => $payment->id,
                'created_by' => $bendahara->id,
            ]);
        }

        // Next 3 students pending verification (Farhan, Siti, Dimas)
        foreach ($week9Dues->slice(25, 3) as $due) {
            Payment::create([
                'student_due_id' => $due->id,
                'amount' => $due->amount,
                'payment_method' => 'qris',
                'proof_file_path' => 'proofs/w9_pending.jpg',
                'payment_date' => Carbon::now()->subHours(3),
                'status' => 'pending',
            ]);
        }

        // 8. Add sample class expenses to ledger
        FinancialTransaction::create([
            'type' => 'expense',
            'amount' => '45000.00',
            'transaction_date' => Carbon::now()->subDays(4)->toDateString(),
            'category' => 'perlengkapan',
            'description' => 'Pembelian spidol dan penghapus whiteboard lab',
            'receipt_path' => 'receipts/spidol.jpg',
            'created_by' => $bendahara->id,
        ]);

        FinancialTransaction::create([
            'type' => 'expense',
            'amount' => '150000.00',
            'transaction_date' => Carbon::now()->subDays(11)->toDateString(),
            'category' => 'dana_sosial',
            'description' => 'Dana sosial: menjenguk rekan kelas sakit (Rian)',
            'receipt_path' => 'receipts/buah.jpg',
            'created_by' => $bendahara->id,
        ]);

        FinancialTransaction::create([
            'type' => 'expense',
            'amount' => '280000.00',
            'transaction_date' => Carbon::now()->subDays(18)->toDateString(),
            'category' => 'akademik',
            'description' => 'Fotokopi modul praktikum basis data (32 eksemplar)',
            'receipt_path' => 'receipts/modul.jpg',
            'created_by' => $bendahara->id,
        ]);
    }
}
