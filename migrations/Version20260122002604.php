<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260122002604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_lines ADD product_variant_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_lines ADD CONSTRAINT FK_CC9FF86BA80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('CREATE INDEX IDX_CC9FF86BA80EF684 ON order_lines (product_variant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_lines DROP FOREIGN KEY FK_CC9FF86BA80EF684');
        $this->addSql('DROP INDEX IDX_CC9FF86BA80EF684 ON order_lines');
        $this->addSql('ALTER TABLE order_lines DROP product_variant_id');
    }
}
