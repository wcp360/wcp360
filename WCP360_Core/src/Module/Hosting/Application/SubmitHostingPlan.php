<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Application;

use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;
use Wcp360\Core\Module\Platform\Application\ApplicationServerSettings;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

final readonly class SubmitHostingPlan
{
    public function __construct(
        private WorkbenchStore $store,
        private ApplicationServerPolicy $policy,
        private ApplicationServerSettings $settings,
    ) {}

    /** @param array<string, mixed> $input
     * @return array{job_id: string, replayed: bool}
     */
    public function submit(string $actor, array $input): array
    {
        $required = ['tenant_id', 'hostname', 'idempotency_key'];
        $allowed = [...$required, 'application_server', 'worker_acknowledged'];
        if (array_diff(array_keys($input), $allowed) || array_diff($required, array_keys($input))) {
            throw new BadRequestHttpException('Required fields: tenant_id, hostname and idempotency_key; optional application_server and worker_acknowledged.');
        }
        foreach ($required as $field) {
            if (!is_string($input[$field])) throw new BadRequestHttpException('Expected string field.');
        }
        $requested = array_key_exists('application_server', $input) ? $input['application_server'] : 'inherit';
        $ack = array_key_exists('worker_acknowledged', $input) ? $input['worker_acknowledged'] : false;
        if (!is_string($requested) || !in_array($ack, [true, false, '1', '0'], true)) {
            throw new BadRequestHttpException('Invalid application-server selection or acknowledgement.');
        }
        if ($requested !== 'inherit') $this->policy->parse($requested);
        $acknowledged = $ack === true || $ack === '1';
        ['tenant_id' => $tenant, 'hostname' => $host, 'idempotency_key' => $key] = $input;
        if (!Uuid::isValid($tenant) || !preg_match('/^[A-Za-z0-9_-]{16,128}$/D', $key)) throw new BadRequestHttpException('Invalid identity or idempotency key.');
        if ($host !== strtolower($host) || strlen($host) > 253 || !str_ends_with($host, '.test') || filter_var($host, FILTER_VALIDATE_IP) || !preg_match('/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+test$/D', $host)) {
            throw new BadRequestHttpException('A lowercase ASCII .test hostname is required for the candidate.');
        }
        $this->store->requirePermission($actor, $tenant);
        // Hash caller intent, not today's global default. A retry keeps its original logical job.
        $semantic = $requested === 'inherit' && !$acknowledged ? [$tenant, $host] : [$tenant, $host, $requested, $acknowledged];
        $fingerprint = hash('sha256', json_encode($semantic, JSON_THROW_ON_ERROR));
        $db = $this->store->db;
        return $db->transactional(function () use ($db, $actor, $tenant, $host, $key, $fingerprint, $requested, $acknowledged): array {
            $lock = $db->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform ? ' FOR UPDATE' : '';
            $db->fetchOne('SELECT id FROM wcp_candidate_tenants WHERE id=?'.$lock, [$tenant]);
            $this->store->requirePermission($actor, $tenant);
            $old = $db->fetchAssociative('SELECT id,fingerprint FROM wcp_candidate_jobs WHERE actor_id=? AND tenant_id=? AND idempotency_key=?', [$actor, $tenant, $key]);
            if ($old) {
                if (!hash_equals($old['fingerprint'], $fingerprint)) throw new ConflictHttpException('IDEMPOTENCY_CONFLICT');
                return ['job_id' => $old['id'], 'replayed' => true];
            }
            $server = $this->policy->select($requested, $this->settings->current()['application_server'], $acknowledged);
            if ($db->fetchOne('SELECT 1 FROM wcp_candidate_sites WHERE hostname=?', [$host])) throw new ConflictHttpException('Hostname already reserved.');
            $site = WorkbenchStore::id();
            $job = WorkbenchStore::id();
            $db->insert('wcp_candidate_sites', [
                'id' => $site, 'tenant_id' => $tenant, 'hostname' => $host, 'state' => 'PLAN_QUEUED', 'revision' => 1,
                'application_server' => $server->value, 'worker_acknowledged' => (int) $acknowledged,
            ]);
            $db->insert('wcp_candidate_jobs', [
                'id' => $job, 'tenant_id' => $tenant, 'site_id' => $site, 'actor_id' => $actor,
                'idempotency_key' => $key, 'fingerprint' => $fingerprint, 'state' => 'QUEUED', 'created_at' => WorkbenchStore::now(),
            ]);
            $this->store->audit($actor, $tenant, 'candidate.hosting.plan.queued', $job);
            return ['job_id' => $job, 'replayed' => false];
        });
    }
}
