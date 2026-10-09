<?php

declare(strict_types=1);

namespace App\Tickets\Domain\Interfaces;

use App\Tickets\Domain\Ticket;

interface TicketRepositoryInterface
{
    public function create(Ticket $ticket): void;

    public function findById(string $id): ?Ticket;
}
