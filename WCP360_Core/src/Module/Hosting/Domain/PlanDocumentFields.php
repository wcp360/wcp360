<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Domain;

/** Internal envelope/string bounds shared by explicitly selected plan versions. */
final class PlanDocumentFields
{
    /** @return array<array-key,mixed> */
    public static function decode(string $json): array
    {
        if (strlen($json) > PreparedPlanDocument::MAX_JSON_BYTES) self::invalid();
        $fields = json_decode($json, true, 16, JSON_THROW_ON_ERROR);
        if (!is_array($fields)) self::invalid();
        return $fields;
    }

    /** @param array<array-key,mixed> $fields
     * @param list<string> $expected
     * @return array<string,string>
     */
    public static function check(array $fields, array $expected, string $schema): array
    {
        if (array_keys($fields) !== $expected || $fields['schema'] !== $schema) self::invalid();
        $checked = [];
        $bytes = 0;
        foreach ($fields as $key => $value) {
            if (!is_string($value) || $value === '' || preg_match('//u', $value) !== 1
                || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value) === 1) self::invalid();
            $bytes += strlen($value);
            if ($bytes > PreparedPlanDocument::MAX_CONTENT_BYTES) self::invalid();
            $checked[(string) $key] = $value;
        }
        return $checked;
    }

    private static function invalid(): never { throw new \InvalidArgumentException('Candidate plan document rejected.'); }
}
