<?php
declare(strict_types=1);
namespace Wcp360Migrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Wcp360\Core\Module\Hosting\Infrastructure\ProvisioningSchema;
final class Version20261031000400 extends AbstractMigration
{
    public function getDescription():string{return 'Isolated-container PHP-FPM activation records; not canonical production provisioning.';}
    public function up(Schema $schema):void{foreach(ProvisioningSchema::statements($this->connection)as $sql)$this->addSql($sql);}
    public function down(Schema $schema):void{$this->throwIrreversibleMigrationException();}
}
