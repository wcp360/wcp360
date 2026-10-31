<?php

declare(strict_types=1);
namespace Wcp360\Core\Shared\Security;
final readonly class RuntimeContext
{
    public function __construct(public string $context, bool $enabled, string $environment, public string $host)
    {
        if (!$enabled || !in_array($environment, ['dev', 'test'], true)) {
            throw new \RuntimeException('The executable candidate requires WCP_CANDIDATE=1 and APP_ENV=dev/test. Production is not qualified.');
        }
        if (!in_array($context, ['admin', 'user', 'worker', 'setup'], true)) throw new \InvalidArgumentException('Unknown runtime context.');
        if (!in_array($host, ['user.localhost', 'admin.localhost', 'localhost'], true)) throw new \InvalidArgumentException('Candidate HTTP is restricted to local development hosts.');
    }
    public function requireBrowserHost(string $host): void
    {
        if (!in_array($this->context, ['admin','user'], true) || $host !== $this->host) throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException('Wrong runtime origin.');
    }
}
