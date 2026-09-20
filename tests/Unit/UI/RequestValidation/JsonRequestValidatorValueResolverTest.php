<?php

declare(strict_types=1);

namespace App\Tests\Unit\UI\RequestValidation;

use App\UI\RequestValidation\JsonRequest;
use App\UI\RequestValidation\JsonRequestValidatorValueResolver;
use App\UI\RequestValidation\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class JsonRequestValidatorValueResolverTest extends TestCase
{
    public function testItValidatesSuccessfully(): void
    {
        $body = [
            'email' => 'johndoe@example.com',
            'password' => 'cGFzc3dvcmQ=',
        ];
        $requestClass = $this->fakeJsonRequest();

        $validator = $this->createMock(Validator::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with($body, $this->isInstanceOf(Assert\Collection::class))
            ->willReturn([]);

        $request = new Request(content: json_encode($body, \JSON_THROW_ON_ERROR));
        $argument = new ArgumentMetadata('request', $requestClass, false, false, null);

        $resolver = new JsonRequestValidatorValueResolver($validator);
        $resolved = iterator_to_array($resolver->resolve($request, $argument));

        $this->assertCount(1, $resolved);
        $this->assertInstanceOf($requestClass, $resolved[0]);
        $this->assertSame($body, $resolved[0]->body());
    }

    /**
     * @return class-string<JsonRequest>
     */
    private function fakeJsonRequest(): string
    {
        return (new class([]) extends JsonRequest {
            public function constraints(): Assert\Collection
            {
                return new Assert\Collection([
                    'email' => [new Assert\NotBlank(), new Assert\Email()],
                    'password' => [new Assert\NotBlank()],
                ]);
            }
        })::class;
    }
}
