<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Infrastructure;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;

/** Shared current-password confirmation after authenticated browser admission. */
final readonly class CurrentPasswordConfirmation
{
    public function __construct(private CandidateSessions $sessions, private CsrfTokenManagerInterface $csrf, private UserPasswordHasherInterface $passwords) {}

    public function confirm(Request $request, Principal $user, string $intent): void
    {
        $fields = $request->request->all();
        if (!is_string($fields['_token'] ?? null) || !is_string($fields['current_password'] ?? null)
            || strlen($fields['current_password']) > 256) {
            throw new BadRequestHttpException('Invalid password confirmation.');
        }
        if (!$request->isMethod('POST') || $request->headers->get('Origin') !== $request->getSchemeAndHttpHost()
            || !$this->csrf->isTokenValid(new CsrfToken($intent, $fields['_token']))) {
            throw new AccessDeniedHttpException('Password confirmation denied.');
        }
        $stamp = $request->getSession()->get('_wcp_registry');
        if (!is_array($stamp) || ($stamp['actor'] ?? null) !== $user->id || ($stamp['context'] ?? null) !== $user->context
            || !is_string($stamp['selector'] ?? null)) {
            throw new AccessDeniedHttpException('Current session required.');
        }
        // Commit the durable budget before hashing; changing the form does not reset it.
        if (!$this->sessions->allowReauthentication($user->id, $user->context, $stamp['selector'], $request->getSession()->getId())) {
            throw new TooManyRequestsHttpException(null, 'Too many password confirmations. Try again later.');
        }
        if (!$this->passwords->isPasswordValid($user, $fields['current_password'])) {
            throw new AccessDeniedHttpException('Password confirmation denied.');
        }
    }
}
