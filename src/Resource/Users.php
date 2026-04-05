<?php

declare(strict_types=1);

namespace SupportLayer\Resource;

use SupportLayer\HttpClient;

final class Users
{
    private HttpClient $http;

    /** @internal */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * List all active users in the organization.
     *
     * @return array{data: list<array{id: int, email: string, first_name: string, last_name: string, name: string}>}
     */
    public function list(): array
    {
        return $this->http->get('/users');
    }

    /**
     * Get a single user by ID.
     *
     * @return array{data: array{id: int, email: string, first_name: string, last_name: string, name: string}}
     */
    public function get(int $id): array
    {
        return $this->http->get('/users/' . $id);
    }
}
