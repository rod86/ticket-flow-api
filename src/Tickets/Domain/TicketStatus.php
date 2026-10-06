<?php

declare(strict_types=1);

namespace App\Tickets\Domain;

use App\Shared\Domain\EnumValuesTrait;

enum TicketStatus: string
{
    use EnumValuesTrait;

    case OPEN = 'open';
    case PROCESS = 'process';
    case RESOLVED = 'resolved';
    case DISMISSED = 'dismissed';
}
