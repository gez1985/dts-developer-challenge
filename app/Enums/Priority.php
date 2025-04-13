<?php

namespace App\Enums;

/**
 * Enum representing the possible priority levels for a task.
 *
 * This enum defines the various priority levels a task can have, such as:
 * - Low
 * - Medium
 * - High
 *
 * The methods in this enum allow for retrieving the priority values, getting options
 * for Filament forms, and providing user-friendly labels for each priority level.
 */
enum Priority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    /**
     * Get all possible priority values.
     *
     * This method returns an array of the raw string values for each of the priority levels
     * (e.g., 'low', 'medium', 'high'). This can be useful for performing operations
     * on the values, such as filtering or saving them to a database.
     *
     * @return array
     */
    public static function getValues(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }

    /**
     * Get Filament select options for task priorities.
     *
     * This method returns an associative array where the keys are the raw values
     * of the priority levels (e.g., 'low', 'medium', 'high') and the values are
     * the user-friendly labels (e.g., 'Low', 'Medium', 'High').
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
     * Get the label for the current priority level.
     *
     * This method provides a human-readable label for the priority level based on its case.
     * For example:
     * - 'Low' for the LOW case
     * - 'Medium' for the MEDIUM case
     * - 'High' for the HIGH case
     *
     * @return string
     */
    public function label(): string
    {
        // Uses a match expression to return a user-friendly label for each priority level
        return match ($this) {
            self::LOW => 'Low',
            self::MEDIUM => 'Medium',
            self::HIGH => 'High',
        };
    }
}
