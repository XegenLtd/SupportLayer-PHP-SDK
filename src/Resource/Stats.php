<?php

declare(strict_types=1);

namespace SupportLayer\Resource;

use SupportLayer\HttpClient;

final class Stats
{
    private HttpClient $http;

    /** @internal */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Get dashboard statistics.
     *
     * @return array{data: array{open: int, my_open: int, unassigned: int, overdue: int, at_risk: int, closed_today: int}}
     */
    public function dashboard(): array
    {
        return $this->http->get('/stats/dashboard');
    }

    /**
     * Get reporting statistics.
     *
     * @return array{data: array<string, mixed>}
     */
    public function reporting(): array
    {
        return $this->http->get('/stats/reporting');
    }
}
