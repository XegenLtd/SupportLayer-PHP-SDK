<?php

declare(strict_types=1);

namespace SupportLayer;

use SupportLayer\Resource\EndUsers;
use SupportLayer\Resource\Lookups;
use SupportLayer\Resource\Messages;
use SupportLayer\Resource\Stats;
use SupportLayer\Resource\Tickets;
use SupportLayer\Resource\Users;

final class SupportLayerClient
{
    private HttpClient $http;

    public readonly Tickets $tickets;
    public readonly Messages $messages;
    public readonly Lookups $lookups;
    public readonly Users $users;
    public readonly EndUsers $endUsers;
    public readonly Stats $stats;

    /**
     * @param string $baseUrl  Your SupportLayer instance URL (e.g. "https://app.supportlayer.io")
     * @param string $apiKey   Your API key (sl_live_...)
     * @param int    $timeout  HTTP timeout in seconds (default 30)
     */
    public function __construct(string $baseUrl, string $apiKey, int $timeout = 30)
    {
        $this->http = new HttpClient($baseUrl, $apiKey, $timeout);

        $this->tickets  = new Tickets($this->http);
        $this->messages = new Messages($this->http);
        $this->lookups  = new Lookups($this->http);
        $this->users    = new Users($this->http);
        $this->endUsers = new EndUsers($this->http);
        $this->stats    = new Stats($this->http);
    }
}
