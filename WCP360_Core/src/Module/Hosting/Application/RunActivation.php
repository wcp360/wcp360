<?php
declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Application;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Hosting\Domain\ProvisioningGateway;
final readonly class RunActivation
{
    public function __construct(private WorkbenchStore $store,private ProvisioningGateway $gateway,private SubmitActivation $availability){}
    public function runOne():?string
    {
        $db=$this->store->db;
        $row=$db->transactional(function()use($db):array|false{
            $lock=$db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform?' FOR UPDATE SKIP LOCKED':'';
            $row=$db->fetchAssociative("SELECT * FROM wcp_candidate_activations WHERE state='QUEUED' ORDER BY created_at,id LIMIT 1".$lock);
            if(!$row)return false;
            $db->executeStatement("UPDATE wcp_candidate_activations SET state='DISPATCHING' WHERE id=?",[$row['id']]);
            $this->store->audit($row['actor_id'],$row['tenant_id'],'candidate.hosting.activation.started',$row['id']);
            return $row;
        });
        if(!$row)return null;
        $state='FAILED';
        try{$request=$this->request($row,'provision_php_fpm');}catch(\Throwable){$request=null;}
        if($request!==null){
            // Any transport exception after dispatch starts is uncertain, never a safe-to-retry failure.
            try{$status=$this->gateway->send($request);$state=match($status){'ACTIVE'=>'ACTIVE','REJECTED'=>'FAILED',default=>'UNKNOWN_OUTCOME'};}
            catch(\Throwable){$state='UNKNOWN_OUTCOME';}
        }
        $this->finish($row,$state);
        return $row['id'];
    }
    public function reconcile():int
    {
        $count=0;
        $rows=$this->store->db->fetchAllAssociative("SELECT * FROM wcp_candidate_activations WHERE state IN ('DISPATCHING','UNKNOWN_OUTCOME') ORDER BY created_at,id");
        foreach($rows as $row){
            try{$status=$this->gateway->send($this->request($row,'inspect_php_fpm'));$state=$status==='ACTIVE'?'ACTIVE':'UNKNOWN_OUTCOME';}
            catch(\Throwable){$state='UNKNOWN_OUTCOME';}
            $this->finish($row,$state);++$count;
        }
        return $count;
    }
    /** @param array<string,mixed> $row
     * @return array{schema:string,operation:string,job_id:string,tenant_id:string,site_id:string,hostname:string,expected_revision:int}
     */
    private function request(array $row,string $operation):array
    {
        if(!$this->availability->enabled)throw new \RuntimeException('Provisioning disabled.');
        $this->store->requireActivationGrant($row['actor_id'],$row['tenant_id']);
        // A queued activation cannot outlive the successful, actor-owned plan that admitted it.
        // Recheck the source job at dispatch and inspection; a changed job fails closed.
        $plan=$this->store->db->fetchOne(
            "SELECT id FROM wcp_candidate_jobs WHERE id=? AND actor_id=? AND tenant_id=? AND site_id=? AND state='SUCCEEDED'",
            [$row['plan_job_id'],$row['actor_id'],$row['tenant_id'],$row['site_id']]
        );
        if(!is_string($plan))throw new \RuntimeException('Plan authority changed.');
        $site=$this->store->db->fetchAssociative('SELECT hostname,revision,application_server,state,web_stack,stack_revision FROM wcp_candidate_sites WHERE id=? AND tenant_id=?',[$row['site_id'],$row['tenant_id']]);
        if(!$site||(int)$site['revision']!==(int)$row['expected_revision']||$site['application_server']!=='php_fpm'||$site['state']!=='PLAN_READY')throw new \RuntimeException('Site revision changed.');
        \Wcp360\Core\Module\Hosting\Domain\SiteRuntimeProfile::fromStorage($site);
        return ['schema'=>'wcp360.candidate.provision/1','operation'=>$operation,'job_id'=>$row['id'],'tenant_id'=>$row['tenant_id'],'site_id'=>$row['site_id'],'hostname'=>$site['hostname'],'expected_revision'=>(int)$row['expected_revision']];
    }
    /** @param array<string,mixed> $row */
    private function finish(array $row,string $state):void
    {
        $this->store->db->transactional(function()use($row,$state):void{
            // Preserve observed physical outcome even if authorization was revoked after dispatch.
            // An unchanged UNKNOWN_OUTCOME is an inspection attempt, not a state transition.
            // Repeated reconciliation must not emit duplicate terminal-state audit events.
            $changed=$this->store->db->executeStatement(
                "UPDATE wcp_candidate_activations SET state=?,completed_at=? WHERE id=? AND state IN ('DISPATCHING','UNKNOWN_OUTCOME') AND state<>?",
                [$state,WorkbenchStore::now(),$row['id'],$state]
            );
            if($changed!==1){
                if($state==='UNKNOWN_OUTCOME' && $this->store->db->fetchOne(
                    'SELECT state FROM wcp_candidate_activations WHERE id=?',[$row['id']]
                )==='UNKNOWN_OUTCOME')return;
                throw new \RuntimeException('Concurrent activation state changed.');
            }
            $this->store->audit($row['actor_id'],$row['tenant_id'],'candidate.hosting.activation.'.strtolower($state),$row['id']);
        });
    }
}
