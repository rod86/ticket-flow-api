<?php

declare(strict_types=1);

namespace App\Tests\Unit\UI\Http\ValueResolver;

use App\UI\Http\Request\AbstractJsonRequest;
use App\UI\Http\Validation\Exception\RequestValidationException;
use App\UI\Http\ValueResolver\JsonRequestValueResolver;
use App\UI\Http\Validation\RequestValidatorInterface;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Constraints;

final class JsonRequestValueResolverTest extends TestCase
{
    public function testItValidatesSuccessfully(): void
    {
        $body = [
            'email' => 'johndoe@example.com',
            'password' => 'cGFzc3dvcmQ=',
        ];
        $httpRequest = Request::create(uri: '/test', method: 'POST', content: json_encode($body));
        $request = $this->fakeJsonRequest($httpRequest);
        $argument = new ArgumentMetadata('request', $request::class, false, false, null);

        $validator = $this->createMock(RequestValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with($body, $request->validationRules())
            ->willReturn([]);

        $resolver = new JsonRequestValueResolver($validator);
        $resolved = array_first(iterator_to_array($resolver->resolve($httpRequest, $argument)));

        Assert::assertInstanceOf($request::class, $resolved);
        Assert::assertSame($body, $resolved->body());
        Assert::assertSame($httpRequest, $resolved->httpRequest());
    }

    public function testMalformedJSONThrowsBadRequestException(): void
    {
        $httpRequest = Request::create(uri: '/test', method: 'POST', content: 'invalid json content');
        $request = $this->fakeJsonRequest($httpRequest);
        $argument = new ArgumentMetadata('request', $request::class, false, false, null);

        $resolver = new JsonRequestValueResolver($this->createStub(RequestValidatorInterface::class));

        $this->expectExceptionObject(new BadRequestHttpException('The request body contains invalid JSON.'));
        iterator_to_array($resolver->resolve($httpRequest, $argument));
    }

    public function testEmptyBodyResolvesEmptyArray(): void
    {
        $httpRequest = Request::create(uri: '/test', method: 'POST', content: ' ');
        $request = $this->fakeJsonRequest($httpRequest);
        $argument = new ArgumentMetadata('request', $request::class, false, false, null);

        $validator = $this->createMock(RequestValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with([], $request->validationRules())
            ->willReturn([]);

        $resolver = new JsonRequestValueResolver($validator);
        $resolved = array_first(iterator_to_array($resolver->resolve($httpRequest, $argument)));

        Assert::assertSame([], $resolved->body());
    }

    public function testOnlyRunsResolverWhenRequestIsJsonRequest(): void
    {
        $request = Request::create(uri: '/test', method: 'POST', content: '');
        $argument = new ArgumentMetadata('request', $request::class, false, false, null);

        $validator = $this->createMock(RequestValidatorInterface::class);
        $validator->expects($this->never())->method('validate');

        $resolver = new JsonRequestValueResolver($validator);
        $resolved = iterator_to_array($resolver->resolve($request, $argument));

        Assert::assertSame([], $resolved);
    }

    public function testItThrowsRequestValidationExceptionWhenErrorsNotEmpty(): void
    {
        $httpRequest = Request::create(uri: '/test', method: 'POST', content: ' ');
        $request = $this->fakeJsonRequest($httpRequest);
        $argument = new ArgumentMetadata('request', $request::class, false, false, null);
        $errors = ['name' => 'This value should not be blank.'];

        $validator = $this->createMock(RequestValidatorInterface::class);
        $validator->expects($this->once())
            ->method('validate')
            ->with([], $request->validationRules())
            ->willReturn($errors);

        $this->expectExceptionObject(new RequestValidationException($errors));

        $resolver = new JsonRequestValueResolver($validator);
        iterator_to_array($resolver->resolve($httpRequest, $argument));
    }

    private function fakeJsonRequest(Request $httpRequest): AbstractJsonRequest
    {
        return (new readonly class ([], $httpRequest) extends AbstractJsonRequest {
            public function validationRules(): array
            {
                return [
                    'email' => [new Constraints\NotBlank(), new Constraints\Email()],
                    'password' => [new Constraints\NotBlank()],
                ];
            }
        });
    }
}
