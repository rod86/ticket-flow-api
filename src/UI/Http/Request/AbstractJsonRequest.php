<?php

declare(strict_types=1);

namespace App\UI\Http\Request;

use Symfony\Component\Validator\Constraints as Assert;

abstract readonly class AbstractJsonRequest
{
    /**
     * @param array<string, mixed> $body
     */
    final public function __construct(
        private array $body
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function body(): array
    {
        return $this->body;
    }

    abstract public function constraints(): Assert\Collection;
}
