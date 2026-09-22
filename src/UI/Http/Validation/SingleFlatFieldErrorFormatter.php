<?php

declare(strict_types=1);

namespace App\UI\Http\Validation;

use Symfony\Component\Validator\ConstraintViolationListInterface;

final class SingleFlatFieldErrorFormatter implements ValidationErrorFormatterInterface
{
    public function format(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $field = $this->flattenPropertyPath($violation->getPropertyPath());
            if (!array_key_exists($field, $errors)) {
                $errors[$field] = $violation->getMessage();
            }
        }

        return $errors;
    }

    private function flattenPropertyPath(string $propertyPath): string
    {
        preg_match_all('/\[([^]]*)]/', $propertyPath, $matches);
        $segments = $matches[1];

        $field = '';
        foreach ($segments as $index => $segment) {
            if ($index === 0) {
                $field = $segment;
            } elseif (ctype_digit($segment)) {
                $field .= '[' . $segment . ']';
            } else {
                $field .= '.' . $segment;
            }
        }

        return $field;
    }
}
