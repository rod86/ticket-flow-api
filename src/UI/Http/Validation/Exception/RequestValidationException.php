<?php

declare(strict_types=1);

namespace App\UI\Http\Validation\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class RequestValidationException extends HttpException
{
    /** @param array<string, mixed> $errors */
    public function __construct(
        private readonly array $errors,
    ) {
        parent::__construct(Response::HTTP_BAD_REQUEST, 'Invalid Request Data');
    }

    /** @return array<string, mixed> */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
