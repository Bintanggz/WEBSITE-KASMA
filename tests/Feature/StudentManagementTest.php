<?php

namespace Tests\Feature;

use App\Models\CashPeriod;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\StudentDue;
use App\Models\User;
use App\Services\CashPeriodService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $treasurer;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->treasurer = User::create([
            'name' => 'Nadya Bendahara',
            'nim' => '220400',
            'email' => 'bendahara@kasma.edu',
            'password' => Hash::make('password123'),
            'role' => 'bendahara',
            'phone_number' => '082198765432',
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $this->student = User::create([
            'name' => 'Bintang Mahasiswa',
            'nim' => '220401',
            'email' => 'bintang@kasma.edu',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'phone_number' => '081234567890',
            'is_active' => true,
            'activated_at' => now(),
        ]);
    }

    public function test_bendahara_can_view_student_management_page(): void
    {
        $response = $this->actingAs($this->treasurer)->get(route('bendahara.mahasiswa.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Data Mahasiswa');
        $response->assertSee('Bintang Mahasiswa');
        $response->assertSee('220401');
    }

    public function test_mahasiswa_cannot_access_student_management_page(): void
    {
        $response = $this->actingAs($this->student)->get(route('bendahara.mahasiswa.index'));

        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_to_login_from_student_management(): void
    {
        $response = $this->get(route('bendahara.mahasiswa.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_bendahara_can_add_student_with_hashed_activation_token_and_expiry(): void
    {
        $response = $this->actingAs($this->treasurer)->post(route('bendahara.mahasiswa.store'), [
            'name' => 'Ahmad Fadhil',
            'nim' => '220402',
            'email' => 'fadhil@kasma.edu',
            'phone_number' => '085211223344',
        ]);

        $response->assertRedirect(route('bendahara.mahasiswa.index'));
        $response->assertSessionHas('success');
        $response->assertSessionHas('new_student_activation');

        $this->assertDatabaseHas('users', [
            'name' => 'Ahmad Fadhil',
            'nim' => '220402',
            'email' => 'fadhil@kasma.edu',
            'role' => 'mahasiswa',
            'password' => null,
            'is_active' => true,
            'activated_at' => null,
        ]);

        $newStudent = User::where('nim', '220402')->first();
        $this->assertNotNull($newStudent->activation_token);
        $this->assertEquals(64, strlen($newStudent->activation_token)); // SHA-256 hash length
        $this->assertNotNull($newStudent->activation_expires_at);
        $this->assertTrue($newStudent->activation_expires_at->isFuture());
        $this->assertFalse($newStudent->isActivated());
        $this->assertTrue($newStudent->hasPendingActivation());
    }

    public function test_adding_student_automatically_assigns_dues_for_existing_cash_periods(): void
    {
        $period1 = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan ke-1',
            'amount' => 10000,
            'start_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'is_active' => true,
        ]);

        $period2 = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 2,
            'name' => 'Pekan ke-2',
            'amount' => 10000,
            'start_date' => now()->addDays(7)->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'is_active' => false,
        ]);

        $this->actingAs($this->treasurer)->post(route('bendahara.mahasiswa.store'), [
            'name' => 'Dimas Arya',
            'nim' => '220403',
            'email' => 'dimas@kasma.edu',
            'phone_number' => '087812345678',
        ]);

        $newStudent = User::where('nim', '220403')->first();
        $this->assertNotNull($newStudent);

        $this->assertDatabaseHas('student_dues', [
            'user_id' => $newStudent->id,
            'cash_period_id' => $period1->id,
            'amount' => 10000,
            'status' => 'unpaid',
        ]);

        $this->assertDatabaseHas('student_dues', [
            'user_id' => $newStudent->id,
            'cash_period_id' => $period2->id,
            'amount' => 10000,
            'status' => 'unpaid',
        ]);
    }

    public function test_duplicate_nim_is_prevented(): void
    {
        $response = $this->actingAs($this->treasurer)->post(route('bendahara.mahasiswa.store'), [
            'name' => 'Impostor NIM',
            'nim' => '220401', // existing NIM
            'email' => 'different@kasma.edu',
            'phone_number' => '081199887766',
        ]);

        $response->assertSessionHasErrors(['nim']);
        $this->assertDatabaseMissing('users', ['email' => 'different@kasma.edu']);
    }

    public function test_duplicate_email_is_prevented(): void
    {
        $response = $this->actingAs($this->treasurer)->post(route('bendahara.mahasiswa.store'), [
            'name' => 'Impostor Email',
            'nim' => '220499',
            'email' => 'bintang@kasma.edu', // existing Email
            'phone_number' => '081199887766',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('users', ['nim' => '220499']);
    }

    public function test_student_can_view_activation_page_with_valid_token(): void
    {
        $plainToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);

        $unactivated = User::create([
            'name' => 'Citra Dewi',
            'nim' => '220404',
            'email' => 'citra@kasma.edu',
            'role' => 'mahasiswa',
            'phone_number' => '081399887766',
            'password' => null,
            'is_active' => true,
            'activation_token' => $hashedToken,
            'activation_expires_at' => now()->addHours(72),
            'activated_at' => null,
        ]);

        $response = $this->get(route('activation.show', [
            'token' => $plainToken,
            'email' => $unactivated->email,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Aktivasi Akun Mahasiswa');
        $response->assertSee('Citra Dewi');
        $response->assertSee('220404');
    }

    public function test_student_can_activate_account_and_set_password(): void
    {
        $plainToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);

        $unactivated = User::create([
            'name' => 'Citra Dewi',
            'nim' => '220404',
            'email' => 'citra@kasma.edu',
            'role' => 'mahasiswa',
            'phone_number' => '081399887766',
            'password' => null,
            'is_active' => true,
            'activation_token' => $hashedToken,
            'activation_expires_at' => now()->addHours(72),
            'activated_at' => null,
        ]);

        $response = $this->post(route('activation.store', ['token' => $plainToken]), [
            'email' => 'citra@kasma.edu',
            'password' => 'citrapassword123',
            'password_confirmation' => 'citrapassword123',
        ]);

        $response->assertRedirect(route('mahasiswa.dashboard'));
        $this->assertAuthenticatedAs($unactivated);

        $unactivated->refresh();
        $this->assertTrue($unactivated->isActivated());
        $this->assertNull($unactivated->activation_token);
        $this->assertNull($unactivated->activation_expires_at);
        $this->assertTrue(Hash::check('citrapassword123', $unactivated->password));
    }

    public function test_student_can_login_using_new_password_after_activation(): void
    {
        $plainToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);

        $unactivated = User::create([
            'name' => 'Eko Prasetyo',
            'nim' => '220405',
            'email' => 'eko@kasma.edu',
            'role' => 'mahasiswa',
            'phone_number' => '081277889900',
            'password' => null,
            'is_active' => true,
            'activation_token' => $hashedToken,
            'activation_expires_at' => now()->addHours(72),
            'activated_at' => null,
        ]);

        // Activate
        $this->post(route('activation.store', ['token' => $plainToken]), [
            'email' => 'eko@kasma.edu',
            'password' => 'ekorocks2026',
            'password_confirmation' => 'ekorocks2026',
        ]);

        // Logout
        $this->post(route('logout'));
        $this->assertGuest();

        // Login using email
        $response = $this->post(route('login'), [
            'login' => 'eko@kasma.edu',
            'password' => 'ekorocks2026',
        ]);
        $response->assertRedirect('/mahasiswa/dashboard');
        $this->assertAuthenticatedAs($unactivated);

        // Logout
        $this->post(route('logout'));

        // Login using NIM
        $response = $this->post(route('login'), [
            'login' => '220405',
            'password' => 'ekorocks2026',
        ]);
        $response->assertRedirect('/mahasiswa/dashboard');
        $this->assertAuthenticatedAs($unactivated);
    }

    public function test_expired_activation_token_is_rejected(): void
    {
        $plainToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);

        $expiredStudent = User::create([
            'name' => 'Fajar Expired',
            'nim' => '220406',
            'email' => 'fajar@kasma.edu',
            'role' => 'mahasiswa',
            'password' => null,
            'is_active' => true,
            'activation_token' => $hashedToken,
            'activation_expires_at' => now()->subHour(), // Expired
            'activated_at' => null,
        ]);

        // View shows expired message
        $response = $this->get(route('activation.show', [
            'token' => $plainToken,
            'email' => $expiredStudent->email,
        ]));
        $response->assertStatus(200);
        $response->assertSee('Tautan Aktivasi Kedaluwarsa');

        // Activation attempt fails
        $postResponse = $this->post(route('activation.store', ['token' => $plainToken]), [
            'email' => 'fajar@kasma.edu',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $postResponse->assertSessionHasErrors(['password']);
        $expiredStudent->refresh();
        $this->assertFalse($expiredStudent->isActivated());
    }

    public function test_single_use_token_cannot_be_reused(): void
    {
        $plainToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);

        $student = User::create([
            'name' => 'Gita Single',
            'nim' => '220407',
            'email' => 'gita@kasma.edu',
            'role' => 'mahasiswa',
            'password' => null,
            'is_active' => true,
            'activation_token' => $hashedToken,
            'activation_expires_at' => now()->addHours(72),
            'activated_at' => null,
        ]);

        // First activation succeeds
        $this->post(route('activation.store', ['token' => $plainToken]), [
            'email' => 'gita@kasma.edu',
            'password' => 'firstpassword123',
            'password_confirmation' => 'firstpassword123',
        ]);

        $this->post(route('logout'));

        // Second activation attempt with same token
        $secondAttempt = $this->post(route('activation.store', ['token' => $plainToken]), [
            'email' => 'gita@kasma.edu',
            'password' => 'secondpassword123',
            'password_confirmation' => 'secondpassword123',
        ]);

        $secondAttempt->assertRedirect(route('login'));
        $student->refresh();
        // Password must still be first password, not overwritten
        $this->assertTrue(Hash::check('firstpassword123', $student->password));
    }

    public function test_invalid_token_is_rejected(): void
    {
        $plainToken = Str::random(64);
        $wrongToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);

        User::create([
            'name' => 'Hani Invalid',
            'nim' => '220408',
            'email' => 'hani@kasma.edu',
            'role' => 'mahasiswa',
            'password' => null,
            'is_active' => true,
            'activation_token' => $hashedToken,
            'activation_expires_at' => now()->addHours(72),
            'activated_at' => null,
        ]);

        $response = $this->get(route('activation.show', [
            'token' => $wrongToken,
            'email' => 'hani@kasma.edu',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tautan Aktivasi Tidak Valid');
    }

    public function test_bendahara_can_resend_activation_token(): void
    {
        $oldHashedToken = hash('sha256', 'old_token_value');

        $unactivated = User::create([
            'name' => 'Indra Pending',
            'nim' => '220409',
            'email' => 'indra@kasma.edu',
            'role' => 'mahasiswa',
            'password' => null,
            'is_active' => true,
            'activation_token' => $oldHashedToken,
            'activation_expires_at' => now()->subDay(),
            'activated_at' => null,
        ]);

        $response = $this->actingAs($this->treasurer)->post(route('bendahara.mahasiswa.resend-activation', $unactivated));

        $response->assertRedirect(route('bendahara.mahasiswa.index'));
        $response->assertSessionHas('new_student_activation');

        $unactivated->refresh();
        $this->assertNotEquals($oldHashedToken, $unactivated->activation_token);
        $this->assertTrue($unactivated->activation_expires_at->isFuture());
    }

    public function test_bendahara_can_deactivate_and_reactivate_student(): void
    {
        $this->assertTrue($this->student->is_active);

        // Deactivate
        $response = $this->actingAs($this->treasurer)->patch(route('bendahara.mahasiswa.toggle-status', $this->student));
        $response->assertRedirect();
        $this->student->refresh();
        $this->assertFalse($this->student->is_active);

        // Reactivate
        $response2 = $this->actingAs($this->treasurer)->patch(route('bendahara.mahasiswa.toggle-status', $this->student));
        $response2->assertRedirect();
        $this->student->refresh();
        $this->assertTrue($this->student->is_active);
    }

    public function test_deactivated_student_does_not_receive_future_dues(): void
    {
        $this->student->update(['is_active' => false]);

        $service = app(CashPeriodService::class);
        $service->createSemesterSet([
            'academic_year' => '2025/2026',
            'semester' => 'ganjil',
            'number_of_weeks' => 1,
            'amount' => 10000,
            'start_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'set_first_active' => true,
        ]);

        $period = CashPeriod::first();
        $this->assertNotNull($period);

        // Inactive student must NOT have student_dues created
        $this->assertDatabaseMissing('student_dues', [
            'user_id' => $this->student->id,
            'cash_period_id' => $period->id,
        ]);
    }

    public function test_historical_dues_and_payments_are_preserved_on_deactivation(): void
    {
        $period = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan ke-1',
            'amount' => 10000,
            'start_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'is_active' => true,
        ]);

        $due = StudentDue::create([
            'user_id' => $this->student->id,
            'cash_period_id' => $period->id,
            'amount' => 10000,
            'status' => 'paid',
        ]);

        $payment = Payment::create([
            'student_due_id' => $due->id,
            'amount' => 10000,
            'payment_method' => 'cash',
            'status' => 'approved',
            'paid_at' => now(),
            'verified_at' => now(),
            'verified_by' => $this->treasurer->id,
        ]);

        // Deactivate student
        $this->actingAs($this->treasurer)->patch(route('bendahara.mahasiswa.toggle-status', $this->student));
        $this->student->refresh();
        $this->assertFalse($this->student->is_active);

        // Historical records remain 100% intact
        $this->assertDatabaseHas('student_dues', [
            'id' => $due->id,
            'user_id' => $this->student->id,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'approved',
        ]);
    }

    public function test_unactivated_student_cannot_login_before_activating(): void
    {
        $plainToken = Str::random(64);
        $hashedToken = hash('sha256', $plainToken);

        User::create([
            'name' => 'Joko Unactivated',
            'nim' => '220410',
            'email' => 'joko@kasma.edu',
            'role' => 'mahasiswa',
            'password' => null,
            'is_active' => true,
            'activation_token' => $hashedToken,
            'activation_expires_at' => now()->addHours(72),
            'activated_at' => null,
        ]);

        $response = $this->post(route('login'), [
            'login' => 'joko@kasma.edu',
            'password' => 'somepassword',
        ]);

        $response->assertSessionHasErrors(['login']);
        $this->assertGuest();
    }

    public function test_existing_seeded_account_can_login_normally(): void
    {
        $response = $this->post(route('login'), [
            'login' => 'bendahara@kasma.edu',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/bendahara/dashboard');
        $this->assertAuthenticatedAs($this->treasurer);
    }
}
