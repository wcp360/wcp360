<?php

declare(strict_types=1);
namespace Wcp360\Core;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
final class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    public function getProjectDir(): string { return dirname(__DIR__); }
    private function runtimeContext(): string
    {
        $context = $_SERVER['WCP_CONTEXT'] ?? $_ENV['WCP_CONTEXT'] ?? getenv('WCP_CONTEXT') ?: 'user';
        if (!in_array($context, ['admin','user','worker','setup'], true)) throw new \RuntimeException('Invalid runtime context.');
        return $context;
    }
    public function boot(): void
    {
        $enabled = $_SERVER['WCP_CANDIDATE'] ?? $_ENV['WCP_CANDIDATE'] ?? getenv('WCP_CANDIDATE');
        if (!filter_var($enabled,FILTER_VALIDATE_BOOLEAN) || !in_array($this->environment,['dev','test'],true)) throw new \RuntimeException('Unqualified development candidate: production and disabled-candidate boot are refused.');
        $this->runtimeContext();
        parent::boot();
    }
    public function getCacheDir(): string { return parent::getCacheDir().'/'.$this->runtimeContext(); }
}
