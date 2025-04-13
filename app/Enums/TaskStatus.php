<?php

namespace App\Enums;

/**
 * Enum representing the possible statuses for a task.
 *
 * This enum defines the various statuses that a task can have, such as:
 * - Pending
 * - In Progress
 * - Completed
 *
 * The methods in this enum allow for retrieving the status values, getting options
 * for Filament forms, and providing user-friendly labels for each status.
 */
enum TaskStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in progress';
    case COMPLETED = 'completed';

    /**
     * Get all possible task status values.
     *
     * This method returns an array of the raw string values for each of the task statuses
     * (e.g., 'pending', 'in progress', 'completed'). This can be useful for performing operations
     * on the values, such as filtering or saving them to a database.
     *
     * @return array
     */
    public static function getValues(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }

    /**
     * Get Filament select options for task statuses.
     *
     * This method returns an associative array where the keys are the raw values
     * of the task statuses (e.g., 'pending', 'in progress', 'completed') and
     * the values are the user-friendly labels (e.g., 'Pending', 'In Progress', 'Completed').
     *
     * This is specifically used to populate select options in Filament forms.
     *
     * @return array
     */
    public static function getFilamentSelectOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn(self $case) => [$case->value => $case->label()])
            ->toArray();
    }

    /**
     * Get the label for the current task status.
     *
     * This method provides a human-readable label for the task status based on its case.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
        };
    }
}
