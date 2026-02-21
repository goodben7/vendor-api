<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260221194138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tablet ADD TB_STATUS VARCHAR(60) DEFAULT \'online\', ADD TB_DEVICE_MODEL VARCHAR(255) DEFAULT NULL, ADD TB_MODE VARCHAR(60) DEFAULT NULL, ADD TP_PLATFORM_ID VARCHAR(16) DEFAULT NULL, ADD TP_DELETED TINYINT DEFAULT 0 NOT NULL, CHANGE TB_TABLE TB_TABLE VARCHAR(16) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `tablet` DROP TB_STATUS, DROP TB_DEVICE_MODEL, DROP TB_MODE, DROP TP_PLATFORM_ID, DROP TP_DELETED, CHANGE TB_TABLE TB_TABLE VARCHAR(16) NOT NULL');
    }
}
