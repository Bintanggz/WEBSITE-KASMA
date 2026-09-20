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

class PaymentVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $bendahara;
    private User $mahasiswa1;
    private User $mahasiswa2;
    private CashPeriod $period;
    private StudentDue $due1;
    private StudentDue $due2;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->bendahara = User::factory()->create([
            'role' => 'bendahara',
            'is_active' => true,
        ]);

        $this->mahasiswa1 = User::factory()->create([
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        $this->mahasiswa2 = User::factory()->create([
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

        $this->due1 = StudentDue::create([
            'cash_period_id' => $this->period->id,
            'user_id' => $this->mahasiswa1->id,
            'amount' => '10000.00',
            'status' => 'unpaid',
        ]);

        $this->due2 = StudentDue::create([
            'cash_period_id' => $this->period->id,
            'user_id' => $this->mahasiswa2->id,
            'amount' => '10000.00',
            'status' => 'unpaid',
        ]);
    }

    private function createFakeJpg(string $filename = 'bukti.jpg'): UploadedFile
    {
        $content = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        return UploadedFile::fake()->createWithContent($filename, $content);
    }

    public function test_mahasiswa_can_view_payment_history(): void
    {
        $response = $this->actingAs($this->mahasiswa1)->get(route('mahasiswa.riwayat.index'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat Pembayaran Kas Saya');
    }

    public function test_mahasiswa_can_submit_payment_with_bank_transfer_and_qris(): void
    {
        $file = $this->createFakeJpg('transfer.jpg');

        $response = $this->actingAs($this->mahasiswa1)->post(route('mahasiswa.payments.store'), [
            'student_due_ids' => [$this->due1->id],
            'payment_method' => 'bank_transfer',
            'proof_file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'student_due_id' => $this->due1->id,
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
            'amount' => '10000.00',
        ]);

        $payment = Payment::where('student_due_id', $this->due1->id)->first();
        $this->assertNotNull($payment->proof_file_path);
        Storage::disk('local')->assertExists($payment->proof_file_path);
    }

    public function test_mahasiswa_cannot_submit_payment_for_another_student_due(): void
    {
        $file = $this->createFakeJpg();

        // Mahasiswa 1 tries to pay for Mahasiswa 2's due
        $response = $this->actingAs($this->mahasiswa1)->post(route('mahasiswa.payments.store'), [
            'student_due_ids' => [$this->due2->id],
            'payment_method' => 'bank_transfer',
            'proof_file' => $file,
        ]);

        $response->assertSessionHasErrors('student_due_ids');
        $this->assertDatabaseMissing('payments', [
            'student_due_id' => $this->due2->id,
        ]);
    }

    public function test_mahasiswa_cannot_submit_duplicate_payment_while_pending(): void
    {
        $file1 = $this->createFakeJpg('bukti1.jpg');
        $this->actingAs($this->mahasiswa1)->post(route('mahasiswa.payments.store'), [
            'student_due_ids' => [$this->due1->id],
            'payment_method' => 'bank_transfer',
            'proof_file' => $file1,
        ]);

        // Try submitting again for same due
        $file2 = $this->createFakeJpg('bukti2.jpg');
        $response = $this->actingAs($this->mahasiswa1)->post(route('mahasiswa.payments.store'), [
            'student_due_ids' => [$this->due1->id],
            'payment_method' => 'qris',
            'proof_file' => $file2,
        ]);

        $response->assertSessionHasErrors('student_due_ids');
        $this->assertEquals(1, Payment::where('student_due_id', $this->due1->id)->count());
    }

    public function test_mahasiswa_cannot_submit_payment_for_already_paid_due(): void
    {
        $this->due1->update(['status' => 'paid']);

        $file = $this->createFakeJpg();
        $response = $this->actingAs($this->mahasiswa1)->post(route('mahasiswa.payments.store'), [
            'student_due_ids' => [$this->due1->id],
            'payment_method' => 'bank_transfer',
            'proof_file' => $file,
        ]);

        $response->assertSessionHasErrors('student_due_ids');
    }

    public function test_rejected_payment_allows_student_to_resubmit(): void
    {
        // 1. Initial submission
        $file1 = $this->createFakeJpg('bukti1.jpg');
        $this->actingAs($this->mahasiswa1)->post(route('mahasiswa.payments.store'), [
            'student_due_ids' => [$this->due1->id],
            'payment_method' => 'bank_transfer',
            'proof_file' => $file1,
        ]);

        $payment1 = Payment::where('student_due_id', $this->due1->id)->first();

        // 2. Treasurer rejects payment
        $this->actingAs($this->bendahara)->patch(route('bendahara.payments.reject', $payment1), [
            'rejection_reason' => 'Bukti buram dan tanggal tidak terbaca',
        ]);

        $this->assertEquals('rejected', $payment1->fresh()->status);
        $this->assertEquals('unpaid', $this->due1->fresh()->status);

        // 3. Student resubmits new valid payment
        $file2 = $this->createFakeJpg('bukti_jelas.jpg');
        $response = $this->actingAs($this->mahasiswa1)->post(route('mahasiswa.payments.store'), [
            'student_due_ids' => [$this->due1->id],
            'payment_method' => 'qris',
            'proof_file' => $file2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify there are now 2 payments (1 rejected, 1 pending)
        $this->assertEquals(2, Payment::where('student_due_id', $this->due1->id)->count());
        $this->assertDatabaseHas('payments', [
            'student_due_id' => $this->due1->id,
            'payment_method' => 'qris',
            'status' => 'pending',
        ]);
    }

    public function test_payment_proof_file_access_is_protected(): void
    {
        $file = $this->createFakeJpg('private_proof.jpg');
        $this->actingAs($this->mahasiswa1)->post(route('mahasiswa.payments.store'), [
            'student_due_ids' => [$this->due1->id],
            'payment_method' => 'bank_transfer',
            'proof_file' => $file,
        ]);

        $payment = Payment::where('student_due_id', $this->due1->id)->first();

        // 1. Mahasiswa 1 (owner) CAN access proof
        $resOwner = $this->actingAs($this->mahasiswa1)->get(route('payments.proof', $payment));
        $resOwner->assertStatus(200);

        // 2. Mahasiswa 2 (other student) CANNOT access proof -> 403 Forbidden
        $resOther = $this->actingAs($this->mahasiswa2)->get(route('payments.proof', $payment));
        $resOther->assertStatus(403);

        // 3. Guest CANNOT access proof -> redirect to login
        $this->post(route('logout'));
        $resGuest = $this->get(route('payments.proof', $payment));
        $resGuest->assertRedirect(route('login'));

        // 4. Bendahara CAN access any proof
        $resTreasurer = $this->actingAs($this->bendahara)->get(route('payments.proof', $payment));
        $resTreasurer->assertStatus(200);
    }

    public function test_bendahara_can_view_verification_index(): void
    {
        $response = $this->actingAs($this->bendahara)->get(route('bendahara.verifikasi.index'));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi Pembayaran Kas Mahasiswa');
    }

    public function test_bendahara_approval_rule_creates_single_income_transaction_atomically(): void
    {
        $payment = Payment::create([
            'student_due_id' => $this->due1->id,
            'amount' => '10000.00',
            'payment_method' => 'bank_transfer',
            'proof_file_path' => 'proofs/dummy.jpg',
            'payment_date' => now(),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->bendahara)->patch(route('bendahara.payments.approve', $payment));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // 1. Payment approved
        $this->assertEquals('approved', $payment->fresh()->status);
        $this->assertEquals($this->bendahara->id, $payment->fresh()->verified_by);
        $this->assertNotNull($payment->fresh()->verified_at);

        // 2. Student due marked as paid
        $this->assertEquals('paid', $this->due1->fresh()->status);

        // 3. Exactly one financial transaction created
        $this->assertDatabaseCount('financial_transactions', 1);
        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'income',
            'payment_id' => $payment->id,
            'amount' => '10000.00',
            'created_by' => $this->bendahara->id,
        ]);
    }

    public function test_bendahara_rejection_requires_reason_and_preserves_unpaid_due(): void
    {
        $payment = Payment::create([
            'student_due_id' => $this->due1->id,
            'amount' => '10000.00',
            'payment_method' => 'bank_transfer',
            'proof_file_path' => 'proofs/dummy.jpg',
            'payment_date' => now(),
            'status' => 'pending',
        ]);

        // Rejection without reason fails validation
        $responseFail = $this->actingAs($this->bendahara)->patch(route('bendahara.payments.reject', $payment), [
            'rejection_reason' => '',
        ]);
        $responseFail->assertSessionHasErrors('rejection_reason');
        $this->assertEquals('pending', $payment->fresh()->status);

        // Rejection with reason succeeds
        $responseSuccess = $this->actingAs($this->bendahara)->patch(route('bendahara.payments.reject', $payment), [
            'rejection_reason' => 'Nominal transfer tidak sesuai dengan tagihan.',
        ]);

        $responseSuccess->assertRedirect();
        $responseSuccess->assertSessionHas('success');

        $this->assertEquals('rejected', $payment->fresh()->status);
        $this->assertEquals('Nominal transfer tidak sesuai dengan tagihan.', $payment->fresh()->rejection_reason);
        $this->assertEquals('unpaid', $this->due1->fresh()->status);
        $this->assertDatabaseCount('financial_transactions', 0);
    }

    public function test_bendahara_can_record_direct_cash_payment(): void
    {
        $response = $this->actingAs($this->bendahara)->post(route('bendahara.cash-payments.store'), [
            'student_due_id' => $this->due1->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('paid', $this->due1->fresh()->status);

        $payment = Payment::where('student_due_id', $this->due1->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('cash', $payment->payment_method);
        $this->assertEquals('approved', $payment->status);
        $this->assertNull($payment->proof_file_path);

        $this->assertDatabaseHas('financial_transactions', [
            'type' => 'income',
            'payment_id' => $payment->id,
            'amount' => '10000.00',
        ]);
    }

    public function test_mahasiswa_cannot_verify_payments_or_record_cash(): void
    {
        $payment = Payment::create([
            'student_due_id' => $this->due1->id,
            'amount' => '10000.00',
            'payment_method' => 'bank_transfer',
            'proof_file_path' => 'proofs/dummy.jpg',
            'payment_date' => now(),
            'status' => 'pending',
        ]);

        $this->actingAs($this->mahasiswa1)
            ->patch(route('bendahara.payments.approve', $payment))
            ->assertStatus(403);

        $this->actingAs($this->mahasiswa1)
            ->patch(route('bendahara.payments.reject', $payment), ['rejection_reason' => 'test'])
            ->assertStatus(403);

        $this->actingAs($this->mahasiswa1)
            ->post(route('bendahara.cash-payments.store'), ['student_due_id' => $this->due1->id])
            ->assertStatus(403);

        $this->actingAs($this->mahasiswa1)
            ->get(route('bendahara.verifikasi.index'))
            ->assertStatus(403);
    }
}
