<?php

declare(strict_types=1);

namespace App\Tickets\Domain;

final readonly class TicketCategory
{
    public function __construct(
        public string $id,
        public string $name,
    ) {
    }
}
