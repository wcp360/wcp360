<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Observability\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\Observability\Application\ListAuditEvents;
use Wcp360\Core\Module\Observability\Domain\AuditListQuery;

final class AuditController extends AbstractController
{
    public function __construct(private readonly ListAuditEvents $events) {}

    #[Route('/workbench/audit', name: 'wcp_audit', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $actor = $this->administrator();
        $query = AuditListQuery::fromArray($request->query->all());

        return $this->render('observability/audit.html.twig', [
            'context' => 'admin', 'filters' => $query->filters, 'page' => $this->events->page($actor->id, $query),
        ]);
    }

    #[Route('/workbench/v1/audit', name: 'wcp_api_audit', methods: ['GET'])]
    public function api(Request $request): JsonResponse
    {
        $actor = $this->administrator();

        return $this->json($this->events->page($actor->id, AuditListQuery::fromArray($request->query->all())));
    }

    private function administrator(): Principal
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $actor = $this->getUser();
        if (!$actor instanceof Principal || $actor->context !== 'admin') throw $this->createAccessDeniedException();

        return $actor;
    }
}
