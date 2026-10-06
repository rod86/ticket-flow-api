<?php

declare(strict_types=1);

namespace App\Tickets\Domain;

use App\Shared\Domain\Entity;

final class TicketCategory extends Entity
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
