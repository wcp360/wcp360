<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\LazyCommand;
use Symfony\Component\Console\Tester\ApplicationTester;
use Wcp360\Core\Module\Hosting\Application\ApplicationCatalogueSource;
use Wcp360\Core\Module\Hosting\Infrastructure\CsvApplicationCatalogueSource;
use Wcp360\Core\Module\Hosting\UI\Console\ApplicationCatalogueCommand;

final class ApplicationCatalogueWiringTest extends KernelTestCase
{
    public function testConfiguredEditorialSourceIsObservedOrExplicitlyAbsentWithoutBlockingBoot(): void
    {
        $kernel = self::bootKernel();
        $source = self::getContainer()->get(ApplicationCatalogueSource::class);
        self::assertInstanceOf(CsvApplicationCatalogueSource::class, $source);
        $path = $kernel->getProjectDir().'/../WCP360_App_Deploy/WCP360_App_Catalogue_Candidat.csv';
        $snapshot = $source->read();
        if (!file_exists($path) && !is_link($path)) {
            self::assertSame('not_configured', $snapshot->status);
            self::assertSame([], $snapshot->entries);
            self::assertNull($snapshot->sha256);
            return;
        }
        self::assertSame('available', $snapshot->status);
        self::assertNotEmpty($snapshot->entries);
        $raw = file_get_contents($path);
        self::assertIsString($raw);
        self::assertSame(hash('sha256', $raw), $snapshot->sha256);
        self::assertSame(strlen($raw), $snapshot->bytes);
        foreach ($snapshot->entries as $entry) {
            self::assertFalse($entry->projection()['installable']);
            self::assertFalse($entry->projection()['deployment_available']);
        }
    }

    public function testKernelDiscoversTheCommandAndSourceWithoutReadingTheExternalCatalogue(): void
    {
        $kernel = self::bootKernel();
        self::assertInstanceOf(CsvApplicationCatalogueSource::class, self::getContainer()->get(ApplicationCatalogueSource::class));
        $application = new Application($kernel);
        $command = $application->find('wcp:candidate:apps:catalogue');
        if ($command instanceof LazyCommand) $command = $command->getCommand();
        self::assertInstanceOf(ApplicationCatalogueCommand::class, $command);
        self::assertSame('wcp:candidate:apps:catalogue', $command->getName());
        // Help uses Symfony's real definition merge without executing the catalogue use case.
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $tester = new ApplicationTester($application);
        self::assertSame(0, $tester->run(['command' => 'wcp:candidate:apps:catalogue', '--help' => true],
            ['interactive' => false, 'decorated' => false]));
        self::assertStringContainsString('--app-profile', $tester->getDisplay());
        self::assertStringContainsString('--profile', $tester->getDisplay());
        self::assertFalse($command->getDefinition()->getOption('profile')->acceptValue());
        self::assertTrue($command->getDefinition()->getOption('app-profile')->isValueRequired());
    }
}
