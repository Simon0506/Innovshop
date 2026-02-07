<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260121124118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE product_variant (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, price DOUBLE PRECISION NOT NULL, stock INT NOT NULL, sku VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_209AA41D4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_variant_option (id INT AUTO_INCREMENT NOT NULL, product_variant_id INT DEFAULT NULL, option_value_id INT NOT NULL, INDEX IDX_1CB5D94AA80EF684 (product_variant_id), INDEX IDX_1CB5D94AD957CA06 (option_value_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_variant ADD CONSTRAINT FK_209AA41D4584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE product_variant_option ADD CONSTRAINT FK_1CB5D94AA80EF684 FOREIGN KEY (product_variant_id) REFERENCES product_variant (id)');
        $this->addSql('ALTER TABLE product_variant_option ADD CONSTRAINT FK_1CB5D94AD957CA06 FOREIGN KEY (option_value_id) REFERENCES option_value (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_variant DROP FOREIGN KEY FK_209AA41D4584665A');
        $this->addSql('ALTER TABLE product_variant_option DROP FOREIGN KEY FK_1CB5D94AA80EF684');
        $this->addSql('ALTER TABLE product_variant_option DROP FOREIGN KEY FK_1CB5D94AD957CA06');
        $this->addSql('DROP TABLE product_variant');
        $this->addSql('DROP TABLE product_variant_option');
    }
}
