<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Tickets;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateTicketController
{
    public function __invoke(Request $request): JsonResponse
    {
        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
