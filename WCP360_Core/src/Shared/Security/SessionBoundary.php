<?php

declare(strict_types=1);
namespace Wcp360\Core\Shared\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
final readonly class SessionBoundary
{
    public function __construct(private RuntimeContext $runtime,private TokenStorageInterface $tokens){}
    #[AsEventListener(event:LoginSuccessEvent::class,method:'login',dispatcher:'security.event_dispatcher.main')]
    public function login(LoginSuccessEvent $event):void
    {
        $session=$event->getRequest()->getSession();
        $session->set('_wcp_context',$this->runtime->context);
        $session->set('_wcp_created',time());
        $session->set('_wcp_last',time());
    }
    #[AsEventListener(event:'kernel.request',method:'request',priority:0)]
    public function request(RequestEvent $event):void
    {
        if(!$event->isMainRequest() || !$this->tokens->getToken()?->getUser() instanceof Principal)return;
        $request=$event->getRequest();
        $session=$request->getSession();
        $now=time();$created=$session->get('_wcp_created');$last=$session->get('_wcp_last');
        $valid=$session->get('_wcp_context')===$this->runtime->context && is_int($created) && is_int($last);
        if($valid){
            try{$valid=!SessionLifetimePolicy::isExpired($this->runtime->context==='admin'?SessionKind::Admin:SessionKind::User,new \DateTimeImmutable('@'.$created),new \DateTimeImmutable('@'.$last),new \DateTimeImmutable('@'.$now));}
            catch(ContractViolation){$valid=false;}
        }
        if(!$valid){$session->invalidate();$this->tokens->setToken(null);$event->setResponse(new RedirectResponse('/login',303));return;}
        $session->set('_wcp_last',$now);
    }
}
