<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Wcp360\Core\Module\Hosting\Application\ListPreparedSites;
use Wcp360\Core\Module\Hosting\Domain\SiteListQuery;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Shared\Http\TenantPageQuery;

final class SiteListController extends AbstractController
{
    public function __construct(private readonly ListPreparedSites $sites, private readonly CandidateAuthorization $authorization) {}

    #[Route('/workbench/sites', name: 'wcp_sites', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $actor = $this->actor();
        $input = $request->query->all();
        $tenants = $this->authorization->tenantsForActor($actor->id);
        if (!array_key_exists('tenant', $input)) {
            if ($tenants === []) {
                if ($input !== []) TenantPageQuery::invalid();

                return $this->render('hosting/sites.html.twig', ['context' => 'user', 'tenants' => [], 'filters' => [],
                    'page' => ['items' => [], 'pagination' => ['limit' => 25, 'next' => null, 'previous' => null]]]);
            }
            $input['tenant'] = $tenants[0]['id'];
        }
        $query = SiteListQuery::fromArray($input);

        return $this->render('hosting/sites.html.twig', ['context' => 'user', 'tenants' => $tenants,
            'filters' => $query->filters(), 'page' => $this->sites->page($actor->id, $query)]);
    }

    #[Route('/workbench/v1/sites', name: 'wcp_api_sites', methods: ['GET'])]
    public function api(Request $request): JsonResponse
    {
        $actor = $this->actor();

        return $this->json($this->sites->page($actor->id, SiteListQuery::fromArray($request->query->all())));
    }

    private function actor(): Principal
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $actor = $this->getUser();
        if (!$actor instanceof Principal || $actor->context !== 'user') throw $this->createAccessDeniedException();

        return $actor;
    }
}
