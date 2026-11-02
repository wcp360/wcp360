<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Wcp360\Core\Module\Hosting\Application\PreviewModernHostingPlan;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;

final class ModernPlanPreviewController extends AbstractController
{
    public function __construct(private readonly PreviewModernHostingPlan $preview) {}

    #[Route('/workbench/settings/modern-preview', name: 'wcp_modern_preview', methods: ['POST'])]
    public function browser(Request $request): Response
    {
        $actor = $this->administrator();
        $this->query($request);
        $fields = $request->request->all();
        if (!is_string($fields['_token'] ?? null)) throw $this->createAccessDeniedException('Origin and CSRF required.');
        $this->csrf($request, $fields['_token']);
        unset($fields['_token']);
        [$host, $acknowledged] = $this->input($fields);
        $report = $this->preview->preview($actor->id, $host, $acknowledged);

        return $this->render('hosting/modern_preview.html.twig', ['context' => 'admin', 'report' => $report,
            'worker_plans_enabled' => true]);
    }

    #[Route('/workbench/v1/web-stacks/modern-preview', name: 'wcp_api_modern_preview', methods: ['POST'])]
    public function api(Request $request): JsonResponse
    {
        $actor = $this->administrator();
        $this->query($request);
        $this->csrf($request, $request->headers->get('X-CSRF-Token'));
        if ($request->getContentTypeFormat() !== 'json' || strlen($request->getContent()) > 4096) {
            throw new BadRequestHttpException('A bounded JSON body is required.');
        }
        [$host, $acknowledged] = $this->input($request->toArray());

        return $this->json($this->preview->preview($actor->id, $host, $acknowledged));
    }

    private function administrator(): Principal
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $actor = $this->getUser();
        if (!$actor instanceof Principal || $actor->context !== 'admin') throw $this->createAccessDeniedException();
        return $actor;
    }

    private function query(Request $request): void
    {
        if ($request->query->count() !== 0) throw new BadRequestHttpException('Modern preview has no query parameters.');
    }

    private function csrf(Request $request, ?string $token): void
    {
        if ($request->headers->get('Origin') !== $request->getSchemeAndHttpHost()
            || !$this->isCsrfTokenValid('modern-plan-preview', $token)) {
            throw $this->createAccessDeniedException('Origin and CSRF required.');
        }
    }

    /** @param array<array-key,mixed> $fields
     * @return array{string,bool}
     */
    private function input(array $fields): array
    {
        if (array_diff(array_keys($fields), ['hostname','worker_acknowledged']) !== []
            || !is_string($fields['hostname'] ?? null)
            || !in_array($fields['worker_acknowledged'] ?? false, [true,false,'1','0'], true)) {
            throw new BadRequestHttpException('Invalid Modern preview input.');
        }
        return [$fields['hostname'], in_array($fields['worker_acknowledged'] ?? false, [true,'1'], true)];
    }
}
