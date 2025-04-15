<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    // Relationships:

    /**
     * Get the user that owns the task.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Other methods:

    /**
     * Get the available status options for tasks.
     *
     * @return array The available status options for tasks.
     */
    public static function getStatusOptions(): array
    {
        return TaskStatus::getValues();
    }

    /**
     * Get the available priority options for tasks.
     *
     * @return array The available priority options for tasks.
     */
    public static function getPriorityOptions(): array
    {
        return Priority::getValues();
    }
}
