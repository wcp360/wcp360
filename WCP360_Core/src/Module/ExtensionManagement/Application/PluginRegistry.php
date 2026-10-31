<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\ExtensionManagement\Application;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
final readonly class PluginRegistry
{
    public function __construct(private WorkbenchStore $store,private string $pluginRoot) {}
    public function installed():array{return $this->store->db->fetchAllAssociative('SELECT id,version,state FROM wcp_candidate_plugins ORDER BY id');}
    public function transition(string $actor,string $id,string $action):void
    {
        $this->store->requireAdmin($actor);
        if($id!=='wcp360-reference-report' || !in_array($action,['install','enable','disable','uninstall'],true))throw new \InvalidArgumentException('Unknown candidate package or operation.');
        // No hooks, plugin includes, network fetch, dependency installation or command execution in the Core.
        $hash=null;
        if(in_array($action,['install','enable'],true)){
        $path=$this->pluginRoot.'/'.$id.'/candidate-plugin.json';
        if(is_link(dirname($path))||is_link($path)||!is_file($path)||filesize($path)>8192)throw new \RuntimeException('Invalid package source.');
        $raw=(string)file_get_contents($path);$manifest=json_decode($raw,true,8,JSON_THROW_ON_ERROR);
        if(array_keys($manifest)!==['id','version','api','mode','capabilities'] || $manifest['id']!==$id || $manifest['version']!=='0.1.0-dev' || $manifest['api']!=='wcp360.candidate.summary/1' || $manifest['mode']!=='detached' || $manifest['capabilities']!==['candidate.summary.read'])throw new \RuntimeException('Candidate manifest rejected.');
        $hash=hash('sha256',$raw);
        }
        $db=$this->store->db;
        $db->transactional(function()use($db,$actor,$id,$action,$hash):void{
            $this->store->requireAdmin($actor,true);
            $old=$db->fetchAssociative('SELECT * FROM wcp_candidate_plugins WHERE id=?',[$id]);
            if($action==='install'){
                if($old)throw new ConflictHttpException('Already installed.');
                $db->insert('wcp_candidate_plugins',['id'=>$id,'version'=>'0.1.0-dev','state'=>'DISABLED','manifest_hash'=>$hash]);
            }else{
                if(!$old)throw new ConflictHttpException('Install the package first.');
                if($action==='enable' && !hash_equals($old['manifest_hash'],$hash))throw new ConflictHttpException('Package changed; requalification required.');
                if($action==='uninstall'){
                    if($old['state']!=='DISABLED')throw new ConflictHttpException('Disable the package before uninstalling.');
                    $db->delete('wcp_candidate_plugins',['id'=>$id]);
                }else{$db->update('wcp_candidate_plugins',['state'=>$action==='enable'?'ENABLED':'DISABLED'],['id'=>$id]);}
            }
            $this->store->audit($actor,null,'candidate.plugin.'.$action,null);
        });
    }
    public function summary(string $actor,string $tenant):array
    {
        $this->store->requirePermission($actor,$tenant);
        if($this->store->db->fetchOne("SELECT state FROM wcp_candidate_plugins WHERE id='wcp360-reference-report'")!=='ENABLED')throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        $rows=$this->store->db->fetchAllAssociative('SELECT state,COUNT(*) AS total FROM wcp_candidate_jobs WHERE tenant_id=? GROUP BY state ORDER BY state',[$tenant]);
        return ['schema'=>'wcp360.candidate.summary/1','counts'=>array_map(static fn(array $r):array=>['state'=>$r['state'],'total'=>(int)$r['total']],$rows)];
    }
}
