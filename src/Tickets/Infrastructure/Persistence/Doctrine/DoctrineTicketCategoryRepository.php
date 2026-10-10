<?php

declare(strict_types=1);

namespace App\Tickets\Infrastructure\Persistence\Doctrine;

use App\Tickets\Domain\Interfaces\TicketCategoryRepositoryInterface;
use App\Tickets\Domain\TicketCategory;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTicketCategoryRepository implements TicketCategoryRepositoryInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function findById(string $id): ?TicketCategory
    {
        return $this->entityManager->getRepository(TicketCategory::class)->find($id);
    }
}
