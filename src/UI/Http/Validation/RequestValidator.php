<?php

declare(strict_types=1);

namespace App\UI\Http\Validation;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

final readonly class RequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private SymfonyValidatorInterface $validator,
        private ValidationErrorFormatterInterface $errorFormatter,
    ) {
    }

    public function validate(array $data, array $rules): array
    {
        $constraints = new Assert\Collection(
            $rules,
            missingFieldsMessage: 'This value is required.',
        );
        $violations = $this->validator->validate($data, $constraints);
        return $violations->count() > 0 ? $this->errorFormatter->format($violations) : [];
    }
}
