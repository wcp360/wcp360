<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Operations\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\Operations\Application\ListHostingJobs;
use Wcp360\Core\Module\Operations\Domain\JobListQuery;

final class JobListController extends AbstractController
{
    public function __construct(private readonly ListHostingJobs $jobs, private readonly CandidateAuthorization $authorization) {}

    #[Route('/workbench/jobs', name: 'wcp_jobs', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $actor = $this->actor();
        $input = $request->query->all();
        $tenants = $this->authorization->tenantsForActor($actor->id);
        if (!array_key_exists('tenant', $input)) {
            if ($tenants === []) {
                if ($input !== []) throw new BadRequestHttpException('Select an accessible hosting workspace first.');

                return $this->render('operations/jobs.html.twig', ['context' => 'user', 'tenants' => [],
                    'filters' => [], 'page' => ['items' => [], 'pagination' => ['limit' => 25, 'next' => null, 'previous' => null]]]);
            }
            $input['tenant'] = $tenants[0]['id'];
        }
        $query = JobListQuery::fromArray($input);

        return $this->render('operations/jobs.html.twig', ['context' => 'user', 'tenants' => $tenants,
            'filters' => $query->filters(), 'page' => $this->jobs->page($actor->id, $query)]);
    }

    #[Route('/workbench/v1/jobs', name: 'wcp_api_jobs', methods: ['GET'])]
    public function api(Request $request): JsonResponse
    {
        $actor = $this->actor();
        $query = JobListQuery::fromArray($request->query->all());

        return $this->json($this->jobs->page($actor->id, $query));
    }

    private function actor(): Principal
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $actor = $this->getUser();
        if (!$actor instanceof Principal || $actor->context !== 'user') throw $this->createAccessDeniedException();

        return $actor;
    }
}
