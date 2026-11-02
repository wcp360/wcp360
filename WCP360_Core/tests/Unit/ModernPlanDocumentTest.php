<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Hosting\Domain\ApplicationServer;
use Wcp360\Core\Module\Hosting\Domain\CandidateHostname;
use Wcp360\Core\Module\Hosting\Domain\ModernPlanDocument;
use Wcp360\Core\Module\Hosting\Domain\PreparedPlanDocument;

final class ModernPlanDocumentTest extends TestCase
{
    /** @return array<string,string> */
    private static function fields(): array
    {
        return [
            'schema' => 'wcp360.candidate.plan/3',
            'web_stack' => 'modern',
            'application_server' => 'frankenphp_worker',
            'caddy' => "# synthetic \u{00e9}\r\nhttp://modern.test:8082 {\n\tbind 127.0.0.1\n}\n",
            'systemd_unit' => "[Service]\nUser=wcp_synthetic\n",
        ];
    }

    public function testModernRetainsExactBytesAndExportsOnlyItsTwoArtifacts(): void
    {
        $fields = self::fields();
        $document = ModernPlanDocument::decode(json_encode($fields, JSON_THROW_ON_ERROR));
        self::assertSame($fields, $document->fields());
        self::assertSame(['caddy', 'systemd'], array_keys($document->files()));
        self::assertSame('Caddyfile', $document->files()['caddy']['name']);
        self::assertSame('frankenphp-modern.service', $document->files()['systemd']['name']);
        self::assertSame($fields['caddy'], $document->files()['caddy']['content']);
        self::assertSame($fields['systemd_unit'], $document->files()['systemd']['content']);
    }

    public function testDecodedContentAllowsExactly64KiBAcrossAllFiveStrings(): void
    {
        $fields = self::fields();
        $overhead = strlen($fields['schema']) + strlen($fields['web_stack'])
            + strlen($fields['application_server']) + strlen($fields['systemd_unit']);
        $fields['caddy'] = str_repeat('/', PreparedPlanDocument::MAX_CONTENT_BYTES - $overhead);
        self::assertSame(65536, array_sum(array_map(strlen(...), $fields)));
        $json = json_encode($fields, JSON_THROW_ON_ERROR);
        self::assertGreaterThan(PreparedPlanDocument::MAX_CONTENT_BYTES, strlen($json));
        self::assertSame($fields, ModernPlanDocument::decode($json)->fields());

        $fields['caddy'] .= 'x';
        $this->expectException(\InvalidArgumentException::class);
        ModernPlanDocument::fromArray($fields);
    }

    public function testJsonEnvelopeAllowsItsBoundaryAndRefusesOneMoreByteBeforeParsing(): void
    {
        $fields = self::fields();
        $json = json_encode($fields, JSON_THROW_ON_ERROR);
        $padded = $json.str_repeat(' ', PreparedPlanDocument::MAX_JSON_BYTES - strlen($json));
        self::assertSame($fields, ModernPlanDocument::decode($padded)->fields());
        $this->expectException(\InvalidArgumentException::class);
        ModernPlanDocument::decode($padded.' ');
    }

    /** @return iterable<string,array{array<array-key,mixed>}> */
    public static function rejectedFields(): iterable
    {
        $fields = self::fields();
        yield 'legacy FPM envelope' => [['schema' => 'wcp360.candidate.plan/1', 'nginx' => 'synthetic', 'php_fpm' => 'synthetic']];
        yield 'legacy Advanced envelope' => [['schema' => 'wcp360.candidate.plan/2', 'nginx' => 'synthetic',
            'application_server' => 'frankenphp_worker', 'caddy' => 'synthetic', 'systemd_unit' => 'synthetic']];
        yield 'FPM schema in Modern shape' => [array_replace($fields, ['schema' => 'wcp360.candidate.plan/1'])];
        yield 'Advanced schema in Modern shape' => [array_replace($fields, ['schema' => 'wcp360.candidate.plan/2'])];
        yield 'Advanced topology marker' => [array_replace($fields, ['web_stack' => 'advanced'])];
        yield 'unknown topology marker' => [array_replace($fields, ['web_stack' => 'synthetic-unknown'])];
        yield 'FPM engine marker' => [array_replace($fields, ['application_server' => 'php_fpm'])];
        yield 'unknown engine marker' => [array_replace($fields, ['application_server' => 'synthetic-unknown'])];
        yield 'extra Nginx artifact' => [$fields + ['nginx' => 'synthetic']];
        yield 'extra FPM artifact' => [$fields + ['php_fpm' => 'synthetic']];
        yield 'wrong field order' => [array_reverse($fields, true)];
        foreach (array_keys($fields) as $missing) {
            $incomplete = $fields;
            unset($incomplete[$missing]);
            yield 'missing '.$missing => [$incomplete];
        }
        yield 'NUL' => [array_replace($fields, ['caddy' => "synthetic\0marker"])];
        yield 'escape control' => [array_replace($fields, ['caddy' => "synthetic\x1bmarker"])];
        yield 'DEL control' => [array_replace($fields, ['systemd_unit' => "synthetic\x7fmarker"])];
        yield 'vertical tab control' => [array_replace($fields, ['systemd_unit' => "synthetic\x0bmarker"])];
        yield 'bad UTF8' => [array_replace($fields, ['caddy' => "\xff"])];
        yield 'empty content' => [array_replace($fields, ['caddy' => ''])];
        yield 'array content' => [array_replace($fields, ['caddy' => ['synthetic']])];
        yield 'boolean content' => [array_replace($fields, ['systemd_unit' => true])];
        yield 'integer content' => [array_replace($fields, ['systemd_unit' => 1])];
        yield 'null content' => [array_replace($fields, ['systemd_unit' => null])];
    }

    /** @param array<array-key,mixed> $fields */
    #[DataProvider('rejectedFields')]
    public function testMalformedOrDifferentTopologyFieldsCannotBecomeModernArtifacts(array $fields): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ModernPlanDocument::fromArray($fields);
    }

    /** @return iterable<string,array{string,class-string<\Throwable>}> */
    public static function rejectedJson(): iterable
    {
        yield 'null' => ['null', \InvalidArgumentException::class];
        yield 'array' => ['[]', \InvalidArgumentException::class];
        yield 'boolean' => ['true', \InvalidArgumentException::class];
        yield 'string' => ['"synthetic"', \InvalidArgumentException::class];
        yield 'broken JSON' => ['{', \JsonException::class];
        yield 'trailing JSON object' => ['{}{}', \JsonException::class];
    }

    /** @param class-string<\Throwable> $exception */
    #[DataProvider('rejectedJson')]
    public function testScalarMalformedAndTrailingJsonCannotBecomeModernArtifacts(string $json, string $exception): void
    {
        $this->expectException($exception);
        ModernPlanDocument::decode($json);
    }

    /** @return iterable<string,array{ApplicationServer}> */
    public static function legacyEngines(): iterable
    {
        yield 'FPM' => [ApplicationServer::PhpFpm];
        yield 'Advanced Worker' => [ApplicationServer::FrankenPhpWorker];
    }

    #[DataProvider('legacyEngines')]
    public function testModernCannotBeConsumedAsEitherLegacyRuntime(ApplicationServer $server): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PreparedPlanDocument::fromArray(self::fields(), $server);
    }

    public function testCandidateHostnamePolicyRetainsAsciiAndLengthBoundaries(): void
    {
        $boundary = str_repeat('a', 63).'.'.str_repeat('b', 63).'.'.str_repeat('c', 63).'.'.str_repeat('d', 56).'.test';
        self::assertSame(253, strlen($boundary));
        foreach (['a.test', '1.test', 'xn--bcher-kva.test', str_repeat('a', 63).'.test', $boundary] as $host) {
            self::assertTrue(CandidateHostname::valid($host), $host);
        }
        foreach ([
            '', '.test', 'test', 'site.com', 'SITE.test', '-site.test', 'site-.test', 'site..test',
            'site_test.test', '127.0.0.1', 'site.test:8082', "site.test\n", "site.test\r\nHost: private",
            'site.test;include /etc/passwd', "caf\u{00e9}.test", str_repeat('a', 64).'.test',
            str_repeat('a', 63).'.'.str_repeat('b', 63).'.'.str_repeat('c', 63).'.'.str_repeat('d', 57).'.test',
        ] as $host) {
            self::assertFalse(CandidateHostname::valid($host), 'Invalid synthetic hostname admitted.');
        }
    }
}
