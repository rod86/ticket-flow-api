<?php

declare(strict_types=1);

namespace App\Tickets\Application\Query\FindTicketById;

use App\Tickets\Domain\Interfaces\TicketRepositoryInterface;

final readonly class FindTicketByIdQueryHandler
{
    public function __construct(
        private TicketRepositoryInterface $repository,
    ) {
    }

    public function __invoke(FindTicketByIdQuery $query): FindTicketByIdResponse
    {
        $ticket = $this->repository->findById($query->id);

        return FindTicketByIdResponse::fromEntity($ticket);
    }
}
