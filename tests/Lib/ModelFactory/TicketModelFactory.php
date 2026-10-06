<?php

declare(strict_types=1);

namespace App\Tests\Lib\ModelFactory;

use App\Tests\Lib\ValueGenerator\FakeValueGenerator;
use App\Tickets\Domain\Ticket;
use App\Tickets\Domain\TicketStatus;

final class TicketModelFactory
{
    public static function create(
        ?string $id = null,
        ?string $subject = null,
        ?string $description = null,
        ?TicketStatus $status = null,
        ?string $customerId = null,
        ?string $categoryId = null,
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null,
    ): Ticket {
        return new Ticket(
            id: $id ?? FakeValueGenerator::uuid(),
            subject: $subject ?? FakeValueGenerator::sentence(),
            description: $description ?? FakeValueGenerator::text(),
            status: $status ?? FakeValueGenerator::randomElement(TicketStatus::values()),
            customerId: $customerId ?? FakeValueGenerator::uuid(),
            categoryId: $categoryId ?? FakeValueGenerator::uuid(),
            createdAt: $createdAt ?? FakeValueGenerator::dateTime('-2 months', 'now'),
            updatedAt: $updatedAt ?? FakeValueGenerator::dateTime('-1 months', 'now'),
        );
    }
}
