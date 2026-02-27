<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227014006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency ADD CY_IS_DEFAULT TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE platform DROP PL_CURRENCY');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `currency` DROP CY_IS_DEFAULT');
        $this->addSql('ALTER TABLE `platform` ADD PL_CURRENCY VARCHAR(3) DEFAULT \'CDF\'');
    }
}
