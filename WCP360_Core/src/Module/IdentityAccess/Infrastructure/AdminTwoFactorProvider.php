<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Scheb\TwoFactorBundle\Security\TwoFactor\AuthenticationContextInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\IdentityAccess\UI\Http\MfaFormRenderer;

/** Scheb withholds ADMIN roles until this DB-backed factor has completed. */
final readonly class AdminTwoFactorProvider implements TwoFactorProviderInterface
{
    public function __construct(private CandidateMfa $mfa, private RequestStack $requests, private MfaFormRenderer $renderer) {}

    public function beginAuthentication(AuthenticationContextInterface $context): bool
    {
        $user = $context->getUser();
        if (!$user instanceof Principal || $user->context !== 'admin') {
            return false;
        }
        $session = $context->getSession();
        $session->remove('_wcp_mfa_proof');
        $session->set('_wcp_mfa_pending', ['actor' => $user->id, 'revision' => $this->mfa->status($user->id)['revision'], 'started' => time()]);

        return true; // Includes administrators who must enroll their first factor.
    }

    public function needsPreparation(): bool { return false; }
    public function prepareAuthentication(object $user): void {}

    public function validateAuthenticationCode(object $user, #[\SensitiveParameter] string $authenticationCode): bool
    {
        $request = $this->requests->getCurrentRequest();
        if (!$request || !$user instanceof Principal || $user->context !== 'admin' || strlen($authenticationCode) > 64) {
            return false;
        }
        $pending = $request->getSession()->get('_wcp_mfa_pending');
        if (!is_array($pending) || ($pending['actor'] ?? null) !== $user->id
            || !is_int($pending['started'] ?? null) || !is_int($pending['revision'] ?? null)
            || $pending['started'] > time() || $pending['started'] + 300 <= time()) {
            return false;
        }
        try {
            $result = $this->mfa->check($user->id, $authenticationCode, $pending['revision']);
        } catch (\Throwable) {
            return false;
        }
        if ($result === null) {
            return false;
        }
        // Only per-request attributes carry one-time disclosures; never the session.
        $request->attributes->set('_wcp_mfa_result', $result);

        return true;
    }

    public function getFormRenderer(): TwoFactorFormRendererInterface { return $this->renderer; }
}
