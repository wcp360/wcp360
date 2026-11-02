<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Wcp360\Core\Module\Hosting\Application\PreviewApplicationPlan;
use Wcp360\Core\Shared\Http\RootPlanPreviewRequest;

final class ApplicationPlanPreviewController extends AbstractController
{
    public function __construct(private readonly PreviewApplicationPlan $preview, private readonly RootPlanPreviewRequest $boundary) {}

    #[Route('/workbench/v1/application/recipes', name: 'wcp_api_application_recipes', methods: ['GET'])]
    public function recipes(Request $request): JsonResponse
    {
        $actor = $this->boundary->actor($request);
        return $this->json($this->preview->recipes($actor->id));
    }

    #[Route('/workbench/settings/application-preview', name: 'wcp_application_preview', methods: ['POST'])]
    public function browser(Request $request): Response
    {
        $actor = $this->boundary->actor($request);
        $fields = $request->request->all();
        if (!is_string($fields['_token'] ?? null)) throw $this->createAccessDeniedException('Origin and CSRF required.');
        $this->boundary->csrf($request, 'application-plan-preview', $fields['_token']);
        unset($fields['_token']);
        [$host, $recipe, $application, $requireHtaccess] = $this->input($fields);
        $report = $this->preview->preview($actor->id, $host, $recipe, $application, $requireHtaccess);
        return $this->render('hosting/application_preview.html.twig', ['context' => 'admin', 'report' => $report,
            'application_recipes' => $this->preview->recipes($actor->id)['recipes'],
            'application_profiles' => \Wcp360\Core\Shared\Platform\ApplicationRecipeCatalogue::profiles()]);
    }

    #[Route('/workbench/v1/application/preview', name: 'wcp_api_application_preview', methods: ['POST'])]
    public function api(Request $request): JsonResponse
    {
        $actor = $this->boundary->actor($request);
        $this->boundary->csrf($request, 'application-plan-preview', $request->headers->get('X-CSRF-Token'));
        if ($request->getContentTypeFormat() !== 'json' || strlen($request->getContent()) > 4096) {
            throw new BadRequestHttpException('A bounded JSON body is required.');
        }
        [$host, $recipe, $application, $requireHtaccess] = $this->input($request->toArray());
        return $this->json($this->preview->preview($actor->id, $host, $recipe, $application, $requireHtaccess));
    }

    /** @param array<array-key,mixed> $fields
     * @return array{string,string,string,bool}
     */
    private function input(array $fields): array
    {
        $htaccess = array_key_exists('require_htaccess', $fields) ? $fields['require_htaccess'] : false;
        $application = array_key_exists('application', $fields) ? $fields['application'] : 'php';
        if (array_diff(array_keys($fields), ['hostname','recipe','application','require_htaccess']) !== []
            || !is_string($fields['hostname'] ?? null) || !is_string($fields['recipe'] ?? null)
            || !is_string($application) || !in_array($htaccess, [true,false,'1','0'], true)) {
            throw new BadRequestHttpException('Invalid Application preview input.');
        }
        return [$fields['hostname'], $fields['recipe'], $application, in_array($htaccess, [true,'1'], true)];
    }
}
