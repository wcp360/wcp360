<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Operations\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Uid\Uuid;

/** Bounded candidate read filters; a cursor is a position, never resource authority. */
#[Exclude]
final readonly class JobListQuery
{
    public const STATES = ['QUEUED', 'RUNNING', 'SUCCEEDED', 'FAILED', 'UNKNOWN_OUTCOME'];

    private function __construct(public string $tenant, public string $state, public string $search,
        public int $limit, public ?string $after, public ?string $before) {}

    /** @param array<array-key,mixed> $input */
    public static function fromArray(array $input): self
    {
        if (array_diff(array_keys($input), ['tenant', 'state', 'q', 'limit', 'after', 'before'])) self::invalid();
        foreach ($input as $value) if (!is_string($value) || strlen($value) > 253) self::invalid();
        $tenant = self::uuid($input['tenant'] ?? '');
        $state = $input['state'] ?? '';
        $search = strtolower(trim($input['q'] ?? ''));
        $limit = $input['limit'] ?? '25';
        $after = ($input['after'] ?? '') !== '' ? self::uuid($input['after']) : null;
        $before = ($input['before'] ?? '') !== '' ? self::uuid($input['before']) : null;
        if (($state !== '' && !in_array($state, self::STATES, true))
            || preg_match('/\A[a-z0-9.-]{0,253}\z/D', $search) !== 1
            || !in_array($limit, ['25', '50', '100'], true) || ($after !== null && $before !== null)) self::invalid();

        return new self($tenant, $state, $search, (int) $limit, $after, $before);
    }

    /** @return array<string,string> Deliberately excludes cursors when filters change. */
    public function filters(): array
    {
        return ['tenant' => $this->tenant, 'state' => $this->state, 'q' => $this->search, 'limit' => (string) $this->limit];
    }

    private static function uuid(string $value): string
    {
        if (strlen($value) !== 36 || !Uuid::isValid($value)) self::invalid();

        return Uuid::fromString($value)->toRfc4122();
    }

    private static function invalid(): never
    {
        throw new BadRequestHttpException('Invalid job-list filters or cursor.');
    }
}
