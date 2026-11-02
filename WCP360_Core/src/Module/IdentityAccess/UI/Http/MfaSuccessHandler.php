<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\UI\Http;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Twig\Environment;

final readonly class MfaSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private Environment $twig) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        $result = $request->attributes->get('_wcp_mfa_result');
        $request->attributes->remove('_wcp_mfa_result');
        if (is_array($result) && isset($result['recovery_codes']) && is_array($result['recovery_codes'])) {
            try {
                return new Response($this->twig->render('identity/recovery_codes.html.twig', ['codes' => $result['recovery_codes']]));
            } catch (\Throwable) {
                return new Response('Recovery codes could not be displayed. Reset your factor from account security to generate new codes.', 503);
            }
        }

        return new RedirectResponse('/', 303);
    }
}
