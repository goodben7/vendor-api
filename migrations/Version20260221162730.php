<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260221162730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE platform ADD PL_PHONE VARCHAR(30) DEFAULT NULL, ADD PL_EMAIL VARCHAR(180) DEFAULT NULL, ADD PL_ALLOW_TABLE_MANAGEMENT TINYINT DEFAULT 1 NOT NULL, ADD PL_ALLOW_ONLINE_ORDER TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user ADD US_PLATFORM_ID VARCHAR(16) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `platform` DROP PL_PHONE, DROP PL_EMAIL, DROP PL_ALLOW_TABLE_MANAGEMENT, DROP PL_ALLOW_ONLINE_ORDER');
        $this->addSql('ALTER TABLE `user` DROP US_PLATFORM_ID');
    }
}
