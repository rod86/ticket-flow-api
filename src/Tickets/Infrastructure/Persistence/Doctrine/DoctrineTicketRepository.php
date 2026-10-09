<?php

declare(strict_types=1);

namespace App\Tickets\Infrastructure\Persistence\Doctrine;

use App\Tickets\Domain\Interfaces\TicketRepositoryInterface;
use App\Tickets\Domain\Ticket;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTicketRepository implements TicketRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function create(Ticket $ticket): void
    {
        throw new \Exception('not implemented');
    }

    public function findById(string $id): ?Ticket
    {
        return $this->entityManager->getRepository(Ticket::class)->findOneBy(['id' => $id]);
    }
}
