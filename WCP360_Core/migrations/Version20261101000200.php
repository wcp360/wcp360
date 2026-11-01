<?php

declare(strict_types=1);

namespace Wcp360Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\PermissionDenialSchema;

final class Version20261101000200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Explicit revocable blocks for the existing candidate hosting-plan permission.';
    }

    public function up(Schema $schema): void
    {
        foreach (PermissionDenialSchema::statements($this->connection) as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Preserve access blocks and audit; use a verified recovery procedure.');
    }
}
