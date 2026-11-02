<?php

declare(strict_types=1);

namespace Wcp360\Core\Shared\Http;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;

/** Common browser boundary; current database authority remains owned by each use case. */
final readonly class RootPlanPreviewRequest
{
    public function __construct(private Security $security, private CsrfTokenManagerInterface $tokens) {}

    public function actor(Request $request): Principal
    {
        $actor = $this->security->getUser();
        if (!$this->security->isGranted('ROLE_ADMIN') || !$actor instanceof Principal || $actor->context !== 'admin') {
            throw new AccessDeniedHttpException('Administrator context required.');
        }
        if ($request->query->count() !== 0) {
            throw new BadRequestHttpException('Plan previews have no query parameters.');
        }
        return $actor;
    }

    public function csrf(Request $request, string $intent, ?string $value): void
    {
        if ($request->headers->get('Origin') !== $request->getSchemeAndHttpHost()
            || !$this->tokens->isTokenValid(new CsrfToken($intent, $value ?? ''))) {
            throw new AccessDeniedHttpException('Origin and CSRF required.');
        }
    }
}
