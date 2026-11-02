<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Application;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Wcp360\Core\Module\Hosting\Domain\ApplicationServer;
use Wcp360\Core\Module\Hosting\Domain\CandidateHostname;
use Wcp360\Core\Module\Hosting\Domain\ModernPlanDocument;
use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;

final readonly class PreviewModernHostingPlan
{
    public function __construct(private CandidateAuthorization $authorization,
        private ApplicationServerPolicy $policy, private ModernPlanRenderer $renderer) {}

    /** @return array<string,mixed> */
    public function preview(string $actor, string $host, bool $acknowledged): array
    {
        $this->authorization->requireAdmin($actor);
        $this->policy->assertAvailable(ApplicationServer::FrankenPhpWorker);
        if (!CandidateHostname::valid($host) || !$acknowledged) {
            throw new BadRequestHttpException('A valid .test hostname and explicit Worker acknowledgement are required.');
        }
        // A new synthetic identifier, never an existing tenant/site selector.
        $site = WorkbenchStore::id();
        try {
            $document = ModernPlanDocument::fromArray($this->renderer->renderModern($site, $host, true));
        } catch (\Throwable) {
            throw new ServiceUnavailableHttpException(null, 'Modern preview is unavailable. No activation was dispatched.');
        }
        $this->authorization->requireAdmin($actor);

        return ['schema' => 'wcp360.candidate.modern-preview/1', 'web_stack' => 'modern',
            'scope' => 'single_site_loopback', 'host_changes' => false, 'persisted' => false,
            'activation_available' => false, 'listener_allocation' => 'not_reserved',
            'preview_site_id' => $site, 'hostname' => $host, 'files' => $document->files()];
    }
}
