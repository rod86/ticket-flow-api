<?php

declare(strict_types=1);

namespace App\Tests\Lib\Fixtures;

use App\Tests\Lib\ModelFactory\CustomerModelFactory;
use App\Tickets\Domain\Customer;
use Doctrine\DBAL\ArrayParameterType;

/**
 * @extends AbstractFixture<Customer>
 */
final class CustomerFixture extends AbstractFixture
{
    public function insert(array $data = []): Customer
    {
        return $this->save(CustomerModelFactory::create(...$data));
    }

    /** Inserts an already built model. Skipped when the model was already inserted by this fixture. */
    public function save(Customer $row): Customer
    {
        $this->connection->insert('customers', [
            'id' => $row->id(),
            'name' => $row->name(),
            'email' => $row->email(),
            'created_at' => $row->createdAt()->format('Y-m-d H:i:s'),
        ]);
        $this->register($row->id());

        return $row;
    }

    public function cleanup(): void
    {
        if ($this->ids === []) {
            return;
        }

        $this->connection->executeStatement(
            'DELETE FROM customers WHERE id IN (?)',
            [$this->ids],
            [ArrayParameterType::STRING],
        );
        $this->ids = [];
    }
}
