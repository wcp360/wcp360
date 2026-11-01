<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Application\ChangeOwnPassword;
use Wcp360\Core\Module\IdentityAccess\Domain\NewAccountPassword;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\CurrentPasswordConfirmation;
use Wcp360\Core\Shared\Security\RuntimeContext;

final class PasswordController extends AbstractController
{
    public function __construct(private readonly ChangeOwnPassword $passwords,
        private readonly CurrentPasswordConfirmation $confirmation, private readonly CandidateMfa $mfa,
        private readonly RuntimeContext $runtime, private readonly TokenStorageInterface $tokens) {}

    #[Route('/account/password', name: 'wcp_account_password', methods: ['GET'])]
    public function index(): Response
    {
        return $this->page($this->owner());
    }

    #[Route('/account/password', name: 'wcp_account_password_change', methods: ['POST'])]
    public function change(#[\SensitiveParameter] Request $request): Response
    {
        $user = $this->owner();
        $fields = $request->request->all();
        if (array_diff(array_keys($fields), ['_token', 'current_password', 'new_password', 'repeat_password'])) {
            return new Response('Invalid password change.', 400);
        }
        foreach (['_token', 'current_password', 'new_password', 'repeat_password'] as $field) {
            if (!is_string($fields[$field] ?? null) || strlen($fields[$field]) > 256) {
                return new Response('Invalid password change.', 400);
            }
        }
        if (!$this->passwords->available()) return new Response('Password change is unavailable on this instance.', 503);
        try {
            NewAccountPassword::require($fields['new_password']);
            if (!hash_equals($fields['new_password'], $fields['repeat_password'])) {
                return $this->page($user, 'Les deux nouveaux mots de passe doivent être identiques.', 422);
            }
        } catch (\InvalidArgumentException) {
            return $this->page($user, 'Choisissez un mot de passe de 14 caractères minimum et de 256 octets maximum.', 422);
        }
        try {
            $this->confirmation->confirm($request, $user, 'password_change');
            $stamp = $request->getSession()->get('_wcp_registry');
            if (!is_array($stamp) || !is_string($stamp['selector'] ?? null)) throw $this->createAccessDeniedException('Current session required.');
            $proof = null;
            if ($user->context === 'admin') {
                $proof = $request->getSession()->get($this->mfa->status($user->id)['enabled'] ? '_wcp_mfa_proof' : '_wcp_password_proof');
                if (!is_array($proof)) throw $this->createAccessDeniedException('Current administrator authentication required.');
            }
            $this->passwords->change($user, $stamp['selector'], $request->getSession()->getId(), $fields['new_password'], $proof);
        } catch (\InvalidArgumentException) {
            return $this->page($user, 'Choisissez un mot de passe différent du mot de passe actuel.', 422);
        } catch (HttpExceptionInterface $exception) {
            // Never delegate credential-bearing confirmation errors to the debug exception page.
            return new Response('Password change denied. Confirm your identity before retrying.', $exception->getStatusCode(), $exception->getHeaders());
        } catch (\Throwable) {
            $request->getSession()->invalidate();
            $this->tokens->setToken(null);
            return new Response('Password change is unavailable. Sign in again before retrying.', 503);
        }
        unset($fields);
        $request->getSession()->invalidate();
        $this->tokens->setToken(null);

        return new RedirectResponse('/login', 303);
    }

    private function owner(): Principal
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if (!$user instanceof Principal || !$user->active || $user->context !== $this->runtime->context
            || !in_array($user->context, ['user', 'admin'], true)) {
            throw $this->createAccessDeniedException('Current account required.');
        }
        return $user;
    }

    private function page(Principal $user, ?string $error = null, int $status = 200): Response
    {
        return $this->render('identity/password.html.twig', [
            'context' => $user->context, 'available' => $this->passwords->available(), 'error' => $error,
        ], new Response(status: $status));
    }
}
