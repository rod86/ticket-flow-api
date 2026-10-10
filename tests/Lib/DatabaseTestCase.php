<?php

declare(strict_types=1);

namespace App\Tests\Lib;

use App\Tickets\Domain\Customer;
use App\Tickets\Domain\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class DatabaseTestCase extends KernelTestCase
{
    protected ?EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
    }

    protected function tearDown(): void
    {
        $this->em->close();
        $this->em = null;
        parent::tearDown();
    }

    protected function getTicketById(string $id): ?Ticket
    {
        return $this->em->getRepository(Ticket::class)->findOneBy(['id' => $id]);
    }

    protected function getCustomerById(string $id): ?Customer
    {
        return $this->em->getRepository(Customer::class)->findOneBy(['id' => $id]);
    }
}
