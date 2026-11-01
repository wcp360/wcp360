<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/** Credential-bearing requests must not expose the development exception renderer. */
final class PasswordChangeErrors
{
    // After normal exception logging, before Symfony's debug/error rendering.
    #[AsEventListener(event: 'kernel.exception', priority: -64)]
    public function redact(#[\SensitiveParameter] ExceptionEvent $event): void
    {
        if (!$event->isMainRequest() || $event->getRequest()->getPathInfo() !== '/account/password') return;
        $error = $event->getThrowable();
        $status = $error instanceof HttpExceptionInterface ? $error->getStatusCode() : 500;
        $headers = $error instanceof HttpExceptionInterface ? $error->getHeaders() : [];
        $event->setResponse(new Response('Password management request could not be completed.', $status, $headers));
    }
}
