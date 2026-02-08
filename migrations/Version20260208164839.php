<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260208164839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `platform_table` (PT_ID VARCHAR(16) NOT NULL, PT_LABEL VARCHAR(120) NOT NULL, PT_ACTIVE TINYINT NOT NULL, PT_CREATED_AT DATETIME NOT NULL, PT_UPDATED_AT DATETIME DEFAULT NULL, PRIMARY KEY (PT_ID)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `tablet` (TB_ID VARCHAR(16) NOT NULL, TB_LABEL VARCHAR(255) DEFAULT NULL, TB_DEVICE_ID VARCHAR(255) DEFAULT NULL, TB_LAST_HEARTBEAT DATETIME DEFAULT NULL, TB_ACTIVE TINYINT NOT NULL, TB_CREATED_AT DATETIME NOT NULL, TB_UPDATED_AT DATETIME DEFAULT NULL, TB_TABLE VARCHAR(16) NOT NULL, INDEX IDX_1A2397825D5D46CC (TB_TABLE), PRIMARY KEY (TB_ID)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE `tablet` ADD CONSTRAINT FK_1A2397825D5D46CC FOREIGN KEY (TB_TABLE) REFERENCES `platform_table` (PT_ID)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `tablet` DROP FOREIGN KEY FK_1A2397825D5D46CC');
        $this->addSql('DROP TABLE `platform_table`');
        $this->addSql('DROP TABLE `tablet`');
    }
}
