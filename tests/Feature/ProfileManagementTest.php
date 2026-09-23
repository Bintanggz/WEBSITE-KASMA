<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $mahasiswa;
    private User $bendahara;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bendahara = User::create([
            'name' => 'Nadya Putri',
            'nim' => '220400',
            'email' => 'bendahara@kasma.edu',
            'password' => Hash::make('password'),
            'role' => 'bendahara',
            'phone_number' => '082198765432',
            'is_active' => true,
            'activated_at' => now(),
        ]);

        $this->mahasiswa = User::create([
            'name' => 'Hafizh Al-Fatih',
            'nim' => '220401',
            'email' => 'hafizh@kasma.edu',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'phone_number' => '081234567890',
            'is_active' => true,
            'activated_at' => now(),
        ]);
    }

    public function test_guest_is_redirected_to_login_when_accessing_profile(): void
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    public function test_student_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->mahasiswa)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Akun &amp; Profil', false);
        $response->assertSee('Hafizh Al-Fatih');
        $response->assertSee('220401');
    }

    public function test_treasurer_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->bendahara)->get(route('profile.edit'));
        $response->assertStatus(200);
        $response->assertSee('Nadya Putri');
        $response->assertSee('Bendahara Kas Kelas');
    }

    public function test_user_can_update_phone_number(): void
    {
        $response = $this->actingAs($this->mahasiswa)->patch(route('profile.update'), [
            'phone_number' => '089876543210',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->mahasiswa->refresh();
        $this->assertEquals('089876543210', $this->mahasiswa->phone_number);
    }

    public function test_user_can_change_password_with_valid_current_password(): void
    {
        $response = $this->actingAs($this->mahasiswa)->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->mahasiswa->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->mahasiswa->password));

        // Verify login works with new password
        $this->post(route('logout'));
        $loginResponse = $this->post(route('login'), [
            'login' => '220401',
            'password' => 'newpassword123',
        ]);
        $loginResponse->assertRedirect(route('mahasiswa.dashboard'));
        $this->assertAuthenticatedAs($this->mahasiswa);
    }

    public function test_user_cannot_change_password_with_incorrect_current_password(): void
    {
        $response = $this->actingAs($this->mahasiswa)->put(route('profile.password'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->mahasiswa->refresh();
        $this->assertTrue(Hash::check('password', $this->mahasiswa->password));
    }

    public function test_user_cannot_change_password_if_confirmation_mismatch(): void
    {
        $response = $this->actingAs($this->mahasiswa)->put(route('profile.password'), [
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentsandipassword',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
