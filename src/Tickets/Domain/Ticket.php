<?php

declare(strict_types=1);

namespace App\Tickets\Domain;

final readonly class Ticket
{
    public function __construct(
        public string $id,
        public string $subject,
        public string $description,
        public TicketStatus $status,
        public Customer $customer,
        public TicketCategory $category,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt,
    ) {
    }
}
