<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $role = Role::query()->where('title', 'player')->first();

        if (! $role) {
            // Allows factories/seed to work even if RoleSeeder hasn't run yet.
            $role = Role::query()->create(['title' => 'player']);
        }

        return [
            'role_id' => $role->id,
            'name' => fake()->name(),
            'display_name' => fake()->userName(),
            'avatar' => null,
            'timezone' => 'UTC',
            'email' => fake()->unique()->safeEmail(),
            // Your User model has a 'password' => 'hashed' cast, so we must pass the plain password.
            'password' => static::$password ??= 'password',
        ];
    }
}
