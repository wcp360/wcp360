<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidOptionException;
use Symfony\Component\Console\Tester\CommandTester;
use Wcp360\Core\Module\Hosting\Application\ApplicationCatalogueSource;
use Wcp360\Core\Module\Hosting\Application\ListApplicationCandidates;
use Wcp360\Core\Module\Hosting\Domain\ApplicationCatalogueEntry;
use Wcp360\Core\Module\Hosting\Domain\ApplicationCatalogueSnapshot;
use Wcp360\Core\Module\Hosting\UI\Console\ApplicationCatalogueCommand;
use Wcp360\Core\Shared\Security\RuntimeContext;
use Wcp360\Core\Tests\Fixtures\ApplicationCatalogueFixture;

final class ApplicationCatalogueCommandTest extends TestCase
{
    public static function allowedContexts(): iterable
    {
        yield 'worker' => ['worker'];
        yield 'setup' => ['setup'];
    }

    #[DataProvider('allowedContexts')]
    public function testAllowedContextsRenderExactMetadataAsRawJson(string $context): void
    {
        $fields = ApplicationCatalogueFixture::fields('<error>Éditorial</error>');
        $fields['Remarque'] = "literal <info>note</info>\nsecond line";
        $entry = ApplicationCatalogueEntry::fromFields($fields);
        $source = $this->createMock(ApplicationCatalogueSource::class);
        $source->expects(self::once())->method('read')->willReturn(ApplicationCatalogueSnapshot::available([$entry], str_repeat('a', 64), 100));
        $command = new ApplicationCatalogueCommand(new ListApplicationCandidates($source), self::runtime($context));
        self::assertSame('wcp:candidate:apps:catalogue', $command->getName());
        foreach (['path', 'url', 'install', 'apply', 'shell'] as $unsupported) self::assertFalse($command->getDefinition()->hasOption($unsupported));
        $tester = new CommandTester($command);

        self::assertSame(Command::SUCCESS, $tester->execute(['--app-profile' => 'PHP', '--limit' => '25', '--offset' => '0'],
            ['interactive' => false, 'decorated' => true]));
        $output = $tester->getDisplay();
        $report = json_decode($output, true, 32, JSON_THROW_ON_ERROR);
        self::assertSame('available', $report['status']);
        self::assertSame('PHP', $report['filters']['profile']);
        self::assertSame($fields, $report['items'][0]['source_fields']);
        self::assertFalse($report['items'][0]['installable']);
        self::assertFalse($report['deployment_available']);
        self::assertStringContainsString('<error>Éditorial</error>', $output);
        self::assertStringNotContainsString("\x1b", $output);
        self::assertCount(1, preg_split('/\r?\n/', trim($output)));
    }

    public static function refusedContexts(): iterable
    {
        yield 'user' => ['user'];
        yield 'admin' => ['admin'];
    }

    #[DataProvider('refusedContexts')]
    public function testOtherContextsCannotReadTheCatalogue(string $context): void
    {
        $source = $this->createMock(ApplicationCatalogueSource::class);
        $source->expects(self::never())->method('read');
        $tester = new CommandTester(new ApplicationCatalogueCommand(new ListApplicationCandidates($source), self::runtime($context)));
        self::assertSame(Command::INVALID, $tester->execute([]));
        $report = json_decode($tester->getDisplay(), true, 16, JSON_THROW_ON_ERROR);
        self::assertSame('refused', $report['status']);
        self::assertFalse($report['installable']);
    }

    public static function invalidArguments(): iterable
    {
        yield 'unsupported limit' => [['--limit' => '1']];
        yield 'leading zero' => [['--offset' => '01']];
        yield 'too large offset' => [['--offset' => '2049']];
        yield 'negative offset' => [['--offset' => '-1']];
        yield 'terminal escape' => [['--app-profile' => "\x1b[31m"]];
        yield 'empty category' => [['--category' => '']];
    }

    #[DataProvider('invalidArguments')]
    public function testInvalidKnownOptionsDoNotReadTheSource(array $arguments): void
    {
        $source = $this->createMock(ApplicationCatalogueSource::class);
        $source->expects(self::never())->method('read');
        $tester = new CommandTester(new ApplicationCatalogueCommand(new ListApplicationCandidates($source), self::runtime('worker')));
        self::assertSame(Command::INVALID, $tester->execute($arguments));
        self::assertSame('invalid', json_decode($tester->getDisplay(), true, 16, JSON_THROW_ON_ERROR)['status']);
    }

    public static function unknownOptions(): iterable
    {
        yield 'path selector' => [['--path' => '/private/catalogue.csv']];
        yield 'URL selector' => [['--url' => 'https://example.invalid/catalogue.csv']];
        yield 'apply flag' => [['--apply' => true]];
        yield 'install flag' => [['--install' => true]];
        yield 'shell fragment' => [['--shell' => 'synthetic']];
    }

    #[DataProvider('unknownOptions')]
    public function testUnknownDomainOptionsAreRejectedWithoutReadingTheSource(array $arguments): void
    {
        $source = $this->createMock(ApplicationCatalogueSource::class);
        $source->expects(self::never())->method('read');
        $tester = new CommandTester(new ApplicationCatalogueCommand(new ListApplicationCandidates($source), self::runtime('worker')));
        $this->expectException(InvalidOptionException::class);
        $tester->execute($arguments);
    }

    public static function unavailableSources(): iterable
    {
        yield 'missing' => [ApplicationCatalogueSnapshot::notConfigured(), 'not_configured'];
        yield 'invalid' => [ApplicationCatalogueSnapshot::failed(), 'failed'];
    }

    #[DataProvider('unavailableSources')]
    public function testMissingAndInvalidSourcesDoNotBecomeSuccessfulOrInstallable(ApplicationCatalogueSnapshot $snapshot, string $status): void
    {
        $source = $this->createMock(ApplicationCatalogueSource::class);
        $source->expects(self::once())->method('read')->willReturn($snapshot);
        $tester = new CommandTester(new ApplicationCatalogueCommand(new ListApplicationCandidates($source), self::runtime('setup')));
        self::assertSame(Command::FAILURE, $tester->execute([]));
        $report = json_decode($tester->getDisplay(), true, 16, JSON_THROW_ON_ERROR);
        self::assertSame($status, $report['status']);
        self::assertSame([], $report['items']);
        self::assertSame(['sha256' => null, 'bytes' => null], $report['source']);
        self::assertFalse($report['deployment_available']);
    }

    public function testAnUnexpectedSourceFailureDoesNotExposePathsOrCauses(): void
    {
        $source = $this->createMock(ApplicationCatalogueSource::class);
        $source->expects(self::once())->method('read')->willThrowException(new \RuntimeException('/private/synthetic-catalogue-cause'));
        $tester = new CommandTester(new ApplicationCatalogueCommand(new ListApplicationCandidates($source), self::runtime('setup')));
        self::assertSame(Command::FAILURE, $tester->execute([]));
        self::assertSame('failed', json_decode($tester->getDisplay(), true, 16, JSON_THROW_ON_ERROR)['status']);
        self::assertStringNotContainsString('private', $tester->getDisplay());
        self::assertStringNotContainsString('synthetic-catalogue-cause', $tester->getDisplay());
    }

    private static function runtime(string $context): RuntimeContext
    {
        return new RuntimeContext($context, true, 'test', 'localhost');
    }
}
