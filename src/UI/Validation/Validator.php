<?php

declare(strict_types=1);

namespace App\UI\Validation;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

final readonly class Validator implements ValidatorInterface
{
    public function __construct(
        private SymfonyValidatorInterface $validator
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
