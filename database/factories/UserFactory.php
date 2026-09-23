<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'nim' => fake()->unique()->numerify('2204##'),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'mahasiswa',
            'phone_number' => fake()->phoneNumber(),
            'is_active' => true,
            'activated_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user is not activated yet.
     */
    public function unactivated(): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => null,
            'activated_at' => null,
            'activation_token' => hash('sha256', Str::random(64)),
            'activation_expires_at' => now()->addHours(72),
        ]);
    }

    /**
     * Indicate that the user is a bendahara.
     */
    public function bendahara(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'bendahara',
        ]);
    }

    /**
     * Indicate that the user is a mahasiswa.
     */
    public function mahasiswa(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'mahasiswa',
        ]);
    }

    /**
     * Indicate that the user is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
