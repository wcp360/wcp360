<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Integration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Hosting\Application\ListApplicationCandidates;
use Wcp360\Core\Module\Hosting\Domain\ApplicationCatalogueQuery;
use Wcp360\Core\Module\Hosting\Infrastructure\CsvApplicationCatalogueSource;
use Wcp360\Core\Tests\Fixtures\ApplicationCatalogueFixture;

final class CsvApplicationCatalogueSourceTest extends TestCase
{
    private string $directory;
    private string $path;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir().'/wcp360-catalogue-'.bin2hex(random_bytes(12));
        self::assertTrue(mkdir($this->directory, 0700));
        $this->path = $this->directory.'/catalogue.csv';
    }

    protected function tearDown(): void
    {
        foreach (['link.csv', 'catalogue.csv'] as $name) {
            $path = $this->directory.'/'.$name;
            if (is_link($path) || is_file($path)) unlink($path);
        }
        if (is_dir($this->directory)) rmdir($this->directory);
    }

    public static function encodings(): iterable
    {
        yield 'UTF8' => [false];
        yield 'UTF8 with BOM' => [true];
    }

    #[DataProvider('encodings')]
    public function testReadsExactQuotedValuesAndDistinguishesApplicationExtensionDeployAndLibrary(bool $bom): void
    {
        $wordpress = ApplicationCatalogueFixture::fields();
        $wordpress['Remarque'] = "Éditorial; with \"quotes\"\r\nsecond line";
        $wordpress['Source vérifiée'] = 'https://example.invalid/reference?x=1;y=2';
        $rows = [$wordpress,
            ApplicationCatalogueFixture::fields('WooCommerce', 'Extensions', 'Extension', 'EXTENSION'),
            ApplicationCatalogueFixture::fields('Discourse', 'Forums', 'Application', 'DEPLOY'),
            ApplicationCatalogueFixture::fields('Synthetic library', 'Libraries', 'Bibliothèque', 'DEPENDANCE')];
        $raw = ApplicationCatalogueFixture::csv($rows, $bom);
        self::assertSame(strlen($raw), file_put_contents($this->path, $raw));
        $before = scandir($this->directory);

        $report = (new ListApplicationCandidates(new CsvApplicationCatalogueSource($this->path)))->list(ApplicationCatalogueQuery::fromArray([]));

        self::assertSame('available', $report['status']);
        self::assertSame(['sha256' => hash('sha256', $raw), 'bytes' => strlen($raw)], $report['source']);
        self::assertSame(['total' => 4, 'matched' => 4, 'returned' => 4], $report['counts']);
        self::assertSame(['WordPress', 'WooCommerce', 'Discourse', 'Synthetic library'], array_column($report['items'], 'name'));
        self::assertSame(['PHP', 'EXTENSION', 'DEPLOY', 'DEPENDANCE'], array_column($report['items'], 'profile'));
        foreach ($report['items'] as $index => $item) {
            self::assertSame($rows[$index], $item['source_fields']);
            self::assertFalse($item['installable']);
            self::assertFalse($item['deployment_available']);
            self::assertArrayNotHasKey('id', $item);
            self::assertArrayNotHasKey('version', $item);
        }
        self::assertSame($before, scandir($this->directory));
        self::assertSame($raw, file_get_contents($this->path));
    }

    public function testCategoryProfileAndPaginationAreAppliedWithoutCrossingFilters(): void
    {
        $rows = [];
        for ($index = 0; $index < 110; ++$index) {
            $rows[] = ApplicationCatalogueFixture::fields('App '.$index, $index % 2 === 0 ? 'CMS' : 'Forums',
                'Application', $index % 2 === 0 ? 'PHP' : 'DEPLOY');
        }
        file_put_contents($this->path, ApplicationCatalogueFixture::csv($rows));
        $service = new ListApplicationCandidates(new CsvApplicationCatalogueSource($this->path));
        $first = $service->list(ApplicationCatalogueQuery::fromArray(['category' => 'CMS', 'profile' => 'PHP', 'limit' => '25']));
        $second = $service->list(ApplicationCatalogueQuery::fromArray(['category' => 'CMS', 'profile' => 'PHP', 'limit' => 25, 'offset' => 25]));
        self::assertSame(['total' => 110, 'matched' => 55, 'returned' => 25], $first['counts']);
        self::assertSame(25, $first['pagination']['next_offset']);
        self::assertSame('App 0', $first['items'][0]['name']);
        self::assertSame('App 50', $second['items'][0]['name']);
        self::assertSame([], array_intersect(array_column($first['items'], 'name'), array_column($second['items'], 'name')));
        self::assertSame(['CMS', 'Forums'], $first['categories']);
        self::assertSame(['DEPLOY', 'PHP'], $first['profiles']);
        $empty = $service->list(ApplicationCatalogueQuery::fromArray(['category' => 'CMS', 'profile' => 'DEPLOY']));
        self::assertSame(0, $empty['counts']['matched']);
        self::assertSame([], $empty['items']);
        self::assertNull($empty['pagination']['next_offset']);
        $beyond = $service->list(ApplicationCatalogueQuery::fromArray(['offset' => 2048]));
        self::assertSame([], $beyond['items']);
        self::assertNull($beyond['pagination']['next_offset']);
    }

    public static function invalidDocuments(): iterable
    {
        $row = ApplicationCatalogueFixture::fields();
        $headers = ApplicationCatalogueFixture::HEADERS;
        $duplicateHeader = $headers;
        $duplicateHeader[1] = 'Nom';
        yield 'duplicate header' => [ApplicationCatalogueFixture::csv([$row], headers: $duplicateHeader)];
        $unknownHeader = $headers;
        $unknownHeader[1] = 'installable';
        yield 'unknown header' => [ApplicationCatalogueFixture::csv([$row], headers: $unknownHeader)];
        yield 'missing column' => [ApplicationCatalogueFixture::csv([array_slice($row, 0, 10)])];
        yield 'extra column' => [ApplicationCatalogueFixture::csv([$row + ['installable' => 'true']])];
        yield 'empty record' => [ApplicationCatalogueFixture::csv([$row])."\r\n"];
        yield 'unterminated quoted source' => [ApplicationCatalogueFixture::csv([]).implode(';', array_values($row)).'"unterminated'];
        yield 'quote in unquoted identity' => [ApplicationCatalogueFixture::csv([]).'bad"identity;'.implode(';', array_slice(array_values($row), 1))."\n"];
        yield 'text after closing quote' => [ApplicationCatalogueFixture::csv([]).implode(';', array_slice(array_values($row), 0, 10)).';"https://example.invalid"tail'."\n"];
        yield 'duplicate name' => [ApplicationCatalogueFixture::csv([$row, $row])];
        yield 'case and spacing collision' => [ApplicationCatalogueFixture::csv([$row, ApplicationCatalogueFixture::fields(' wordpress ')])];
        yield 'Unicode compatibility collision' => [ApplicationCatalogueFixture::csv([$row, ApplicationCatalogueFixture::fields('ＷｏｒｄＰｒｅｓｓ')])];
        yield 'control in identity' => [ApplicationCatalogueFixture::csv([array_replace($row, ['Nom' => "Word\tPress"])])];
        yield 'bidi in identity' => [ApplicationCatalogueFixture::csv([array_replace($row, ['Nom' => "Word\u{202e}Press"])])];
        yield 'NUL in prose' => [ApplicationCatalogueFixture::csv([array_replace($row, ['Remarque' => "note\0private"])])];
        yield 'ESC in prose' => [ApplicationCatalogueFixture::csv([array_replace($row, ['Remarque' => "\x1b[31mnote"])])];
        yield 'bad UTF8' => [ApplicationCatalogueFixture::csv([$row])."\xff"];
        yield 'oversized identity' => [ApplicationCatalogueFixture::csv([array_replace($row, ['Nom' => str_repeat('a', 257)])])];
        yield 'oversized prose' => [ApplicationCatalogueFixture::csv([array_replace($row, ['Remarque' => str_repeat('a', 8193)])])];
        yield 'oversized bytes' => [str_repeat(' ', CsvApplicationCatalogueSource::MAX_BYTES + 1)];
        yield 'empty source' => [''];
    }

    #[DataProvider('invalidDocuments')]
    public function testMalformedUnboundedOrAmbiguousSourcesHaveNoPartialPublication(string $raw): void
    {
        file_put_contents($this->path, $raw);
        $snapshot = (new CsvApplicationCatalogueSource($this->path))->read();
        self::assertSame('failed', $snapshot->status);
        self::assertSame([], $snapshot->entries);
        self::assertNull($snapshot->sha256);
        self::assertNull($snapshot->bytes);
    }

    public function testEntryLimitIsAcceptedExactlyAndRefusedWithoutPartialResultsAboveIt(): void
    {
        $rows = [];
        for ($index = 0; $index < CsvApplicationCatalogueSource::MAX_ENTRIES; ++$index) {
            $rows[] = ApplicationCatalogueFixture::fields('App '.$index);
        }
        file_put_contents($this->path, ApplicationCatalogueFixture::csv($rows));
        $source = new CsvApplicationCatalogueSource($this->path);
        $snapshot = $source->read();
        self::assertSame('available', $snapshot->status);
        self::assertCount(2048, $snapshot->entries);
        $rows[] = ApplicationCatalogueFixture::fields('App overflow');
        file_put_contents($this->path, ApplicationCatalogueFixture::csv($rows));
        $overflow = $source->read();
        self::assertSame('failed', $overflow->status);
        self::assertSame([], $overflow->entries);
    }

    public function testExactByteLimitIsAcceptedAndOneMoreByteIsRefused(): void
    {
        $rows = [];
        for ($index = 0; $index < 130; ++$index) {
            $row = ApplicationCatalogueFixture::fields('Boundary app '.$index);
            $row['Remarque'] = '';
            $rows[] = $row;
        }
        $remaining = CsvApplicationCatalogueSource::MAX_BYTES - strlen(ApplicationCatalogueFixture::csv($rows));
        foreach ($rows as &$row) {
            $length = min(8192, $remaining);
            $row['Remarque'] = str_repeat('a', $length);
            $remaining -= $length;
        }
        unset($row);
        self::assertSame(0, $remaining);
        $raw = ApplicationCatalogueFixture::csv($rows);
        self::assertSame(CsvApplicationCatalogueSource::MAX_BYTES, strlen($raw));
        file_put_contents($this->path, $raw);
        $source = new CsvApplicationCatalogueSource($this->path);
        self::assertSame('available', $source->read()->status);
        file_put_contents($this->path, 'x', FILE_APPEND);
        self::assertSame('failed', $source->read()->status);
    }

    public function testMissingDirectoryAndWrapperSourcesRemainDistinctAndNonExecutable(): void
    {
        $source = new CsvApplicationCatalogueSource($this->path);
        self::assertSame('not_configured', $source->read()->status);
        self::assertSame('failed', (new CsvApplicationCatalogueSource($this->directory))->read()->status);
        foreach (['php://memory', 'https://example.invalid/catalogue.csv', 'phar:///private/archive/catalogue.csv'] as $path) {
            self::assertSame('failed', (new CsvApplicationCatalogueSource($path))->read()->status);
        }
        self::assertSame(['.', '..'], scandir($this->directory));
    }

    public function testFinalSymlinkIsRefusedWithoutFollowingIt(): void
    {
        if (PHP_OS_FAMILY !== 'Linux') self::markTestSkipped('Synthetic symlink refusal is qualified on Linux.');
        file_put_contents($this->path, ApplicationCatalogueFixture::csv([ApplicationCatalogueFixture::fields()]));
        self::assertTrue(symlink($this->path, $this->directory.'/link.csv'));
        self::assertSame('failed', (new CsvApplicationCatalogueSource($this->directory.'/link.csv'))->read()->status);
        self::assertSame('available', (new CsvApplicationCatalogueSource($this->path))->read()->status);
    }

    public function testTheHashTracksExactObservedBytesAcrossEditsWithoutInventingPersistentIds(): void
    {
        $source = new CsvApplicationCatalogueSource($this->path);
        $first = ApplicationCatalogueFixture::csv([ApplicationCatalogueFixture::fields()]);
        file_put_contents($this->path, $first);
        $before = $source->read();
        $second = ApplicationCatalogueFixture::csv([ApplicationCatalogueFixture::fields('Discourse', 'Forums', 'Application', 'DEPLOY')], true);
        file_put_contents($this->path, $second);
        $after = $source->read();
        self::assertSame(hash('sha256', $first), $before->sha256);
        self::assertSame(hash('sha256', $second), $after->sha256);
        self::assertNotSame($before->sha256, $after->sha256);
        self::assertSame('Discourse', $after->entries[0]->projection()['name']);
    }
}
