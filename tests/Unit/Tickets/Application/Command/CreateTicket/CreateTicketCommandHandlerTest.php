<?php

declare(strict_types=1);

namespace App\Tests\Unit\Tickets\Application\Command\CreateTicket;

use App\Tests\Lib\ModelFactory\CustomerModelFactory;
use App\Tests\Lib\ModelFactory\TicketCategoryModelFactory;
use App\Tests\Lib\ModelFactory\TicketModelFactory;
use App\Tests\Lib\ValueGenerator\FakeValueGenerator;
use App\Tickets\Application\Command\CreateTicket\CreateTicketCommand;
use App\Tickets\Application\Command\CreateTicket\CreateTicketCommandHandler;
use App\Tickets\Domain\Customer;
use App\Tickets\Domain\Exception\TicketCategoryNotFoundException;
use App\Tickets\Domain\Interfaces\CustomerRepositoryInterface;
use App\Tickets\Domain\Interfaces\TicketCategoryRepositoryInterface;
use App\Tickets\Domain\Interfaces\TicketRepositoryInterface;
use App\Tickets\Domain\Ticket;
use App\Tickets\Domain\TicketStatus;
use PHPUnit\Framework\TestCase;

function createCommand(Ticket $ticket, Customer $customer): CreateTicketCommand
{
    return new CreateTicketCommand(
        id: $ticket->id(),
        subject: $ticket->subject(),
        description: $ticket->description(),
        customerId: $customer->id(),
        customerName: $customer->name(),
        customerEmail: $customer->email(),
        categoryId: $ticket->categoryId(),
        createdAt: $ticket->createdAt(),
    );
}

final class CreateTicketCommandHandlerTest extends TestCase
{
    public function testCreatesTicket(): void
    {
        $createdAt = FakeValueGenerator::dateTime();
        $customer = CustomerModelFactory::create();
        $category = TicketCategoryModelFactory::create();
        $ticket = TicketModelFactory::create(
            status: TicketStatus::OPEN,
            customerId: $customer->id(),
            categoryId: $category->id(),
            createdAt: $createdAt,
            updatedAt: $createdAt,
        );

        $command = createCommand($ticket, $customer);

        $customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $customerRepository->expects($this->once())
            ->method('findByEmail')
            ->with($customer->email())
            ->willReturn($customer);

        $customerRepository->expects($this->never())
            ->method('create');

        $categoryRepository = $this->createMock(TicketCategoryRepositoryInterface::class);
        $categoryRepository->expects($this->once())
            ->method('findById')
            ->with($category->id())
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

    public function testCreatesCustomerAndTicketWhenCustomerDoesNotExist(): void
    {
        $createdAt = FakeValueGenerator::dateTime();
        $customer = CustomerModelFactory::create(createdAt: $createdAt);
        $category = TicketCategoryModelFactory::create();
        $ticket = TicketModelFactory::create(
            status: TicketStatus::OPEN,
            customerId: $customer->id(),
            categoryId: $category->id(),
            createdAt: $createdAt,
            updatedAt: $createdAt,
        );

        $command = createCommand($ticket, $customer);

        $customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $customerRepository->expects($this->once())
            ->method('findByEmail')
            ->with($customer->email())
            ->willReturn(null);

        $customerRepository->expects($this->once())
            ->method('create')
            ->with($customer);

        $categoryRepository = $this->createMock(TicketCategoryRepositoryInterface::class);
        $categoryRepository->expects($this->once())
            ->method('findById')
            ->with($category->id())
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

    public function testThrowsWhenCategoryDoesNotExist(): void
    {
        $createdAt = FakeValueGenerator::dateTime();
        $customer = CustomerModelFactory::create(createdAt: $createdAt);
        $category = TicketCategoryModelFactory::create();
        $ticket = TicketModelFactory::create(
            status: TicketStatus::OPEN,
            customerId: $customer->id(),
            categoryId: $category->id(),
            createdAt: $createdAt,
            updatedAt: $createdAt,
        );

        $command = createCommand($ticket, $customer);

        $customerRepository = $this->createMock(CustomerRepositoryInterface::class);
        $customerRepository->expects($this->never())
            ->method('create');

        $categoryRepository = $this->createMock(TicketCategoryRepositoryInterface::class);
        $categoryRepository->expects($this->once())
            ->method('findById')
            ->with($category->id())
            ->willReturn(null);

        $ticketRepository = $this->createStub(TicketRepositoryInterface::class);


        $handler = new CreateTicketCommandHandler(
            $ticketRepository,
            $customerRepository,
            $categoryRepository,
        );

        $this->expectExceptionObject(
            TicketCategoryNotFoundException::withId($category->id())
        );

        $handler->__invoke($command);
    }
}
