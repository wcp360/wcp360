<?php
declare(strict_types=1);
namespace Wcp360Migrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Wcp360\Core\Module\Hosting\Infrastructure\ActivationGrantsSchema;
final class Version20261031000500 extends AbstractMigration
{
    public function getDescription():string{return 'Explicit isolated-activation grants; plan permission alone never authorizes Linux effects.';}
    public function up(Schema $schema):void{foreach(ActivationGrantsSchema::statements($this->connection)as $sql)$this->addSql($sql);}
    public function down(Schema $schema):void{$this->throwIrreversibleMigrationException();}
}
