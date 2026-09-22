<?php

declare(strict_types=1);

namespace App\Tests\Unit\UI\Http\Validation;

use App\UI\Http\Validation\RequestValidator;
use App\UI\Http\Validation\ValidationErrorFormatterInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RequestValidatorTest extends TestCase
{
    public function testDataPassesValidation(): void
    {
        $rules = ['name' => new Assert\NotBlank()];
        $data = ['name' => 'john'];
        $violations = new ConstraintViolationList();

        $symfonyValidatorMock = $this->createMock(ValidatorInterface::class);
        $formatterMock = $this->createMock(ValidationErrorFormatterInterface::class);
        $symfonyValidatorMock->expects($this->once())
            ->method('validate')
            ->with($data, new Assert\Collection($rules))
            ->willReturn($violations);
        $formatterMock->expects($this->never())
            ->method('format');

        $validator = new RequestValidator($symfonyValidatorMock, $formatterMock);
        $result = $validator->validate($data, $rules);

        $this->assertSame([], $result);
    }

    public function testDataFailsValidation(): void
    {
        $rules = ['name' => new Assert\NotBlank()];
        $data = ['name' => 'john'];
        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'This value should not be blank.',
                messageTemplate: null,
                parameters: [],
                root: null,
                propertyPath: '[name]',
                invalidValue: '',
            )
        ]);

        $expectedErrors = [
            'name' => 'This value should not be blank.',
        ];

        $symfonyValidatorMock = $this->createMock(ValidatorInterface::class);
        $formatterMock = $this->createMock(ValidationErrorFormatterInterface::class);
        $symfonyValidatorMock->expects($this->once())
            ->method('validate')
            ->willReturn($violations);

        $formatterMock->expects($this->once())
            ->method('format')
            ->with($violations)
            ->willReturn($expectedErrors);

        $validator = new RequestValidator($symfonyValidatorMock, $formatterMock);
        $result = $validator->validate($data, $rules);

        $this->assertSame($expectedErrors, $result);
    }
}
