<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title'   => $this->faker->sentence,
            'task_category_id' => 1,
            'description' => $this->faker->text,
            'start_at' => now(),
            'end_at' => now()->addDay(),
        ];
    }
}
