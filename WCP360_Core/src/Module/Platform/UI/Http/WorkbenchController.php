<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Platform\UI\Http;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Hosting\Application\SubmitHostingPlan;
use Wcp360\Core\Module\Hosting\Application\SubmitActivation;
use Wcp360\Core\Module\Hosting\Application\ApplicationServerPolicy;
use Wcp360\Core\Module\Platform\Application\ApplicationServerSettings;
use Wcp360\Core\Module\ExtensionManagement\Application\PluginRegistry;
use Wcp360\Core\Shared\Security\RuntimeContext;
final class WorkbenchController extends AbstractController
{
    #[Route('/health',name:'wcp_health',methods:['GET'])]
    public function health(WorkbenchStore $store):JsonResponse
    {
        try{$store->db->fetchOne('SELECT 1');return $this->json(['status'=>'ok','mode'=>'development-candidate']);}
        catch(\Throwable){return $this->json(['status'=>'unavailable'],503);}
    }
    #[Route('/login',name:'wcp_login',methods:['GET','POST'])]
    public function login(AuthenticationUtils $auth,RuntimeContext $runtime,\Wcp360\Core\Module\IdentityAccess\Application\CompletePasswordRecovery $recovery):Response
    {
        if($this->getUser()) {
            if($runtime->context==='admin' && $auth->getLastAuthenticationError(false)!==null)return $this->redirectToRoute('wcp_account_reauthenticate');
            return $this->redirectToRoute('wcp_dashboard');
        }
        return $this->render('setup/login.html.twig',['context'=>$runtime->context,'error'=>$auth->getLastAuthenticationError(),'recovery_available'=>$recovery->available()]);
    }
    #[Route('/logout',name:'wcp_logout',methods:['POST'])]
    public function logout():never{throw new \LogicException('Handled by Symfony Security.');}
    #[Route('/',name:'wcp_dashboard',methods:['GET'])]
    public function index(Request $request,WorkbenchStore $store,RuntimeContext $runtime,PluginRegistry $plugins,ApplicationServerSettings $settings,ApplicationServerPolicy $policy):Response
    {
        $user=$this->getUser();if(!$user instanceof Principal)throw $this->createAccessDeniedException();
        $tenants=$runtime->context==='user'?$store->tenants($user->id):[];
        $tenant=$request->query->getString('tenant',$tenants[0]['id']??'');
        $jobs=$tenant!==''?$store->jobs($user->id,$tenant):[];
        return $this->render('user/dashboard.html.twig',['idempotency_key'=>WorkbenchStore::id(),'context'=>$runtime->context,'tenants'=>$tenants,'tenant'=>$tenant,'jobs'=>$jobs,'runtime_settings'=>$settings->current(),'worker_plans_enabled'=>$policy->workerPlansEnabled,'plugins'=>$runtime->context==='admin'?$plugins->installed():[]]);
    }

    #[Route('/workbench/settings/application-server',name:'wcp_application_server',methods:['POST'])]
    public function applicationServer(Request $request,ApplicationServerSettings $settings):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if(!$this->isCsrfTokenValid('application-server',$request->request->getString('_token')))throw $this->createAccessDeniedException('Invalid CSRF token.');
        $fields=$request->request->all();
        if(array_diff(array_keys($fields),['_token','application_server','revision']) || !isset($fields['application_server'],$fields['revision']) || !is_string($fields['application_server']) || !is_string($fields['revision']) || !preg_match('/^[1-9][0-9]{0,8}$/D',$fields['revision']))throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('Invalid runtime settings.');
        $user=$this->getUser();if(!$user instanceof Principal)throw $this->createAccessDeniedException();
        $settings->change($user->id,$fields['application_server'],(int)$fields['revision']);
        return $this->redirectToRoute('wcp_dashboard',[],303);
    }
    #[Route('/workbench/plans',name:'wcp_plan',methods:['POST'])]
    public function submit(Request $request,SubmitHostingPlan $submit):Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if(!$this->isCsrfTokenValid('hosting-plan',$request->request->getString('_token')))throw $this->createAccessDeniedException('Invalid CSRF token.');
        $fields=$request->request->all();unset($fields['_token']);
        $user=$this->getUser();if(!$user instanceof Principal)throw $this->createAccessDeniedException();
        $result=$submit->submit($user->id,$fields);
        return $this->redirectToRoute('wcp_job',['id'=>$result['job_id']],303);
    }
    #[Route('/workbench/v1/plans',name:'wcp_api_plan',methods:['POST'])]
    public function apiSubmit(Request $request,SubmitHostingPlan $submit):JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if($request->headers->get('Origin')!==$request->getSchemeAndHttpHost() || !$this->isCsrfTokenValid('hosting-plan',$request->headers->get('X-CSRF-Token')))throw $this->createAccessDeniedException('Origin and CSRF required.');
        if($request->getContentTypeFormat()!=='json' || strlen($request->getContent())>4096)throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException('A bounded JSON body is required.');
        $user=$this->getUser();if(!$user instanceof Principal)throw $this->createAccessDeniedException();
        return $this->json($submit->submit($user->id,$request->toArray()),202);
    }
    #[Route('/workbench/jobs/{id}',name:'wcp_job',methods:['GET'])]
    public function job(string $id,WorkbenchStore $store,RuntimeContext $runtime,SubmitActivation $activations):Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user=$this->getUser();if(!$user instanceof Principal)throw $this->createAccessDeniedException();
        $job=$store->job($user->id,$id);$allowed=false;
        if($activations->enabled){try{$store->requireActivationGrant($user->id,$job['tenant_id']);$allowed=true;}catch(\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException){}}
        return $this->render('user/job.html.twig',['context'=>$runtime->context,'job'=>$job,'activation'=>$store->db->fetchAssociative('SELECT id,state FROM wcp_candidate_activations WHERE plan_job_id=?',[$id]),'provisioning_enabled'=>$allowed]);
    }
    #[Route('/workbench/jobs/{id}/activate',name:'wcp_activate',methods:['POST'])]
    public function activate(string $id,Request $request,SubmitActivation $activations):Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if(array_keys($request->request->all())!==['_token']||!$this->isCsrfTokenValid('activate-'.$id,$request->request->getString('_token')))throw $this->createAccessDeniedException('Invalid activation request.');
        $user=$this->getUser();if(!$user instanceof Principal)throw $this->createAccessDeniedException();
        $activations->submit($user->id,$id);
        return $this->redirectToRoute('wcp_job',['id'=>$id],303);
    }
    #[Route('/workbench/plugins/{action}',name:'wcp_plugin',requirements:['action'=>'install|enable|disable|uninstall'],methods:['POST'])]
    public function plugin(string $action,Request $request,PluginRegistry $plugins):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if(!$this->isCsrfTokenValid('plugin-'.$action,$request->request->getString('_token')))throw $this->createAccessDeniedException('Invalid CSRF token.');
        $user=$this->getUser();if(!$user instanceof Principal)throw $this->createAccessDeniedException();
        $plugins->transition($user->id,'wcp360-reference-report',$action);
        return $this->redirectToRoute('wcp_dashboard',[],303);
    }
    #[Route('/workbench/v1/summary/{tenant}',name:'wcp_summary',methods:['GET'])]
    public function summary(string $tenant,PluginRegistry $plugins):JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user=$this->getUser();if(!$user instanceof Principal)throw $this->createAccessDeniedException();
        return $this->json($plugins->summary($user->id,$tenant));
    }
}
