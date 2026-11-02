<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Application;

use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Wcp360\Core\Module\Hosting\Domain\ApplicationServer;
use Wcp360\Core\Module\Hosting\Domain\PreparedPlanDocument;
use Wcp360\Core\Module\Operations\Infrastructure\WorkbenchStore;

/** Pure in-memory exports from an authorized successful plan; never reads or writes server files. */
final readonly class PreparedPlanFiles
{
    public function __construct(private WorkbenchStore $store) {}

    /** @return array{job:array<string,mixed>,files:array<string,array{label:string,name:string,content:string}>} */
    public function inspect(string $actor, string $id): array
    {
        $row = $this->store->job($actor, $id);
        $files = [];
        if ($row['state'] === 'SUCCEEDED') {
            try {
                if (!is_string($row['result'])) throw new \InvalidArgumentException('Missing plan.');
                $files = PreparedPlanDocument::decode($row['result'], ApplicationServer::from($row['application_server']))->files();
            } catch (\Throwable) {
                // Never reflect corrupt JSON, embedded data or decoder/driver details to the browser.
                throw new ServiceUnavailableHttpException(null, 'Prepared plan files are unavailable.');
            }
        }
        $this->store->requirePermission($actor, $row['tenant_id']);

        return ['job' => array_intersect_key($row, array_flip([
            'id', 'tenant_id', 'state', 'application_server', 'created_at', 'started_at', 'completed_at', 'request_id',
        ])), 'files' => $files];
    }

    /** @return array{name:string,content:string} */
    public function download(string $actor, string $id, string $artifact): array
    {
        if (!in_array($artifact, ['nginx', 'php-fpm', 'caddy', 'systemd'], true)) throw new NotFoundHttpException();
        $view = $this->inspect($actor, $id);
        if ($view['job']['state'] !== 'SUCCEEDED') throw new ConflictHttpException('A successful prepared plan is required.');
        $file = $view['files'][$artifact] ?? null;
        if ($file === null) throw new NotFoundHttpException();

        return ['name' => 'wcp360-'.(string) $view['job']['id'].'-'.$file['name'], 'content' => $file['content']];
    }
}
