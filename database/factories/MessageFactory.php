<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'body' => $this->faker->sentence(),
            'sender' => User::factory(),  // Create a new User as sender
            'receiver' => User::factory(),  // Create a new User as receiver
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
