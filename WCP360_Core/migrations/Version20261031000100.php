<?php

declare(strict_types=1);
namespace Wcp360Migrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchSchema;
final class Version20261031000100 extends AbstractMigration
{
    public function getDescription(): string { return 'Experimental workbench tables only; no canonical production schema.'; }
    public function up(Schema $schema): void { foreach (WorkbenchSchema::statements($this->connection) as $sql) $this->addSql($sql); }
    public function down(Schema $schema): void { $this->throwIrreversibleMigrationException('Preserve workbench audit and state; restore a verified backup instead.'); }
}
