<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Operations\Application;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Hosting\Infrastructure\GoPlanRenderer;
final readonly class RunHostingPlan
{
    public function __construct(private WorkbenchStore $store,private GoPlanRenderer $renderer) {}
    public function runOne(): ?string
    {
        $db=$this->store->db;
        $job=$db->transactional(function()use($db):array|false{
            $lock=$db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform ? ' FOR UPDATE SKIP LOCKED' : '';
            $row=$db->fetchAssociative("SELECT * FROM wcp_candidate_jobs WHERE state='QUEUED' ORDER BY created_at,id LIMIT 1".$lock);
            if(!$row)return false;
            $db->executeStatement("UPDATE wcp_candidate_jobs SET state='RUNNING',started_at=? WHERE id=? AND state='QUEUED'",[WorkbenchStore::now(),$row['id']]);
            $this->store->audit($row['actor_id'],$row['tenant_id'],'candidate.hosting.plan.started',$row['id']);
            return $row;
        });
        if(!$job)return null;
        $state='SUCCEEDED'; $result=null;
        try{
            $this->store->requirePermission($job['actor_id'],$job['tenant_id']);
            $host=$db->fetchOne('SELECT hostname FROM wcp_candidate_sites WHERE id=? AND tenant_id=?',[$job['site_id'],$job['tenant_id']]);
            $result=json_encode($this->renderer->render($job['site_id'],$host),JSON_THROW_ON_ERROR);
        }catch(\Throwable){$state='FAILED';}
        $db->transactional(function()use($db,$job,$state,$result):void{
            // Recheck revocation before publishing even this non-privileged plan.
            if($state==='SUCCEEDED') $this->store->requirePermission($job['actor_id'],$job['tenant_id']);
            $changed=$db->executeStatement("UPDATE wcp_candidate_jobs SET state=?,result=?,completed_at=? WHERE id=? AND state='RUNNING'",[$state,$result,WorkbenchStore::now(),$job['id']]);
            if($changed!==1) throw new \RuntimeException('Job state changed; result publication refused.');
            $db->executeStatement('UPDATE wcp_candidate_sites SET state=?,revision=revision+1 WHERE id=? AND tenant_id=?',[$state==='SUCCEEDED'?'PLAN_READY':'PLAN_FAILED',$job['site_id'],$job['tenant_id']]);
            $this->store->audit($job['actor_id'],$job['tenant_id'],'candidate.hosting.plan.'.strtolower($state),$job['id']);
        });
        return $job['id'];
    }
    public function markInterrupted(): int
    {
        // No blind replay. A crashed RUNNING job remains explicit until operator reconciliation.
        return $this->store->db->transactional(function():int{
            $rows=$this->store->db->fetchAllAssociative("SELECT id,actor_id,tenant_id FROM wcp_candidate_jobs WHERE state='RUNNING'");
            foreach($rows as $row){$this->store->db->executeStatement("UPDATE wcp_candidate_jobs SET state='UNKNOWN_OUTCOME' WHERE id=? AND state='RUNNING'",[$row['id']]);$this->store->audit($row['actor_id'],$row['tenant_id'],'candidate.hosting.plan.interrupted',$row['id']);}
            return count($rows);
        });
    }
}
