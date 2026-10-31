<?php
declare(strict_types=1);
namespace Wcp360\FileManager\Controller;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\{Request,Response,JsonResponse};
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\{CsrfToken,CsrfTokenManagerInterface};
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Wcp360\FileManager\Contract\{FileBackend,IdentityProvider,FileError};
use Wcp360\FileManager\Infrastructure\LocalBackend;
#[IsGranted('ROLE_FILE_MANAGER')]
final readonly class FileController
{
    public function __construct(private FileBackend $backend,private IdentityProvider $identities,private CsrfTokenManagerInterface $csrf,private LoggerInterface $logger) {}
    #[Route('/file-manager', name:'fm_home', methods:['GET'])]
    public function index(): Response
    { return $this->headers(new Response(file_get_contents(dirname(__DIR__,2).'/resources/public/index.html'))); }
    #[Route('/file-manager/assets/{name}', name:'fm_asset', requirements:['name'=>'app\\.js|style\\.css|pro\\.js'], methods:['GET'])]
    public function asset(string $name): Response
    { return $this->headers(new Response(file_get_contents(dirname(__DIR__,2).'/resources/public/'.$name),200,['Content-Type'=>str_ends_with($name,'.js')?'text/javascript; charset=utf-8':'text/css; charset=utf-8'])); }
    #[Route('/api/v1/files/session', name:'fm_session', methods:['GET'])]
    public function session(): JsonResponse
    { $id=$this->identities->current(); return $this->headers(new JsonResponse(['data'=>['csrf'=>$this->csrf->getToken('fm_write')->getValue(),'user'=>$id->subjectId,'can_write'=>$id->canWrite,'logout_csrf'=>$this->csrf->getToken('logout')->getValue()]])); }
    #[Route('/api/v1/files/{operation}', name:'fm_read', requirements:['operation'=>'list|read|download|trash_list|properties|capabilities|disk_usage|versions|audit|share_list'], methods:['GET'])]
    #[Route('/api/v1/files/{operation}', name:'fm_write', requirements:['operation'=>'create|save|mkdir|rename|upload|trash|restore|copy|chmod|quota_set|version_restore|snapshot|lock_acquire|lock_renew|lock_release|share_create|share_revoke|trash_purge|search'], methods:['POST'])]
    public function execute(Request $request,string $operation): Response
    {
        $requestId=bin2hex(random_bytes(16)); $identity=$this->identities->current();
        try {
            if ($request->isMethod('POST')) {
                if (!$identity->canWrite && $operation!=='search') { throw new FileError('READ_ONLY','Accès en lecture seule.',403); }
                if (!$this->csrf->isTokenValid(new CsrfToken('fm_write',$request->headers->get('X-CSRF-Token','')))) { throw new FileError('CSRF_INVALID','Session expirée. Rechargez la page.',403); }
                if ($operation==='upload') {
                    $file=$request->files->get('file');
                    if (!$file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile || !$file->isValid()) { throw new FileError('INVALID_UPLOAD','Import invalide ou trop volumineux.'); }
                    if ($file->getSize()>LocalBackend::MAX_FILE) { throw new FileError('FILE_TOO_LARGE','Import limité à 20 Mio.',413); }
                    $input=['path'=>$request->request->get('path'),'content'=>file_get_contents($file->getPathname())];
                } else {
                    if (strlen($request->getContent())>7*1048576) { throw new FileError('REQUEST_TOO_LARGE','Requête trop volumineuse.',413); }
                    if (!str_starts_with(strtolower($request->headers->get('Content-Type','')),'application/json')) { throw new FileError('INVALID_CONTENT_TYPE','JSON requis.',415); }
                    try { $input=json_decode($request->getContent(),true,32,JSON_THROW_ON_ERROR); }
                    catch (\JsonException) { throw new FileError('INVALID_JSON','JSON invalide.'); }
                    if (!is_array($input) || array_is_list($input)) { throw new FileError('INVALID_JSON','Objet JSON requis.'); }
                }
            } else { $input=$request->query->all(); }
            $input['_audit']=['ip'=>$request->getClientIp(),'request_id'=>$requestId];
            $result=$this->backend->execute($identity,$operation,$input);
            $this->audit($requestId,$identity->subjectId,$identity->tenantId,$operation,'success');
            if (in_array($operation,['download','snapshot'],true)) {
                $response=new Response($result['content'],200,['Content-Type'=>'application/octet-stream','Content-Disposition'=>HeaderUtils::makeDisposition('attachment',$result['name'],'download.bin')]);
            } else { $response=new JsonResponse(['status'=>'success','data'=>$result,'request_id'=>$requestId]); }
        } catch (FileError $e) {
            $this->audit($requestId,$identity->subjectId,$identity->tenantId,$operation,$e->errorCode);
            $response=new JsonResponse(['error'=>['code'=>$e->errorCode,'message'=>$e->getMessage()],'request_id'=>$requestId],$e->httpStatus);
        } catch (\Throwable $e) {
            // Never log raw exception messages, file contents, full paths, tokens or credentials.
            $this->audit($requestId,$identity->subjectId,$identity->tenantId,$operation,'INTERNAL_ERROR');
            $response=new JsonResponse(['error'=>['code'=>'INTERNAL_ERROR','message'=>'Opération impossible. Consultez le journal avec cet identifiant.'],'request_id'=>$requestId],500);
        }
        $response->headers->set('X-Request-ID',$requestId); return $this->headers($response);
    }
    private function audit(string $id,string $subject,string $tenant,string $op,string $result): void
    { $this->logger->info('file_manager.operation',['request_id'=>$id,'subject'=>$subject,'tenant'=>$tenant,'operation'=>$op,'result'=>$result]); }
    private function headers(Response $r): Response
    {
        $r->headers->set('Cache-Control','no-store'); $r->headers->set('X-Content-Type-Options','nosniff');
        $r->headers->set('Referrer-Policy','no-referrer');
        $r->headers->set('Content-Security-Policy',"default-src 'none'; script-src 'self'; style-src 'self'; connect-src 'self'; img-src 'self'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'");
        return $r;
    }
}
