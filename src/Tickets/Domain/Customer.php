<?php

declare(strict_types=1);

namespace App\Tickets\Domain;

use App\Shared\Domain\Entity;

final class Customer extends Entity
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly \DateTimeImmutable $createdAt,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
