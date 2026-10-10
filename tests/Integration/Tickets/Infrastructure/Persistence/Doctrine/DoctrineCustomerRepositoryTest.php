<?php

declare(strict_types=1);

namespace App\Tests\Integration\Tickets\Infrastructure\Persistence\Doctrine;

use App\Tests\Lib\Fixtures\CustomerFixture;
use App\Tests\Lib\ValueGenerator\FakeValueGenerator;
use App\Tickets\Infrastructure\Persistence\Doctrine\DoctrineCustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineCustomerRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $em;
    private DoctrineCustomerRepository $repository;
    private CustomerFixture $customerFixture;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = static::getContainer();
        $this->em = $container->get(EntityManagerInterface::class);
        $this->repository = new DoctrineCustomerRepository($this->em);

        $this->customerFixture = new CustomerFixture($this->em->getConnection());
    }

    protected function tearDown(): void
    {
        $this->customerFixture->cleanup();

        $this->em->close();
        $this->em = null;
        parent::tearDown();
    }

    public function testFindByEmailReturnsItem(): void
    {
        $customer = $this->customerFixture->insert();

        $result = $this->repository->findByEmail($customer->email());

        $this->assertEquals($customer, $result);
    }

    public function testFindByEmailReturnsNullWhenNotFound(): void
    {
        $result = $this->repository->findByEmail(FakeValueGenerator::email());

        $this->assertNull($result);
    }
}
