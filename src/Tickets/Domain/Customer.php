<?php

declare(strict_types=1);

namespace App\Tickets\Domain;

final readonly class Customer
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $email,
        public readonly \DateTimeImmutable $createdAt,
    ) {
    }
}
