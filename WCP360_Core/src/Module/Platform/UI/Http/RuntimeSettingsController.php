<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Platform\UI\Http;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Wcp360\Core\Module\IdentityAccess\Domain\Principal;
use Wcp360\Core\Module\Platform\Application\ApplicationServerSettings;
use Wcp360\Core\Shared\Http\RequestReference;

final class RuntimeSettingsController extends AbstractController
{
    public function __construct(private readonly ApplicationServerSettings $settings, private readonly RequestReference $references) {}

    #[Route('/workbench/settings', name: 'wcp_runtime_settings', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $actor = $this->administrator();
        $this->noQuery($request);

        return $this->page($actor->id);
    }

    #[Route('/workbench/settings/application-server', name: 'wcp_application_server', methods: ['POST'])]
    public function change(Request $request): Response
    {
        $actor = $this->administrator();
        if (array_diff(array_keys($request->query->all()), ['view']) !== []) throw new BadRequestHttpException('Invalid settings navigation.');
        $view = $request->query->getString('view', '');
        if (!in_array($view, ['', 'settings'], true)) throw new BadRequestHttpException('Invalid settings navigation.');
        $fields = $request->request->all();
        if (!is_string($fields['_token'] ?? null) || !$this->isCsrfTokenValid('application-server', $fields['_token'])
            || $request->headers->get('Origin') !== $request->getSchemeAndHttpHost()) {
            throw $this->createAccessDeniedException('Origin and CSRF required.');
        }
        unset($fields['_token']);
        [$server, $revision] = $this->input($fields);
        try {
            $this->settings->change($actor->id, $server, $revision, $this->references->get($request));
        } catch (ConflictHttpException $error) {
            if ($view !== 'settings') throw $error;
            return $this->page($actor->id, true);
        }
        if ($view === 'settings') {
            $this->addFlash('runtime_settings', 'Le serveur d’application par défaut a été enregistré.');
            return $this->redirectToRoute('wcp_runtime_settings', [], 303);
        }

        // Keep the existing dashboard form's route, CSRF intent and redirect.
        return $this->redirectToRoute('wcp_dashboard', [], 303);
    }

    #[Route('/workbench/v1/settings/application-server', name: 'wcp_api_runtime_settings', methods: ['GET'])]
    public function api(Request $request): JsonResponse
    {
        $actor = $this->administrator();
        $this->noQuery($request);

        return $this->json($this->settings->inspect($actor->id));
    }

    #[Route('/workbench/v1/settings/application-server', name: 'wcp_api_runtime_settings_change', methods: ['POST'])]
    public function apiChange(Request $request): JsonResponse
    {
        $actor = $this->administrator();
        $this->noQuery($request);
        if ($request->headers->get('Origin') !== $request->getSchemeAndHttpHost()
            || !$this->isCsrfTokenValid('application-server', $request->headers->get('X-CSRF-Token'))) {
            throw $this->createAccessDeniedException('Origin and CSRF required.');
        }
        if ($request->getContentTypeFormat() !== 'json' || strlen($request->getContent()) > 4096) {
            throw new BadRequestHttpException('A bounded JSON body is required.');
        }
        [$server, $revision] = $this->input($request->toArray());
        $this->settings->change($actor->id, $server, $revision, $this->references->get($request));

        return $this->json($this->settings->inspect($actor->id));
    }

    #[Route('/workbench/v1/web-stacks', name: 'wcp_api_web_stacks', methods: ['GET'])]
    public function webStacks(Request $request): JsonResponse
    {
        $actor = $this->administrator();
        $this->noQuery($request);

        return $this->json($this->settings->webStacks($actor->id));
    }

    private function administrator(): Principal
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $actor = $this->getUser();
        if (!$actor instanceof Principal || $actor->context !== 'admin') throw $this->createAccessDeniedException();

        return $actor;
    }

    private function noQuery(Request $request): void
    {
        if ($request->query->count() !== 0) throw new BadRequestHttpException('Runtime settings have no query parameters.');
    }

    /** @param array<array-key,mixed> $fields
     * @return array{string,int}
     */
    private function input(array $fields): array
    {
        $revision = $fields['revision'] ?? null;
        if (array_diff(array_keys($fields), ['application_server', 'revision']) !== []
            || !is_string($fields['application_server'] ?? null)
            || (!is_string($revision) && !is_int($revision))
            || preg_match('/\A[1-9][0-9]{0,8}\z/D', (string) $revision) !== 1) {
            throw new BadRequestHttpException('Invalid runtime settings.');
        }

        return [$fields['application_server'], (int) $revision];
    }

    private function page(string $actor, bool $conflict = false): Response
    {
        return $this->render('platform/settings.html.twig', ['context' => 'admin',
            'report' => $this->settings->inspect($actor), 'conflict' => $conflict,
            'web_stacks' => $this->settings->webStacks($actor)],
            new Response(status: $conflict ? 409 : 200));
    }
}
