<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse([
            'name' => 'John Doe',
            'age' => 25,
            'is_active' => true,
        ]);
    }
}
