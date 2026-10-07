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
        private Customer $customer,
        private TicketCategory $category,
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

    public function customer(): Customer
    {
        return $this->customer;
    }

    public function category(): TicketCategory
    {
        return $this->category;
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
        Customer $customer,
        TicketCategory $category,
        \DateTimeImmutable $createdAt,
    ): self {
        return new self(
            id: $id,
            subject: $subject,
            description: $description,
            status: TicketStatus::OPEN,
            customer: $customer,
            category: $category,
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
            'customer' => $this->customer->toArray(),
            'category' => $this->category->toArray(),
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
