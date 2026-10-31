<?php
declare(strict_types=1);
require '/opt/wcp360/WCP360_Core/vendor/autoload.php';
use Wcp360\Core\Kernel;
use Wcp360\Core\Tests\Fixtures\WorkbenchFixture;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Operations\Application\RunHostingPlan;
use Wcp360\Core\Module\Hosting\Application\{SubmitHostingPlan,SubmitActivation,RunActivation};
if(PHP_OS_FAMILY!=='Linux'||getenv('WCP_ISOLATED_TEST_CONTAINER')!=='1'||!is_file('/.dockerenv'))throw new RuntimeException('Isolated test container required.');
$kernel=new Kernel('test',true);$kernel->boot();$container=$kernel->getContainer()->get('test.service_container');
$store=$container->get(WorkbenchStore::class);
switch($argv[1]??''){
case 'seed':
    $fixture=WorkbenchFixture::reset($store->db);$requests=[];
    foreach(['alice','bob']as $name){
        $actor=$fixture['users'][$name];$tenant=$fixture['tenants'][$name];
        $plan=$container->get(SubmitHostingPlan::class)->submit($actor,['tenant_id'=>$tenant,'hostname'=>$name.'.test','idempotency_key'=>'provision-fixture-'.$name]);
        $container->get(RunHostingPlan::class)->runOne();
        $activation=$container->get(SubmitActivation::class)->submit($actor,$plan['job_id']);
        $job=$store->job($actor,$plan['job_id']);
        $requests[]=['schema'=>'wcp360.candidate.provision/1','operation'=>'provision_php_fpm','job_id'=>$activation,'tenant_id'=>$tenant,'site_id'=>$job['site_id'],'hostname'=>$name.'.test','expected_revision'=>2];
    }
    file_put_contents('/tmp/wcp-provision-requests.json',json_encode($requests,JSON_THROW_ON_ERROR));
    break;
case 'run':
    $worker=$container->get(RunActivation::class);$worker->runOne();$worker->runOne();
    if((int)$store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_activations WHERE state='ACTIVE'")!==2)throw new RuntimeException('Both physical sites must be confirmed ACTIVE.');
    if($worker->runOne()!==null)throw new RuntimeException('Duplicate activation dispatched.');
    break;
case 'reconcile':
    $store->db->executeStatement("UPDATE wcp_candidate_activations SET state='UNKNOWN_OUTCOME'");
    $container->get(RunActivation::class)->reconcile();
    if((int)$store->db->fetchOne("SELECT COUNT(*) FROM wcp_candidate_activations WHERE state='ACTIVE'")!==2)throw new RuntimeException('Inspection failed to confirm the existing sites.');
    break;
default:throw new RuntimeException('Unknown fixture mode.');
}
$kernel->shutdown();
