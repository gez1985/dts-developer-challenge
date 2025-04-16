<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Task;
use App\Models\User;
use Tests\TestCase;
use Carbon\Carbon;
use App\Enums\TaskStatus;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_tasks()
    {
        $user = User::factory()->create();
        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson('/api/tasks');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data') // Check if 'data' contains exactly 3 items
            ->assertJsonStructure([
                'data' => [
                    '*' => [ // This specifies that each item inside 'data' is an array with these keys
                        'id',
                        'title',
                        'description',
                        'priority',
                        'status',
                        'dueDate',
                    ],
                ],
            ]);
    }

    public function test_show_task_by_id()
    {
        // Create a user and task
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("api/tasks/{$task->id}");

        $response->assertStatus(200);

        // Check if the response matches the expected structure
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'description',
                'priority',
                'status',
                'dueDate',
            ]
        ]);

        // Check if the values in the response match the task data
        $response->assertJson([
            'data' => [
                'id' => $task->id,
                'title' => $task->title,
                'description' => $task->description,
                'priority' => $task->priority->value,
                'status' => $task->status->value,
                'dueDate' => Carbon::parse($task->due_date)->toDateTimeString(),
            ]
        ]);
    }

    public function test_can_store_a_new_task_for_authenticated_user()
    {
        // Create a user to authenticate
        $user = User::factory()->create();

        // Data to store the task
        $taskData = [
            'title' => 'Test Task',
            'description' => 'This is a description of the task.',
            'priority' => 'medium',
            'status' => 'pending',
            'due_date' => '2025-12-31 00:00',
        ];

        // Make the POST request to store the task
        $response = $this->actingAs($user)->postJson('/api/tasks', $taskData);

        // Assert the task is created in the database
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'This is a description of the task.',
            'priority' => 'medium',
            'due_date' => '2025-12-31 00:00',
            'user_id' => $user->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'Test Task',
                    'description' => 'This is a description of the task.',
                    'priority' => 'medium',
                    'dueDate' => '2025-12-31 00:00:00',
                ]
            ]);
    }

    public function test_can_delete_a_task_for_authenticated_user()
    {
        $user = User::factory()->create();

        // Create a task for the user
        $task = Task::factory()->create(['user_id' => $user->id]);

        // Assert the task exists in the database before deletion
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => $user->id,
        ]);

        // Make the DELETE request to delete the task
        $response = $this->actingAs($user)->deleteJson('/api/tasks/' . $task->id);

        // Assert the task is deleted from the database
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);

        // Assert the response is correct
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Task deleted',
            ]);
    }

    public function test_returns_not_found_if_task_does_not_exist()
    {
        $user = User::factory()->create();

        // Try deleting a task that doesn't exist (using a non-existent ID)
        $response = $this->actingAs($user)->deleteJson('/api/tasks/99999');

        // Assert the response returns a 404 status code and a "Task not found" message
        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Task not found',
            ]);
    }

    public function test_cannot_create_task_without_required_fields()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/tasks', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'priority', 'status']);
    }

    public function test_user_can_update_their_own_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create([
            'title' => 'Old Title',
            'status' => TaskStatus::PENDING,
        ]);

        $updatedData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'priority' => 'high',
            'status' => 'completed',
            'due_date' => '2025-12-31',
        ];

        $response = $this->actingAs($user)
            ->putJson("/api/tasks/{$task->id}", $updatedData);

        $response->assertOk()
            ->assertJsonFragment(['title' => 'Updated Title']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'priority' => 'high',
            'status' => 'completed',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_update_someone_elses_task()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->for($otherUser)->create();

        $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", [
            'title' => 'Hacked Title',
            'description' => 'Hacked',
            'priority' => 'low',
            'status' => 'pending',
            'due_date' => '2025-12-31',
        ]);

        $response->assertStatus(403);
    }

    public function test_updating_task_with_invalid_data_fails_validation()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", [
            'title' => '', // required
            'priority' => 'invalid', // invalid enum
            'status' => 'not_a_status', // invalid enum
            'due_date' => 'not a date',
        ]);

        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJsonValidationErrors(['title', 'priority', 'status', 'due_date']);
    }
}
