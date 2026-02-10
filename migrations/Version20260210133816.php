<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260210133816 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `payment` (PA_ID VARCHAR(16) NOT NULL, PA_AMOUNT NUMERIC(17, 2) NOT NULL, PA_METHOD VARCHAR(30) NOT NULL, PA_PROVIDER VARCHAR(255) DEFAULT NULL, PA_TRANSACTION_REF VARCHAR(255) DEFAULT NULL, PA_STATUS VARCHAR(1) NOT NULL, PA_RAW_RESPONSE_JSON JSON DEFAULT NULL, PA_PAID_AT DATETIME NOT NULL, PA_CREATED_AT DATETIME NOT NULL, PA_UPDATED_AT DATETIME DEFAULT NULL, PA_ORDER VARCHAR(16) NOT NULL, INDEX IDX_6D28840DEBFF4E75 (PA_ORDER), PRIMARY KEY (PA_ID)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE `payment` ADD CONSTRAINT FK_6D28840DEBFF4E75 FOREIGN KEY (PA_ORDER) REFERENCES `order` (OR_ID)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `payment` DROP FOREIGN KEY FK_6D28840DEBFF4E75');
        $this->addSql('DROP TABLE `payment`');
    }
}
