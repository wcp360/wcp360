<?php
declare(strict_types=1);
namespace Wcp360\FileManager\Standalone;
use Symfony\Component\HttpFoundation\{Request,Response,HeaderUtils};
use Symfony\Component\Routing\Attribute\Route;
use Wcp360\FileManager\Infrastructure\LocalBackend;
use Wcp360\FileManager\Contract\FileError;
final class ShareController
{
    #[Route('/s/{token}',name:'fm_share',requirements:['token'=>'[a-f0-9]{64}'],methods:['GET','POST'])]
    public function share(Request $request,string $token,LocalBackend $storage): Response
    {
        $headers=['Cache-Control'=>'no-store','Referrer-Policy'=>'no-referrer','X-Content-Type-Options'=>'nosniff','Content-Security-Policy'=>"default-src 'none'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'"];
        if($request->isMethod('POST')){
            try{$password=$request->request->get('password','');if(!is_string($password))throw new FileError('SHARE_DENIED','Accès refusé.',403);
                $result=$storage->sharedDownload($token,$password,$request->getClientIp()??'unknown');
                return new Response($result['content'],200,$headers+['Content-Type'=>'application/octet-stream','Content-Disposition'=>HeaderUtils::makeDisposition('attachment',$result['name'],'download.bin')]);
            }catch(FileError $e){return new Response(htmlspecialchars($e->getMessage(),ENT_QUOTES,'UTF-8'),$e->httpStatus,$headers);}
        }
        return new Response('<!doctype html><html lang="fr"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Fichier partagé · WCP360</title><h1>Téléchargement protégé</h1><p>Utilisez le mot de passe transmis par le propriétaire.</p><form method="post"><label>Mot de passe <input type="password" name="password" required maxlength="256" autocomplete="off"></label><button>Télécharger</button></form></html>',200,$headers);
    }
}
