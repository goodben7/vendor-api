<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260207110249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `platform` (PL_ID VARCHAR(16) NOT NULL, PL_NAME VARCHAR(180) DEFAULT NULL, PL_ADDRESS LONGTEXT DEFAULT NULL, PL_DESCRIPTION LONGTEXT DEFAULT NULL, PL_CURRENCY VARCHAR(3) DEFAULT NULL, PL_PAYMENT_CONFIG JSON DEFAULT NULL, PL_ACTIVE TINYINT NOT NULL, PL_CREATED_AT DATETIME NOT NULL, PL_UPDATED_AT DATETIME DEFAULT NULL, PRIMARY KEY (PL_ID)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `platform`');
    }
}
