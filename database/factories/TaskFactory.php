<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Task;
use App\Models\User;
use App\Enums\TaskStatus;
use App\Enums\Priority;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(TaskStatus::getValues()),
            'priority' => $this->faker->randomElement(Priority::getValues()),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}
