<?php

declare(strict_types=1);

namespace App\Tickets\Infrastructure\Persistence\Doctrine;

use App\Tickets\Domain\Customer;
use App\Tickets\Domain\Interfaces\CustomerRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineCustomerRepository implements CustomerRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function findByEmail(string $email): ?Customer
    {
        return $this->entityManager->getRepository(Customer::class)->findOneBy(['email' => $email]);
    }

    public function create(Customer $customer): void
    {
        throw new \LogicException('Not implemented');
    }
}
