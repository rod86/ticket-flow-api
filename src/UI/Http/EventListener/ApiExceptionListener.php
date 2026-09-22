<?php

declare(strict_types=1);

namespace App\UI\Http\EventListener;

use App\UI\Http\Validation\Exception\RequestValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final readonly class ApiExceptionListener
{
    public function __construct(
        private bool $debug,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();

            $responseBody = [
                'message' => $exception->getMessage() ?: Response::$statusTexts[$statusCode],
            ];
            if ($exception instanceof RequestValidationException) {
                $responseBody['errors'] = $exception->getErrors();
            }

            $event->setResponse(new JsonResponse($responseBody, $statusCode));
            return;
        }

        if ($this->debug) {
            return; // Keep Symfony debug page while developing
        }

        $event->setResponse(new JsonResponse(
            ['message' => Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR]],
            Response::HTTP_INTERNAL_SERVER_ERROR
        ));
    }
}
