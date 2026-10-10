<?php

declare(strict_types=1);

namespace App\Tests\Integration\Tickets\Infrastructure\Persistence\Doctrine;

use App\Tests\Lib\ValueGenerator\FakeValueGenerator;
use App\Tickets\Domain\TicketCategory;
use App\Tickets\Infrastructure\Persistence\Doctrine\DoctrineTicketCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineTicketCategoryRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $em;
    private DoctrineTicketCategoryRepository $repository;

    private TicketCategory $billingCategory;


    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        $this->repository = new DoctrineTicketCategoryRepository($this->em);

        $this->billingCategory = new TicketCategory(
            id: '8e56a31f-fe37-4dd5-831f-edff5bd6452e',
            name: 'Billing & Payments'
        );
    }

    protected function tearDown(): void
    {
        $this->em->close();
        $this->em = null;
        parent::tearDown();
    }

    public function testFindByIdReturnsItem(): void
    {
        $result = $this->repository->findById('8e56a31f-fe37-4dd5-831f-edff5bd6452e');

        $this->assertEquals($this->billingCategory, $result);
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findById(FakeValueGenerator::uuid());

        $this->assertNull($result);
    }
}
