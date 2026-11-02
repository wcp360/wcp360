<?php

declare(strict_types=1);

namespace Wcp360\Core\Tests\Fixtures;

final class ApplicationCatalogueFixture
{
    public const HEADERS = ['Nom', 'Catégorie', 'Nature', 'Profil proposé', 'Accès proposé', 'Lot cible',
        'Qualification', 'Remarque', 'Origine', 'Source de repérage', 'Source vérifiée'];

    public static function fields(string $name = 'WordPress', string $category = 'CMS', string $nature = 'Application', string $profile = 'PHP'): array
    {
        return array_combine(self::HEADERS, [$name, $category, $nature, $profile, 'USER', 'Candidate batch',
            'CANDIDAT', 'Synthetic note.', 'Synthetic fixture', 'Synthetic discovery source', '']);
    }

    public static function csv(array $entries, bool $bom = false, ?array $headers = null): string
    {
        $stream = fopen('php://memory', 'w+b');
        if ($stream === false) throw new \RuntimeException('Synthetic fixture unavailable.');
        try {
            fputcsv($stream, $headers ?? self::HEADERS, ';', '"', '', "\r\n");
            foreach ($entries as $entry) fputcsv($stream, array_values($entry), ';', '"', '', "\r\n");
            rewind($stream);
            $csv = stream_get_contents($stream);
            if ($csv === false) throw new \RuntimeException('Synthetic fixture unavailable.');
            return ($bom ? "\xef\xbb\xbf" : '').$csv;
        } finally { fclose($stream); }
    }
}
