<?php

declare(strict_types=1);

namespace App\Tickets\Application\Query\FindTicketById;

use App\Shared\Application\Bus\ResponseInterface;
use App\Tickets\Domain\Ticket;
use DateTimeImmutable;

final readonly class FindTicketByIdResponse implements ResponseInterface
{
    /**
     * @param string $id
     * @param string $subject
     * @param string $description
     * @param string $status
     * @param array<string, mixed> $category
     * @param array<string, mixed> $customer
     * @param DateTimeImmutable $createdAt
     * @param DateTimeImmutable $updatedAt
     */
    public function __construct(
        private readonly string $id,
        private readonly string $subject,
        private readonly string $description,
        private readonly string $status,
        private readonly array $category,
        private readonly array $customer,
        private readonly DateTimeImmutable $createdAt,
        private readonly DateTimeImmutable $updatedAt,
    ) {
    }

    public function data(): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'description' => $this->description,
            'status' => $this->status,
            'customer' => $this->customer,
            'category' => $this->category,
            'created_at' => $this->createdAt->format(DATE_ATOM),
            'updated_at' => $this->updatedAt->format(DATE_ATOM),
        ];
    }

    public static function fromEntity(Ticket $ticket): self
    {
        return new self(
            id: $ticket->id(),
            subject: $ticket->subject(),
            description: $ticket->description(),
            status: $ticket->status()->value,
            category: [
                'id' => $ticket->category()->id(),
                'title' => $ticket->category()->name(),
            ],
            customer: [
                'id' => $ticket->customer()->id(),
                'name' => $ticket->customer()->name(),
                'email' => $ticket->customer()->email(),
            ],
            createdAt: $ticket->createdAt(),
            updatedAt: $ticket->updatedAt()
        );
    }
}
