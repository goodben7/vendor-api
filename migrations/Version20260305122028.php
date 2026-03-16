<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260305122028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tablet ADD TB_TABLET_ACCOUNT_CREATED TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user ADD US_TABLET_ACCOUNT_CREATED TINYINT DEFAULT 0 NOT NULL, CHANGE US_PHONE US_PHONE VARCHAR(120) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `tablet` DROP TB_TABLET_ACCOUNT_CREATED');
        $this->addSql('ALTER TABLE `user` DROP US_TABLET_ACCOUNT_CREATED, CHANGE US_PHONE US_PHONE VARCHAR(15) DEFAULT NULL');
    }
}
