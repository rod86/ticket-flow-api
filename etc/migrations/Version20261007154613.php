<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261007154613 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Tickets context tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE customers (
              id UUID NOT NULL,
              name VARCHAR(100) NOT NULL,
              email VARCHAR(100) NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              PRIMARY KEY (id)
            )
        SQL);
        $this->addSql('CREATE UNIQUE INDEX uniq_customers_email ON customers (email)');
        $this->addSql(<<<'SQL'
            CREATE TABLE tickets (
              id UUID NOT NULL,
              subject VARCHAR(255) NOT NULL,
              description TEXT NOT NULL,
              status VARCHAR(20) NOT NULL,
              category_id UUID NOT NULL,
              customer_id UUID NOT NULL,
              created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
              PRIMARY KEY (id)
            )
        SQL);
        $this->addSql('CREATE INDEX IDX_54469DF412469DE2 ON tickets (category_id)');
        $this->addSql('CREATE INDEX IDX_54469DF49395C3F3 ON tickets (customer_id)');
        $this->addSql(<<<'SQL'
            CREATE TABLE tickets_categories (
              id UUID NOT NULL,
              name VARCHAR(50) NOT NULL,
              PRIMARY KEY (id)
            )
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              tickets
            ADD
              CONSTRAINT FK_54469DF412469DE2 FOREIGN KEY (category_id) REFERENCES tickets_categories (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE
              tickets
            ADD
              CONSTRAINT FK_54469DF49395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tickets DROP CONSTRAINT FK_54469DF412469DE2');
        $this->addSql('ALTER TABLE tickets DROP CONSTRAINT FK_54469DF49395C3F3');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE tickets');
        $this->addSql('DROP TABLE tickets_categories');
    }
}
