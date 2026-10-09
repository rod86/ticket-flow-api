<?php

declare(strict_types=1);

namespace App\Tickets\Domain\Exception;

final class TicketNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Ticket "%s" not found.', $id));
    }
}
