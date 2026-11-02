<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Operations\Application;

use Wcp360\Core\Module\Hosting\Application\ApplicationServerPolicy;
use Wcp360\Core\Module\Hosting\Infrastructure\GoPlanRenderer;
use Wcp360\Core\Module\Operations\Infrastructure\HostingPlanQueue;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;

final readonly class RunHostingPlan
{
    private HostingPlanQueue $queue;

    public function __construct(private WorkbenchStore $store, private GoPlanRenderer $renderer, private ApplicationServerPolicy $policy)
    {
        $this->queue = new HostingPlanQueue($store);
    }

    public function runOne(): ?string
    {
        // The claim and required audit commit before any external renderer starts.
        $job = $this->queue->claim();
        if ($job === null) return null;
        $result = null;
        try {
            $this->store->requirePermission($job['actor_id'], $job['tenant_id']);
            $site = $this->store->db->fetchAssociative(
                'SELECT hostname,application_server,worker_acknowledged FROM wcp_candidate_sites WHERE id=? AND tenant_id=?',
                [$job['site_id'], $job['tenant_id']]);
            if ($site === false) throw new \RuntimeException('Plan site unavailable.');
            $server = $this->policy->select($site['application_server'], 'php_fpm', (bool) $site['worker_acknowledged']);
            $result = json_encode($this->renderer->render($job['site_id'], $site['hostname'], $server, (bool) $site['worker_acknowledged']), JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            // No privileged effect is dispatched by the renderer; publish a bounded failure.
            $result = null;
        }
        if (!$this->queue->finish($job['id'], $result)) {
            throw new \RuntimeException('Job state changed; result publication refused.');
        }

        return $job['id'];
    }

    public function markInterrupted(): int
    {
        return $this->queue->markInterrupted();
    }
}
