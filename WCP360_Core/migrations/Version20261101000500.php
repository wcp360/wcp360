<?php

declare(strict_types=1);

namespace Wcp360Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\UsernameSchema;

final class Version20261101000500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Separate candidate login usernames from contact email; retain account IDs, passwords and relations.';
    }

    public function up(Schema $schema): void
    {
        foreach (UsernameSchema::statements($this->connection) as $statement) {
            $this->addSql($statement['sql'], $statement['parameters']);
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Preserve assigned login names and account identity; use a verified recovery procedure.');
    }
}
