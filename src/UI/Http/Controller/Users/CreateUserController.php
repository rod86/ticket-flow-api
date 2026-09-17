<?php

declare(strict_types=1);

namespace App\UI\Http\Controller\Users;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateUserController
{
    public function __invoke(Request $request): JsonResponse
    {
        // $body = json_decode($request->getContent(), true);

        return new JsonResponse(null, Response::HTTP_CREATED);
    }
}
