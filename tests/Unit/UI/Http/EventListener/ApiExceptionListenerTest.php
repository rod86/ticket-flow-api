<?php

declare(strict_types=1);

namespace App\Tests\Unit\UI\Http\EventListener;

use App\UI\Http\EventListener\ApiExceptionListener;
use App\UI\Http\Validation\Exception\RequestValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Throwable;

final class ApiExceptionListenerTest extends TestCase
{
    public static function httpErrorsProvider(): \Generator
    {
        yield 'Too many requests' => [
            new TooManyRequestsHttpException(null, 'Too many requests. try later'),
            Response::HTTP_TOO_MANY_REQUESTS,
            ['message' => 'Too many requests. try later']
        ];
        yield 'message from status code' => [
            new NotFoundHttpException(),
            Response::HTTP_NOT_FOUND,
            ['message' => 'Not Found']
        ];
        yield 'request validation exception' => [
            new RequestValidationException(['name' => 'This value should not be blank.']),
            Response::HTTP_BAD_REQUEST,
            ['message' => 'Invalid Request Data', 'errors' => ['name' => 'This value should not be blank.']]
        ];
    }


    /**
     * @param Throwable $exception
     * @param int $expectedStatus
     * @param array<string, mixed> $expectedBody
     * @throws Exception
     */
    #[DataProvider('httpErrorsProvider')]
    public function testHandlesHttpErrors(\Throwable $exception, int $expectedStatus, array $expectedBody): void
    {
        $event = $this->dispatchEvent($exception);

        $this->assertJsonResponse(
            $event->getResponse(),
            $expectedStatus,
            $expectedBody
        );
    }

    public function testIgnoresSubRequests(): void
    {
        $event = $this->dispatchEvent(
            new \RuntimeException('Could not handle exception'),
            false,
            false
        );

        $this->assertNull($event->getResponse());
    }

    public function testHandlesUnhandledErrorsWhenDebugIsOff(): void
    {
        $event = $this->dispatchEvent(new \RuntimeException('Could not handle exception'));

        $this->assertJsonResponse(
            $event->getResponse(),
            Response::HTTP_INTERNAL_SERVER_ERROR,
            ['message' => 'Internal Server Error']
        );
    }

    public function testLeavesExceptionWhenDebugIsOn(): void
    {
        $event = $this->dispatchEvent(new \RuntimeException('Could not handle exception'), true);

        $this->assertNull($event->getResponse());
    }

    /**
     * @param Throwable $exception
     * @param bool $debug
     * @param bool $isMainRequest
     * @return ExceptionEvent
     * @throws Exception
     */
    private function dispatchEvent(
        Throwable $exception,
        bool $debug = false,
        bool $isMainRequest = true
    ): ExceptionEvent {
        $event = new ExceptionEvent(
            $this->createStub(HttpKernelInterface::class),
            Request::create('/endpoint', 'POST'),
            $isMainRequest ? HttpKernelInterface::MAIN_REQUEST : HttpKernelInterface::SUB_REQUEST,
            $exception,
        );

        $eventListener = new ApiExceptionListener($debug);
        $eventListener($event);

        return $event;
    }

    /**
     * @param Response|null $response
     * @param int $expectedStatus
     * @param array<string, mixed> $expectedBody
     * @return void
     */
    private function assertJsonResponse(?Response $response, int $expectedStatus, array $expectedBody): void
    {
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame($expectedStatus, $response->getStatusCode());
        $this->assertSame(
            $expectedBody,
            json_decode($response->getContent(), true)
        );
    }
}
