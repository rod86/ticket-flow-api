<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus;

interface ResponseInterface
{
    /**
     * @return array<string, mixed>
     */
    public function data(): array;
}
