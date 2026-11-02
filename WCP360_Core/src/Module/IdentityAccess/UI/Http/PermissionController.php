<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Wcp360\Core\Module\IdentityAccess\Application\CandidatePermissionAdministration;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;

final class PermissionController extends AbstractController
{
    public function __construct(private readonly CandidatePermissionAdministration $administration) {}

    #[Route('/workbench/access', name: 'wcp_access_permissions', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $actor = $this->administrator();
        if (array_diff(array_keys($request->query->all()), ['page', 'user'])) throw new BadRequestHttpException('Invalid access-list filters.');
        $page = $request->query->getString('page', '1');
        $user = $request->query->getString('user', '');
        if ($user !== '') $user = \Wcp360\Core\Shared\Http\CursorPageQuery::uuid($user);
        if (preg_match('/^[1-9][0-9]{0,4}$/D', $page) !== 1 || (int) $page > 10000) {
            throw new BadRequestHttpException('Invalid access-list page.');
        }

        return $this->render('identity/permissions.html.twig', [
            'context' => 'admin', 'page_title' => 'Accès des utilisateurs',
            'report' => $this->administration->listing($actor->id, (int) $page, $user !== '' ? $user : null),
        ]);
    }

    #[Route('/workbench/access/{actor}/{tenant}/denial', name: 'wcp_access_denial', methods: ['POST'])]
    public function change(Request $request, string $actor, string $tenant): Response
    {
        $administrator = $this->administrator();
        $view = $request->query->getString('view', '');
        if (array_diff(array_keys($request->query->all()), ['view']) || !in_array($view, ['', 'user'], true)) throw new BadRequestHttpException('Invalid access-change view.');
        $input = $request->request->all();
        if (array_diff(array_keys($input), ['_token', 'denied']) || !is_string($input['_token'] ?? null)
            || !is_string($input['denied'] ?? null) || !in_array($input['denied'], ['0', '1'], true)) {
            throw new BadRequestHttpException('Invalid access change.');
        }
        if (!$this->isCsrfTokenValid('candidate_access_denial_'.$actor.'_'.$tenant, $input['_token'])) {
            return new Response('Access change denied.', 403);
        }
        $this->administration->setDenied($administrator->id, $actor, $tenant, $input['denied'] === '1');

        return $view === 'user'
            ? $this->redirectToRoute('wcp_access_permissions', ['user' => $actor], 303)
            : new RedirectResponse('/workbench/access', 303);
    }

    private function administrator(): Principal
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        if (!$user instanceof Principal || $user->context !== 'admin') {
            throw $this->createAccessDeniedException('Current administrator required.');
        }

        return $user;
    }
}
