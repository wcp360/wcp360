<?php

declare(strict_types=1);

namespace Wcp360Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\SessionSchema;

final class Version20261101000300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Separate durable candidate USER and ADMIN session registries with verifier-only persistence.';
    }

    public function up(Schema $schema): void
    {
        foreach (SessionSchema::statements($this->connection) as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Preserve session revocations and audit; use a verified recovery procedure.');
    }
}
