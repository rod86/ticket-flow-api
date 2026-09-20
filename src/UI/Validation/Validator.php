<?php

declare(strict_types=1);

namespace App\UI\Validation;

use Symfony\Component\Validator\Constraints as Assert;

interface Validator
{
    /** @return array<string, string> Errors list */
    public function validate(array $data, Assert\Collection $rules): array;
}
