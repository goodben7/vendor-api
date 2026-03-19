<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260227000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create exchange_rate table (missing baseline for subsequent migrations)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE exchange_rate (EX_ID INT AUTO_INCREMENT NOT NULL, EX_BASE_CUR_ID VARCHAR(16) NOT NULL, EX_TARGET_CUR_ID VARCHAR(16) NOT NULL, EX_RATE NUMERIC(17, 2) NOT NULL, EX_PLATFORM_ID VARCHAR(16) DEFAULT NULL, EX_CREATED_AT DATETIME NOT NULL, EX_ACTIVE TINYINT NOT NULL, PRIMARY KEY (EX_ID)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE INDEX IDX_E9521FAB2675C2F6 ON exchange_rate (EX_BASE_CUR_ID)');
        $this->addSql('CREATE INDEX IDX_E9521FAB9772C11 ON exchange_rate (EX_TARGET_CUR_ID)');
        $this->addSql('CREATE INDEX IDX_E9521FAB5DCEEC8F ON exchange_rate (EX_PLATFORM_ID)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE exchange_rate');
    }
}

