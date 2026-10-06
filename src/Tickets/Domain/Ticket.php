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
        public string $customerId,
        public string $categoryId,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt,
    ) {
    }

    public static function open(
        string $id,
        string $subject,
        string $description,
        string $customerId,
        string $categoryId,
        \DateTimeImmutable $createdAt,
    ): self {
        return new self(
            id: $id,
            subject: $subject,
            description: $description,
            status: TicketStatus::OPEN,
            customerId: $customerId,
            categoryId: $categoryId,
            createdAt: $createdAt,
            updatedAt: $createdAt,
        );
    }
}
