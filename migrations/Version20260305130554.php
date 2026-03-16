<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260305130554 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity CHANGE AC_ACTIVITY AC_ACTIVITY VARCHAR(120) NOT NULL');
        $this->addSql('ALTER TABLE tablet ADD TB_USER_ID VARCHAR(16) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_TABLET_DEVICE_ID ON tablet (TB_DEVICE_ID)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `activity` CHANGE AC_ACTIVITY AC_ACTIVITY VARCHAR(20) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_TABLET_DEVICE_ID ON `tablet`');
        $this->addSql('ALTER TABLE `tablet` DROP TB_USER_ID');
    }
}
