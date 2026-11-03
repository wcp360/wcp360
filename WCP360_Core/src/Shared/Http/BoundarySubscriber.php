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
    /** @var \WeakMap<\Symfony\Component\HttpFoundation\Request, string> */
    private \WeakMap $issuedNonces;

    public function __construct(private RuntimeContext $context)
    {
        // Weak keys prevent long-lived workers from retaining completed requests.
        $this->issuedNonces = new \WeakMap();
    }

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
        $nonce = base64_encode(random_bytes(18));
        $this->issuedNonces[$request] = $nonce;
        $request->attributes->set('_csp_nonce', $nonce);
    }

    public function response(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        // Only a nonce minted by this subscriber for this Request is trusted.
        // A syntactically valid Request attribute is not proof of issuance.
        $nonce = $this->issuedNonces[$request] ?? null;
        if ($nonce === null) {
            // Early errors can bypass the request listener entirely.
            $nonce = base64_encode(random_bytes(18));
            $this->issuedNonces[$request] = $nonce;
        }
        // Rebind the authoritative nonce if another listener changed an attribute.
        $request->attributes->set('_csp_nonce', $nonce);

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
        } else {
            // Never inherit HSTS from a downstream response on plain HTTP,
            // including an early error that skipped the request listener.
            $headers->remove('Strict-Transport-Security');
        }
    }
}
