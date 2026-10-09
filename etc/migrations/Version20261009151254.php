<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261009151254 extends AbstractMigration
{
    private const array CATEGORIES = [
        '8453c4a9-35fe-463e-9a36-ffdd2a3cd72a' => 'Technical Support',
        '8e56a31f-fe37-4dd5-831f-edff5bd6452e' => 'Billing & Payments',
        '0c70250d-69de-40f5-a009-82be90b7e59e' => 'Account & Access',
        '480b74f4-873a-4b3f-87f4-d71bd1988cee' => 'Orders & Shipping',
        'c3abaf39-28ec-4f0f-aa8e-698d786c5a27' => 'Returns & Refunds',
        'd7c0e57e-df64-4845-9fd5-df687f0c67c5' => 'General Inquiry',
    ];

    public function getDescription(): string
    {
        return 'Ticket categories';
    }

    public function up(Schema $schema): void
    {
        foreach (self::CATEGORIES as $id => $name) {
            $this->addSql(
                'INSERT INTO tickets_categories (id, name) VALUES (:id, :name)',
                ['id' => $id, 'name' => $name],
            );
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'DELETE FROM tickets_categories WHERE id IN (:ids)',
            ['ids' => array_keys(self::CATEGORIES)],
            ['ids' => \Doctrine\DBAL\ArrayParameterType::STRING],
        );
    }
}
