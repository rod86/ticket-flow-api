<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\Command;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Infrastructure\Bus\Exception\CommandHandlerNotRegisteredException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class MessengerCommandBus implements CommandBusInterface
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    public function dispatch(Command $command): void
    {
        try {
            $this->messageBus->dispatch($command);
        } catch (NoHandlerForMessageException) {
            throw new CommandHandlerNotRegisteredException($command::class);
        } catch (HandlerFailedException $exception) {
            throw $exception->getPrevious();
        }
    }
}
