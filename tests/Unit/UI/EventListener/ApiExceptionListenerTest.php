<?php

declare(strict_types=1);

namespace App\Tests\Unit\UI\EventListener;

use App\UI\EventListener\ApiExceptionListener;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ApiExceptionListenerTest extends TestCase
{
    public function testNotHandlesExceptionWhenSubRequest(): void
    {
        $exception = new \RuntimeException('Could not handle exception');
        $event = $this->createExceptionEvent($exception, false);
        $listener = new ApiExceptionListener(true);
        $eventListener = new ApiExceptionListener(debug: false);
        $eventListener->__invoke($event);
        $this->assertNull($event->getResponse());
    }

    public function testReturnsValidationErrors(): void
    {
        $violations = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'This value is not a valid email address.',
                messageTemplate: 'This value is not a valid email address.',
                parameters: [],
                root: null,
                propertyPath: 'email',
                invalidValue: '',
            ),
            new ConstraintViolation(
                message: 'This value should not be blank.',
                messageTemplate: 'This value should not be blank.',
                parameters: [],
                root: null,
                propertyPath: 'password',
                invalidValue: '',
            ),
        ]);

        $validationException = new ValidationFailedException(null, $violations);
        $exception = HttpException::fromStatusCode(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Validation failed.',
            $validationException,
        );
        $event = $this->createExceptionEvent($exception);

        $eventListener = new ApiExceptionListener(debug: false);
        $eventListener->__invoke($event);
        $response = $event->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertSame(
            [
                'message' => 'Invalid Request Data',
                'errors' => [
                    'email' => 'This value is not a valid email address.',
                    'password' => 'This value should not be blank.',
                ],
            ],
            json_decode($response->getContent(), true),
        );
    }

    public static function httpErrorsProvider(): \Generator
    {
        yield 'with message' => [new HttpException(429, 'Too many requests. try later'), 'Too many requests. try later'];
        yield 'message from status code' => [new HttpException(404), 'Not found'];
    }

    #[DataProvider('httpErrorsProvider')]
    public function testReturnsHttpErrors($exception, $expectedMessage): void
    {
        $code = $exception->getStatusCode();
        $event = $this->createExceptionEvent($exception);

        $eventListener = new ApiExceptionListener(debug: false);
        $eventListener->__invoke($event);
        $response = $event->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame($code, $response->getStatusCode());
        $this->assertSame(
            [
                'message' => $expectedMessage,
            ],
            json_decode($response->getContent(), true),
        );
    }

    private function createExceptionEvent(\Throwable $exception, bool $isMainRequest = true): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            Request::create('/endpoint', 'POST'),
            $isMainRequest ? HttpKernelInterface::MAIN_REQUEST : HttpKernelInterface::SUB_REQUEST,
            $exception,
        );
    }

    public function testReturnsUnhandledErrorsWhenDebugIsOff(): void
    {
        $exception = new \RuntimeException('Could not handle exception');
        $event = $this->createExceptionEvent($exception);

        $eventListener = new ApiExceptionListener(debug: false);
        $eventListener->__invoke($event);
        $response = $event->getResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertSame(
            [
                'message' => 'Internal Server Error',
            ],
            json_decode($response->getContent(), true),
        );
    }

    public function testLeavesExceptionWhenDebugIsOn(): void
    {
        $exception = new \RuntimeException('Could not handle exception');
        $event = $this->createExceptionEvent($exception);

        $eventListener = new ApiExceptionListener(debug: true);
        $eventListener->__invoke($event);

        $this->assertNull($event->getResponse());
    }
}
