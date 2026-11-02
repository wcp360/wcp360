<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Platform\Application;

use Wcp360\Core\Module\IdentityAccess\Application\CandidateAuthorization;

/** Current ROOT-panel authority is represented by the existing active ADMIN context.
 * @phpstan-import-type Observation from ServerObservation
 * @phpstan-type Report array{schema:string,observed_at:string,scope:string,server:Observation}
 */
final readonly class ServerOverview
{
    public function __construct(private CandidateAuthorization $authorization, private ServerObservation $observation) {}

    /** @return Report */
    public function inspect(string $actor): array
    {
        $this->authorization->requireAdmin($actor);
        $server = $this->observation->read();
        $this->authorization->requireAdmin($actor);

        return ['schema' => 'wcp360.candidate.server-overview/1', 'observed_at' => gmdate('Y-m-d\TH:i:s\Z'),
            'scope' => 'current_process_namespace', 'server' => $server];
    }
}
