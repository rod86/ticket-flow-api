<?php

declare(strict_types=1);

namespace App\Tests\Lib\Utils;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class Database
{
    public function __construct(
        private Connection $connection
    ) {
    }

    /**
     * @return array<string, mixed>|null
     * @throws Exception
     */
    public function getTicketById(string $id): ?array
    {
        $row = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('tickets')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        return $row === false ? null : $row;
    }
}
