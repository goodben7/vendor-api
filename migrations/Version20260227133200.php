<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227133200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exchange_rate ADD CONSTRAINT FK_E9521FAB2675C2F6 FOREIGN KEY (EX_BASE_CUR_ID) REFERENCES `currency` (CY_ID)');
        $this->addSql('ALTER TABLE exchange_rate ADD CONSTRAINT FK_E9521FAB9772C11 FOREIGN KEY (EX_TARGET_CUR_ID) REFERENCES `currency` (CY_ID)');
        $this->addSql('ALTER TABLE payment ADD PA_EX_RATE_USED NUMERIC(17, 6) DEFAULT NULL, ADD PA_PAID_AMOUNT NUMERIC(17, 2) DEFAULT NULL, ADD PA_CURRENCY VARCHAR(16) DEFAULT NULL, ADD PA_PAID_CURRENCY VARCHAR(16) DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D5ED45EB FOREIGN KEY (PA_CURRENCY) REFERENCES `currency` (CY_ID)');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DD9C9552C FOREIGN KEY (PA_PAID_CURRENCY) REFERENCES `currency` (CY_ID)');
        $this->addSql('CREATE INDEX IDX_6D28840D5ED45EB ON payment (PA_CURRENCY)');
        $this->addSql('CREATE INDEX IDX_6D28840DD9C9552C ON payment (PA_PAID_CURRENCY)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exchange_rate DROP FOREIGN KEY FK_E9521FAB2675C2F6');
        $this->addSql('ALTER TABLE exchange_rate DROP FOREIGN KEY FK_E9521FAB9772C11');
        $this->addSql('ALTER TABLE `payment` DROP FOREIGN KEY FK_6D28840D5ED45EB');
        $this->addSql('ALTER TABLE `payment` DROP FOREIGN KEY FK_6D28840DD9C9552C');
        $this->addSql('DROP INDEX IDX_6D28840D5ED45EB ON `payment`');
        $this->addSql('DROP INDEX IDX_6D28840DD9C9552C ON `payment`');
        $this->addSql('ALTER TABLE `payment` DROP PA_EX_RATE_USED, DROP PA_PAID_AMOUNT, DROP PA_CURRENCY, DROP PA_PAID_CURRENCY');
    }
}
