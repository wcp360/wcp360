<?php

declare(strict_types=1);

namespace Wcp360Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\MfaSchema;

final class Version20261101000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Candidate administrator MFA with encrypted factors, durable throttles and single-use recovery verifiers.';
    }

    public function up(Schema $schema): void
    {
        foreach (MfaSchema::statements($this->connection) as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Preserve authentication revisions and audit; use a verified recovery procedure.');
    }
}
