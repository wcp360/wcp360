<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Security;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Scheb\TwoFactorBundle\Security\Http\Authenticator\TwoFactorAuthenticator;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvent;
use Scheb\TwoFactorBundle\Security\TwoFactor\Event\TwoFactorAuthenticationEvents;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;

/** Candidate session admission; no automatic registration of existing cookies. */
final readonly class SessionBoundary
{
    public function __construct(private RuntimeContext $runtime, private TokenStorageInterface $tokens, private CandidateSessions $sessions, private CandidateMfa $mfa) {}

    #[AsEventListener(event: 'kernel.request', priority: 12)]
    public function beforeFirewall(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || !in_array($this->runtime->context, ['user', 'admin'], true)) return;
        $request = $event->getRequest();
        if ($request->getPathInfo() === '/logout' && !$request->isMethod('POST')) {
            $event->setResponse(new Response('POST required.', 405, ['Allow' => 'POST']));
            return;
        }
        $session = $request->getSession();
        if (!$session->has('_security_main') && !$session->has('_wcp_registry')) return;
        try {
            $stamp = $this->stamp($request);
            if ($stamp === null || !$this->sessions->current($stamp['actor'], $stamp['context'], $stamp['selector'], $session->getId())) throw new \RuntimeException('Current session required.');
            $request->attributes->set('_wcp_registry_current', $stamp + ['binding' => CandidateSessions::binding($stamp['actor'], $stamp['context'], $session->getId())]);
        } catch (\Throwable) {
            $this->invalidate($request);
            $event->setResponse($request->isMethodSafe() ? new RedirectResponse('/login') : new Response('Current session required.', 403));
        }
    }

    // Bind after Symfony's final strategy, which may rotate again after Scheb COMPLETE.
    #[AsEventListener(event: LoginSuccessEvent::class, dispatcher: 'security.event_dispatcher.main', priority: -64)]
    public function login(LoginSuccessEvent $event): void
    {
        $request = $event->getRequest();
        $user = $event->getAuthenticatedToken()->getUser();
        try {
            if (!$user instanceof Principal || $user->context !== $this->runtime->context) throw new \RuntimeException('Current identity required.');
            $session = $request->getSession();
            $previous = $request->attributes->get('_wcp_registry_current');
            $mfa = $event->getAuthenticator() instanceof TwoFactorAuthenticator;
            if ($mfa) {
                $proof = $session->get('_wcp_mfa_proof');
                if ($user->context !== 'admin' || $event->getAuthenticatedToken() instanceof TwoFactorTokenInterface
                    || !is_array($previous) || ($previous['actor'] ?? null) !== $user->id || ($previous['context'] ?? null) !== 'admin'
                    || !is_string($previous['selector'] ?? null) || !is_string($previous['binding'] ?? null)
                    || !is_array($proof) || ($proof['actor'] ?? null) !== $user->id || !is_int($proof['at'] ?? null)
                    || $proof['at'] > time() || $proof['at'] + 300 <= time()) throw new \RuntimeException('Current MFA session required.');
                $session->migrate(true);
                $this->sessions->upgrade($user->id, 'admin', $previous['selector'], $previous['binding'], $session->getId());
                $selector = $previous['selector'];
            } else {
                $status = $user->context === 'admin' ? $this->mfa->status($user->id) : null;
                $pending = $event->getAuthenticatedToken() instanceof TwoFactorTokenInterface;
                if ($status !== null && ($status['enabled'] || $request->attributes->get('_wcp_mfa_enrollment_actor') === $user->id) !== $pending) {
                    throw new \RuntimeException('Current administrator authentication required.');
                }
                if ($status !== null && !$pending) {
                    $session->remove('_wcp_mfa_proof');
                    $session->remove('_wcp_mfa_pending');
                    $session->set('_wcp_password_proof', ['actor' => $user->id, 'revision' => $status['revision'], 'at' => time()]);
                }
                if (is_array($previous) && is_string($previous['actor'] ?? null) && is_string($previous['context'] ?? null)
                    && is_string($previous['selector'] ?? null) && is_string($previous['binding'] ?? null)) {
                    $this->sessions->retire($previous['actor'], $previous['context'], $previous['selector'], $previous['binding']);
                }
                $session->migrate(true);
                $selector = $this->sessions->issue($user->id, $user->context, $session->getId());
                $session->set('_wcp_context', $user->context);
                $session->set('_wcp_created', time());
            }
            $session->set('_wcp_registry', ['actor' => $user->id, 'context' => $user->context, 'selector' => $selector]);
            $session->set('_wcp_last', time());
        } catch (\Throwable) {
            $this->invalidate($request);
            $event->setResponse(new Response('Session registration is unavailable. Sign in again.', 403));
            $event->stopPropagation();
        }
    }

    #[AsEventListener(event: 'kernel.request', priority: 0)]
    public function request(RequestEvent $event): void
    {
        if (!$event->isMainRequest() || !$this->tokens->getToken()?->getUser() instanceof Principal) return;
        $request = $event->getRequest();
        $session = $request->getSession();
        $now = time(); $created = $session->get('_wcp_created'); $last = $session->get('_wcp_last');
        // A future activity timestamp is not a valid session heartbeat.
        // Never let it suppress idle expiry; the DB registry already rejects
        // last_seen_at > now, so the browser-side admission must agree.
        $valid = $session->get('_wcp_context') === $this->runtime->context
            && is_int($created) && is_int($last) && $last <= $now;
        if ($valid) {
            try {
                $valid = !SessionLifetimePolicy::isExpired($this->runtime->context === 'admin' ? SessionKind::Admin : SessionKind::User,
                    new \DateTimeImmutable('@'.$created), new \DateTimeImmutable('@'.$last), new \DateTimeImmutable('@'.$now));
                $this->requireCurrent($request);
            } catch (\Throwable) { $valid = false; }
        }
        if (!$valid) {
            $this->invalidate($request);
            $event->setResponse($request->isMethodSafe() ? new RedirectResponse('/login') : new Response('Current session required.', 403));
            return;
        }
        $session->set('_wcp_last', $now);
    }

    #[AsEventListener(event: 'kernel.controller')]
    public function beforeMutation(ControllerEvent $event): void
    {
        if ($event->isMainRequest() && !$event->getRequest()->isMethodSafe() && $this->tokens->getToken()?->getUser() instanceof Principal) $this->requireCurrent($event->getRequest());
    }

    #[AsEventListener(event: TwoFactorAuthenticationEvents::ATTEMPT)]
    public function beforeMfa(TwoFactorAuthenticationEvent $event): void { $this->requireCurrent($event->getRequest()); }

    #[AsEventListener(event: LogoutEvent::class, dispatcher: 'security.event_dispatcher.main', priority: 128)]
    public function logout(LogoutEvent $event): void
    {
        if (!$event->getToken()?->getUser() instanceof Principal) return;
        $request = $event->getRequest();
        if (!$request->isMethod('POST') || $request->headers->get('Origin') !== $request->getSchemeAndHttpHost()) throw new AccessDeniedHttpException('Exact logout origin required.');
        $this->requireCurrent($request);
        $stamp = $this->stamp($request);
        if ($stamp === null) throw new AccessDeniedHttpException('Current session required.');
        try {
            $this->sessions->retire($stamp['actor'], $stamp['context'], $stamp['selector'], CandidateSessions::binding($stamp['actor'], $stamp['context'], $request->getSession()->getId()));
        } catch (\Throwable) {
            $this->invalidate($request);
            throw new AccessDeniedHttpException('Session revocation unavailable.');
        }
    }

    private function requireCurrent(Request $request): void
    {
        $stamp = $this->stamp($request); $token = $this->tokens->getToken(); $user = $token?->getUser();
        try {
            if ($stamp === null || !$user instanceof Principal || $stamp['actor'] !== $user->id || $stamp['context'] !== $user->context
                || !$this->sessions->current($user->id, $user->context, $stamp['selector'], $request->getSession()->getId(),
                    $user->context === 'admin' && !$token instanceof TwoFactorTokenInterface && $this->mfa->status($user->id)['enabled'] ? 2 : 1, false)) throw new \RuntimeException('Current session required.');
        } catch (\Throwable) {
            $this->invalidate($request);
            throw new AccessDeniedHttpException('Current session required.');
        }
    }

    /** @return array{actor: string, context: string, selector: string}|null */
    private function stamp(Request $request): ?array
    {
        $stamp = $request->getSession()->get('_wcp_registry');
        if (!is_array($stamp) || !is_string($stamp['actor'] ?? null) || !is_string($stamp['selector'] ?? null)
            || ($stamp['context'] ?? null) !== $this->runtime->context || !in_array($stamp['context'], ['user', 'admin'], true)) return null;
        return ['actor' => $stamp['actor'], 'context' => $stamp['context'], 'selector' => $stamp['selector']];
    }

    private function invalidate(Request $request): void { $request->getSession()->invalidate(); $this->tokens->setToken(null); }
}
