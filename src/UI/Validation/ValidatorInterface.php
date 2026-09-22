<?php

declare(strict_types=1);

namespace App\UI\Validation;

use Symfony\Component\Validator\Constraint;

interface ValidatorInterface
{
    /**
     * @param array<string, mixed> $data Data to validate
     * @param array<string, Constraint|list<Constraint>> $rules Validation rules
     * @return array<string, string> Errors list
     */
    public function validate(array $data, array $rules): array;
}
