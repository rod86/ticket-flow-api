<?php

declare(strict_types=1);

namespace App\Tests\Integration\Tickets\Infrastructure\Persistence\Doctrine;

use App\Tests\Lib\DatabaseTestCase;
use App\Tests\Lib\Fixtures\CustomerFixture;
use App\Tests\Lib\Fixtures\TicketFixture;
use App\Tests\Lib\ModelFactory\TicketModelFactory;
use App\Tests\Lib\ValueGenerator\FakeValueGenerator;
use App\Tickets\Domain\Customer;
use App\Tickets\Domain\TicketCategory;
use App\Tickets\Infrastructure\Persistence\Doctrine\DoctrineTicketRepository;

final class DoctrineTicketRepositoryTest extends DatabaseTestCase
{
    private DoctrineTicketRepository $repository;
    private TicketFixture $ticketFixture;
    private CustomerFixture $customerFixture;

    private Customer $customer;
    private TicketCategory $billingCategory;

    protected function setUp(): void
    {
        parent::setUp();

        $connection = $this->em->getConnection();
        $this->repository = new DoctrineTicketRepository($this->em);
        $this->ticketFixture = new TicketFixture($connection);
        $this->customerFixture = new CustomerFixture($connection);

        $this->customer = $this->customerFixture->insert();
        $this->billingCategory = new TicketCategory(
            id: '8e56a31f-fe37-4dd5-831f-edff5bd6452e',
            name: 'Billing & Payments'
        );
    }

    protected function tearDown(): void
    {
        $this->ticketFixture->cleanup();
        $this->customerFixture->cleanup();

        parent::tearDown();
    }

    public function testFindByIdReturnsItem(): void
    {
        $ticket = $this->ticketFixture->insert(['category' => $this->billingCategory, 'customer' => $this->customer]);

        $result = $this->repository->findById($ticket->id());

        $this->assertEquals($ticket, $result);
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findById(FakeValueGenerator::uuid());

        $this->assertNull($result);
    }

    public function testCreatesTicket(): void
    {
        $ticket = TicketModelFactory::create(
            customer: $this->em->getReference(Customer::class, $this->customer->id()),
            category: $this->em->getReference(TicketCategory::class, $this->billingCategory->id()),
        );
        $this->ticketFixture->register($ticket->id());

        $this->repository->create($ticket);

        $result = $this->getTicketById($ticket->id());
        $this->assertEquals($ticket, $result);
    }
}
