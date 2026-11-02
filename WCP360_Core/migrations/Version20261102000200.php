<?php

declare(strict_types=1);

namespace Wcp360Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Wcp360\Core\Module\Hosting\Infrastructure\SiteRuntimeProfileSchema;

final class Version20261102000200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Persist explicit candidate site topology and revision without reinterpreting existing Worker plans.';
    }

    public function up(Schema $schema): void
    {
        foreach (SiteRuntimeProfileSchema::statements($this->connection) as $statement) $this->addSql($statement);
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Preserve site topology metadata; use a reviewed recovery procedure.');
    }
}
