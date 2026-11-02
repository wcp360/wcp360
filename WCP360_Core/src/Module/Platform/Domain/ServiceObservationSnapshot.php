<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Platform\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

/** Bounded systemd property observations; no raw output, installation or health inference.
 * @phpstan-type Unit array{id:string,load_state:string,active_state:string,sub_state:string,unit_file_state:?string}
 */
#[Exclude]
final readonly class ServiceObservationSnapshot
{
    private const PROPERTIES = ['Id','LoadState','ActiveState','SubState','UnitFileState'];

    /** @param list<Unit> $units */
    private function __construct(public string $status, public array $units) {}

    public static function notConfigured(): self { return new self('not_configured', []); }
    public static function failed(): self { return new self('failed', []); }

    public static function fromSystemdShow(string $raw): self
    {
        if (strlen($raw) > 32768 || preg_match('//u', $raw) !== 1
            || preg_match('/[\x00-\x09\x0B-\x1F\x7F]/', $raw) === 1) self::invalid();
        $content = trim($raw, "\n");
        if ($content === '') self::invalid();
        $blocks = preg_split('/\n{2,}/', $content);
        if ($blocks === false || count($blocks) > 14) self::invalid();

        $units = [];
        $allowed = ServiceUnitCatalogue::unitNames();
        foreach ($blocks as $block) {
            $lines = explode("\n", $block);
            if (count($lines) !== 5) self::invalid();
            $properties = [];
            foreach ($lines as $line) {
                $parts = explode('=', $line, 2);
                if (count($parts) !== 2 || !in_array($parts[0], self::PROPERTIES, true)
                    || array_key_exists($parts[0], $properties)) self::invalid();
                $properties[$parts[0]] = $parts[1];
            }
            if (count($properties) !== 5 || !isset($properties['Id'], $properties['LoadState'], $properties['ActiveState'],
                $properties['SubState'], $properties['UnitFileState'])) self::invalid();
            if (!in_array($properties['Id'], $allowed, true)) self::invalid();
            foreach (['LoadState','ActiveState','SubState','UnitFileState'] as $property) {
                if ($property === 'UnitFileState' && $properties[$property] === '') continue;
                if (preg_match('/\A[A-Za-z0-9_-]{1,64}\z/D', $properties[$property]) !== 1) self::invalid();
            }
            $unit = ['id' => $properties['Id'], 'load_state' => $properties['LoadState'],
                'active_state' => $properties['ActiveState'], 'sub_state' => $properties['SubState'],
                'unit_file_state' => $properties['UnitFileState'] === '' ? null : $properties['UnitFileState']];
            $id = $unit['id'];
            if (isset($units[$id])) {
                if ($units[$id] !== $unit) self::invalid();
                continue;
            }
            $units[$id] = $unit;
        }
        return new self('observed', array_values($units));
    }

    private static function invalid(): never { throw new \InvalidArgumentException('Service observation rejected.'); }
}
