<?php

declare(strict_types=1);

namespace App\Tests\Integration\Tickets\Infrastructure\Persistence\Doctrine;

use App\Tests\Lib\DatabaseTestCase;
use App\Tests\Lib\ValueGenerator\FakeValueGenerator;
use App\Tickets\Domain\TicketCategory;
use App\Tickets\Infrastructure\Persistence\Doctrine\DoctrineTicketCategoryRepository;

final class DoctrineTicketCategoryRepositoryTest extends DatabaseTestCase
{
    private DoctrineTicketCategoryRepository $repository;

    private TicketCategory $billingCategory;


    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new DoctrineTicketCategoryRepository($this->em);

        $this->billingCategory = new TicketCategory(
            id: '8e56a31f-fe37-4dd5-831f-edff5bd6452e',
            name: 'Billing & Payments'
        );
    }

    public function testFindByIdReturnsItem(): void
    {
        $result = $this->repository->findById($this->billingCategory->id());

        $this->assertEquals($this->billingCategory, $result);
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findById(FakeValueGenerator::uuid());

        $this->assertNull($result);
    }
}
