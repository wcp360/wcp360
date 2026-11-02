<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Platform\Application;

use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;
use Wcp360\Core\Module\Platform\Domain\ServiceObservationSnapshot;
use Wcp360\Core\Module\Platform\Domain\ServiceUnitCatalogue;

/** Current ROOT-panel authority uses the existing active ADMIN context.
 * @phpstan-import-type Unit from ServiceObservationSnapshot
 * @phpstan-type Service array{id:string,label:string,owner:string,observation:string,units:list<Unit>}
 * @phpstan-type Report array{schema:string,observed_at:string,scope:string,status:string,coverage_complete:bool,control_available:bool,health_verified:bool,services:list<Service>}
 */
final readonly class ServiceOverview
{
    public function __construct(private CandidateAuthorization $authorization, private ServiceObservation $observation) {}

    /** @return Report */
    public function inspect(string $actor): array
    {
        $this->authorization->requireAdmin($actor);
        try { $snapshot = $this->observation->read(); }
        catch (\Throwable) { $snapshot = ServiceObservationSnapshot::failed(); }
        $this->authorization->requireAdmin($actor);

        $indexed = [];
        foreach ($snapshot->units as $unit) $indexed[$unit['id']] = $unit;
        $services = [];
        foreach (ServiceUnitCatalogue::entries() as $entry) {
            $units = [];
            foreach ($entry['units'] as $name) {
                if (isset($indexed[$name])) $units[] = $indexed[$name];
            }
            $services[] = ['id' => $entry['id'], 'label' => $entry['label'], 'owner' => $entry['owner'],
                'observation' => $snapshot->status !== 'observed' ? 'unavailable' : ($units === [] ? 'not_observed' : 'observed'),
                'units' => $units];
        }
        return ['schema' => 'wcp360.candidate.service-overview/1', 'observed_at' => gmdate('Y-m-d\TH:i:s\Z'),
            'scope' => 'current_process_namespace', 'status' => $snapshot->status,
            'coverage_complete' => false, 'control_available' => false, 'health_verified' => false, 'services' => $services];
    }
}
