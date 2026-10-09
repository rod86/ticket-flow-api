<?php

declare(strict_types=1);

namespace App\Tickets\Application\Query\FindTicketById;

use App\Shared\Application\Bus\QueryInterface;

final readonly class FindTicketByIdQuery implements QueryInterface
{
    public function __construct(
        public string $id,
    ) {
    }
}
