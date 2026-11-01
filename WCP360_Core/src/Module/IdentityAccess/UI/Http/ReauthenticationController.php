<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\UI\Http;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Presents a new full sign-in through the existing password/MFA authenticators. */
final class ReauthenticationController extends AbstractController
{
    #[Route('/account/reauthenticate', name: 'wcp_account_reauthenticate', methods: ['GET'])]
    public function confirm(RuntimeContext $runtime, TokenStorageInterface $tokens, AuthenticationUtils $auth): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        if ($runtime->context !== 'admin' || !$user instanceof Principal || $user->context !== 'admin'
            || $tokens->getToken() instanceof TwoFactorTokenInterface) {
            throw $this->createAccessDeniedException('Current administrator authentication required.');
        }

        return $this->render('identity/reauthenticate.html.twig', [
            'identity' => $user->getUserIdentifier(),
            'error' => $auth->getLastAuthenticationError(),
        ]);
    }
}
