<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Wcp360\Core\Shared\Http\HttpErrorResponder;

/** Credential-bearing requests must not expose the development exception renderer. */
final readonly class PasswordChangeErrors
{
    public function __construct(private HttpErrorResponder $responder) {}

    // After normal exception logging, before Symfony's debug/error rendering.
    #[AsEventListener(event: 'kernel.exception', priority: -64)]
    public function redact(#[\SensitiveParameter] ExceptionEvent $event): void
    {
        if (!$event->isMainRequest() || !in_array($event->getRequest()->getPathInfo(), ['/account/password', '/reset-password', '/account/security/mfa/recovery-codes', '/account/sessions/revoke-others'], true)) return;
        $event->setResponse($this->responder->exception($event->getRequest(), $event->getThrowable()));
    }
}
