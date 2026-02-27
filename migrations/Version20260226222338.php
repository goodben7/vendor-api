<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260226222338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `currency` (CY_ID VARCHAR(16) NOT NULL, CY_CODE VARCHAR(3) NOT NULL, CY_LABEL VARCHAR(255) DEFAULT NULL, CY_SYMBOL VARCHAR(6) NOT NULL, CY_ACTIVE TINYINT NOT NULL, CY_PLATFORM_ID VARCHAR(16) DEFAULT NULL, CY_CREATED_AT DATETIME NOT NULL, CY_UPDATED_AT DATETIME DEFAULT NULL, PRIMARY KEY (CY_ID)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE `currency`');
    }
}
