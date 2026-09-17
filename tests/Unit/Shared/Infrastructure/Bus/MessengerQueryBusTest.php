<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\Query;
use App\Shared\Application\Bus\Response;
use App\Shared\Infrastructure\Bus\Exception\QueryHandlerNotRegisteredException;
use App\Shared\Infrastructure\Bus\MessengerQueryBus;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class MessengerQueryBusTest extends TestCase
{
    public function testSendsQueryAndReturnsMessage(): void
    {
        $query = $this->createStub(Query::class);
        $expectedResult = $this->createStub(Response::class);
        $envelope = new Envelope($query, [
            new HandledStamp($expectedResult, 'someHandler'),
        ]);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->with($query)
            ->willReturn($envelope);

        $queryBus = new MessengerQueryBus($messageBus);
        $result = $queryBus->ask($query);

        $this->assertSame($expectedResult, $result);
    }

    public function testHandlesExceptionWhenHandlerFails(): void
    {
        $query = $this->createStub(Query::class);
        $envelope = new Envelope($query, [
            new HandledStamp([], 'someHandler'),
        ]);
        $previousException = new \InvalidArgumentException('Invalid uuid value');
        $exception = new HandlerFailedException($envelope, [$previousException]);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->willThrowException($exception);

        $this->expectExceptionObject($previousException);

        $queryBus = new MessengerQueryBus($messageBus);
        $queryBus->ask($query);
    }

    public function testHandlesExceptionWhenNoHandlerFound(): void
    {
        $query = $this->createStub(Query::class);
        $exception = new QueryHandlerNotRegisteredException($query::class);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus->expects($this->once())
            ->method('dispatch')
            ->willThrowException(new NoHandlerForMessageException('No handler found'));

        $this->expectExceptionObject($exception);

        $queryBus = new MessengerQueryBus($messageBus);
        $queryBus->ask($query);
    }
}
