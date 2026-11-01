<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactoryInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CompletePasswordRecovery;
use Wcp360\Core\Module\IdentityAccess\Domain\NewAccountPassword;
use Wcp360\Core\Shared\Security\RuntimeContext;

/** Anonymous bearer-code redemption; no account lookup/request or automatic login. */
final class PasswordRecoveryController extends AbstractController
{
    public function __construct(private readonly CompletePasswordRecovery $recovery, private readonly RuntimeContext $runtime,
        private readonly RateLimiterFactoryInterface $limiter, private readonly TokenStorageInterface $tokens) {}

    #[Route('/reset-password', name: 'wcp_password_recovery', methods: ['GET'])]
    public function index(#[\SensitiveParameter] Request $request): Response
    {
        if ($this->getUser() !== null) return new Response('Sign out before using account recovery.', 403);
        if ($request->query->count() !== 0) return new Response('Recovery codes must be submitted through the form.', 400);
        return $this->page(null, $this->recovery->available() ? 200 : 503);
    }

    #[Route('/reset-password', name: 'wcp_password_recovery_complete', methods: ['POST'])]
    public function complete(#[\SensitiveParameter] Request $request): Response
    {
        if ($this->getUser() !== null) return new Response('Sign out before using account recovery.', 403);
        if (!$this->recovery->available()) return $this->page(null, 503);
        if ($request->query->count() !== 0 || $request->headers->get('Origin') !== $request->getSchemeAndHttpHost()) {
            return new Response('Recovery request refused.', 403);
        }
        $fields = $request->request->all();
        if (array_diff(array_keys($fields), ['_token', 'recovery_code', 'new_password', 'repeat_password'])) {
            return new Response('Invalid recovery request.', 400);
        }
        foreach (['_token', 'recovery_code', 'new_password', 'repeat_password'] as $field) {
            if (!is_string($fields[$field] ?? null) || strlen($fields[$field]) > 256) return new Response('Invalid recovery request.', 400);
        }
        if (!$this->isCsrfTokenValid('password_recovery', $fields['_token'])) return new Response('Recovery request refused.', 403);
        $limit = $this->limiter->create($this->recovery->limiterKey($request->getClientIp() ?? 'unknown'))->consume(1);
        if (!$limit->isAccepted()) {
            return new Response('Too many recovery attempts. Try again later.', 429,
                ['Retry-After' => (string) max(1, $limit->getRetryAfter()->getTimestamp() - time())]);
        }
        try {
            NewAccountPassword::require($fields['new_password']);
            if (!hash_equals($fields['new_password'], $fields['repeat_password'])) throw new \InvalidArgumentException('Password confirmation mismatch.');
        } catch (\InvalidArgumentException) {
            return $this->page('Saisissez deux mots de passe identiques, avec au moins 14 caractères et au plus 256 octets.', 422);
        }
        try {
            $this->recovery->complete($fields['recovery_code'], $fields['new_password']);
        } catch (\Throwable $error) {
            if ($error->getMessage() === CompletePasswordRecovery::UNCERTAIN) {
                $request->getSession()->invalidate();
                return new Response('Recovery outcome is uncertain. Contact the instance operator before retrying.', 503);
            }
            return $this->page('Le code est invalide, expiré ou déjà utilisé. Demandez un nouveau code à votre opérateur.', 422);
        }
        unset($fields);
        $request->getSession()->invalidate();
        $this->tokens->setToken(null);
        return new RedirectResponse('/login', 303);
    }

    private function page(?string $error, int $status): Response
    {
        return $this->render('identity/password_recovery.html.twig', ['context' => $this->runtime->context,
            'available' => $this->recovery->available(), 'error' => $error], new Response(status: $status));
    }
}
