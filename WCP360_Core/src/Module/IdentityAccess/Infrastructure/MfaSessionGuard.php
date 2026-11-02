<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvent;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvents;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Shared\Security\RuntimeContext;

final readonly class MfaSessionGuard
{
    public function __construct(private RuntimeContext $runtime, private TokenStorageInterface $tokens, private CandidateMfa $mfa) {}

    // Before the security firewall: CSRF authentication must not consume a code
    // on a cross-origin request, including the firewall's /mfa/check endpoint.
    #[AsEventListener(event: 'kernel.request', priority: 16)]
    public function origin(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || $this->runtime->context !== 'admin') {
            return;
        }
        $request = $event->getRequest();
        if ((!$request->isMethodSafe() && $request->headers->get('Origin') !== $request->getSchemeAndHttpHost())
            || ($request->getPathInfo() === '/logout' && !$request->isMethod('POST'))) {
            throw new AccessDeniedHttpException('Exact administrator origin required.');
        }
    }

    #[AsEventListener(event: TwoFactorAuthenticationEvents::COMPLETE)]
    public function completed(TwoFactorAuthenticationEvent $event): void
    {
        $user = $event->getToken()->getUser();
        $result = $event->getRequest()->attributes->get('_wcp_mfa_result');
        if (!$user instanceof Principal || $user->context !== 'admin' || !is_array($result) || !is_int($result['revision'] ?? null)) {
            throw new AccessDeniedHttpException('Current administrator MFA required.');
        }
        $session = $event->getRequest()->getSession();
        $session->migrate(true);
        $session->remove('_wcp_mfa_pending');
        $session->set('_wcp_mfa_proof', ['actor' => $user->id, 'revision' => $result['revision'], 'at' => time()]);
    }

    #[AsEventListener(event: 'kernel.request', priority: -8)]
    public function current(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || $this->runtime->context !== 'admin') {
            return;
        }
        $request = $event->getRequest();
        if (in_array($request->getPathInfo(), ['/health', '/login', '/logout'], true)) {
            return;
        }
        $token = $this->tokens->getToken();
        $user = $token?->getUser();
        if (!$user instanceof Principal) {
            return;
        }
        $pending = $token instanceof TwoFactorTokenInterface;
        try {
            $status = $this->mfa->status($user->id);
            $stamp = $request->getSession()->get($pending ? '_wcp_mfa_pending' : ($status['enabled'] ? '_wcp_mfa_proof' : '_wcp_password_proof'));
            $valid = is_array($stamp) && ($stamp['actor'] ?? null) === $user->id
                && ($stamp['revision'] ?? null) === $status['revision'];
            $at = is_array($stamp) ? ($stamp[$pending ? 'started' : 'at'] ?? null) : null;
            $valid = $valid && is_int($at) && $at <= time()
                && ($pending ? ($at + 300 > time() && $status['configured'] && ($status['enabled'] || ($stamp['enrollment'] ?? false) === true)) : (!$status['enabled'] || $status['configured']));
        } catch (\Throwable) {
            $valid = false;
        }
        if (!$valid) {
            $request->getSession()->invalidate();
            $this->tokens->setToken(null);
            $event->setResponse($request->isMethodSafe() ? new RedirectResponse('/login', 303) : new Response('Current administrator authentication required.', 403));
            return;
        }
        if (!$pending && !$request->isMethodSafe() && $at + 300 <= time()) {
            throw new AccessDeniedHttpException('Confirm your identity at /account/reauthenticate before retrying this action.');
        }
    }
}
