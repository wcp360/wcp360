<?php
declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Application;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
final readonly class SubmitActivation
{
    public function __construct(private WorkbenchStore $store,public bool $enabled=false){}
    public function submit(string $actor,string $planId):string
    {
        if(!$this->enabled)throw new AccessDeniedHttpException('Isolated provisioning is disabled.');
        $plan=$this->store->job($actor,$planId);
        $this->store->requireActivationGrant($actor,$plan['tenant_id']);
        return $this->store->db->transactional(function()use($actor,$planId,$plan):string{
            $db=$this->store->db;
            $lock=$db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform?' FOR UPDATE':'';
            $site=$db->fetchAssociative('SELECT * FROM wcp_candidate_sites WHERE id=? AND tenant_id=?'.$lock,[$plan['site_id'],$plan['tenant_id']]);
            $this->store->requireActivationGrant($actor,$plan['tenant_id']);
            if(!$site||$site['application_server']!=='php_fpm'||$site['state']!=='PLAN_READY'||(int)$site['revision']!==2||$plan['state']!=='SUCCEEDED')throw new ConflictHttpException('A successful initial PHP-FPM plan is required.');
            try {
                \Wcp360\Core\Module\Hosting\Domain\SiteRuntimeProfile::fromStorage($site);
            } catch (\InvalidArgumentException) {
                throw new ConflictHttpException('A valid Classic runtime profile is required.');
            }
            $old=$db->fetchOne('SELECT id FROM wcp_candidate_activations WHERE plan_job_id=?',[$planId]);
            if(is_string($old))return $old;
            $id=WorkbenchStore::id();
            $db->insert('wcp_candidate_activations',['id'=>$id,'plan_job_id'=>$planId,'tenant_id'=>$plan['tenant_id'],'site_id'=>$plan['site_id'],'actor_id'=>$actor,'expected_revision'=>2,'state'=>'QUEUED','created_at'=>WorkbenchStore::now()]);
            $this->store->audit($actor,$plan['tenant_id'],'candidate.hosting.activation.queued',$id);
            return $id;
        });
    }
}
