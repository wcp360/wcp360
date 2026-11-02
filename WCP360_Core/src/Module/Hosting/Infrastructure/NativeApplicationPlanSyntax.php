<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Infrastructure;

use Wcp360\Core\Module\Hosting\Application\ApplicationPlanSyntax;
use Wcp360\Core\Module\Hosting\Domain\ApplicationPlanDocument;

final readonly class NativeApplicationPlanSyntax implements ApplicationPlanSyntax
{
    public function __construct(private NginxSyntaxProbe $nginx, private CaddySyntaxProbe $caddy) {}

    public function check(ApplicationPlanDocument $document): array
    {
        $checks = [];
        foreach ($document->files() as $artifact => $file) {
            $checks[$artifact] = match ($artifact) {
                'nginx' => $this->nginx->check($file['content']),
                'caddy' => $this->caddy->check($file['content'], $document->fields()['application_server'] === 'frankenphp_classic'),
                'apache' => 'not_configured',
                // Synthetic pools have no installed identities. FPM -t also resolves NSS users/groups.
                'php_fpm', 'htaccess', 'systemd_unit' => 'not_run',
                default => throw new \LogicException('Unknown generated artifact.'),
            };
        }
        return $checks;
    }
}
