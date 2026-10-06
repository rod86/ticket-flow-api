<?php

declare(strict_types=1);

namespace App\Tickets\Domain\Interfaces;

use App\Tickets\Domain\TicketCategory;

interface TicketCategoryRepositoryInterface
{
    public function findById(string $id): ?TicketCategory;
}
