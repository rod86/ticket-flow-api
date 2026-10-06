<?php

declare(strict_types=1);

namespace App\Tickets\Domain;

final readonly class Customer
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public \DateTimeImmutable $createdAt,
    ) {
    }
}
