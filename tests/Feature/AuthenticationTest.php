<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $treasurer;
    protected User $inactiveStudent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->student = User::create([
            'name' => 'Hafizh Al-Fatih',
            'nim' => '220401',
            'email' => 'hafizh@kasma.edu',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'phone_number' => '081234567890',
            'is_active' => true,
        ]);

        $this->treasurer = User::create([
            'name' => 'Nadya Putri',
            'nim' => '220400',
            'email' => 'bendahara@kasma.edu',
            'password' => Hash::make('password123'),
            'role' => 'bendahara',
            'phone_number' => '082198765432',
            'is_active' => true,
        ]);

        $this->inactiveStudent = User::create([
            'name' => 'Inactive Student',
            'nim' => '220499',
            'email' => 'inactive@kasma.edu',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
            'is_active' => false,
        ]);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Masuk ke KASMA');
    }

    public function test_mahasiswa_can_authenticate_using_email(): void
    {
        $response = $this->post('/login', [
            'login' => 'hafizh@kasma.edu',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->student);
        $response->assertRedirect('/mahasiswa/dashboard');
    }

    public function test_mahasiswa_can_authenticate_using_nim(): void
    {
        $response = $this->post('/login', [
            'login' => '220401',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->student);
        $response->assertRedirect('/mahasiswa/dashboard');
    }

    public function test_bendahara_can_authenticate_using_email(): void
    {
        $response = $this->post('/login', [
            'login' => 'bendahara@kasma.edu',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->treasurer);
        $response->assertRedirect('/bendahara/dashboard');
    }

    public function test_bendahara_can_authenticate_using_nim(): void
    {
        $response = $this->post('/login', [
            'login' => '220400',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($this->treasurer);
        $response->assertRedirect('/bendahara/dashboard');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $response = $this->post('/login', [
            'login' => 'hafizh@kasma.edu',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    public function test_inactive_users_cannot_authenticate(): void
    {
        $response = $this->post('/login', [
            'login' => 'inactive@kasma.edu',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    public function test_users_can_logout(): void
    {
        $response = $this->actingAs($this->student)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_users_are_redirected_to_login(): void
    {
        $this->get('/mahasiswa/dashboard')->assertRedirect('/login');
        $this->get('/bendahara/dashboard')->assertRedirect('/login');
    }

    public function test_mahasiswa_cannot_access_bendahara_routes(): void
    {
        $response = $this->actingAs($this->student)->get('/bendahara/dashboard');

        $response->assertStatus(403);
    }

    public function test_bendahara_cannot_access_mahasiswa_routes(): void
    {
        $response = $this->actingAs($this->treasurer)->get('/mahasiswa/dashboard');

        $response->assertStatus(403);
    }

    public function test_authenticated_user_accessing_login_page_is_redirected_to_their_dashboard(): void
    {
        $this->actingAs($this->student)->get('/login')->assertRedirect('/mahasiswa/dashboard');
        $this->actingAs($this->treasurer)->get('/login')->assertRedirect('/bendahara/dashboard');
    }

    public function test_root_url_redirects_to_appropriate_dashboard_for_authenticated_users(): void
    {
        $this->actingAs($this->student)->get('/')->assertRedirect('/mahasiswa/dashboard');
        $this->actingAs($this->treasurer)->get('/')->assertRedirect('/bendahara/dashboard');
    }
}
