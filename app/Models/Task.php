<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\TaskStatus;
use App\Enums\Priority;

class Task extends Model
{
    use HasFactory;

    // the database table name
    protected $table = 'tasks';

    // the fillable model attributes
    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'due_date',
    ];

    // Cast status and priority to enums when retrieving from DB
    protected $casts = [
        'status' => TaskStatus::class,
        'priority' => Priority::class,
    ];

    // Get the status options for the task (using the TaskStatus enum)
    public static function getStatusOptions(): array
    {
        return TaskStatus::getValues();
    }

    // Get the priority options for the task (using the Priority enum)
    public static function getPriorityOptions(): array
    {
        return Priority::getValues();
    }
}
