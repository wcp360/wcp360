<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final readonly class HttpErrorSubscriber implements EventSubscriberInterface
{
    public function __construct(private RequestReference $references, private HttpErrorResponder $responder,
        private TokenStorageInterface $tokens) {}

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => ['begin', 256],
            // Preserve Security's entry points and the credential-route boundary.
            'kernel.exception' => ['exception', -96],
            'kernel.response' => ['response', -64],
        ];
    }

    public function begin(RequestEvent $event): void
    {
        if ($event->isMainRequest()) $this->references->begin($event->getRequest());
    }

    public function exception(#[\SensitiveParameter] ExceptionEvent $event): void
    {
        if ($event->isMainRequest()) {
            $event->setResponse($this->responder->exception($event->getRequest(), $event->getThrowable()));
        }
    }

    public function response(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) return;
        $request = $event->getRequest();
        // These workbench APIs have no resource redirects. Preserve a denied
        // security response as JSON rather than sending fetch clients to HTML.
        if (CandidateApiRequest::matches($request) && $event->getResponse()->isRedirect()) {
            $failure = $this->tokens->getToken() instanceof TwoFactorTokenInterface
                ? HttpFailure::mfaRequired() : HttpFailure::fromStatus(401);
            $event->setResponse($this->responder->respond($request, $failure));
        }
        $event->getResponse()->headers->set(RequestReference::HEADER, $this->references->get($request));
    }
}
