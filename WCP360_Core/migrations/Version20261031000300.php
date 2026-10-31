<?php

declare(strict_types=1);
namespace Wcp360Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Wcp360\Core\Module\Hosting\Infrastructure\ApplicationServerSchema;

final class Version20261031000300 extends AbstractMigration
{
    public function getDescription(): string { return 'Candidate per-site application server and versioned global default; existing sites remain PHP-FPM.'; }
    public function up(Schema $schema): void
    {
        foreach (ApplicationServerSchema::statements() as $sql) $this->addSql($sql);
    }
    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Preserve explicit runtime selections; use a verified candidate backup.');
    }
}
