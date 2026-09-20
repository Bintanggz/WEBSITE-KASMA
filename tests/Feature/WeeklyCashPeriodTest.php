<?php

namespace Tests\Feature;

use App\Models\CashPeriod;
use App\Models\StudentDue;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyCashPeriodTest extends TestCase
{
    use RefreshDatabase;

    private User $bendahara;
    private User $mahasiswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bendahara = User::factory()->create([
            'role' => 'bendahara',
            'is_active' => true,
        ]);

        $this->mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);
    }

    public function test_bendahara_can_view_cash_periods_index(): void
    {
        CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan ke-1',
            'amount' => '10000.00',
            'start_date' => now()->startOfWeek(),
            'due_date' => now()->endOfWeek(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->bendahara)->get(route('bendahara.iuran.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Iuran Kas');
        $response->assertSee('Pekan ke-1');
    }

    public function test_bendahara_can_create_semester_cash_period_set_with_automated_student_dues(): void
    {
        // Add 2 more active mahasiswa and 1 inactive mahasiswa
        $mhs2 = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
        $mhs3 = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
        $mhsInactive = User::factory()->create(['role' => 'mahasiswa', 'is_active' => false]);

        $response = $this->actingAs($this->bendahara)->post(route('bendahara.iuran.store'), [
            'academic_year' => '2026/2027',
            'semester' => 'ganjil',
            'number_of_weeks' => 4,
            'amount' => 15000,
            'start_date' => '2026-09-01',
            'due_date' => '2026-09-08',
            'set_first_active' => '1',
        ]);

        $response->assertRedirect(route('bendahara.iuran.index'));
        $response->assertSessionHas('success');

        // Check 4 cash periods created
        $this->assertDatabaseCount('cash_periods', 4);

        $p1 = CashPeriod::where('week_number', 1)->first();
        $this->assertNotNull($p1);
        $this->assertTrue($p1->is_active);
        $this->assertEquals('15000.00', $p1->amount);
        $this->assertEquals('Pekan ke-1', $p1->name);

        $p4 = CashPeriod::where('week_number', 4)->first();
        $this->assertNotNull($p4);
        $this->assertFalse($p4->is_active);
        $this->assertEquals('2026-09-22', $p4->start_date->format('Y-m-d'));
        $this->assertEquals('2026-09-29', $p4->due_date->format('Y-m-d'));

        // Check student dues: 3 active students * 4 weeks = 12 dues
        $this->assertDatabaseCount('student_dues', 12);

        // Verify inactive student received 0 dues
        $this->assertEquals(0, StudentDue::where('user_id', $mhsInactive->id)->count());

        // Verify active students each have 4 dues with snapshot amount and unpaid status
        foreach ([$this->mahasiswa, $mhs2, $mhs3] as $mhs) {
            $dues = StudentDue::where('user_id', $mhs->id)->get();
            $this->assertCount(4, $dues);
            foreach ($dues as $due) {
                $this->assertEquals('15000.00', $due->amount);
                $this->assertEquals('unpaid', $due->status);
            }
        }
    }

    public function test_duplicate_cash_period_set_is_prevented(): void
    {
        CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan ke-1',
            'amount' => '10000.00',
            'start_date' => now()->startOfWeek(),
            'due_date' => now()->endOfWeek(),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->bendahara)->post(route('bendahara.iuran.store'), [
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'number_of_weeks' => 2,
            'amount' => 10000,
            'start_date' => now()->format('Y-m-d'),
            'due_date' => now()->addWeek()->format('Y-m-d'),
        ]);

        $response->assertSessionHasErrors(['academic_year']);
    }

    public function test_bendahara_can_activate_and_deactivate_cash_period(): void
    {
        $p1 = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan ke-1',
            'amount' => '10000.00',
            'start_date' => now()->startOfWeek(),
            'due_date' => now()->endOfWeek(),
            'is_active' => true,
        ]);

        $p2 = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 2,
            'name' => 'Pekan ke-2',
            'amount' => '10000.00',
            'start_date' => now()->addWeek()->startOfWeek(),
            'due_date' => now()->addWeek()->endOfWeek(),
            'is_active' => false,
        ]);

        // Activate period 2
        $response = $this->actingAs($this->bendahara)->patch(route('bendahara.iuran.activate', $p2));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertFalse($p1->fresh()->is_active);
        $this->assertTrue($p2->fresh()->is_active);

        // Deactivate period 2
        $response = $this->actingAs($this->bendahara)->patch(route('bendahara.iuran.deactivate', $p2));
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertFalse($p2->fresh()->is_active);
    }

    public function test_bendahara_can_view_cash_period_detail_with_paid_and_unpaid_counts(): void
    {
        $period = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan ke-1',
            'amount' => '10000.00',
            'start_date' => now()->startOfWeek(),
            'due_date' => now()->endOfWeek(),
            'is_active' => true,
        ]);

        $mhsPaid = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Siti Aisyah', 'is_active' => true]);
        $mhsUnpaid = User::factory()->create(['role' => 'mahasiswa', 'name' => 'Budi Pratama', 'is_active' => true]);

        StudentDue::create([
            'cash_period_id' => $period->id,
            'user_id' => $mhsPaid->id,
            'amount' => '10000.00',
            'status' => 'paid',
        ]);

        StudentDue::create([
            'cash_period_id' => $period->id,
            'user_id' => $mhsUnpaid->id,
            'amount' => '10000.00',
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->bendahara)->get(route('bendahara.iuran.show', $period));

        $response->assertStatus(200);
        $response->assertSee('Status Pembayaran Pekan ke-1');
        $response->assertSee('Siti Aisyah');
        $response->assertSee('Budi Pratama');
        $response->assertSee('Lunas');
        $response->assertSee('Belum Bayar');
    }

    public function test_mahasiswa_can_view_their_obligations_and_dues_history(): void
    {
        $period = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan ke-1',
            'amount' => '10000.00',
            'start_date' => now()->startOfWeek(),
            'due_date' => now()->endOfWeek(),
            'is_active' => true,
        ]);

        StudentDue::create([
            'cash_period_id' => $period->id,
            'user_id' => $this->mahasiswa->id,
            'amount' => '10000.00',
            'status' => 'unpaid',
        ]);

        $response = $this->actingAs($this->mahasiswa)->get(route('mahasiswa.iuran.index'));

        $response->assertStatus(200);
        $response->assertSee('Kewajiban Iuran Kas Mingguan');
        $response->assertSee('Pekan ke-1');
        $response->assertSee('Belum Lunas');
    }

    public function test_mahasiswa_cannot_access_bendahara_iuran_routes(): void
    {
        $period = CashPeriod::create([
            'academic_year' => '2025/2026',
            'semester' => 'genap',
            'week_number' => 1,
            'name' => 'Pekan ke-1',
            'amount' => '10000.00',
            'start_date' => now()->startOfWeek(),
            'due_date' => now()->endOfWeek(),
            'is_active' => true,
        ]);

        $this->actingAs($this->mahasiswa)->get(route('bendahara.iuran.index'))->assertStatus(403);
        $this->actingAs($this->mahasiswa)->post(route('bendahara.iuran.store'), [])->assertStatus(403);
        $this->actingAs($this->mahasiswa)->patch(route('bendahara.iuran.activate', $period))->assertStatus(403);
        $this->actingAs($this->mahasiswa)->patch(route('bendahara.iuran.deactivate', $period))->assertStatus(403);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get(route('bendahara.iuran.index'))->assertRedirect(route('login'));
        $this->get(route('mahasiswa.iuran.index'))->assertRedirect(route('login'));
    }
}
