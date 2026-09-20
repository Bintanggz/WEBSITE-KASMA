<?php

namespace Tests\Feature;

use App\Models\CashPeriod;
use App\Models\Payment;
use App\Models\StudentDue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MahasiswaPaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $mahasiswa;
    private CashPeriod $period;
    private StudentDue $due;

    protected function setUp(): void
    {
        parent::setUp();

        // Create bendahara
        User::factory()->create([
            'role' => 'bendahara',
            'is_active' => true,
        ]);

        // Create mahasiswa
        $this->mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        // Create active cash period
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

        // Create unpaid due
        $this->due = StudentDue::create([
            'cash_period_id' => $this->period->id,
            'user_id' => $this->mahasiswa->id,
            'amount' => '10000.00',
            'status' => 'unpaid',
        ]);
    }

    private function createFakeJpg(string $filename = 'bukti.jpg'): UploadedFile
    {
        // Minimal valid 1x1 JPEG binary string to pass getimagesize/mime checks without requiring GD extension
        $content = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');
        return UploadedFile::fake()->createWithContent($filename, $content);
    }

    public function test_mahasiswa_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get('/mahasiswa/dashboard');

        $response->assertOk();
        $response->assertSee($this->mahasiswa->name);
        $response->assertSee('Pekan ke-1');
        $response->assertSee('Rp 10.000');
    }

    public function test_mahasiswa_can_submit_payment_proof(): void
    {
        Storage::fake('public');

        $file = $this->createFakeJpg('bukti_transfer.jpg');

        $response = $this->actingAs($this->mahasiswa)->post('/mahasiswa/payments', [
            'student_due_ids' => [$this->due->id],
            'payment_method' => 'bank_transfer',
            'proof_file' => $file,
        ]);

        $response->assertRedirect('/mahasiswa/dashboard');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('payments', [
            'student_due_id' => $this->due->id,
            'amount' => '10000.00',
            'payment_method' => 'bank_transfer',
            'status' => 'pending',
        ]);

        $payment = Payment::where('student_due_id', $this->due->id)->first();
        $this->assertNotNull($payment);
        Storage::disk('public')->assertExists($payment->proof_file_path);
    }

    public function test_mahasiswa_cannot_submit_payment_for_another_student_due(): void
    {
        Storage::fake('public');

        $otherStudent = User::factory()->create([
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        $otherDue = StudentDue::create([
            'cash_period_id' => $this->period->id,
            'user_id' => $otherStudent->id,
            'amount' => '10000.00',
            'status' => 'unpaid',
        ]);

        $file = $this->createFakeJpg('bukti.jpg');

        $response = $this->actingAs($this->mahasiswa)->post('/mahasiswa/payments', [
            'student_due_ids' => [$otherDue->id],
            'payment_method' => 'bank_transfer',
            'proof_file' => $file,
        ]);

        $response->assertSessionHasErrors('student_due_ids');
        $this->assertDatabaseMissing('payments', [
            'student_due_id' => $otherDue->id,
        ]);
    }

    public function test_mahasiswa_cannot_submit_duplicate_pending_payment(): void
    {
        Storage::fake('public');

        Payment::create([
            'student_due_id' => $this->due->id,
            'amount' => '10000.00',
            'payment_method' => 'bank_transfer',
            'proof_file_path' => 'proofs/test.jpg',
            'payment_date' => now(),
            'status' => 'pending',
        ]);

        $file = $this->createFakeJpg('bukti2.jpg');

        $response = $this->actingAs($this->mahasiswa)->post('/mahasiswa/payments', [
            'student_due_ids' => [$this->due->id],
            'payment_method' => 'bank_transfer',
            'proof_file' => $file,
        ]);

        $response->assertSessionHasErrors('student_due_ids');
    }

    public function test_payment_submission_requires_valid_image(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('dokumen.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->mahasiswa)->post('/mahasiswa/payments', [
            'student_due_ids' => [$this->due->id],
            'payment_method' => 'bank_transfer',
            'proof_file' => $file,
        ]);

        $response->assertSessionHasErrors('proof_file');
    }
}
