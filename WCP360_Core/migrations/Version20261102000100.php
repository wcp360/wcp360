<?php

declare(strict_types=1);

namespace Wcp360Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Wcp360\Core\Module\Operations\Infrastructure\PlanCorrelationSchema;

final class Version20261102000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Persist the originating request reference for candidate hosting plans and their audit events.';
    }

    public function up(Schema $schema): void
    {
        foreach (PlanCorrelationSchema::statements($this->connection) as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Preserve diagnostic history; use a reviewed recovery procedure.');
    }
}
