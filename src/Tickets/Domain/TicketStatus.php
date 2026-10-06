<?php

declare(strict_types=1);

namespace App\Tickets\Domain;

enum TicketStatus: string
{
    case OPEN = 'open';
    case PROCESS = 'process';
    case RESOLVED = 'resolved';
    case DISMISSED = 'dismissed';

    /** @return array<string> */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
