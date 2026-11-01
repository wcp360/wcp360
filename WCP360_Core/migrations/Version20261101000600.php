<?php

declare(strict_types=1);

namespace Wcp360Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\PasswordChangeSchema;

final class Version20261101000600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create private surface-bound password-change routines; runtime table credential writes stay denied.';
    }

    public function up(Schema $schema): void
    {
        foreach (PasswordChangeSchema::statements($this->connection) as $statement) $this->addSql($statement);
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Credential capability retirement requires an explicit verified maintenance procedure.');
    }
}
