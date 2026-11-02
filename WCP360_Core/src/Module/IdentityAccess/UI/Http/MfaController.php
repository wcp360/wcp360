<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\UI\Http;

use Scheb\TwoFactorBundle\Security\Authentication\Token\TwoFactorTokenInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Application\DisableAdministratorMfa;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\CurrentPasswordConfirmation;

final class MfaController extends AbstractController
{
    public function __construct(private readonly CandidateMfa $mfa, private readonly TokenStorageInterface $tokens,
        private readonly CurrentPasswordConfirmation $confirmation, private readonly DisableAdministratorMfa $disable) {}

    #[Route('/account/security/mfa/enable', name: 'wcp_mfa_enable', methods: ['GET'])]
    public function enable(): Response
    {
        $user = $this->administrator();
        $status = $this->mfa->status($user->id);
        if ($status['enabled']) return $this->redirectToRoute('wcp_account_security');

        return $this->render('identity/mfa_enable.html.twig', ['configured' => $status['configured']]);
    }

    #[Route('/mfa/enroll', name: 'wcp_mfa_enroll', methods: ['POST'])]
    public function enroll(Request $request, Security $security): Response
    {
        $user = $this->administrator();
        $this->confirmation->confirm($request, $user, 'mfa_enroll');
        try {
            $setup = $this->mfa->startEnrollment($user->id);
            // Server-only intent, created after password/CSRF/Origin validation.
            // Reuse Symfony/Scheb's login, rotation, registry and completion pipeline.
            $request->attributes->set('_wcp_mfa_enrollment_actor', $user->id);
            try {
                $response = $security->login($user, 'form_login');
            } finally {
                $request->attributes->remove('_wcp_mfa_enrollment_actor');
            }
            if ($response !== null && $response->getStatusCode() >= 400) return $response;
            if (!$this->tokens->getToken() instanceof TwoFactorTokenInterface) throw new \RuntimeException('Enrollment authentication required.');

            return $this->render('identity/mfa.html.twig', ['enabled' => false, 'configured' => true, 'error' => false, 'secret' => $setup['secret']]);
        } catch (HttpExceptionInterface $exception) {
            throw $exception;
        } catch (\Throwable) {
            $request->getSession()->invalidate();
            $this->tokens->setToken(null);

            return new Response('MFA enrollment is unavailable or temporarily limited.', 503);
        }
    }

    #[Route('/account/security', name: 'wcp_account_security', methods: ['GET'])]
    public function security(): Response
    {
        $user = $this->administrator();

        return $this->render('identity/security.html.twig', ['context' => 'admin', 'mfa' => $this->mfa->status($user->id)]);
    }

    #[Route('/mfa/reset', name: 'wcp_mfa_reset', methods: ['POST'])]
    public function reset(Request $request): Response
    {
        $user = $this->administrator();
        $this->confirmation->confirm($request, $user, 'mfa_reset');
        if (!$this->mfa->status($user->id)['enabled']) return new Response('MFA is not enabled.', 409);
        $this->disable->disable($user->id);
        $request->getSession()->invalidate();
        $this->tokens->setToken(null);

        return new RedirectResponse('/login', 303);
    }

    private function administrator(): Principal
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        if (!$user instanceof Principal || $user->context !== 'admin' || $this->tokens->getToken() instanceof TwoFactorTokenInterface) {
            throw $this->createAccessDeniedException('Current administrator required.');
        }

        return $user;
    }
}
