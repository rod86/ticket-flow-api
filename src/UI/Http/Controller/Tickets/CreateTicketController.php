<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Tickets;

use App\UI\Http\Request\Tickets\CreateTicketRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class CreateTicketController
{
    public function __invoke(CreateTicketRequest $request): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
