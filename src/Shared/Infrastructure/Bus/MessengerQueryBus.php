<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\Query;
use App\Shared\Application\Bus\QueryBusInterface;
use App\Shared\Application\Bus\Response;
use App\Shared\Infrastructure\Bus\Exception\QueryHandlerNotRegisteredException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class MessengerQueryBus implements QueryBusInterface
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function ask(Query $query): ?Response
    {
        try {
            $envelope = $this->messageBus->dispatch($query);

            return $envelope->last(HandledStamp::class)?->getResult();
        } catch (NoHandlerForMessageException) {
            throw new QueryHandlerNotRegisteredException($query::class);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
