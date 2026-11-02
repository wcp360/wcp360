<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

/** Candidate editorial metadata; values never grant permission or deployment admission. */
#[Exclude]
final readonly class ApplicationCatalogueEntry
{
    public const HEADERS = ['Nom', 'Catégorie', 'Nature', 'Profil proposé', 'Accès proposé', 'Lot cible',
        'Qualification', 'Remarque', 'Origine', 'Source de repérage', 'Source vérifiée'];
    private const FIELD_LIMITS = [256, 256, 128, 64, 128, 128, 128, 8192, 256, 4096, 2048];

    /** @param array<string,string> $fields */
    private function __construct(private array $fields, public string $normalizedName) {}

    /** @param array<array-key,mixed> $fields */
    public static function fromFields(array $fields): self
    {
        if (array_keys($fields) !== self::HEADERS) throw new \InvalidArgumentException('Candidate catalogue entry rejected.');
        $checked = [];
        foreach (self::HEADERS as $position => $header) {
            $value = $fields[$header];
            if (!is_string($value) || strlen($value) > self::FIELD_LIMITS[$position]
                || preg_match('//u', $value) !== 1
                || preg_match('/[\p{Cc}\p{Cf}]/u', str_replace(["\r", "\n", "\t"], '', $value)) === 1
                || (!in_array($position, [7, 9, 10], true)
                    && ($value === '' || preg_match('/[\p{Cc}\p{Cf}]/u', $value) === 1))) {
                throw new \InvalidArgumentException('Candidate catalogue entry rejected.');
            }
            $checked[$header] = $value;
        }
        $normalized = \Normalizer::normalize($checked['Nom'], \Normalizer::FORM_KC);
        if ($normalized === false) throw new \InvalidArgumentException('Candidate catalogue entry rejected.');
        $normalized = preg_replace('/\s+/u', ' ', $normalized);
        if ($normalized === null || trim($normalized) === '') throw new \InvalidArgumentException('Candidate catalogue entry rejected.');

        return new self($checked, mb_strtolower(trim($normalized), 'UTF-8'));
    }

    public function category(): string { return $this->fields['Catégorie']; }
    public function profile(): string { return $this->fields['Profil proposé']; }

    /** @return array{name:string,category:string,nature:string,profile:string,source_fields:array<string,string>,installable:false,deployment_available:false} */
    public function projection(): array
    {
        return ['name' => $this->fields['Nom'], 'category' => $this->category(), 'nature' => $this->fields['Nature'],
            'profile' => $this->profile(), 'source_fields' => $this->fields,
            'installable' => false, 'deployment_available' => false];
    }
}
