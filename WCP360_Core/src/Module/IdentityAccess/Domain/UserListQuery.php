<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\IdentityAccess\Domain;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Wcp360\Core\Shared\Http\CursorPageQuery;

/** Fixed ROOT inventory transport; usernames and cursors never establish authority. */
#[Exclude]
final readonly class UserListQuery
{
    private function __construct(public string $search, public string $state, public int $limit,
        public ?string $after, public ?string $before) {}

    /** @param array<array-key,mixed> $input */
    public static function fromArray(array $input): self
    {
        $page = CursorPageQuery::fromArray($input, ['q', 'state']);
        $search = strtolower(trim($input['q'] ?? ''));
        $state = $input['state'] ?? '';
        if (strlen($search) > 32 || preg_match('/\A[a-z0-9_]{0,32}\z/D', $search) !== 1
            || !in_array($state, ['', 'active', 'inactive'], true)) CursorPageQuery::invalid();

        return new self($search, $state, $page->limit, $page->after, $page->before);
    }

    /** @return array<string,string> */
    public function filters(): array
    {
        return ['q' => $this->search, 'state' => $this->state, 'limit' => (string) $this->limit];
    }
}
