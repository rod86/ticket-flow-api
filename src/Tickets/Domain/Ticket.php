<?php

declare(strict_types=1);

namespace App\Tickets\Domain;

use App\Shared\Domain\Entity;

final class Ticket extends Entity
{
    public function __construct(
        private string $id,
        private string $subject,
        private string $description,
        private TicketStatus $status,
        private string $customerId,
        private string $categoryId,
        private \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function subject(): string
    {
        return $this->subject;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function status(): TicketStatus
    {
        return $this->status;
    }

    public function customerId(): string
    {
        return $this->customerId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
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
