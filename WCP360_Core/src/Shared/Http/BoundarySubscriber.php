<?php

declare(strict_types=1);
namespace Wcp360\Core\Shared\Http;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Wcp360\Core\Shared\Security\RuntimeContext;
final readonly class BoundarySubscriber implements EventSubscriberInterface
{
    public function __construct(private RuntimeContext $context) {}
    public static function getSubscribedEvents(): array { return ['kernel.request' => ['request', 128], 'kernel.response' => ['response', -128]]; }
    public function request(RequestEvent $event): void { if ($event->isMainRequest()) { $this->context->requireBrowserHost($event->getRequest()->getHost()); $event->getRequest()->attributes->set('_csp_nonce', base64_encode(random_bytes(18))); } }
    public function response(ResponseEvent $event): void
    {
        $headers = $event->getResponse()->headers;
        $headers->set('Content-Security-Policy', "default-src 'none'; script-src 'self' 'nonce-".$event->getRequest()->attributes->get('_csp_nonce')."'; style-src 'self'; img-src 'self' data:; connect-src 'self'; font-src 'self'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'");
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('Referrer-Policy', 'no-referrer');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $headers->set('Cache-Control', 'no-store, private');
        $headers->set('X-WCP360-Status', 'development-candidate');
    }
}
