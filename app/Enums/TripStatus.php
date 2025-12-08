<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;
final class TripStatus extends Enum
{
    const Draft     = 0;
    const Active    = 1;
    const Completed = 2;
    const Archived  = 3;

    /**
     * Get human-readable description for each status.
     *
     * @return string
     */
    public static function getDescription($value): string
    {
        return match ($value) {
            self::Draft     => 'Draft',
            self::Active    => 'Active',
            self::Completed => 'Completed',
            self::Archived  => 'Archived',
            default => 'Unknown',
        };
    }
}
