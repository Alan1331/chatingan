<?php

namespace Database\Factories;

use App\Models\User; // Ensure this is the correct namespace
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class; // Ensure this points to App\Models\User

    public function definition()
    {
        return [
            'name'           => $this->faker->name(),
            'email'          => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'       => Hash::make('password123'), // Default password
            'address'        => $this->faker->address(),
            'gender'         => $this->faker->boolean(),
            'marital_status' => $this->faker->randomElement(['single', 'married', 'divorced', 'widowed']),
            'remember_token' => Str::random(10),
        ];
    }
}
