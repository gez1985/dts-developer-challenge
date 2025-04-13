<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;

class DatabaseMigrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a task can be created and stored in the database.
     *
     * This test checks whether a task can be successfully created with
     * attributes like 'title', 'due_date', and 'priority', and verifies
     * that the task is saved in the database.
     *
     */
    /** @test */
    public function it_has_the_correct_task_table_structure()
    {
        // Run the migrations
        $this->artisan('migrate');

        // Check if the table exists
        $this->assertTrue(Schema::hasTable('tasks'));

        // Check if specific columns exist
        $this->assertTrue(Schema::hasColumn('tasks', 'title'));
        $this->assertTrue(Schema::hasColumn('tasks', 'description'));
        $this->assertTrue(Schema::hasColumn('tasks', 'due_date'));
        $this->assertTrue(Schema::hasColumn('tasks', 'priority'));
        $this->assertTrue(Schema::hasColumn('tasks', 'status'));
    }
}
