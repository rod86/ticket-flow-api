<?php

declare(strict_types=1);

namespace App\Tickets\Application\Command\CreateTicket;

final readonly class CreateTicketCommand
{
    public function __construct(
        public string $id,
        public string $subject,
        public string $description,
        public string $customerId,
        public string $customerName,
        public string $customerEmail,
        public string $categoryId,
        public \DateTimeImmutable $createdAt,
    ) {
    }
}
