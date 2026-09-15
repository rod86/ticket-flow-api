<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController {

    public function __invoke(): JsonResponse 
    {
        return new JsonResponse([
            "data" => "hello world"
        ]);
    }
}