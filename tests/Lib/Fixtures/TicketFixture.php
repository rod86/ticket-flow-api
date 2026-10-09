<?php

declare(strict_types=1);

namespace App\Tests\Lib\Fixtures;

use App\Tests\Lib\ModelFactory\CustomerModelFactory;
use App\Tests\Lib\ModelFactory\TicketModelFactory;
use App\Tickets\Domain\Ticket;
use Doctrine\DBAL\ArrayParameterType;

/**
 * @extends AbstractFixture<Ticket>
 */
final class TicketFixture extends AbstractFixture
{
    public function insert(array $data = []): Ticket
    {
        return $this->save(TicketModelFactory::create(...$data));
    }

    public function save(Ticket $row): Ticket
    {
        $this->connection->insert('tickets', [
            'id' => $row->id(),
            'subject' => $row->subject(),
            'description' => $row->description(),
            'status' => $row->status()->value,
            'category_id' => $row->category()->id(),
            'customer_id' => $row->customer()->id(),
            'created_at' => $row->createdAt()->format('Y-m-d H:i:s'),
            'updated_at' => $row->updatedAt()->format('Y-m-d H:i:s'),
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
            'DELETE FROM tickets WHERE id IN (?)',
            [$this->ids],
            [ArrayParameterType::STRING],
        );
        $this->ids = [];
    }
}
