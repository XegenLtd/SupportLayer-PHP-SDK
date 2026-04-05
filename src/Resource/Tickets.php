<?php

declare(strict_types=1);

namespace SupportLayer\Resource;

use SupportLayer\HttpClient;

final class Tickets
{
    private HttpClient $http;

    /** @internal */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * List tickets with optional filters.
     *
     * @param array{
     *     search?: string,
     *     status?: string,
     *     assignee?: string|int,
     *     category?: int,
     *     product?: int,
     *     sla?: string,
     * } $filters
     * @return array{data: list<array<string, mixed>>, total: int}
     */
    public function list(array $filters = []): array
    {
        return $this->http->get('/tickets', $filters);
    }

    /**
     * Get a single ticket by ID.
     *
     * @return array{data: array<string, mixed>}
     */
    public function get(int $id): array
    {
        return $this->http->get('/tickets/' . $id);
    }

    /**
     * Create a new ticket.
     *
     * @param array{
     *     subject: string,
     *     body: string,
     *     reporter_email?: string,
     *     assignee_id?: int,
     *     category_id?: int,
     *     product_id?: int,
     *     urgency_id?: int,
     *     tag_ids?: list<int>,
     * } $data
     * @return array{data: array<string, mixed>}
     */
    public function create(array $data): array
    {
        return $this->http->post('/tickets', $data);
    }

    /**
     * Update a ticket. Only include fields you want to change.
     *
     * @param array{
     *     subject?: string,
     *     body?: string,
     *     status_id?: int,
     *     category_id?: int|null,
     *     product_id?: int|null,
     *     urgency_id?: int,
     *     assignee_id?: int|null,
     *     tag_ids?: list<int>,
     * } $data
     * @return array{data: array<string, mixed>}
     */
    public function update(int $id, array $data): array
    {
        return $this->http->put('/tickets/' . $id, $data);
    }

    /**
     * Close a ticket.
     *
     * @return array{data: array<string, mixed>}
     */
    public function close(int $id): array
    {
        return $this->http->post('/tickets/' . $id . '/close');
    }

    /**
     * Assign or unassign a ticket.
     *
     * @return array{data: array<string, mixed>}
     */
    public function assign(int $id, ?int $assigneeId): array
    {
        return $this->http->post('/tickets/' . $id . '/assign', [
            'assignee_id' => $assigneeId,
        ]);
    }

    /**
     * Escalate a ticket to a target user.
     *
     * @return array{data: array<string, mixed>}
     */
    public function escalate(int $id, int $targetUserId): array
    {
        return $this->http->post('/tickets/' . $id . '/escalate', [
            'target_user_id' => $targetUserId,
        ]);
    }
}
