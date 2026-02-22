<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260222195536 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE platform ADD PL_ADMIN_ACCOUNT_CREATED TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE user ADD US_ADMIN_ACCOUNT_CREATED TINYINT DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `platform` DROP PL_ADMIN_ACCOUNT_CREATED');
        $this->addSql('ALTER TABLE `user` DROP US_ADMIN_ACCOUNT_CREATED');
    }
}
