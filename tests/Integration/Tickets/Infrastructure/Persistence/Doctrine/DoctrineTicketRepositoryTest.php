<?php

declare(strict_types=1);

namespace App\Tests\Integration\Tickets\Infrastructure\Persistence\Doctrine;

use App\Tests\Lib\Fixtures\CustomerFixture;
use App\Tests\Lib\Fixtures\TicketCategoryFixture;
use App\Tests\Lib\Fixtures\TicketFixture;
use App\Tickets\Domain\Customer;
use App\Tickets\Domain\TicketCategory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineTicketRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $em;
    // private DoctrineTicketRepository $repository;
    private TicketFixture $ticketFixture;
    private CustomerFixture $customerFixture;
    private TicketCategoryFixture $categoryFixture;

    private Customer $customer;
    private TicketCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        //$this->repository = $container->get(DoctrineTicketRepository::class);

        $this->ticketFixture = new TicketFixture($this->em->getConnection());
        $this->customerFixture = new CustomerFixture($this->em->getConnection());
        $this->categoryFixture = new TicketCategoryFixture($this->em->getConnection());
        $this->category = $this->categoryFixture->insert();
        $this->customer = $this->customerFixture->insert();
    }

    protected function tearDown(): void
    {
        $this->ticketFixture->cleanup();
        $this->categoryFixture->cleanup();
        $this->customerFixture->cleanup();

        $this->em->close();
        $this->em = null;
        parent::tearDown();
    }

    public function testInsert(): void
    {
        $customer = $this->ticketFixture->insert(['category' => $this->category, 'customer' => $this->customer]);
        $this->assertNull(null);
    }
}
