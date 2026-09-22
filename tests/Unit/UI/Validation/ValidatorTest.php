<?php

declare(strict_types=1);

namespace App\Tests\Unit\UI\Validation;

use App\UI\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidatorTest extends TestCase
{
    public function testDataPassesValidation(): void
    {
        $rules = ['name' => new Assert\NotBlank()];
        $data = ['name' => 'john', 'age' => 20];
        $violations = new ConstraintViolationList();

        $symfonyValidatorMock = $this->createMock(ValidatorInterface::class);
        $symfonyValidatorMock->expects($this->once())
            ->method('validate')
            ->with($data, $rules)
            ->willReturn($violations);

        $validator = new Validator($symfonyValidatorMock);
        $result = $validator->validate($data, $rules);

        $this->assertSame([], $result);
    }

    public function testDataFailsValidation(): void
    {
        $rules = ['name' => new Assert\NotBlank()];
        $data = ['name' => 'john', 'age' => 20];
        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'This value should not be blank.',
                messageTemplate: 'This value should not be blank.',
                parameters: [],
                root: null,
                propertyPath: 'name',
                invalidValue: '',
            ),
            new ConstraintViolation(
                message: 'This value is not a integer.',
                messageTemplate: 'This value is not a integer.',
                parameters: [],
                root: null,
                propertyPath: 'age',
                invalidValue: '',
            ),
        ]);
        $expectedErrors = [
            'name' => 'This value should not be blank.',
            'age' => 'This value is not a integer.',
        ];

        $symfonyValidatorMock = $this->createMock(ValidatorInterface::class);
        $symfonyValidatorMock->expects($this->once())
            ->method('validate')
            ->with($data, $rules)
            ->willReturn($violations);

        $validator = new Validator($symfonyValidatorMock);
        $result = $validator->validate($data, $rules);

        $this->assertSame($expectedErrors, $result);
    }
}
