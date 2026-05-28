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
        $studentId = fake()->unique()->numerify('##########');
        return [
            'name' => fake()->name(),
            'student_id' => $studentId,
            'teams_email' => $studentId . '@std.jadara.edu.jo',
            'role' => 'student',
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function instructor(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'instructor',
                'teams_email' => $attributes['student_id'] . '@doc.jadara.edu.jo',
            ];
        });
    }
}
