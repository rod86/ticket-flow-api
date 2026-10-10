<?php

declare(strict_types=1);

namespace App\Tests\Lib\Utils;

use App\Tickets\Domain\Customer;
use App\Tickets\Domain\Ticket;
use Doctrine\ORM\EntityManagerInterface;

final readonly class Database
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function getTicketById(string $id): ?Ticket
    {
        return $this->em->getRepository(Ticket::class)->findOneBy(['id' => $id]);
    }

    public function getCustomerById(string $id): ?Customer
    {
        return $this->em->getRepository(Customer::class)->findOneBy(['id' => $id]);
    }
}
