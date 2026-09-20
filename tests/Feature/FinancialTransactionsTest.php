<?php

namespace Tests\Feature;

use App\Models\CashPeriod;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\StudentDue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinancialTransactionsTest extends TestCase
{
    use RefreshDatabase;

    private User $bendahara;
    private User $mahasiswa;
    private CashPeriod $period;
    private StudentDue $due;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->bendahara = User::factory()->create([
            'role' => 'bendahara',
            'is_active' => true,
        ]);

        $this->mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        $this->period = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan ke-1',
            'amount' => '10000.00',
            'start_date' => now()->startOfWeek(),
            'due_date' => now()->endOfWeek(),
            'is_active' => true,
        ]);

        $this->due = StudentDue::create([
            'cash_period_id' => $this->period->id,
            'user_id' => $this->mahasiswa->id,
            'amount' => '10000.00',
            'status' => 'paid',
        ]);
    }

    private function createFakeJpg(string $filename = 'nota.jpg'): UploadedFile
    {
        $content = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        return UploadedFile::fake()->createWithContent($filename, $content);
    }

    public function test_cash_balance_is_computed_strictly_from_ledger(): void
    {
        // 1. Manual income
        FinancialTransaction::create([
            'type' => 'income',
            'amount' => '50000.00',
            'transaction_date' => now()->toDateString(),
            'category' => 'Donasi',
            'description' => 'Donasi Alumni TI-3A',
            'created_by' => $this->bendahara->id,
        ]);

        // 2. Payment-based income
        $payment = Payment::create([
            'student_due_id' => $this->due->id,
            'amount' => '10000.00',
            'payment_date' => now(),
            'payment_method' => 'bank_transfer',
            'status' => 'approved',
            'verified_by' => $this->bendahara->id,
            'verified_at' => now(),
        ]);

        FinancialTransaction::create([
            'type' => 'income',
            'amount' => '10000.00',
            'transaction_date' => now()->toDateString(),
            'category' => 'Iuran Kas',
            'description' => 'Pembayaran iuran kas mahasiswa',
            'payment_id' => $payment->id,
            'created_by' => $this->bendahara->id,
        ]);

        // 3. Manual expense
        FinancialTransaction::create([
            'type' => 'expense',
            'amount' => '25000.00',
            'transaction_date' => now()->toDateString(),
            'category' => 'Konsumsi',
            'description' => 'Konsumsi Rapat Kelas',
            'created_by' => $this->bendahara->id,
        ]);

        // Balance should be: 50,000 + 10,000 - 25,000 = 35,000
        $balance = FinancialTransaction::currentBalance();
        $this->assertEquals(35000.00, $balance);
    }

    public function test_treasurer_can_view_transactions_with_filters(): void
    {
        FinancialTransaction::create([
            'type' => 'income',
            'amount' => '50000.00',
            'transaction_date' => '2026-09-01',
            'category' => 'Sponsorship',
            'description' => 'Dana Sponsor Dies Natalis',
            'created_by' => $this->bendahara->id,
        ]);

        FinancialTransaction::create([
            'type' => 'expense',
            'amount' => '15000.00',
            'transaction_date' => '2026-09-10',
            'category' => 'Alat Tulis',
            'description' => 'Spidol Papan Tulis',
            'created_by' => $this->bendahara->id,
        ]);

        // Access index page
        $response = $this->actingAs($this->bendahara)->get(route('bendahara.transaksi.index'));
        $response->assertStatus(200);
        $response->assertSee('Buku Transaksi Kas &amp; Pembukuan Kelas', false);
        $response->assertSee('Dana Sponsor Dies Natalis');
        $response->assertSee('Spidol Papan Tulis');

        // Filter by type: income
        $incomeFilter = $this->actingAs($this->bendahara)->get(route('bendahara.transaksi.index', ['type' => 'income']));
        $incomeFilter->assertStatus(200);
        $incomeFilter->assertSee('Dana Sponsor Dies Natalis');
        $incomeFilter->assertDontSee('Spidol Papan Tulis');

        // Filter by type: expense
        $expenseFilter = $this->actingAs($this->bendahara)->get(route('bendahara.transaksi.index', ['type' => 'expense']));
        $expenseFilter->assertStatus(200);
        $expenseFilter->assertSee('Spidol Papan Tulis');
        $expenseFilter->assertDontSee('Dana Sponsor Dies Natalis');

        // Filter by search keyword
        $searchResponse = $this->actingAs($this->bendahara)->get(route('bendahara.transaksi.index', ['search' => 'Spidol']));
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Spidol Papan Tulis');
        $searchResponse->assertDontSee('Dana Sponsor Dies Natalis');
    }

    public function test_treasurer_can_create_manual_income_and_expense_transactions(): void
    {
        // 1. Create manual income
        $incomeResponse = $this->actingAs($this->bendahara)->post(route('bendahara.transaksi.store'), [
            'type' => 'income',
            'amount' => 75000,
            'category' => 'Donasi',
            'description' => 'Sumbangan sukarela dosen pembimbing',
            'transaction_date' => '2026-09-20',
        ]);

        $incomeResponse->assertRedirect(route('bendahara.transaksi.index'));
        $incomeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'income',
            'amount' => '75000.00',
            'category' => 'Donasi',
            'description' => 'Sumbangan sukarela dosen pembimbing',
            'payment_id' => null,
            'created_by' => $this->bendahara->id,
        ]);

        // 2. Create manual expense
        $expenseResponse = $this->actingAs($this->bendahara)->post(route('bendahara.transaksi.store'), [
            'type' => 'expense',
            'amount' => 25000,
            'category' => 'Fotokopi',
            'description' => 'Fotokopi silabus mata kuliah',
            'transaction_date' => '2026-09-20',
        ]);

        $expenseResponse->assertRedirect(route('bendahara.transaksi.index'));
        $expenseResponse->assertSessionHas('success');

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'expense',
            'amount' => '25000.00',
            'category' => 'Fotokopi',
            'description' => 'Fotokopi silabus mata kuliah',
            'payment_id' => null,
            'created_by' => $this->bendahara->id,
        ]);
    }

    public function test_treasurer_can_create_manual_transaction_with_receipt(): void
    {
        $file = $this->createFakeJpg('kuitansi.jpg');

        $response = $this->actingAs($this->bendahara)->post(route('bendahara.transaksi.store'), [
            'type' => 'expense',
            'amount' => 50000,
            'category' => 'Konsumsi',
            'description' => 'Beli snack box rapat',
            'transaction_date' => '2026-09-20',
            'receipt_file' => $file,
        ]);

        $response->assertRedirect(route('bendahara.transaksi.index'));

        $tx = FinancialTransaction::where('description', 'Beli snack box rapat')->first();
        $this->assertNotNull($tx);
        $this->assertNotNull($tx->receipt_path);
        Storage::disk('local')->assertExists($tx->receipt_path);
    }

    public function test_treasurer_can_edit_manual_transaction(): void
    {
        $tx = FinancialTransaction::create([
            'type' => 'expense',
            'amount' => '30000.00',
            'transaction_date' => '2026-09-15',
            'category' => 'Kebersihan',
            'description' => 'Beli sapu kelas',
            'created_by' => $this->bendahara->id,
        ]);

        $response = $this->actingAs($this->bendahara)->put(route('bendahara.transaksi.update', $tx), [
            'type' => 'expense',
            'amount' => 35000,
            'category' => 'Kebersihan',
            'description' => 'Beli sapu dan pel lantai',
            'transaction_date' => '2026-09-16',
        ]);

        $response->assertRedirect(route('bendahara.transaksi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('financial_transactions', [
            'id' => $tx->id,
            'amount' => '35000.00',
            'description' => 'Beli sapu dan pel lantai',
        ]);
        $this->assertEquals('2026-09-16', $tx->fresh()->transaction_date->toDateString());
    }

    public function test_treasurer_can_delete_manual_transaction(): void
    {
        $tx = FinancialTransaction::create([
            'type' => 'expense',
            'amount' => '10000.00',
            'transaction_date' => '2026-09-15',
            'category' => 'Operasional',
            'description' => 'Baterai remote AC',
            'created_by' => $this->bendahara->id,
        ]);

        $response = $this->actingAs($this->bendahara)->delete(route('bendahara.transaksi.destroy', $tx));

        $response->assertRedirect(route('bendahara.transaksi.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('financial_transactions', [
            'id' => $tx->id,
        ]);
    }

    public function test_payment_based_transactions_cannot_be_edited_or_deleted(): void
    {
        $payment = Payment::create([
            'student_due_id' => $this->due->id,
            'amount' => '10000.00',
            'payment_date' => now(),
            'payment_method' => 'bank_transfer',
            'status' => 'approved',
            'verified_by' => $this->bendahara->id,
            'verified_at' => now(),
        ]);

        $tx = FinancialTransaction::create([
            'type' => 'income',
            'amount' => '10000.00',
            'transaction_date' => now()->toDateString(),
            'category' => 'Iuran Kas',
            'description' => 'Pembayaran iuran kas mahasiswa',
            'payment_id' => $payment->id,
            'created_by' => $this->bendahara->id,
        ]);

        $this->assertTrue($tx->isPaymentBased());
        $this->assertFalse($tx->isManual());

        // Attempt edit
        $editResponse = $this->actingAs($this->bendahara)->put(route('bendahara.transaksi.update', $tx), [
            'type' => 'income',
            'amount' => 99999,
            'category' => 'Hacked',
            'description' => 'Percobaan modifikasi iuran',
            'transaction_date' => '2026-09-20',
        ]);

        $editResponse->assertRedirect(route('bendahara.transaksi.index'));
        $editResponse->assertSessionHas('error');

        // Database remains unchanged
        $this->assertDatabaseHas('financial_transactions', [
            'id' => $tx->id,
            'amount' => '10000.00',
            'description' => 'Pembayaran iuran kas mahasiswa',
            'payment_id' => $payment->id,
        ]);

        // Attempt delete
        $deleteResponse = $this->actingAs($this->bendahara)->delete(route('bendahara.transaksi.destroy', $tx));

        $deleteResponse->assertRedirect(route('bendahara.transaksi.index'));
        $deleteResponse->assertSessionHas('error');

        // Database still has record
        $this->assertDatabaseHas('financial_transactions', [
            'id' => $tx->id,
        ]);
    }

    public function test_student_can_view_financial_transparency_page(): void
    {
        FinancialTransaction::create([
            'type' => 'income',
            'amount' => '50000.00',
            'transaction_date' => '2026-09-01',
            'category' => 'Donasi',
            'description' => 'Dana Hibah Kelas',
            'created_by' => $this->bendahara->id,
        ]);

        FinancialTransaction::create([
            'type' => 'expense',
            'amount' => '20000.00',
            'transaction_date' => '2026-09-05',
            'category' => 'Konsumsi',
            'description' => 'Konsumsi Diskusi Kelompok',
            'created_by' => $this->bendahara->id,
        ]);

        $response = $this->actingAs($this->mahasiswa)->get(route('mahasiswa.keuangan.index'));

        $response->assertStatus(200);
        $response->assertSee('Transparansi Keuangan Kas Kelas');
        $response->assertSee('Dana Hibah Kelas');
        $response->assertSee('Konsumsi Diskusi Kelompok');
        $response->assertSee('Rp 30.000'); // Net balance: 50k - 20k = 30k
    }

    public function test_student_cannot_create_edit_or_delete_transactions(): void
    {
        $tx = FinancialTransaction::create([
            'type' => 'expense',
            'amount' => '10000.00',
            'transaction_date' => '2026-09-15',
            'category' => 'Operasional',
            'description' => 'Beli spidol',
            'created_by' => $this->bendahara->id,
        ]);

        // Attempt create
        $createResponse = $this->actingAs($this->mahasiswa)->post(route('bendahara.transaksi.store'), [
            'type' => 'income',
            'amount' => 50000,
            'category' => 'Hack',
            'description' => 'Student hack',
            'transaction_date' => '2026-09-20',
        ]);
        $createResponse->assertStatus(403);

        // Attempt update
        $updateResponse = $this->actingAs($this->mahasiswa)->put(route('bendahara.transaksi.update', $tx), [
            'type' => 'expense',
            'amount' => 5000,
            'category' => 'Hack',
            'description' => 'Student edit',
            'transaction_date' => '2026-09-20',
        ]);
        $updateResponse->assertStatus(403);

        // Attempt delete
        $deleteResponse = $this->actingAs($this->mahasiswa)->delete(route('bendahara.transaksi.destroy', $tx));
        $deleteResponse->assertStatus(403);
    }

    public function test_authenticated_users_can_view_transaction_receipt(): void
    {
        $file = $this->createFakeJpg('kuitansi_resmi.jpg');
        $path = $file->store('receipts', 'local');

        $tx = FinancialTransaction::create([
            'type' => 'expense',
            'amount' => '40000.00',
            'transaction_date' => '2026-09-18',
            'category' => 'Kebersihan',
            'description' => 'Beli kantong sampah dan cairan pel',
            'receipt_path' => $path,
            'created_by' => $this->bendahara->id,
        ]);

        // Guest is redirected
        $guestResponse = $this->get(route('transactions.receipt', $tx));
        $guestResponse->assertRedirect(route('login'));

        // Student can view receipt for transparency
        $studentResponse = $this->actingAs($this->mahasiswa)->get(route('transactions.receipt', $tx));
        $studentResponse->assertStatus(200);

        // Treasurer can view receipt
        $treasurerResponse = $this->actingAs($this->bendahara)->get(route('transactions.receipt', $tx));
        $treasurerResponse->assertStatus(200);
    }
}
