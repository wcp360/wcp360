<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\UI\Http;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wcp360\Core\Module\IdentityAccess\Application\RegenerateMfaRecoveryCodes;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\CurrentPasswordConfirmation;

final class MfaRecoveryController extends AbstractController
{
    public function __construct(private readonly RegenerateMfaRecoveryCodes $renewal,
        private readonly CurrentPasswordConfirmation $confirmation, private readonly TokenStorageInterface $tokens) {}

    #[Route('/account/security/mfa/recovery-codes', name: 'wcp_mfa_recovery_regenerate', methods: ['POST'])]
    public function regenerate(#[\SensitiveParameter] Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $actor = $this->getUser();
        if (!$actor instanceof Principal || $actor->context !== 'admin' || $this->tokens->getToken() instanceof TwoFactorTokenInterface) {
            throw $this->createAccessDeniedException('Current administrator required.');
        }
        if ($request->query->count() !== 0 || array_diff(array_keys($request->request->all()), ['_token', 'current_password']) !== []) {
            throw new BadRequestHttpException('Invalid recovery code renewal.');
        }
        $this->confirmation->confirm($request, $actor, 'mfa_recovery_regenerate');
        $session = $request->getSession();
        $stamp = $session->get('_wcp_registry');
        if (!is_array($stamp) || !is_string($stamp['selector'] ?? null)) {
            throw $this->createAccessDeniedException('Current administrator session required.');
        }
        $proof = $session->get('_wcp_mfa_proof');
        try {
            $codes = $this->renewal->regenerate($actor, $stamp['selector'], $session->getId(), is_array($proof) ? $proof : null);
        } catch (\Throwable $error) {
            $session->invalidate();
            $this->tokens->setToken(null);
            throw $error;
        }
        // All durable sessions are revoked. Neither the session nor a redirect carries raw codes.
        $session->invalidate();
        $this->tokens->setToken(null);
        try {
            return $this->render('identity/recovery_codes.html.twig', ['codes' => $codes, 'renewed' => true]);
        } catch (\Throwable) {
            return new Response('Codes could not be displayed. Sign in with your authenticator and renew them again from account security.', 503);
        }
    }
}
