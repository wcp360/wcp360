<?php
declare(strict_types=1);
namespace Wcp360\FileManager\Standalone;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
final class Kernel extends BaseKernel
{
    use MicroKernelTrait;
    public function getProjectDir(): string { return __DIR__; }
}
