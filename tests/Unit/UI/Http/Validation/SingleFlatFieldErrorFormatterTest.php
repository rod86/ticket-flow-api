<?php

declare(strict_types=1);

namespace App\Tests\Unit\UI\Http\Validation;

use App\UI\Http\Validation\SingleFlatFieldErrorFormatter;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

final class SingleFlatFieldErrorFormatterTest extends TestCase
{
    public function testFormatsErrors(): void
    {
        $violations = new ConstraintViolationList([
            $this->buildConstraintsViolation('[email]', 'This value should not be blank.'),
            $this->buildConstraintsViolation('[role]', 'This value is not a valid choice.'),
            $this->buildConstraintsViolation('[role]', 'This value is too short.'),
            $this->buildConstraintsViolation('[address][city]', 'This value should not be blank.'),
            $this->buildConstraintsViolation('[tags][0]', 'This value should not be blank.'),
            $this->buildConstraintsViolation('[tags][1]', 'This value is not a valid choice.'),
            $this->buildConstraintsViolation('[items][0][name]', 'This value should not be blank.'),
            $this->buildConstraintsViolation('[items][0][quantity]', 'This value should be positive.'),
            $this->buildConstraintsViolation('[items][1][quantity]', 'This value should be integer.'),
        ]);
        $expectedErrors = [
            'email' => 'This value should not be blank.',
            'role' => 'This value is not a valid choice.',
            'address.city' => 'This value should not be blank.',
            'tags[0]' => 'This value should not be blank.',
            'tags[1]' => 'This value is not a valid choice.',
            'items[0].name' => 'This value should not be blank.',
            'items[0].quantity' => 'This value should be positive.',
            'items[1].quantity' => 'This value should be integer.',
        ];

        $formatter = new SingleFlatFieldErrorFormatter();
        $result = $formatter->format($violations);

        Assert::assertSame($expectedErrors, $result);
    }

    private function buildConstraintsViolation(string $propertyPath, string $message): ConstraintViolation
    {
        return new ConstraintViolation(
            message: $message,
            messageTemplate: null,
            parameters: [],
            root: null,
            propertyPath: $propertyPath,
            invalidValue: '',
        );
    }
}
