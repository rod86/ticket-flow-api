<?php

declare(strict_types=1);

namespace App\UI\Validation;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class DataValidator implements Validator
{
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {
    }

    public function validate(array $data, Assert\Collection $rules): array
    {
        $errors = [];
        $violations = $this->validator->validate($data, $rules);
        if ($violations->count()) {
            /** @var ConstraintViolationInterface $violation */
            foreach ($violations as $violation) {
                $field = $violation->getPropertyPath();
                $errors[$field] = $violation->getMessage();
            }
        }

        return $errors;
    }
}
