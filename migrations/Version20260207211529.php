<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260207211529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `option_group` (OG_ID VARCHAR(16) NOT NULL, OG_LABEL VARCHAR(120) NOT NULL, OG_IS_REQUIRED TINYINT NOT NULL, OG_MAX_CHOICES INT NOT NULL, OG_IS_AVAILABLE TINYINT NOT NULL, OG_CREATED_AT DATETIME NOT NULL, OG_UPDATED_AT DATETIME DEFAULT NULL, OG_PRODUCT VARCHAR(16) NOT NULL, INDEX IDX_542BF9ADCBAD9E36 (OG_PRODUCT), PRIMARY KEY (OG_ID)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `option_item` (OI_ID VARCHAR(16) NOT NULL, OI_LABEL VARCHAR(120) NOT NULL, OI_PRICE_DELTA NUMERIC(17, 2) NOT NULL, OI_CREATED_AT DATETIME NOT NULL, OI_UPDATED_AT DATETIME DEFAULT NULL, OI_GROUP VARCHAR(16) NOT NULL, INDEX IDX_F7158FFDE562F4C8 (OI_GROUP), PRIMARY KEY (OI_ID)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `product` (PD_ID VARCHAR(16) NOT NULL, PD_LABEL VARCHAR(120) NOT NULL, PD_DESCRIPTION VARCHAR(255) DEFAULT NULL, PD_BASE_PRICE NUMERIC(17, 2) NOT NULL, PD_IS_AVAILABLE TINYINT NOT NULL, PD_CREATED_AT DATETIME NOT NULL, PD_UPDATED_AT DATETIME DEFAULT NULL, PD_CATEGORY VARCHAR(16) DEFAULT NULL, INDEX IDX_D34A04AD8CDE1F51 (PD_CATEGORY), PRIMARY KEY (PD_ID)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE `option_group` ADD CONSTRAINT FK_542BF9ADCBAD9E36 FOREIGN KEY (OG_PRODUCT) REFERENCES `product` (PD_ID)');
        $this->addSql('ALTER TABLE `option_item` ADD CONSTRAINT FK_F7158FFDE562F4C8 FOREIGN KEY (OI_GROUP) REFERENCES `option_group` (OG_ID)');
        $this->addSql('ALTER TABLE `product` ADD CONSTRAINT FK_D34A04AD8CDE1F51 FOREIGN KEY (PD_CATEGORY) REFERENCES `category` (CT_ID)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `option_group` DROP FOREIGN KEY FK_542BF9ADCBAD9E36');
        $this->addSql('ALTER TABLE `option_item` DROP FOREIGN KEY FK_F7158FFDE562F4C8');
        $this->addSql('ALTER TABLE `product` DROP FOREIGN KEY FK_D34A04AD8CDE1F51');
        $this->addSql('DROP TABLE `option_group`');
        $this->addSql('DROP TABLE `option_item`');
        $this->addSql('DROP TABLE `product`');
    }
}
