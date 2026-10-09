<?php

declare(strict_types=1);

namespace App\Tests\Unit\Tickets\Application\Query\FindTicketById;

use App\Tests\Lib\ModelFactory\CustomerModelFactory;
use App\Tests\Lib\ModelFactory\TicketCategoryModelFactory;
use App\Tests\Lib\ModelFactory\TicketModelFactory;
use App\Tests\Lib\ValueGenerator\FakeValueGenerator;
use App\Tickets\Application\Query\FindTicketById\FindTicketByIdQuery;
use App\Tickets\Application\Query\FindTicketById\FindTicketByIdQueryHandler;
use App\Tickets\Application\Query\FindTicketById\FindTicketByIdResponse;
use App\Tickets\Domain\Exception\TicketNotFoundException;
use App\Tickets\Domain\Interfaces\TicketRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class FindTicketByIdQueryHandlerTest extends TestCase
{
    public function testReturnsTicket(): void
    {
        $customer = CustomerModelFactory::create();
        $category = TicketCategoryModelFactory::create();
        $ticket = TicketModelFactory::create(
            customer: $customer,
            category: $category,
        );

        $repository = $this->createMock(TicketRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with($ticket->id())
            ->willReturn($ticket);

        $handler = new FindTicketByIdQueryHandler($repository);
        $result = $handler->__invoke(new FindTicketByIdQuery($ticket->id()));

        $this->assertInstanceOf(FindTicketByIdResponse::class, $result);
        $this->assertSame(
            [
                'id' => $ticket->id(),
                'subject' => $ticket->subject(),
                'description' => $ticket->description(),
                'status' => $ticket->status()->value,
                'customer' => [
                    'id' => $customer->id(),
                    'name' => $customer->name(),
                    'email' => $customer->email(),
                ],
                'category' => [
                    'id' => $category->id(),
                    'title' => $category->name(),
                ],
                'created_at' => $ticket->createdAt()->format(DATE_ATOM),
                'updated_at' => $ticket->updatedAt()->format(DATE_ATOM),
            ],
            $result->data(),
        );
    }

    public function testThrowsExceptionWhenTicketNotFound(): void
    {
        $id = FakeValueGenerator::uuid();

        $repository = $this->createMock(TicketRepositoryInterface::class);
        $repository->expects($this->once())
            ->method('findById')
            ->with($id)
            ->willReturn(null);

        $this->expectExceptionObject(TicketNotFoundException::withId($id));

        $handler = new FindTicketByIdQueryHandler($repository);
        $handler->__invoke(new FindTicketByIdQuery($id));
    }
}
