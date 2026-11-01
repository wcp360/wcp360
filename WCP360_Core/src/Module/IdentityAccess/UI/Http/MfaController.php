<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\UI\Http;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;

final class MfaController extends AbstractController
{
    public function __construct(private readonly CandidateMfa $mfa, private readonly TokenStorageInterface $tokens) {}

    #[Route('/mfa/enroll', name: 'wcp_mfa_enroll', methods: ['POST'])]
    public function enroll(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof Principal || $user->context !== 'admin' || !$this->tokens->getToken() instanceof TwoFactorTokenInterface
            || !$this->isCsrfTokenValid('mfa_enroll', $request->request->getString('_token'))) {
            return new Response('MFA enrollment denied.', 403);
        }
        try {
            $setup = $this->mfa->startEnrollment($user->id);
            $pending = $request->getSession()->get('_wcp_mfa_pending');
            if (!is_array($pending)) {
                throw new \RuntimeException();
            }
            $pending['revision'] = $this->mfa->status($user->id)['revision'];
            $request->getSession()->set('_wcp_mfa_pending', $pending);

            return $this->render('identity/mfa.html.twig', ['enabled' => false, 'configured' => true, 'error' => false, 'secret' => $setup['secret']]);
        } catch (\Throwable) {
            return new Response('MFA enrollment is unavailable or temporarily limited.', 503);
        }
    }

    #[Route('/account/security', name: 'wcp_account_security', methods: ['GET'])]
    public function security(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('identity/security.html.twig', ['context' => 'admin']);
    }

    #[Route('/mfa/reset', name: 'wcp_mfa_reset', methods: ['POST'])]
    public function reset(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $password = $request->request->getString('current_password');
        if (!$user instanceof Principal || !$this->isCsrfTokenValid('mfa_reset', $request->request->getString('_token'))
            || strlen($password) > 256 || !$hasher->isPasswordValid($user, $password)) {
            throw $this->createAccessDeniedException('MFA recovery denied.');
        }
        unset($password);
        $this->mfa->reset($user->id);
        $request->getSession()->invalidate();
        $this->tokens->setToken(null);

        return new RedirectResponse('/login', 303);
    }
}
