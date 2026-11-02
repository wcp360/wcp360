<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Platform\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\Platform\Application\ServerOverview;

final class ServerController extends AbstractController
{
    public function __construct(private readonly ServerOverview $overview) {}

    #[Route('/workbench/server', name: 'wcp_server', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $report = $this->report($request);

        return $this->render('platform/server.html.twig', ['context' => 'admin', 'report' => $report]);
    }

    #[Route('/workbench/v1/server', name: 'wcp_api_server', methods: ['GET'])]
    public function api(Request $request): JsonResponse
    {
        return $this->json($this->report($request));
    }

    /** @return array<string,mixed> */
    private function report(Request $request): array
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $actor = $this->getUser();
        if (!$actor instanceof Principal || $actor->context !== 'admin') throw $this->createAccessDeniedException();
        if ($request->query->count() !== 0) throw new BadRequestHttpException('Server overview has no query parameters.');

        return $this->overview->inspect($actor->id);
    }
}
