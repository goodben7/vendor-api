<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260222185332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` ADD OR_PLATFORM_ID VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item ADD OI_PLATFORM_ID VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_item_option ADD OO_PLATFORM_ID VARCHAR(16) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `order` DROP OR_PLATFORM_ID');
        $this->addSql('ALTER TABLE `order_item` DROP OI_PLATFORM_ID');
        $this->addSql('ALTER TABLE `order_item_option` DROP OO_PLATFORM_ID');
    }
}
