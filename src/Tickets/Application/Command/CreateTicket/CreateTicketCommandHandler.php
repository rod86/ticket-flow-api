<?php

declare(strict_types=1);

namespace App\Tickets\Application\Command\CreateTicket;

use App\Tickets\Domain\Interfaces\CustomerRepositoryInterface;
use App\Tickets\Domain\Interfaces\TicketCategoryRepositoryInterface;
use App\Tickets\Domain\Interfaces\TicketRepositoryInterface;
use App\Tickets\Domain\Ticket;

final readonly class CreateTicketCommandHandler
{
    public function __construct(
        private TicketRepositoryInterface $repository,
        private CustomerRepositoryInterface $customerRepository,
        private TicketCategoryRepositoryInterface $ticketCategoryRepository
    ) {
    }

    public function __invoke(CreateTicketCommand $command): void
    {
        $customer = $this->customerRepository->findByEmail($command->customerEmail);
        $category = $this->ticketCategoryRepository->findById($command->categoryId);
        $this->repository->create(
            Ticket::open(
                id: $command->id,
                subject: $command->subject,
                description: $command->description,
                customerId: $customer->id,
                categoryId: $category->id,
                createdAt: $command->createdAt,
            )
        );
    }
}
