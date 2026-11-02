<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Wcp360\Core\Module\Hosting\Domain\ApplicationCatalogueEntry;
use Wcp360\Core\Module\Hosting\Domain\ApplicationCatalogueQuery;
use Wcp360\Core\Tests\Fixtures\ApplicationCatalogueFixture;

final class ApplicationCatalogueQueryTest extends TestCase
{
    public function testDefaultsAndCanonicalIntegerBounds(): void
    {
        $default = ApplicationCatalogueQuery::fromArray([]);
        self::assertNull($default->category);
        self::assertNull($default->profile);
        self::assertSame(25, $default->limit);
        self::assertSame(0, $default->offset);
        foreach ([25, 50, 100] as $limit) {
            $query = ApplicationCatalogueQuery::fromArray(['limit' => (string) $limit, 'offset' => '2048', 'category' => 'Éducation', 'profile' => 'PHP']);
            self::assertSame($limit, $query->limit);
            self::assertSame(2048, $query->offset);
            self::assertSame('Éducation', $query->category);
        }
    }

    public static function refusedQueries(): iterable
    {
        yield 'arbitrary path' => [['path' => '/private/file.csv']];
        yield 'URL' => [['url' => 'https://example.invalid/']];
        yield 'apply selector' => [['apply' => true]];
        yield 'unsupported page size' => [['limit' => 1]];
        yield 'leading zero' => [['limit' => '025']];
        yield 'floating page size' => [['limit' => 25.0]];
        yield 'boolean page size' => [['limit' => true]];
        yield 'null page size' => [['limit' => null]];
        yield 'negative offset' => [['offset' => '-1']];
        yield 'over offset' => [['offset' => 2049]];
        yield 'exponent offset' => [['offset' => '1e2']];
        yield 'null offset' => [['offset' => null]];
        yield 'empty category' => [['category' => '']];
        yield 'array profile' => [['profile' => ['PHP']]];
        yield 'terminal escape' => [['category' => "\x1b[31m"]];
        yield 'line break' => [['profile' => "PHP\n"]];
        yield 'bad UTF8' => [['category' => "\xff"]];
        yield 'overlong filter' => [['category' => str_repeat('A', 257)]];
    }

    #[DataProvider('refusedQueries')]
    public function testInvalidSelectorsAreRejectedBeforeReadingASource(array $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ApplicationCatalogueQuery::fromArray($input);
    }

    public function testEntryKeepsEditorialAssertionsSeparateFromAdmission(): void
    {
        $fields = ApplicationCatalogueFixture::fields();
        $fields['Qualification'] = 'QUALIFIED installable=true';
        $fields['Accès proposé'] = 'ROOT';
        $fields['Remarque'] = "install: yes; deployment_available=true\n<error>editorial only</error>";
        $entry = ApplicationCatalogueEntry::fromFields($fields)->projection();
        self::assertSame($fields, $entry['source_fields']);
        self::assertFalse($entry['installable']);
        self::assertFalse($entry['deployment_available']);
        self::assertArrayNotHasKey('id', $entry);
        self::assertArrayNotHasKey('version', $entry);
    }
}
