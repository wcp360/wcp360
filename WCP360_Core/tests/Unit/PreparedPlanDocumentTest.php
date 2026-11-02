<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Hosting\Domain\ApplicationServer;
use Wcp360\Core\Module\Hosting\Domain\PreparedPlanDocument;

final class PreparedPlanDocumentTest extends TestCase
{
    private static function fpm(): array
    {
        return ['schema' => 'wcp360.candidate.plan/1', 'nginx' => "server {\n}\n", 'php_fpm' => "[pool]\n; commenté\r\n"];
    }

    public function testFpmDocumentRetainsOriginalFieldsAndMapsOnlyItsTwoArtifacts(): void
    {
        $fields = self::fpm();
        $document = PreparedPlanDocument::decode(json_encode($fields, JSON_THROW_ON_ERROR), ApplicationServer::PhpFpm);
        self::assertSame($fields, $document->fields());
        self::assertSame(['nginx', 'php-fpm'], array_keys($document->files()));
        self::assertSame('php-fpm.conf', $document->files()['php-fpm']['name']);
        self::assertSame($fields['php_fpm'], $document->files()['php-fpm']['content']);
    }

    public function testWorkerDocumentHasOnlyNginxCaddyAndSystemdFiles(): void
    {
        $fields = ['schema' => 'wcp360.candidate.plan/2', 'nginx' => 'worker proxy',
            'application_server' => 'frankenphp_worker', 'caddy' => "localhost {\n}\n", 'systemd_unit' => "[Service]\n"];
        $document = PreparedPlanDocument::fromArray($fields, ApplicationServer::FrankenPhpWorker);
        self::assertSame($fields, $document->fields());
        self::assertSame(['nginx', 'caddy', 'systemd'], array_keys($document->files()));
        self::assertSame('Caddyfile', $document->files()['caddy']['name']);
        self::assertSame('frankenphp.service', $document->files()['systemd']['name']);
    }

    public function testDecodedContentLimitAllowsItsBoundaryAndRejectsOneMoreByte(): void
    {
        $fields = self::fpm();
        $overhead = strlen($fields['schema']) + strlen($fields['php_fpm']);
        $fields['nginx'] = str_repeat('/', PreparedPlanDocument::MAX_CONTENT_BYTES - $overhead);
        // PHP persistence can escape each slash even though the Go transport did not.
        $json = json_encode($fields, JSON_THROW_ON_ERROR);
        self::assertGreaterThan(PreparedPlanDocument::MAX_CONTENT_BYTES, strlen($json));
        self::assertSame($fields, PreparedPlanDocument::decode($json, ApplicationServer::PhpFpm)->fields());
        $fields['nginx'] .= 'x';
        $this->expectException(\InvalidArgumentException::class);
        PreparedPlanDocument::fromArray($fields, ApplicationServer::PhpFpm);
    }

    public function testOversizedStoredEnvelopeIsRefusedBeforeParsing(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PreparedPlanDocument::decode(str_repeat(' ', PreparedPlanDocument::MAX_JSON_BYTES + 1), ApplicationServer::PhpFpm);
    }

    public static function invalidFields(): iterable
    {
        yield 'wrong schema' => [array_replace(self::fpm(), ['schema' => 'private-invalid-schema'])];
        yield 'unknown field' => [self::fpm() + ['extra' => 'private-extra-value']];
        yield 'missing field' => [['schema' => 'wcp360.candidate.plan/1', 'nginx' => 'x']];
        yield 'wrong order' => [['nginx' => 'x', 'schema' => 'wcp360.candidate.plan/1', 'php_fpm' => 'x']];
        yield 'array' => [array_replace(self::fpm(), ['nginx' => ['private-array']])];
        yield 'empty' => [array_replace(self::fpm(), ['nginx' => ''])];
        yield 'nul' => [array_replace(self::fpm(), ['nginx' => "private\0marker"])];
        yield 'escape control' => [array_replace(self::fpm(), ['nginx' => "private\x1bmarker"])];
        yield 'bad UTF8' => [array_replace(self::fpm(), ['nginx' => "\xff"])];
        yield 'wrong runtime schema' => [['schema' => 'wcp360.candidate.plan/2', 'nginx' => 'x',
            'application_server' => 'frankenphp_worker', 'caddy' => 'x', 'systemd_unit' => 'x']];
    }

    #[DataProvider('invalidFields')]
    public function testMalformedFieldsAreNotAcceptedAsExportableConfiguration(array $fields): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PreparedPlanDocument::fromArray($fields, ApplicationServer::PhpFpm);
    }

    public function testWorkerRuntimeMarkerMustMatchTheSelectedEngine(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PreparedPlanDocument::fromArray(['schema' => 'wcp360.candidate.plan/2', 'nginx' => 'x',
            'application_server' => 'php_fpm', 'caddy' => 'x', 'systemd_unit' => 'x'], ApplicationServer::FrankenPhpWorker);
    }

    public function testJsonScalarIsNotADocument(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PreparedPlanDocument::decode('null', ApplicationServer::PhpFpm);
    }
}
