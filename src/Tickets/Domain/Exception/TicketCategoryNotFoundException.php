<?php

declare(strict_types=1);

namespace App\Tickets\Domain\Exception;

final class TicketCategoryNotFoundException extends \DomainException
{
    public static function withId(string $id): self
    {
        return new self(sprintf('Ticket category "%s" not found.', $id));
    }
}
