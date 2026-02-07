<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260207115421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `category` (CT_ID VARCHAR(16) NOT NULL, CT_LABEL VARCHAR(120) NOT NULL, CT_POSITION INT NOT NULL, CT_ACTIVE TINYINT NOT NULL, CT_CREATED_AT DATETIME NOT NULL, CT_UPDATED_AT DATETIME DEFAULT NULL, CT_MENU VARCHAR(16) NOT NULL, INDEX IDX_64C19C1AF07AA5A (CT_MENU), PRIMARY KEY (CT_ID)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `menu` (MN_ID VARCHAR(16) NOT NULL, MN_LABEL VARCHAR(120) NOT NULL, MN_ACTIVE TINYINT NOT NULL, MN_CREATED_AT DATETIME NOT NULL, MN_UPDATED_AT DATETIME DEFAULT NULL, PRIMARY KEY (MN_ID)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE `category` ADD CONSTRAINT FK_64C19C1AF07AA5A FOREIGN KEY (CT_MENU) REFERENCES `menu` (MN_ID)');
        $this->addSql('ALTER TABLE platform CHANGE PL_CURRENCY PL_CURRENCY VARCHAR(3) DEFAULT \'CDF\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `category` DROP FOREIGN KEY FK_64C19C1AF07AA5A');
        $this->addSql('DROP TABLE `category`');
        $this->addSql('DROP TABLE `menu`');
        $this->addSql('ALTER TABLE `platform` CHANGE PL_CURRENCY PL_CURRENCY VARCHAR(3) DEFAULT NULL');
    }
}
