<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Tickets;

use App\Shared\Application\Bus\QueryBusInterface;
use App\Tickets\Application\Query\FindTicketById\FindTicketByIdQuery;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class FindTicketByIdController
{
    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(string $id): JsonResponse
    {
        $response = $this->queryBus->ask(new FindTicketByIdQuery($id));

        return new JsonResponse($response->data());
    }
}
