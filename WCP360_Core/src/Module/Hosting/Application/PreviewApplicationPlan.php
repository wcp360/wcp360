<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Application;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Wcp360\Core\Module\Hosting\Domain\CandidateHostname;
use Wcp360\Core\Module\Hosting\Domain\ApplicationPlanDocument;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Shared\Platform\ApplicationRecipeCatalogue;

/** Inert Application-specific plans, separate from generic durable site engines. */
final readonly class PreviewApplicationPlan
{
    public function __construct(private CandidateAuthorization $authorization, private ApplicationPlanRenderer $renderer) {}

    /** @return array<string,mixed> */
    public function recipes(string $actor): array
    {
        $this->authorization->requireAdmin($actor);
        $report = ApplicationRecipeCatalogue::report();
        $this->authorization->requireAdmin($actor);
        return $report;
    }

    /** @return array<string,mixed> */
    public function preview(string $actor, string $host, string $recipe, string $application = 'php', bool $requireHtaccess = false): array
    {
        $this->authorization->requireAdmin($actor);
        if (!CandidateHostname::valid($host)) {
            throw new BadRequestHttpException('A lowercase ASCII .test hostname is required.');
        }
        try {
            ApplicationRecipeCatalogue::requireProfile($application);
            $choice = ApplicationRecipeCatalogue::choice($recipe);
        } catch (\InvalidArgumentException) {
            throw new BadRequestHttpException('Unknown Application recipe.');
        }
        if ($requireHtaccess && !$choice['htaccess_supported']) {
            throw new BadRequestHttpException('Native .htaccess requires the Apache/PHP-FPM recipe.');
        }
        $site = WorkbenchStore::id();
        try {
            $document = ApplicationPlanDocument::fromArray($this->renderer->renderApplication($site, $host, $recipe, $application), $recipe, $application);
        } catch (\Throwable) {
            throw new ServiceUnavailableHttpException(null, 'Application preview is unavailable. No installation was dispatched.');
        }
        $this->authorization->requireAdmin($actor);
        return ['schema' => 'wcp360.candidate.application-preview/1', 'application' => $application,
            'recipe' => $recipe, 'web_server' => $choice['web_server'], 'application_server' => $choice['application_server'],
            'htaccess_supported' => $choice['htaccess_supported'], 'scope' => 'single_site_loopback',
            'host_changes' => false, 'persisted' => false, 'deployment_available' => false,
            'performance_measured' => false, 'worker_mode' => false, 'dynamic_page_cache' => 'disabled',
            'listener_allocation' => 'not_reserved', 'preview_site_id' => $site, 'hostname' => $host,
            'files' => $document->files()];
    }
}
