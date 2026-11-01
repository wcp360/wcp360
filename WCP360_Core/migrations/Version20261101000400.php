<?php

declare(strict_types=1);

namespace Wcp360Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Wcp360\Core\Module\Setup\Infrastructure\CandidateRowLockSchema;

final class Version20261101000400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Constant-zero row-lock columns allow candidate runtime locking without identity-column UPDATE grants.';
    }

    public function up(Schema $schema): void
    {
        foreach (CandidateRowLockSchema::statements() as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Runtime grants may depend on row-lock columns; use a verified recovery procedure.');
    }
}
