<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Platform\Application;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Wcp360\Core\Module\Hosting\Application\ApplicationServerPolicy;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;

final readonly class ApplicationServerSettings
{
    public function __construct(private WorkbenchStore $store, private ApplicationServerPolicy $policy) {}

    /** @return array{application_server: string, revision: int} */
    public function current(): array
    {
        $row = $this->store->db->fetchAssociative('SELECT application_server,revision FROM wcp_candidate_runtime_settings WHERE id=1');
        if (!$row) throw new \RuntimeException('Migrate the candidate runtime settings first.');
        return ['application_server' => $row['application_server'], 'revision' => (int) $row['revision']];
    }

    /** @return array{schema:string,settings:array{application_server:string,revision:int},capabilities:array{frankenphp_worker_plans:bool}} */
    public function inspect(string $actor): array
    {
        $this->store->requireAdmin($actor);
        $settings = $this->current();
        $this->store->requireAdmin($actor);

        return ['schema' => 'wcp360.candidate.runtime-settings/1', 'settings' => $settings,
            'capabilities' => ['frankenphp_worker_plans' => $this->policy->workerPlansEnabled]];
    }

    public function change(string $actor, string $value, int $expectedRevision, ?string $requestId = null): void
    {
        \Wcp360\Core\Shared\Correlation\RequestId::assertValid($requestId);
        $server = $this->policy->parse($value);
        if ($expectedRevision < 1) throw new BadRequestHttpException('Invalid settings revision.');
        $this->store->db->transactional(function () use ($actor, $server, $expectedRevision, $requestId): void {
            $this->store->requireAdmin($actor, true);
            $this->policy->assertAvailable($server);
            $changed = $this->store->db->executeStatement(
                'UPDATE wcp_candidate_runtime_settings SET application_server=?,revision=revision+1 WHERE id=1 AND revision=?',
                [$server->value, $expectedRevision],
            );
            if ($changed !== 1) throw new ConflictHttpException('Runtime settings changed; refresh before retrying.');
            $this->store->audit($actor, null, 'candidate.runtime.default.changed', null, $requestId);
        });
    }
}
