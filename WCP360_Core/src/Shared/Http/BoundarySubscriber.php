<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Candidate browser boundary. Production admission remains separately gated. */
final readonly class BoundarySubscriber implements EventSubscriberInterface
{
    public function __construct(private RuntimeContext $context) {}

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => ['request', 128],
            'kernel.response' => ['response', -128],
        ];
    }

    public function request(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $this->context->requireBrowserHost($request->getHost());
        $request->attributes->set('_csp_nonce', base64_encode(random_bytes(18)));
    }

    public function response(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $nonce = $request->attributes->get('_csp_nonce');

        // Early exception responses can bypass the request listener. Never emit an
        // empty or malformed nonce in their browser security policy.
        if (!is_string($nonce) || preg_match('/^[A-Za-z0-9+\/]{24}$/D', $nonce) !== 1) {
            $nonce = base64_encode(random_bytes(18));
            $request->attributes->set('_csp_nonce', $nonce);
        }

        $headers = $event->getResponse()->headers;
        $headers->set('Content-Security-Policy',
            "default-src 'none'; base-uri 'none'; object-src 'none'; frame-ancestors 'none'; ".
            "form-action 'self'; script-src 'self' 'nonce-".$nonce."'; ".
            "style-src 'self' 'nonce-".$nonce."'; img-src 'self' data: blob:; ".
            "font-src 'self'; connect-src 'self'; frame-src 'none'"
        );
        $headers->set('X-Frame-Options', 'DENY');
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('Referrer-Policy', 'no-referrer');
        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $headers->set('Cache-Control', 'no-store, private');
        $headers->set('X-WCP360-Status', 'development-candidate');

        // Development HTTP endpoints must not advertise an HSTS policy.
        if ($request->isSecure()) {
            $headers->set('Strict-Transport-Security', 'max-age=31536000');
        }
    }
}
