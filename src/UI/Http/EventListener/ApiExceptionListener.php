<?php

declare(strict_types=1);

namespace App\UI\Http\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ApiExceptionListener
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

        if ($exception->getPrevious() instanceof ValidationFailedException) {
            $errors = [];
            foreach ($exception->getPrevious()->getViolations() as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            $event->setResponse(new JsonResponse(
                [
                    'message' => 'Invalid Request Data',
                    'errors' => $errors,
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY,
            ));

            return;
        }

        if ($exception instanceof HttpExceptionInterface) {
            $code = $exception->getStatusCode();
            $message = $exception->getMessage() ?: ucfirst(strtolower(Response::$statusTexts[$code]));

            $event->setResponse(new JsonResponse(
                ['message' => $message],
                $code,
            ));

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
