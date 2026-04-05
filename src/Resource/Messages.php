<?php

declare(strict_types=1);

namespace SupportLayer\Resource;

use SupportLayer\HttpClient;

final class Messages
{
    private HttpClient $http;

    /** @internal */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * List all messages on a ticket.
     *
     * @return array{data: list<array<string, mixed>>, total: int}
     */
    public function list(int $ticketId): array
    {
        return $this->http->get('/tickets/' . $ticketId . '/messages');
    }

    /**
     * Add a message to a ticket.
     *
     * @param array{body: string, internal?: bool} $data
     * @return array{data: array<string, mixed>}
     */
    public function create(int $ticketId, array $data): array
    {
        return $this->http->post('/tickets/' . $ticketId . '/messages', $data);
    }
}
