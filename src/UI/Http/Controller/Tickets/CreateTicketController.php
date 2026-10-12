<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Tickets;

use App\Shared\Application\Bus\CommandBusInterface;
use App\Tickets\Application\Command\CreateTicket\CreateTicketCommand;
use App\UI\Http\Request\Tickets\CreateTicketRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

final readonly class CreateTicketController
{
    public function __construct(
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(CreateTicketRequest $request): JsonResponse
    {
        $body = $request->body();

        $this->commandBus->dispatch(
            new CreateTicketCommand(
                id: Uuid::v7()->toString(),
                subject: $body['subject'],
                description: $body['description'],
                customerId: Uuid::v7()->toString(),
                customerName: $body['customer_name'],
                customerEmail: $body['customer_email'],
                categoryId: $body['category_id'],
                createdAt: new \DateTimeImmutable(),
            )
        );
        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
