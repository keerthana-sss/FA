<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TripFileType extends Enum
{
    const Receipt = 0;
    const Photo   = 1;
    const Ticket  = 2;
    const Other   = 3;

    /**
     * Get description for each type.
     */
    public static function description($value): string
    {
        return match ($value) {
            self::Receipt => 'Receipt',
            self::Photo   => 'Photo',
            self::Ticket  => 'Ticket',
            self::Other   => 'Other',
            default       => 'Unknown',
        };
    }
}
