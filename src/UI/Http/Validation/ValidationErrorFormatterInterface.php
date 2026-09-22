<?php

declare(strict_types=1);

namespace App\UI\Http\Validation;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ValidationErrorFormatterInterface
{
    /** @return array<string, mixed> */
    public function format(ConstraintViolationListInterface $violations): array;
}
