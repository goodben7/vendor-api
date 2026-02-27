<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227015758 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE platform ADD PL_CURRENCY VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE platform ADD CONSTRAINT FK_3952D0CBF013CE5B FOREIGN KEY (PL_CURRENCY) REFERENCES `currency` (CY_ID)');
        $this->addSql('CREATE INDEX IDX_3952D0CBF013CE5B ON platform (PL_CURRENCY)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `platform` DROP FOREIGN KEY FK_3952D0CBF013CE5B');
        $this->addSql('DROP INDEX IDX_3952D0CBF013CE5B ON `platform`');
        $this->addSql('ALTER TABLE `platform` DROP PL_CURRENCY');
    }
}
