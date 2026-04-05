<?php

declare(strict_types=1);

namespace SupportLayer\Resource;

use SupportLayer\HttpClient;

final class EndUsers
{
    private HttpClient $http;

    /** @internal */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * List all end users in the organization.
     *
     * @return array{data: list<array{id: int, email: string, first_name: string, last_name: string, name: string, status: string}>}
     */
    public function list(): array
    {
        return $this->http->get('/end-users');
    }

    /**
     * Create a new end user.
     *
     * @param array{email: string, first_name: string, last_name: string} $data
     * @return array{data: array{id: int, email: string, first_name: string, last_name: string, name: string}}
     */
    public function create(array $data): array
    {
        return $this->http->post('/end-users', $data);
    }
}
