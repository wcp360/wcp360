<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Wcp360\Core\Module\IdentityAccess\Infrastructure\CurrentPasswordConfirmation;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateMfa;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateSessions;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Shared\Security\RuntimeContext;

final class SessionController extends AbstractController
{
    public function __construct(private readonly CandidateSessions $sessions, private readonly RuntimeContext $runtime,
        private readonly TokenStorageInterface $tokens, private readonly CurrentPasswordConfirmation $confirmation,
        private readonly CandidateMfa $mfa) {}

    #[Route('/account/sessions', name: 'wcp_account_sessions', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $user = $this->owner();
        $page = $request->query->getString('page', '1');
        if (preg_match('/^[1-9][0-9]{0,4}$/D', $page) !== 1 || (int) $page > 10000) throw new BadRequestHttpException('Invalid session page.');
        $selector = $this->selector($request);

        try {
            $requiredStrength = $user->context === 'admin' && $this->mfa->status($user->id)['enabled'] ? 2 : 1;

            return $this->render('identity/sessions.html.twig', ['context' => $user->context, 'page_title' => 'Mes sessions',
                'report' => $this->sessions->listing($user->id, $user->context, $selector, (int) $page, $requiredStrength)]);
        } catch (HttpExceptionInterface $exception) {
            throw $exception;
        } catch (\Throwable) {
            return new Response('Session management is unavailable. Try again later.', 503);
        }
    }

    #[Route('/account/sessions/revoke-all', name: 'wcp_sessions_revoke_all', methods: ['POST'])]
    public function revokeAll(Request $request): Response
    {
        return $this->change($request, null);
    }

    #[Route('/account/sessions/{id}/revoke', name: 'wcp_sessions_revoke', methods: ['POST'])]
    public function revoke(Request $request, string $id): Response
    {
        return $this->change($request, $id);
    }

    private function change(Request $request, ?string $target): Response
    {
        $user = $this->owner();
        $selector = $this->selector($request);
        $input = $request->request->all();
        if (array_diff(array_keys($input), ['_token', 'current_password']) || !is_string($input['_token'] ?? null)
            || !is_string($input['current_password'] ?? null) || strlen($input['current_password']) > 256) throw new BadRequestHttpException('Invalid session revocation.');
        $intent = $target === null ? 'session_revoke_all' : 'session_revoke_'.$target;
        try {
            $this->confirmation->confirm($request, $user, $intent);
            unset($input);
            if ($target === null) {
                $this->sessions->revokeAll($user->id, $user->context);
            } else {
                $this->sessions->revoke($user->id, $user->context, $target);
            }
        } catch (HttpExceptionInterface $exception) {
            throw $exception;
        } catch (\Throwable) {
            return new Response('Session management is unavailable. Try again later.', 503);
        }
        if ($target === null || $target === $selector) {
            $request->getSession()->invalidate();
            $this->tokens->setToken(null);

            return new RedirectResponse('/login', 303);
        }

        return new RedirectResponse('/account/sessions', 303);
    }

    private function owner(): Principal
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        if (!$user instanceof Principal || !in_array($user->context, ['user', 'admin'], true) || $user->context !== $this->runtime->context) {
            throw $this->createAccessDeniedException('Current session owner required.');
        }

        return $user;
    }

    private function selector(Request $request): string
    {
        $stamp = $request->getSession()->get('_wcp_registry');
        if (!is_array($stamp) || !is_string($stamp['selector'] ?? null)) throw $this->createAccessDeniedException('Current session required.');

        return $stamp['selector'];
    }
}
