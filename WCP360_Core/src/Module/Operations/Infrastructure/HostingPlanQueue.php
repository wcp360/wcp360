<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Operations\Infrastructure;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Uid\Uuid;
use Wcp360\Core\Module\Hosting\Domain\SiteRuntimeProfile;

/**
 * Fixed candidate plan transitions; not a general job executor or lease manager.
 *
 * @phpstan-type ClaimedPlan array{id:string,tenant_id:string,site_id:string,actor_id:string,request_id:?string}
 */
final readonly class HostingPlanQueue
{
    public const RECOVERY_BATCH_SIZE = 100;
    private const FIELDS = 'id,tenant_id,site_id,actor_id,request_id';

    public function __construct(private WorkbenchStore $store) {}

    /** @return ClaimedPlan|null */
    public function claim(): ?array
    {
        $this->requireOwnTransaction();

        return $this->store->db->transactional(function (): ?array {
            $db = $this->store->db;
            $row = $db->fetchAssociative('SELECT '.self::FIELDS." FROM wcp_candidate_jobs WHERE state='QUEUED' ORDER BY created_at,id LIMIT 1".$this->lock(true));
            if ($row === false) return null;
            $changed = $db->executeStatement("UPDATE wcp_candidate_jobs SET state='RUNNING',started_at=? WHERE id=? AND state='QUEUED'",
                [WorkbenchStore::now(), $row['id']]);
            if ($changed !== 1) return null;
            $this->store->audit($row['actor_id'], $row['tenant_id'], 'candidate.hosting.plan.started', $row['id'], $row['request_id']);

            return [
                'id' => (string) $row['id'], 'tenant_id' => (string) $row['tenant_id'],
                'site_id' => (string) $row['site_id'], 'actor_id' => (string) $row['actor_id'],
                'request_id' => $row['request_id'] === null ? null : (string) $row['request_id'],
            ];
        });
    }

    /**
     * Publish only a still-running plan. A lost publication race has no writes/audit.
     * A NULL result means rendering failed. Observed access revocation discards a rendered result.
     */
    public function finish(string $id, ?string $result, ?SiteRuntimeProfile $expectedProfile = null): bool
    {
        $this->requireOwnTransaction();
        if (!Uuid::isValid($id)) throw new \InvalidArgumentException('Invalid candidate job identity.');

        return $this->store->db->transactional(function () use ($id, $result, $expectedProfile): bool {
            $db = $this->store->db;
            $row = $db->fetchAssociative('SELECT '.self::FIELDS." FROM wcp_candidate_jobs WHERE id=? AND state='RUNNING'".$this->lock(), [$id]);
            if ($row === false) return false;
            if ($result !== null) {
                try {
                    $this->store->requirePermission($row['actor_id'], $row['tenant_id']);
                } catch (AccessDeniedHttpException) {
                    // Revocation observed at publication is a known refusal, never a successful plan.
                    $result = null;
                }
            }
            if ($result !== null && $expectedProfile !== null) {
                // Hold the site row lock through publication; a changed topology cannot publish old output.
                $site = $db->fetchAssociative(
                    'SELECT application_server,web_stack,stack_revision FROM wcp_candidate_sites WHERE id=? AND tenant_id=?'.$this->lock(),
                    [$row['site_id'], $row['tenant_id']]);
                try {
                    if ($site === false || !$expectedProfile->matches(SiteRuntimeProfile::fromStorage($site))) $result = null;
                } catch (\InvalidArgumentException) {
                    $result = null;
                }
            }
            $state = $result === null ? 'FAILED' : 'SUCCEEDED';
            $changed = $db->executeStatement("UPDATE wcp_candidate_jobs SET state=?,result=?,completed_at=? WHERE id=? AND state='RUNNING'",
                [$state, $result, WorkbenchStore::now(), $id]);
            if ($changed !== 1) return false;
            $siteChanged = $db->executeStatement('UPDATE wcp_candidate_sites SET state=?,revision=revision+1 WHERE id=? AND tenant_id=?',
                [$state === 'SUCCEEDED' ? 'PLAN_READY' : 'PLAN_FAILED', $row['site_id'], $row['tenant_id']]);
            if ($siteChanged !== 1) throw new \RuntimeException('Plan site transition unavailable.');
            $this->store->audit($row['actor_id'], $row['tenant_id'], 'candidate.hosting.plan.'.strtolower($state), $id, $row['request_id']);

            return true;
        });
    }

    /** Maintenance only: stop rendering workers first; this cannot detect live renderers or lost hosts. */
    public function markInterrupted(): int
    {
        $this->requireOwnTransaction();

        return $this->store->db->transactional(function (): int {
            $db = $this->store->db;
            $rows = $db->fetchAllAssociative('SELECT '.self::FIELDS." FROM wcp_candidate_jobs WHERE state='RUNNING' ORDER BY created_at,id LIMIT "
                .self::RECOVERY_BATCH_SIZE.$this->lock(true));
            $changedCount = 0;
            foreach ($rows as $row) {
                $changed = $db->executeStatement("UPDATE wcp_candidate_jobs SET state='UNKNOWN_OUTCOME' WHERE id=? AND state='RUNNING'", [$row['id']]);
                if ($changed !== 1) continue;
                $this->store->audit($row['actor_id'], $row['tenant_id'], 'candidate.hosting.plan.interrupted', $row['id'], $row['request_id']);
                ++$changedCount;
            }

            return $changedCount;
        });
    }

    private function lock(bool $skipLocked = false): string
    {
        return $this->store->db->getDatabasePlatform() instanceof PostgreSQLPlatform
            ? ($skipLocked ? ' FOR UPDATE SKIP LOCKED' : ' FOR UPDATE') : '';
    }

    private function requireOwnTransaction(): void
    {
        if ($this->store->db->isTransactionActive()) {
            throw new \LogicException('Candidate queue transitions require their own database transaction.');
        }
    }
}
