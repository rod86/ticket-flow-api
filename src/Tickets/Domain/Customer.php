<?php

declare(strict_types=1);

namespace App\Tickets\Domain;

use App\Shared\Domain\Entity;

final class Customer extends Entity
{
    public function __construct(
        private string $id,
        private string $name,
        private string $email,
        private \DateTimeImmutable $createdAt
    ) {
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
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
