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

final class ApiExceptionListenerTest extends TestCase
{
    public function testIgnoresSubRequests(): void
    {
        $event = $this->dispatchEvent(
            new \RuntimeException('Could not handle exception'),
            false,
            false
        );
        $this->assertNull($event->getResponse());
    }

    public function testReturnsValidationErrors(): void
    {
        $validationException = new ValidationFailedException(
            null,
            new ConstraintViolationList([
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
            ])
        );
        $exception = HttpException::fromStatusCode(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            'Validation failed.',
            $validationException,
        );
        $event = $this->dispatchEvent($exception);

        $this->assertJsonResponse(
            $event->getResponse(),
            Response::HTTP_UNPROCESSABLE_ENTITY,
            [
                'message' => 'Invalid Request Data',
                'errors' => [
                    'email' => 'This value is not a valid email address.',
                    'password' => 'This value should not be blank.',
                ],
            ]
        );
    }

    public static function httpErrorsProvider(): \Generator
    {
        yield 'with message' => [new HttpException(Response::HTTP_TOO_MANY_REQUESTS, 'Too many requests. try later'), 'Too many requests. try later'];
        yield 'message from status code' => [new HttpException(Response::HTTP_NOT_FOUND), 'Not found'];
    }

    #[DataProvider('httpErrorsProvider')]
    public function testReturnsHttpErrors(HttpException $exception, string $expectedMessage): void
    {
        $event = $this->dispatchEvent($exception);

        $this->assertJsonResponse(
            $event->getResponse(),
            $exception->getStatusCode(),
            ['message' => $expectedMessage]
        );
    }

    public function testReturnsUnhandledErrorsWhenDebugIsOff(): void
    {
        $exception = new \RuntimeException('Could not handle exception');
        $event = $this->dispatchEvent($exception);

        $this->assertJsonResponse(
            $event->getResponse(),
            Response::HTTP_INTERNAL_SERVER_ERROR,
            ['message' => 'Internal Server Error']
        );
    }

    public function testLeavesExceptionWhenDebugIsOn(): void
    {
        $exception = new \RuntimeException('Could not handle exception');
        $event = $this->dispatchEvent($exception, true);

        $this->assertNull($event->getResponse());
    }

    private function dispatchEvent(\Throwable $exception, bool $debug = false, bool $isMainRequest = true): ExceptionEvent
    {
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            Request::create('/endpoint', 'POST'),
            $isMainRequest ? HttpKernelInterface::MAIN_REQUEST : HttpKernelInterface::SUB_REQUEST,
            $exception,
        );

        $eventListener = new ApiExceptionListener($debug);
        $eventListener($event);

        return $event;
    }

    private function assertJsonResponse(?Response $response, int $expectedStatus, array $expectedBody): void
    {
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame($expectedStatus, $response->getStatusCode());
        $this->assertSame(
            json_decode($response->getContent(), true),
            $expectedBody
        );
    }
}
