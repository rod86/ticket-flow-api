<?php

declare(strict_types=1);

namespace App\Tests\Unit\Tickets\Application\Command\CreateTicket;

use App\Tests\Lib\ModelFactory\CustomerModelFactory;
use App\Tests\Lib\ModelFactory\TicketModelFactory;
use App\Tests\Lib\ValueGenerator\FakeValueGenerator;
use App\Tickets\Application\Command\CreateTicket\CreateTicketCommand;
use App\Tickets\Application\Command\CreateTicket\CreateTicketCommandHandler;
use App\Tickets\Domain\Customer;
use App\Tickets\Domain\Interfaces\CustomerRepositoryInterface;
use App\Tickets\Domain\Interfaces\TicketCategoryRepositoryInterface;
use App\Tickets\Domain\Interfaces\TicketRepositoryInterface;
use App\Tickets\Domain\Ticket;
use App\Tickets\Domain\TicketStatus;
use PHPUnit\Framework\TestCase;

function createCommand(Ticket $ticket, Customer $customer): CreateTicketCommand
{
    return new CreateTicketCommand(
        id: $ticket->id,
        subject: $ticket->subject,
        description: $ticket->description,
        customerId: $customer->id,
        customerName: $customer->name,
        customerEmail: $customer->email,
        categoryId: $ticket->category->id,
        createdAt: $ticket->createdAt,
    );
}

final class CreateTicketCommandHandlerTest extends TestCase
{
    public function testCreatesTicket(): void
    {
        $createdAt = FakeValueGenerator::dateTime();
        $ticket = TicketModelFactory::create(
            status: TicketStatus::NEW,
            createdAt: $createdAt,
            updatedAt: $createdAt,
        );
        $customer = $ticket->customer;
        $category = $ticket->category;

        $command = createCommand($ticket, $customer);

        $customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $customerRepository->expects($this->once())
            ->method('findByEmail')
            ->with($customer->email)
            ->willReturn($customer);

        $categoryRepository = $this->createMock(TicketCategoryRepositoryInterface::class);
        $categoryRepository->expects($this->once())
            ->method('findById')
            ->with($category->id)
            ->willReturn($category);

        $ticketRepository = $this->createMock(TicketRepositoryInterface::class);
        $ticketRepository->expects($this->once())
            ->method('create')
            ->with($ticket);

        $handler = new CreateTicketCommandHandler(
            $ticketRepository,
            $customerRepository,
            $categoryRepository,
        );
        $handler->__invoke($command);
    }
}
