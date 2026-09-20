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

class BendaharaManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $bendahara;
    private User $mahasiswa;
    private CashPeriod $period;
    private StudentDue $due;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bendahara = User::factory()->create([
            'name' => 'Nadya Bendahara',
            'role' => 'bendahara',
            'is_active' => true,
        ]);

        $this->mahasiswa = User::factory()->create([
            'name' => 'Budi Mahasiswa',
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
            'status' => 'unpaid',
        ]);
    }

    private function createFakeJpg(string $filename = 'nota.jpg'): UploadedFile
    {
        $content = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        return UploadedFile::fake()->createWithContent($filename, $content);
    }

    public function test_bendahara_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->bendahara)->get('/bendahara/dashboard');

        $response->assertOk();
        $response->assertSee('Panel Pengelolaan Kas');
        $response->assertSee('Pekan ke-1 Aktif');
    }

    public function test_bendahara_can_approve_pending_payment(): void
    {
        $payment = Payment::create([
            'student_due_id' => $this->due->id,
            'amount' => '10000.00',
            'payment_method' => 'bank_transfer',
            'proof_file_path' => 'proofs/sample.jpg',
            'payment_date' => now(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->bendahara)->patch("/bendahara/payments/{$payment->id}/approve");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check payment is approved
        $payment->refresh();
        $this->assertEquals('approved', $payment->status);
        $this->assertEquals($this->bendahara->id, $payment->verified_by);
        $this->assertNotNull($payment->verified_at);

        // Check due is paid
        $this->due->refresh();
        $this->assertEquals('paid', $this->due->status);

        // Check ledger transaction created
        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'income',
            'amount' => '10000.00',
            'payment_id' => $payment->id,
            'created_by' => $this->bendahara->id,
        ]);

        // Check balance
        $this->assertEquals('10000.00', FinancialTransaction::currentBalance());
    }

    public function test_bendahara_can_reject_pending_payment(): void
    {
        $payment = Payment::create([
            'student_due_id' => $this->due->id,
            'amount' => '10000.00',
            'payment_method' => 'qris',
            'proof_file_path' => 'proofs/sample.jpg',
            'payment_date' => now(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->bendahara)->patch("/bendahara/payments/{$payment->id}/reject", [
            'rejection_reason' => 'Nominal transfer tidak sesuai dengan tagihan.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals('rejected', $payment->status);
        $this->assertEquals('Nominal transfer tidak sesuai dengan tagihan.', $payment->rejection_reason);

        // Due remains unpaid
        $this->due->refresh();
        $this->assertEquals('unpaid', $this->due->status);

        // No income created
        $this->assertDatabaseMissing('financial_transactions', [
            'payment_id' => $payment->id,
        ]);
    }

    public function test_bendahara_can_record_direct_cash_payment(): void
    {
        $response = $this->actingAs($this->bendahara)->post('/bendahara/cash-payments', [
            'student_due_id' => $this->due->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check due marked paid
        $this->due->refresh();
        $this->assertEquals('paid', $this->due->status);

        // Check approved cash payment created
        $this->assertDatabaseHas('payments', [
            'student_due_id' => $this->due->id,
            'amount' => '10000.00',
            'payment_method' => 'cash',
            'status' => 'approved',
            'verified_by' => $this->bendahara->id,
        ]);

        // Check ledger transaction created
        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'income',
            'amount' => '10000.00',
            'created_by' => $this->bendahara->id,
        ]);
    }

    public function test_bendahara_can_record_class_expense(): void
    {
        Storage::fake('public');

        $file = $this->createFakeJpg('nota.jpg');

        $response = $this->actingAs($this->bendahara)->post('/bendahara/transactions/expense', [
            'amount' => 45000,
            'category' => 'Perlengkapan',
            'description' => 'Beli spidol whiteboard lab',
            'transaction_date' => now()->toDateString(),
            'receipt_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'expense',
            'amount' => '45000.00',
            'category' => 'Perlengkapan',
            'description' => 'Beli spidol whiteboard lab',
            'created_by' => $this->bendahara->id,
        ]);
    }

    public function test_mahasiswa_cannot_access_bendahara_actions(): void
    {
        $payment = Payment::create([
            'student_due_id' => $this->due->id,
            'amount' => '10000.00',
            'payment_method' => 'qris',
            'proof_file_path' => 'proofs/sample.jpg',
            'payment_date' => now(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->mahasiswa)->patch("/bendahara/payments/{$payment->id}/approve");
        $response->assertForbidden();

        $response = $this->actingAs($this->mahasiswa)->post('/bendahara/cash-payments', [
            'student_due_id' => $this->due->id,
        ]);
        $response->assertForbidden();

        $response = $this->actingAs($this->mahasiswa)->post('/bendahara/transactions/expense', [
            'amount' => 20000,
            'category' => 'Lainnya',
            'description' => 'Unauthorized expense',
            'transaction_date' => now()->toDateString(),
        ]);
        $response->assertForbidden();
    }
}
