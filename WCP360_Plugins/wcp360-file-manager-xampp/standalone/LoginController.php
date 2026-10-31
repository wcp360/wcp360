<?php
declare(strict_types=1);
namespace Wcp360\FileManager\Standalone;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
final class LoginController
{
    #[Route('/login',name:'fm_login',methods:['GET','POST'])]
    public function login(CsrfTokenManagerInterface $csrf, AuthenticationUtils $auth): Response
    {
        $token=htmlspecialchars($csrf->getToken('authenticate')->getValue(),ENT_QUOTES,'UTF-8');
        $error=$auth->getLastAuthenticationError() ? '<p class="error" role="alert">Connexion refusée. Vérifiez vos identifiants.</p>':'';
        $nonce=base64_encode(random_bytes(18));
        $html=<<<HTML
<!doctype html><html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Connexion · WCP360 File Manager</title><style nonce="$nonce">
*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;grid-template-columns:1.05fr 1fr;font:15px system-ui,sans-serif;color:#172c44;background:#f5f7fb}.intro{padding:9vh 9vw;background:#102943;color:white;display:flex;flex-direction:column;justify-content:center}.brand{font-size:24px;font-weight:750;letter-spacing:-.7px;margin-bottom:80px}.brand span{color:#79a7ff}.intro h1{font-size:clamp(32px,3.8vw,54px);line-height:1.13;letter-spacing:-1.8px;margin:0 0 25px}.intro p{color:#b5c7dc;line-height:1.8;max-width:390px}.intro small{margin-top:65px;color:#7895b6}.login{display:flex;align-items:center;justify-content:center;padding:35px}.card{width:390px;background:white;padding:38px;border-radius:16px;border:1px solid #e2e8f1;box-shadow:0 20px 60px #142b4510}.card h2{font-size:26px;margin:0 0 9px;letter-spacing:-.7px}.card>p{font-size:13px;color:#7a899d;line-height:1.6;margin-bottom:30px}label{display:block;font-size:13px;font-weight:600;margin-bottom:8px}input{display:block;width:100%;padding:12px 13px;border:1px solid #dce3ee;border-radius:7px;font:inherit;margin-bottom:21px;outline:none}input:focus{border-color:#316de4;box-shadow:0 0 0 3px #316de419}button{background:#245ee8;color:white;border:0;border-radius:7px;width:100%;padding:13px;font:600 14px system-ui;cursor:pointer}button:hover{background:#174dc8}.error{background:#fff0f0;color:#a72c2c;padding:12px;border-radius:6px;font-size:13px}.foot{margin-top:24px;font-size:11px;color:#8290a2;text-align:center}@media(max-width:760px){body{display:block}.intro{padding:28px}.brand{margin-bottom:18px}.intro h1{font-size:27px;letter-spacing:-.7px}.intro p,.intro small{display:none}.login{padding:24px 16px}.card{padding:28px}}
</style></head><body><section class="intro"><div class="brand">WCP<span>360</span> / FILE MANAGER</div><h1>Vos fichiers.<br>Votre espace de travail.</h1><p>Organisez vos projets, retrouvez vos documents et gardez la maîtrise de chaque modification.</p><small>ESPACE PRIVÉ · ACCÈS AUTHENTIFIÉ</small></section><main class="login"><div class="card"><h2>Connexion</h2><p>Retrouvez votre gestionnaire de fichiers.</p>$error<form method="post" action="/login"><label for="username">Identifiant</label><input id="username" name="_username" required autocomplete="username" autofocus><label for="password">Mot de passe</label><input id="password" type="password" name="_password" required autocomplete="current-password"><input type="hidden" name="_csrf_token" value="$token"><button>Se connecter</button></form><div class="foot">WCP360 File Manager · 0.3.0 alpha</div></div></main></body></html>
HTML;
        return new Response($html,200,['Cache-Control'=>'no-store','X-Content-Type-Options'=>'nosniff','Content-Security-Policy'=>"default-src 'none'; style-src 'nonce-$nonce'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'"]);
    }
    #[Route('/logout',name:'fm_logout',methods:['POST'])]
    public function logout(): never { throw new \LogicException('Handled by Symfony firewall'); }
    #[Route('/',name:'fm_root',methods:['GET'])]
    public function root(): Response { return new \Symfony\Component\HttpFoundation\RedirectResponse('/file-manager'); }
}
