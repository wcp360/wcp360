<?php

declare(strict_types=1);
namespace Wcp360\Core\Module\Hosting\Application;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Wcp360\Core\Module\Hosting\Domain\ApplicationServer;

final readonly class ApplicationServerPolicy
{
    public function __construct(public bool $workerPlansEnabled = false) {}

    public function parse(string $value): ApplicationServer
    {
        return ApplicationServer::tryFrom($value) ?? throw new BadRequestHttpException('Unknown application server.');
    }

    public function assertAvailable(ApplicationServer $server): void
    {
        if ($server === ApplicationServer::FrankenPhpWorker && !$this->workerPlansEnabled) {
            throw new AccessDeniedHttpException('FrankenPHP Worker plans are disabled on this candidate node.');
        }
    }

    public function select(string $requested, string $default, bool $acknowledged): ApplicationServer
    {
        $server = $this->parse($requested === 'inherit' ? $default : $requested);
        $this->assertAvailable($server);
        if ($server === ApplicationServer::FrankenPhpWorker && !$acknowledged) {
            throw new BadRequestHttpException('Worker mode requires an explicit application-compatibility acknowledgement.');
        }
        return $server;
    }
}
