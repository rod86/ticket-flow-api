<?php

declare(strict_types=1);

namespace App\Tests\Integration\Tickets\Infrastructure\Persistence\Doctrine;

use App\Tests\Lib\DatabaseTestCase;
use App\Tests\Lib\Fixtures\CustomerFixture;
use App\Tests\Lib\ModelFactory\CustomerModelFactory;
use App\Tests\Lib\ValueGenerator\FakeValueGenerator;
use App\Tickets\Infrastructure\Persistence\Doctrine\DoctrineCustomerRepository;

final class DoctrineCustomerRepositoryTest extends DatabaseTestCase
{
    private DoctrineCustomerRepository $repository;
    private CustomerFixture $customerFixture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new DoctrineCustomerRepository($this->em);
        $this->customerFixture = new CustomerFixture($this->em->getConnection());
    }

    protected function tearDown(): void
    {
        $this->customerFixture->cleanup();

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

    public function testCreatesCustomer(): void
    {
        $customer = CustomerModelFactory::create();
        $this->customerFixture->register($customer->id());

        $this->repository->create($customer);

        $result = $this->getCustomerById($customer->id());
        $this->assertEquals($customer, $result);
    }
}
