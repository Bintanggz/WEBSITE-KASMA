<?php

namespace Tests\Feature;

use App\Models\CashPeriod;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\StudentDue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreasurerReportTest extends TestCase
{
    use RefreshDatabase;

    private User $bendahara;
    private User $mahasiswa1;
    private User $mahasiswa2;
    private CashPeriod $period1;
    private CashPeriod $period2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bendahara = User::factory()->create([
            'name' => 'Bendahara Kasma',
            'role' => 'bendahara',
            'is_active' => true,
        ]);

        $this->mahasiswa1 = User::factory()->create([
            'name' => 'Ahmad Mahasiswa',
            'nim' => '210001',
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        $this->mahasiswa2 = User::factory()->create([
            'name' => 'Budi Mahasiswa',
            'nim' => '210002',
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        $this->period1 = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan ke-1',
            'amount' => '10000.00',
            'start_date' => now()->subWeeks(1)->startOfWeek(),
            'due_date' => now()->subWeeks(1)->endOfWeek(),
            'is_active' => false,
        ]);

        $this->period2 = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 2,
            'name' => 'Pekan ke-2',
            'amount' => '10000.00',
            'start_date' => now()->startOfWeek(),
            'due_date' => now()->endOfWeek(),
            'is_active' => true,
        ]);

        // Dues for period 1: mahasiswa1 paid, mahasiswa2 unpaid
        $due1Student1 = StudentDue::create([
            'cash_period_id' => $this->period1->id,
            'user_id' => $this->mahasiswa1->id,
            'amount' => '10000.00',
            'status' => 'paid',
        ]);

        $payment1 = Payment::create([
            'student_due_id' => $due1Student1->id,
            'amount' => '10000.00',
            'payment_method' => 'bank_transfer',
            'proof_file_path' => 'proofs/p1.jpg',
            'payment_date' => now()->subDays(5),
            'status' => 'approved',
            'verified_by' => $this->bendahara->id,
            'verified_at' => now()->subDays(4),
        ]);

        FinancialTransaction::create([
            'type' => 'income',
            'amount' => '10000.00',
            'payment_id' => $payment1->id,
            'category' => 'iuran_mingguan',
            'description' => 'Pembayaran Iuran Kas Pekan ke-1 - Ahmad Mahasiswa',
            'transaction_date' => now()->subDays(4),
            'created_by' => $this->bendahara->id,
        ]);

        StudentDue::create([
            'cash_period_id' => $this->period1->id,
            'user_id' => $this->mahasiswa2->id,
            'amount' => '10000.00',
            'status' => 'unpaid',
        ]);

        // Dues for period 2: both unpaid
        StudentDue::create([
            'cash_period_id' => $this->period2->id,
            'user_id' => $this->mahasiswa1->id,
            'amount' => '10000.00',
            'status' => 'unpaid',
        ]);

        StudentDue::create([
            'cash_period_id' => $this->period2->id,
            'user_id' => $this->mahasiswa2->id,
            'amount' => '10000.00',
            'status' => 'unpaid',
        ]);

        // An expense transaction
        FinancialTransaction::create([
            'type' => 'expense',
            'amount' => '3000.00',
            'payment_id' => null,
            'category' => 'perlengkapan',
            'description' => 'Beli Spidol Whiteboard',
            'transaction_date' => now()->subDays(2),
            'created_by' => $this->bendahara->id,
        ]);
    }

    public function test_bendahara_can_access_reports_page(): void
    {
        $response = $this->actingAs($this->bendahara)->get('/bendahara/laporan');

        $response->assertOk();
        $response->assertSee('Laporan &amp; Rekapitulasi Kas Kelas', false);
        $response->assertSee('Total Pemasukan');
        $response->assertSee('Total Pengeluaran');
        $response->assertSee('Saldo Kas Terkini (Ledger)');
        // Balance: 10,000 - 3,000 = 7,000
        $response->assertSee('Rp 7.000');
        // Total income: 10,000
        $response->assertSee('+Rp 10.000');
        // Total expense: 3,000
        $response->assertSee('-Rp 3.000');
    }

    public function test_reports_show_accurate_weekly_collection_summary(): void
    {
        $response = $this->actingAs($this->bendahara)->get('/bendahara/laporan');

        $response->assertOk();
        // Check Pekan 1: 1 lunas (50%), 1 belum
        $response->assertSee('Pekan ke-1');
        $response->assertSee('50%');
        // Check Pekan 2: 0 lunas (0%), 2 belum
        $response->assertSee('Pekan ke-2');
        $response->assertSee('0%');
    }

    public function test_reports_show_accurate_student_compliance(): void
    {
        $response = $this->actingAs($this->bendahara)->get('/bendahara/laporan');

        $response->assertOk();
        $response->assertSee('Ahmad Mahasiswa');
        $response->assertSee('Budi Mahasiswa');
        $response->assertSee('210001');
        $response->assertSee('210002');
    }

    public function test_bendahara_can_filter_transactions_by_type(): void
    {
        // Filter income
        $responseIncome = $this->actingAs($this->bendahara)
            ->get('/bendahara/laporan?type=income');

        $responseIncome->assertOk();
        $responseIncome->assertSee('Pembayaran Iuran Kas Pekan ke-1 - Ahmad Mahasiswa');
        $responseIncome->assertDontSee('Beli Spidol Whiteboard');

        // Filter expense
        $responseExpense = $this->actingAs($this->bendahara)
            ->get('/bendahara/laporan?type=expense');

        $responseExpense->assertOk();
        $responseExpense->assertSee('Beli Spidol Whiteboard');
        $responseExpense->assertDontSee('Pembayaran Iuran Kas Pekan ke-1 - Ahmad Mahasiswa');
    }

    public function test_bendahara_can_filter_transactions_by_date_range(): void
    {
        $startDate = now()->subDays(3)->toDateString();
        $endDate = now()->toDateString();

        $response = $this->actingAs($this->bendahara)
            ->get("/bendahara/laporan?start_date={$startDate}&end_date={$endDate}");

        $response->assertOk();
        // Expense was 2 days ago, so it should be visible
        $response->assertSee('Beli Spidol Whiteboard');
        // Income was 4 days ago, so outside the window
        $response->assertDontSee('Pembayaran Iuran Kas Pekan ke-1 - Ahmad Mahasiswa');
    }

    public function test_bendahara_can_filter_transactions_by_cash_period(): void
    {
        $response = $this->actingAs($this->bendahara)
            ->get("/bendahara/laporan?cash_period_id={$this->period1->id}");

        $response->assertOk();
        $response->assertSee('Pembayaran Iuran Kas Pekan ke-1 - Ahmad Mahasiswa');
        // Expense does not belong to period 1
        $response->assertDontSee('Beli Spidol Whiteboard');
    }

    public function test_student_cannot_access_reports_page(): void
    {
        $response = $this->actingAs($this->mahasiswa1)->get('/bendahara/laporan');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login_from_reports(): void
    {
        $response = $this->get('/bendahara/laporan');

        $response->assertRedirect('/login');
    }

    public function test_bendahara_dashboard_displays_real_total_income_and_expense(): void
    {
        $response = $this->actingAs($this->bendahara)->get('/bendahara/dashboard');

        $response->assertOk();
        // Net balance: 7,000
        $response->assertSee('Rp 7.000');
        // Income: +Rp 10.000
        $response->assertSee('+Rp 10.000');
        // Expense: -Rp 3.000
        $response->assertSee('-Rp 3.000');
        // Navigation link to Laporan
        $response->assertSee(route('bendahara.laporan.index'));
    }

    public function test_mahasiswa_dashboard_displays_dynamic_academic_and_financial_data(): void
    {
        $response = $this->actingAs($this->mahasiswa1)->get('/mahasiswa/dashboard');

        $response->assertOk();
        $response->assertSee('2025/2026');
        $response->assertSee('Semester Genap');
        // Mahasiswa 1 has paid period 1, but period 2 is active and unpaid
        $response->assertSee('Rp 10.000');
        $response->assertSee('Bayar Iuran Pekan Ini Sekarang');
    }
}
