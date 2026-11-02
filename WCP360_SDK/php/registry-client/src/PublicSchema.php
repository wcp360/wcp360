<?php
declare(strict_types=1);
namespace Wcp360\Sdk\Registry;

/** Validator for the bounded schema subset used by this pinned candidate contract. */
final class PublicSchema
{
    public static function validate(mixed $envelope, string $shape): array
    {
        $name = ['status'=>'Status','package'=>'Package','packages'=>'Packages','releases'=>'Releases','update'=>'Update'][$shape] ?? null;
        if (!$name) throw new ClientException('invalid_response_shape');
        static $schema;
        if ($schema === null) {
            $json = file_get_contents(__DIR__ . '/../contracts/response.schema.json');
            if ($json === false) throw new ClientException('contract_unavailable');
            $schema = json_decode($json, true, 64, JSON_THROW_ON_ERROR);
        }
        if (!self::matches($envelope, $schema['$defs'][$name . 'Envelope'], $schema)) throw new ClientException('contract_mismatch');
        $data = $envelope['data'];
        if ($shape === 'update' && ($data['update_available'] !== ($data['recommended'] !== null) || ($data['recommended'] !== null && ($data['recommended']['type'] !== $data['type'] || $data['recommended']['slug'] !== $data['slug'] || $data['recommended']['channel'] !== $data['channel'])))) throw new ClientException('contract_mismatch');
        return $data;
    }

    private static function matches(mixed $value, array $rule, array $schema): bool
    {
        if (isset($rule['$ref'])) return self::matches($value, $schema['$defs'][substr($rule['$ref'], 8)], $schema);
        if (isset($rule['anyOf'])) {
            foreach ($rule['anyOf'] as $alternative) if (self::matches($value, $alternative, $schema)) return true;
            return false;
        }
        if (array_key_exists('const', $rule)) return $value === $rule['const'];
        if (isset($rule['enum']) && !in_array($value, $rule['enum'], true)) return false;
        switch ($rule['type'] ?? '') {
            case 'null': return $value === null;
            case 'boolean': return is_bool($value);
            case 'integer': return is_int($value) && $value >= ($rule['minimum'] ?? PHP_INT_MIN) && $value <= ($rule['maximum'] ?? PHP_INT_MAX);
            case 'string':
                // UTF-8 JSON has already been checked by json_decode; contract bounds use characters.
                if (!is_string($value)) return false;
                $length = preg_match_all('/./us', $value);
                return $length !== false && $length >= ($rule['minLength'] ?? 0) && $length <= ($rule['maxLength'] ?? PHP_INT_MAX)
                    && (!isset($rule['pattern']) || preg_match('~' . str_replace('~','\\~',$rule['pattern']) . '~uD', $value) === 1);
            case 'array':
                if (!is_array($value) || !array_is_list($value) || count($value) < ($rule['minItems'] ?? 0) || count($value) > ($rule['maxItems'] ?? PHP_INT_MAX)) return false;
                foreach ($value as $item) if (!self::matches($item, $rule['items'], $schema)) return false;
                return true;
            case 'object':
                if (!is_array($value)) return false;
                foreach ($rule['required'] ?? [] as $key) if (!array_key_exists($key, $value)) return false;
                foreach ($value as $key=>$item) {
                    if (!isset($rule['properties'][$key])) return false;
                    if (!self::matches($item, $rule['properties'][$key], $schema)) return false;
                }
                return true;
            default: return false;
        }
    }
}
