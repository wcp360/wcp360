<?php

declare(strict_types=1);

namespace Wcp360\Core\Module\Hosting\Application;

use Wcp360\Core\Module\Hosting\Domain\ApplicationCatalogueQuery;
use Wcp360\Core\Module\Hosting\Domain\ApplicationCatalogueSnapshot;

final readonly class ListApplicationCandidates
{
    public function __construct(private ApplicationCatalogueSource $source) {}

    /** @return array<string,mixed> */
    public function list(ApplicationCatalogueQuery $query): array
    {
        try { $snapshot = $this->source->read(); }
        catch (\Throwable) { $snapshot = ApplicationCatalogueSnapshot::failed(); }
        $categories = [];
        $profiles = [];
        $matching = [];
        foreach ($snapshot->entries as $entry) {
            $categories[] = $entry->category();
            $profiles[] = $entry->profile();
            if (($query->category === null || $entry->category() === $query->category)
                && ($query->profile === null || $entry->profile() === $query->profile)) $matching[] = $entry;
        }
        $categories = array_values(array_unique($categories));
        $profiles = array_values(array_unique($profiles));
        sort($categories, SORT_STRING);
        sort($profiles, SORT_STRING);
        $page = array_slice($matching, $query->offset, $query->limit);
        $items = [];
        foreach ($page as $entry) $items[] = $entry->projection();
        $next = $query->offset + count($page);

        return ['schema' => 'wcp360.candidate.application-catalogue/1', 'status' => $snapshot->status,
            'scope' => 'editorial_metadata', 'installable' => false, 'deployment_available' => false,
            'source' => ['sha256' => $snapshot->sha256, 'bytes' => $snapshot->bytes],
            'counts' => ['total' => count($snapshot->entries), 'matched' => count($matching), 'returned' => count($items)],
            'categories' => $categories, 'profiles' => $profiles,
            'filters' => ['category' => $query->category, 'profile' => $query->profile],
            'pagination' => ['limit' => $query->limit, 'offset' => $query->offset,
                'next_offset' => $next < count($matching) ? $next : null],
            'items' => $items];
    }
}
