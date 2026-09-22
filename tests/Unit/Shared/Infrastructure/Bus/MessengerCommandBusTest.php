<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\CommandInterface;
use App\Shared\Infrastructure\Bus\Exception\CommandHandlerNotRegisteredException;
use App\Shared\Infrastructure\Bus\MessengerCommandBus;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class MessengerCommandBusTest extends TestCase
{
    public function testDispatchesCommandToMessageBus(): void
    {
        $command = $this->createStub(CommandInterface::class);
        $envelope = new Envelope($command, [
            new HandledStamp(null, 'someHandler'),
        ]);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->with($command)
            ->willReturn($envelope);

        $commandBus = new MessengerCommandBus($messageBus);
        $commandBus->dispatch($command);

        $this->addToAssertionCount(1);
    }

    public function testUnwrapsPreviousExceptionOnHandlerFailure(): void
    {
        $command = $this->createStub(CommandInterface::class);
        $envelope = new Envelope($command, [
            new HandledStamp(null, 'someHandler'),
        ]);
        $previousException = new \InvalidArgumentException('Invalid uuid value');
        $exception = new HandlerFailedException($envelope, [$previousException]);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->willThrowException($exception);

        $this->expectExceptionObject($previousException);

        $commandBus = new MessengerCommandBus($messageBus);
        $commandBus->dispatch($command);
    }

    public function testThrowsWhenNoHandlerRegistered(): void
    {
        $command = $this->createStub(CommandInterface::class);
        $exception = new CommandHandlerNotRegisteredException($command::class);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->willThrowException(new NoHandlerForMessageException('No handler found'));

        $this->expectExceptionObject($exception);

        $commandBus = new MessengerCommandBus($messageBus);
        $commandBus->dispatch($command);
    }
}
