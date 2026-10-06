<?php

declare(strict_types=1);

namespace App\Shared\Domain;

abstract class Entity
{
    /** @return array<string, mixed> */
    abstract public function toArray(): array;
}
