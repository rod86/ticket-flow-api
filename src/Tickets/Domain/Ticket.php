<?php

declare(strict_types=1);

namespace App\Tickets\Domain;

use App\Shared\Domain\Entity;

final class Ticket extends Entity
{
    public function __construct(
        public readonly string $id,
        public readonly string $subject,
        public readonly string $description,
        public readonly TicketStatus $status,
        public readonly string $customerId,
        public readonly string $categoryId,
        public readonly \DateTimeImmutable $createdAt,
        public readonly \DateTimeImmutable $updatedAt,
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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'description' => $this->description,
            'status' => $this->status,
            'customer_id' => $this->customerId,
            'category_id' => $this->categoryId,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
