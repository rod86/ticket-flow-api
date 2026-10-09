<?php

declare(strict_types=1);

namespace App\Tests\Lib\Fixtures;

use App\Tests\Lib\ModelFactory\CustomerModelFactory;
use App\Tests\Lib\ModelFactory\TicketCategoryModelFactory;
use App\Tickets\Domain\Customer;
use App\Tickets\Domain\TicketCategory;
use Doctrine\DBAL\ArrayParameterType;

/**
 * @extends AbstractFixture<TicketCategory>
 */
final class TicketCategoryFixture extends AbstractFixture
{
    public function insert(array $data = []): TicketCategory
    {
        return $this->save(TicketCategoryModelFactory::create(...$data));
    }

    /** Inserts an already built model. Skipped when the model was already inserted by this fixture. */
    public function save(TicketCategory $row): TicketCategory
    {
        $this->connection->insert('tickets_categories', [
            'id' => $row->id(),
            'name' => $row->name(),
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
            'DELETE FROM tickets_categories WHERE id IN (?)',
            [$this->ids],
            [ArrayParameterType::STRING],
        );
        $this->ids = [];
    }
}
