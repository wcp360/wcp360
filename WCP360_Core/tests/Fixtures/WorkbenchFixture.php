<?php

declare(strict_types=1);
namespace Wcp360\Core\Tests\Fixtures;
use Doctrine\DBAL\Connection;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchSchema;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
final class WorkbenchFixture
{
    public const PASSWORD='local-test-password-only-2026';
    public static function reset(Connection $db,bool $applicationServers=true,bool $usernames=true,bool $siteProfiles=true):array
    {
        $sqlite=$db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\SQLitePlatform;
        if(!$sqlite && $db->fetchOne('SELECT current_database()')!=='wcp360_test')throw new \RuntimeException('Fixture cleanup requires the dedicated wcp360_test database.');
        if($sqlite)$db->executeStatement('PRAGMA foreign_keys = ON');
        foreach(\Wcp360\Core\Module\IdentityAccess\Infrastructure\PasswordChangeSchema::fixtureDrops($db) as $sql)$db->executeStatement($sql);
        foreach(['user_sessions','admin_sessions','permission_denials','mfa_recovery','mfa','activation_grants','activations','runtime_settings','plugins','audit','jobs','sites','memberships','tenants','accounts','users'] as $table)$db->executeStatement('DROP TABLE IF EXISTS wcp_candidate_'.$table);
        foreach(array_merge(WorkbenchSchema::statements($db),WorkbenchSchema::auditStatements($db),\Wcp360\Core\Module\Operations\Infrastructure\PlanCorrelationSchema::statements($db),\Wcp360\Core\Module\IdentityAccess\Infrastructure\MfaSchema::statements($db),\Wcp360\Core\Module\IdentityAccess\Infrastructure\PermissionDenialSchema::statements($db),\Wcp360\Core\Module\IdentityAccess\Infrastructure\SessionSchema::statements($db),\Wcp360\Core\Module\Setup\Infrastructure\CandidateRowLockSchema::statements(),($applicationServers?array_merge(\Wcp360\Core\Module\Hosting\Infrastructure\ApplicationServerSchema::statements(),\Wcp360\Core\Module\Hosting\Infrastructure\ProvisioningSchema::statements($db),\Wcp360\Core\Module\Hosting\Infrastructure\ActivationGrantsSchema::statements($db),($siteProfiles?\Wcp360\Core\Module\Hosting\Infrastructure\SiteRuntimeProfileSchema::statements($db):[])):[])) as $sql)$db->executeStatement($sql);
        if($usernames){
            \Wcp360\Core\Module\IdentityAccess\Infrastructure\UsernameSchema::upgrade($db);
            foreach(\Wcp360\Core\Module\IdentityAccess\Infrastructure\PasswordChangeSchema::statements($db) as $sql)$db->executeStatement($sql);
        }
        $hash=password_hash(self::PASSWORD,PASSWORD_ARGON2ID,['memory_cost'=>8192,'time_cost'=>1]);
        $users=[];$tenants=[];
        foreach(['alice','bob','admin']as $name){
            $id=WorkbenchStore::id();$users[$name]=$id;
            $row=['id'=>$id,'email'=>$name.'@example.test','password_hash'=>$hash,'context'=>$name==='admin'?'admin':'user','active'=>1];
            if($usernames)$row['username']=$name;
            $db->insert('wcp_candidate_users',$row);
            if($name==='admin')continue;
            $account=WorkbenchStore::id();$tenant=WorkbenchStore::id();$tenants[$name]=$tenant;
            $db->insert('wcp_candidate_accounts',['id'=>$account,'name'=>$name]);
            $db->insert('wcp_candidate_tenants',['id'=>$tenant,'account_id'=>$account,'name'=>$name]);
            $db->insert('wcp_candidate_memberships',['user_id'=>$id,'tenant_id'=>$tenant,'permission'=>'candidate.hosting.plan']);
        }
        return ['users'=>$users,'tenants'=>$tenants];
    }
}
