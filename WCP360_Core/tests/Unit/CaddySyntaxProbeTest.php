<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;
use Wcp360\Core\Module\Hosting\Infrastructure\CaddySyntaxProbe;
use Wcp360\Core\Module\Hosting\Infrastructure\NativeSyntaxTools;
use Wcp360\Core\Module\Hosting\Infrastructure\SyntaxProcessResult;
use Wcp360\Core\Module\Hosting\Infrastructure\SyntaxProcessRunner;

final class CaddySyntaxProbeTest extends TestCase
{
    private const CONFIG = "{\n    admin off\n    auto_https off\n}\nhttp://probe.test:8082 {\n    bind 127.0.0.1\n    respond 204\n}\n";
    private const ADAPTED = '{"admin":{"disabled":true},"apps":{"http":{"servers":{"srv0":{"listen":["127.0.0.1:8082"]}}}}}';

    public static function tools(): iterable
    {
        yield 'Caddy' => [false, 'caddy', '/usr/bin/caddy'];
        yield 'FrankenPHP' => [true, 'frankenphp', '/usr/local/bin/frankenphp'];
    }

    #[DataProvider('tools')]
    public function testOnlyFixedAdaptArgumentsWithUnchangedStdinAndNoProvisioning(bool $frankenphp, string $tool, string $binary): void
    {
        $tools = $this->createMock(NativeSyntaxTools::class);
        $tools->expects(self::once())->method('binary')->with($tool)->willReturn($binary);
        $runner = $this->createMock(SyntaxProcessRunner::class);
        $captured = null;
        $runner->expects(self::once())->method('run')->willReturnCallback(
            static function (Process $process) use (&$captured): SyntaxProcessResult {
                $captured = $process;
                return new SyntaxProcessResult('passed', self::ADAPTED);
            });
        self::assertSame('passed', (new CaddySyntaxProbe($runner, $tools))->check(self::CONFIG, $frankenphp));
        self::assertInstanceOf(Process::class, $captured);
        $expected = new Process([$binary, 'adapt', '--config', '-', '--adapter', 'caddyfile']);
        self::assertSame($expected->getCommandLine(), $captured->getCommandLine());
        self::assertSame(self::CONFIG, $captured->getInput());
        self::assertSame('/', $captured->getWorkingDirectory());
        self::assertSame(5.0, $captured->getTimeout());
        foreach (['--validate', ' reload', ' start', ' run'] as $forbidden) {
            self::assertStringNotContainsString($forbidden, $captured->getCommandLine());
        }
    }

    #[DataProvider('tools')]
    public function testUnavailableProviderDoesNotRunOrFallBack(bool $frankenphp, string $tool, string $binary): void
    {
        $tools = $this->createMock(NativeSyntaxTools::class);
        $tools->expects(self::once())->method('binary')->with($tool)->willReturn(null);
        $runner = $this->createMock(SyntaxProcessRunner::class);
        $runner->expects(self::never())->method('run');
        self::assertSame('not_configured', (new CaddySyntaxProbe($runner, $tools))->check(self::CONFIG, $frankenphp));
    }

    public static function refusedInputs(): iterable
    {
        yield 'empty document' => [''];
        yield 'over content byte limit' => [str_repeat(' ', 65537)];
        yield 'import at start' => ["import /private/Caddyfile\n"];
        yield 'import in block' => ["http://probe.test {\n import /private/*\n}\n"];
        yield 'inline import' => ['http://probe.test { import /private/Caddyfile }'];
        yield 'environment expansion' => ['http://probe.test { respond {$PRIVATE_VALUE} }'];
    }

    #[DataProvider('refusedInputs')]
    public function testUnboundedOrExternallyExpandedSourceNeverReachesProvider(string $content): void
    {
        $tools = $this->createMock(NativeSyntaxTools::class);
        $tools->expects(self::never())->method('binary');
        $runner = $this->createMock(SyntaxProcessRunner::class);
        $runner->expects(self::never())->method('run');
        self::assertSame('failed', (new CaddySyntaxProbe($runner, $tools))->check($content, false));
    }

    public static function invalidOutputs(): iterable
    {
        yield 'empty' => [''];
        yield 'malformed' => ['{"apps":'];
        yield 'scalar' => ['true'];
        yield 'null' => ['null'];
        yield 'root array' => ['[]'];
        yield 'empty object' => ['{}'];
        yield 'apps array' => ['{"apps":[]}'];
        yield 'missing http' => ['{"apps":{}}'];
        yield 'http array' => ['{"apps":{"http":[]}}'];
        yield 'missing servers' => ['{"apps":{"http":{}}}'];
        yield 'servers array' => ['{"apps":{"http":{"servers":[]}}}'];
        yield 'empty servers' => ['{"apps":{"http":{"servers":{}}}}'];
        yield 'boolean server' => ['{"apps":{"http":{"servers":{"srv0":false}}}}'];
        yield 'null server' => ['{"apps":{"http":{"servers":{"srv0":null}}}}'];
        yield 'array server' => ['{"apps":{"http":{"servers":{"srv0":[]}}}}'];
        yield 'invalid UTF8' => ["\xff"];
        yield 'trailing JSON' => [self::ADAPTED.'{}'];
        yield 'depth limit' => ['{"apps":{"http":{"servers":{"srv0":{}}}},"extra":'.str_repeat('{"child":', 65).'null'.str_repeat('}', 65).'}'];
        yield 'over stdout byte limit' => [self::ADAPTED.str_repeat(' ', 262145 - strlen(self::ADAPTED))];
    }

    #[DataProvider('invalidOutputs')]
    public function testMalformedUnboundedOrNonCaddyOutputCannotPass(string $output): void
    {
        $tools = $this->createMock(NativeSyntaxTools::class);
        $tools->expects(self::once())->method('binary')->with('caddy')->willReturn('/usr/bin/caddy');
        $runner = $this->createMock(SyntaxProcessRunner::class);
        $runner->expects(self::once())->method('run')->willReturn(new SyntaxProcessResult('passed', $output));
        self::assertSame('failed', (new CaddySyntaxProbe($runner, $tools))->check(self::CONFIG, false));
    }

    public function testBoundedAdaptedDocumentIsAcceptedAtTheExactOutputLimit(): void
    {
        $tools = $this->createMock(NativeSyntaxTools::class);
        $tools->expects(self::once())->method('binary')->with('caddy')->willReturn('/usr/bin/caddy');
        $runner = $this->createMock(SyntaxProcessRunner::class);
        $output = self::ADAPTED.str_repeat(' ', 262144 - strlen(self::ADAPTED));
        $runner->expects(self::once())->method('run')->willReturn(new SyntaxProcessResult('passed', $output));
        self::assertSame('passed', (new CaddySyntaxProbe($runner, $tools))->check(self::CONFIG, false));
    }

    public function testSourceIsAcceptedAtTheExactInputLimitWithoutRewriting(): void
    {
        $tools = $this->createMock(NativeSyntaxTools::class);
        $tools->expects(self::once())->method('binary')->with('caddy')->willReturn('/usr/bin/caddy');
        $runner = $this->createMock(SyntaxProcessRunner::class);
        $content = self::CONFIG.str_repeat(' ', 65536 - strlen(self::CONFIG));
        $captured = null;
        $runner->expects(self::once())->method('run')->willReturnCallback(
            static function (Process $process) use (&$captured): SyntaxProcessResult {
                $captured = $process->getInput();
                return new SyntaxProcessResult('passed', self::ADAPTED);
            });
        self::assertSame('passed', (new CaddySyntaxProbe($runner, $tools))->check($content, false));
        self::assertSame($content, $captured);
    }

    public function testFailedNativeProcessCannotPublishValidLookingOutputOrPrivateDiagnostics(): void
    {
        $tools = $this->createMock(NativeSyntaxTools::class);
        $tools->expects(self::once())->method('binary')->with('frankenphp')->willReturn('/usr/local/bin/frankenphp');
        $runner = $this->createMock(SyntaxProcessRunner::class);
        $runner->expects(self::once())->method('run')->willReturn(new SyntaxProcessResult('failed', self::ADAPTED));
        self::assertSame('failed', (new CaddySyntaxProbe($runner, $tools))->check(self::CONFIG, true));
    }

    public function testRunnerFailureIsRedactedAndDoesNotFallBackToAnotherProvider(): void
    {
        $tools = $this->createMock(NativeSyntaxTools::class);
        $tools->expects(self::once())->method('binary')->with('caddy')->willReturn('/usr/bin/caddy');
        $runner = $this->createMock(SyntaxProcessRunner::class);
        $runner->expects(self::once())->method('run')->willThrowException(new \RuntimeException('private-native-parser-cause'));
        self::assertSame('failed', (new CaddySyntaxProbe($runner, $tools))->check(self::CONFIG, false));
    }
}
