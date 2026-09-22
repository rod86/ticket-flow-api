<?php

declare(strict_types=1);

namespace App\UI\Http\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;

abstract readonly class AbstractJsonRequest
{
    /**
     * @param array<string, mixed> $body
     */
    final public function __construct(
        private array $body,
        private Request $httpRequest,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function body(): array
    {
        return $this->body;
    }

    public function httpRequest(): Request
    {
        return $this->httpRequest;
    }

    /** @return array<string, Constraint|list<Constraint>> */
    abstract public function validationRules(): array;
}
