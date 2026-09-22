<?php

declare(strict_types=1);

namespace App\Tests\Unit\UI\Http\ValueResolver;

use App\UI\Http\Request\AbstractJsonRequest;
use App\UI\Http\ValueResolver\JsonRequestValueResolver;
use App\UI\Validation\ValidatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints as Assert;

class JsonRequestValueResolverTest extends TestCase
{
    public function testItValidatesSuccessfully(): void
    {
        $body = [
            'email' => 'johndoe@example.com',
            'password' => 'cGFzc3dvcmQ=',
        ];
        $httpRequest = new Request(content: json_encode($body, \JSON_THROW_ON_ERROR));
        $request = $this->fakeJsonRequest($httpRequest);
        $argument = new ArgumentMetadata('request', $request::class, false, false, null);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with($body, $request->validationRules())
            ->willReturn([]);

        $resolver = new JsonRequestValueResolver($validator);
        $resolved = array_first(iterator_to_array($resolver->resolve($httpRequest, $argument)));

        $this->assertInstanceOf($request::class, $resolved);
        $this->assertSame($body, $resolved->body());
        $this->assertSame($httpRequest, $resolved->httpRequest());
    }

    public function testThrowsErrorWhenJsonParsingFails(): void
    {
        $httpRequest = new Request(content: 'invalid json content');
        $request = $this->fakeJsonRequest($httpRequest);
        $argument = new ArgumentMetadata('request', $request::class, false, false, null);

        $resolver = new JsonRequestValueResolver($this->createStub(ValidatorInterface::class));

        $this->expectExceptionObject(new BadRequestHttpException('The request body contains invalid JSON.'));
        iterator_to_array($resolver->resolve($httpRequest, $argument));
    }

    private function fakeJsonRequest(Request $httpRequest): AbstractJsonRequest
    {
        return (new readonly class ([], $httpRequest) extends AbstractJsonRequest {
            public function validationRules(): array
            {
                return [
                    'email' => [new Assert\NotBlank(), new Assert\Email()],
                    'password' => [new Assert\NotBlank()],
                ];
            }
        });
    }
}
