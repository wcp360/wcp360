<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\UI\Http;

use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\TwoFactorFormRendererInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;

final readonly class MfaFormRenderer implements TwoFactorFormRendererInterface
{
    public function __construct(private CandidateMfa $mfa, private TokenStorageInterface $tokens, private Environment $twig) {}

    /** @param array<string, mixed> $templateVars */
    public function renderForm(Request $request, array $templateVars): Response
    {
        $user = $this->tokens->getToken()?->getUser();
        if (!$user instanceof Principal || $user->context !== 'admin') {
            throw new AccessDeniedException('Administrator MFA required.');
        }
        $status = $this->mfa->status($user->id);

        return new Response($this->twig->render('identity/mfa.html.twig', [
            'enabled' => $status['enabled'], 'configured' => $status['configured'],
            'error' => !empty($templateVars['authenticationError']), 'secret' => null,
        ]), $status['configured'] ? 200 : 503);
    }
}
