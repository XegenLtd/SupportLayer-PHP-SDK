<?php

declare(strict_types=1);

namespace SupportLayer\Resource;

use SupportLayer\HttpClient;

final class Lookups
{
    private HttpClient $http;

    /** @internal */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * List ticket statuses.
     *
     * @return array{data: list<array{id: int, key: string, label: string, sort_order: int, pauses_sla: bool, is_locked: bool}>}
     */
    public function statuses(): array
    {
        return $this->http->get('/statuses');
    }

    /**
     * List ticket categories.
     *
     * @return array{data: list<array{id: int, name: string, slug: string, sort_order: int, parent_id: int|null}>}
     */
    public function categories(): array
    {
        return $this->http->get('/categories');
    }

    /**
     * List urgency levels.
     *
     * @return array{data: list<array{id: int, name: string, slug: string, sort_order: int, first_response_minutes: int}>}
     */
    public function urgencies(): array
    {
        return $this->http->get('/urgencies');
    }

    /**
     * List tags.
     *
     * @return array{data: list<array{id: int, name: string, color: string|null}>}
     */
    public function tags(): array
    {
        return $this->http->get('/tags');
    }

    /**
     * List products.
     *
     * @return array{data: list<array{id: int, name: string, slug: string, sort_order: int}>}
     */
    public function products(): array
    {
        return $this->http->get('/products');
    }
}
