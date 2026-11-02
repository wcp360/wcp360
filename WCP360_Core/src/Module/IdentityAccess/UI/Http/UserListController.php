<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Wcp360\Core\Module\IdentityAccess\Application\ListHostingUsers;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\IdentityAccess\Domain\UserListQuery;

final class UserListController extends AbstractController
{
    public function __construct(private readonly ListHostingUsers $users) {}

    #[Route('/workbench/users', name: 'wcp_users', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $admin = $this->administrator();
        $query = UserListQuery::fromArray($request->query->all());

        return $this->render('identity/users.html.twig', ['context' => 'admin',
            'filters' => $query->filters(), 'page' => $this->users->page($admin->id, $query)]);
    }

    #[Route('/workbench/v1/users', name: 'wcp_api_users', methods: ['GET'])]
    public function api(Request $request): JsonResponse
    {
        $admin = $this->administrator();

        return $this->json($this->users->page($admin->id, UserListQuery::fromArray($request->query->all())));
    }

    private function administrator(): Principal
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $actor = $this->getUser();
        if (!$actor instanceof Principal || $actor->context !== 'admin') throw $this->createAccessDeniedException();

        return $actor;
    }
}
