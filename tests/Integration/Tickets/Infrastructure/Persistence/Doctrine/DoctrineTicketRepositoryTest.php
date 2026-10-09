<?php

declare(strict_types=1);

namespace App\Tests\Integration\Tickets\Infrastructure\Persistence\Doctrine;

use App\Tests\Lib\Fixtures\CustomerFixture;
use App\Tests\Lib\Fixtures\TicketFixture;
use App\Tickets\Domain\Customer;
use App\Tickets\Domain\TicketCategory;
use App\Tickets\Infrastructure\Persistence\Doctrine\DoctrineTicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineTicketRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $em;
    private DoctrineTicketRepository $repository;
    private TicketFixture $ticketFixture;
    private CustomerFixture $customerFixture;

    private Customer $customer;
    private TicketCategory $billingCategory;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        $this->repository = $container->get(DoctrineTicketRepository::class);

        $this->ticketFixture = new TicketFixture($this->em->getConnection());
        $this->customerFixture = new CustomerFixture($this->em->getConnection());
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

        $this->em->close();
        $this->em = null;
        parent::tearDown();
    }

    public function testFindByIdReturnsItem(): void
    {
        $ticket = $this->ticketFixture->insert(['category' => $this->billingCategory, 'customer' => $this->customer]);

        $result = $this->repository->findById($ticket->id());

        $this->assertEquals($ticket, $result);
    }
}
