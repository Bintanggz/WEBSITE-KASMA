<?php

namespace Tests\Feature;

use App\Models\CashPeriod;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\StudentDue;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseArchitectureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user creation and role helpers.
     */
    public function test_user_creation_and_role_helpers(): void
    {
        $student = User::create([
            'name' => 'Hafizh Al-Fatih',
            'nim' => '220401',
            'email' => 'hafizh@kasma.edu',
            'password' => 'password123',
            'role' => 'mahasiswa',
            'phone_number' => '08123456789',
            'is_active' => true,
        ]);

        $treasurer = User::create([
            'name' => 'Nadya Putri',
            'nim' => '220402',
            'email' => 'nadya@kasma.edu',
            'password' => 'password123',
            'role' => 'bendahara',
            'is_active' => true,
        ]);

        $this->assertTrue($student->isMahasiswa());
        $this->assertFalse($student->isBendahara());
        $this->assertTrue($treasurer->isBendahara());
        $this->assertFalse($treasurer->isMahasiswa());
        $this->assertEquals('220401', $student->nim);
    }

    /**
     * Test CashPeriod creation and unique constraint on (academic_year, semester, week_number).
     */
    public function test_cash_period_unique_constraint(): void
    {
        CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan 1',
            'amount' => '10000.00',
            'start_date' => '2026-02-01',
            'due_date' => '2026-02-06',
            'is_active' => false,
        ]);

        $this->expectException(QueryException::class);

        // Attempt duplicate definition for week 1 in the same semester
        CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan 1 Duplikat',
            'amount' => '10000.00',
            'start_date' => '2026-02-01',
            'due_date' => '2026-02-06',
            'is_active' => false,
        ]);
    }

    /**
     * Test StudentDue unique constraint on (cash_period_id, user_id).
     */
    public function test_student_due_unique_constraint(): void
    {
        $student = User::create([
            'name' => 'Mahasiswa Test',
            'nim' => '220410',
            'email' => 'mhs@kasma.edu',
            'password' => 'secret',
            'role' => 'mahasiswa',
        ]);

        $period = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan 1',
            'amount' => '10000.00',
            'start_date' => '2026-02-01',
            'due_date' => '2026-02-06',
        ]);

        StudentDue::create([
            'cash_period_id' => $period->id,
            'user_id' => $student->id,
            'amount' => $period->amount,
            'status' => 'unpaid',
        ]);

        $this->expectException(QueryException::class);

        // Attempt duplicate obligation for same student and period
        StudentDue::create([
            'cash_period_id' => $period->id,
            'user_id' => $student->id,
            'amount' => $period->amount,
            'status' => 'unpaid',
        ]);
    }

    /**
     * Test partial unique index: at most one approved payment per student due.
     */
    public function test_one_approved_payment_per_student_due(): void
    {
        $student = User::create([
            'name' => 'Student A',
            'nim' => '220420',
            'email' => 'studenta@kasma.edu',
            'password' => 'secret',
            'role' => 'mahasiswa',
        ]);

        $period = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan 1',
            'amount' => '10000.00',
            'start_date' => '2026-02-01',
            'due_date' => '2026-02-06',
        ]);

        $due = StudentDue::create([
            'cash_period_id' => $period->id,
            'user_id' => $student->id,
            'amount' => '10000.00',
            'status' => 'paid',
        ]);

        // First approved payment
        Payment::create([
            'student_due_id' => $due->id,
            'amount' => '10000.00',
            'payment_method' => 'bank_transfer',
            'proof_file_path' => 'proofs/transfer_1.jpg',
            'status' => 'approved',
        ]);

        $this->expectException(QueryException::class);

        // Attempt second approved payment for the same due
        Payment::create([
            'student_due_id' => $due->id,
            'amount' => '10000.00',
            'payment_method' => 'bank_transfer',
            'proof_file_path' => 'proofs/transfer_2.jpg',
            'status' => 'approved',
        ]);
    }

    /**
     * Test rejected payment allows a new payment to be submitted.
     */
    public function test_rejected_payment_allows_resubmission(): void
    {
        $student = User::create([
            'name' => 'Student B',
            'nim' => '220421',
            'email' => 'studentb@kasma.edu',
            'password' => 'secret',
            'role' => 'mahasiswa',
        ]);

        $period = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan 1',
            'amount' => '10000.00',
            'start_date' => '2026-02-01',
            'due_date' => '2026-02-06',
        ]);

        $due = StudentDue::create([
            'cash_period_id' => $period->id,
            'user_id' => $student->id,
            'amount' => '10000.00',
            'status' => 'unpaid',
        ]);

        // 1. First payment rejected
        $firstPayment = Payment::create([
            'student_due_id' => $due->id,
            'amount' => '10000.00',
            'payment_method' => 'bank_transfer',
            'proof_file_path' => 'proofs/blur.jpg',
            'status' => 'rejected',
            'rejection_reason' => 'Bukti buram',
        ]);

        $this->assertTrue($firstPayment->isRejected());

        // 2. Second payment can be submitted as pending without constraint violation
        $secondPayment = Payment::create([
            'student_due_id' => $due->id,
            'amount' => '10000.00',
            'payment_method' => 'bank_transfer',
            'proof_file_path' => 'proofs/clear.jpg',
            'status' => 'pending',
        ]);

        $this->assertTrue($secondPayment->isPending());
        $this->assertCount(2, $due->payments);
    }

    /**
     * Test direct cash payment recorded by treasurer.
     */
    public function test_direct_cash_payment_by_treasurer(): void
    {
        $student = User::create([
            'name' => 'Student C',
            'nim' => '220422',
            'email' => 'studentc@kasma.edu',
            'password' => 'secret',
            'role' => 'mahasiswa',
        ]);

        $treasurer = User::create([
            'name' => 'Treasurer',
            'nim' => '220400',
            'email' => 'treasurer@kasma.edu',
            'password' => 'secret',
            'role' => 'bendahara',
        ]);

        $period = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan 1',
            'amount' => '10000.00',
            'start_date' => '2026-02-01',
            'due_date' => '2026-02-06',
        ]);

        $due = StudentDue::create([
            'cash_period_id' => $period->id,
            'user_id' => $student->id,
            'amount' => '10000.00',
            'status' => 'paid',
        ]);

        // Cash payment directly approved with proof_file_path = NULL
        $cashPayment = Payment::create([
            'student_due_id' => $due->id,
            'amount' => '10000.00',
            'payment_method' => 'cash',
            'proof_file_path' => null,
            'status' => 'approved',
            'verified_by' => $treasurer->id,
            'verified_at' => now(),
        ]);

        $this->assertTrue($cashPayment->isCash());
        $this->assertTrue($cashPayment->isApproved());
        $this->assertNull($cashPayment->proof_file_path);

        // Immediate financial transaction created
        $transaction = FinancialTransaction::create([
            'type' => 'income',
            'amount' => $cashPayment->amount,
            'transaction_date' => now()->toDateString(),
            'category' => 'iuran_kas',
            'description' => 'Iuran Kas Tunai Pekan 1 - Student C (220422)',
            'payment_id' => $cashPayment->id,
            'created_by' => $treasurer->id,
        ]);

        $this->assertEquals('10000.00', FinancialTransaction::currentBalance());
    }

    /**
     * Test balance calculation and that pending/rejected payments have zero balance impact.
     */
    public function test_balance_calculation_only_counts_approved_transactions(): void
    {
        $treasurer = User::create([
            'name' => 'Bendahara 1',
            'nim' => '220499',
            'email' => 'bendahara@kasma.edu',
            'password' => 'secret',
            'role' => 'bendahara',
        ]);

        $student = User::create([
            'name' => 'Student D',
            'nim' => '220423',
            'email' => 'studentd@kasma.edu',
            'password' => 'secret',
            'role' => 'mahasiswa',
        ]);

        $period = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan 1',
            'amount' => '10000.00',
            'start_date' => '2026-02-01',
            'due_date' => '2026-02-06',
        ]);

        $due = StudentDue::create([
            'cash_period_id' => $period->id,
            'user_id' => $student->id,
            'amount' => '10000.00',
            'status' => 'unpaid',
        ]);

        // Pending payment: should NOT affect balance
        Payment::create([
            'student_due_id' => $due->id,
            'amount' => '10000.00',
            'payment_method' => 'qris',
            'proof_file_path' => 'proofs/qris.jpg',
            'status' => 'pending',
        ]);

        $this->assertEquals('0.00', FinancialTransaction::currentBalance());

        // Record approved income 1: Rp 50.000
        FinancialTransaction::create([
            'type' => 'income',
            'amount' => '50000.00',
            'transaction_date' => now()->toDateString(),
            'category' => 'iuran_kas',
            'description' => 'Setoran Iuran Kas Batch',
            'created_by' => $treasurer->id,
        ]);

        // Record class expense: Rp 15.000
        FinancialTransaction::create([
            'type' => 'expense',
            'amount' => '15000.00',
            'transaction_date' => now()->toDateString(),
            'category' => 'perlengkapan',
            'description' => 'Pembelian spidol whiteboard',
            'created_by' => $treasurer->id,
        ]);

        // Net balance: 50,000 - 15,000 = 35,000
        $this->assertEquals('35000.00', FinancialTransaction::currentBalance());
    }

    /**
     * Test inactive students are ignored when creating dues.
     */
    public function test_inactive_students_can_be_filtered(): void
    {
        User::create([
            'name' => 'Active Student',
            'nim' => '220431',
            'email' => 'active@kasma.edu',
            'password' => 'secret',
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Inactive Student',
            'nim' => '220432',
            'email' => 'inactive@kasma.edu',
            'password' => 'secret',
            'role' => 'mahasiswa',
            'is_active' => false,
        ]);

        $activeStudents = User::where('role', 'mahasiswa')->where('is_active', true)->get();

        $this->assertCount(1, $activeStudents);
        $this->assertEquals('220431', $activeStudents->first()->nim);
    }
}
