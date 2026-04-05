# SupportLayer PHP SDK

Official PHP SDK for the [SupportLayer](https://supportlayer.io) REST API.

## Requirements

- PHP 8.1 or later
- cURL extension
- JSON extension

## Installation

Install via Composer:

```bash
composer require supportlayer/php-sdk
```

## Quick start

```php
use SupportLayer\SupportLayerClient;

$client = new SupportLayerClient('https://yourapp.supportlayer.io', 'sl_live_YOUR_API_KEY');

// List open tickets
$response = $client->tickets->list(['status' => 'open']);

foreach ($response['data'] as $ticket) {
    echo $ticket['id'] . ': ' . $ticket['subject'] . "\n";
}
```

## Authentication

All requests require an API key. Generate one from the **API keys** page in your SupportLayer admin panel. Pass it as the second argument when creating the client:

```php
$client = new SupportLayerClient('https://yourapp.supportlayer.io', 'sl_live_...');
```

You can optionally set a custom timeout (in seconds) as the third argument:

```php
$client = new SupportLayerClient('https://yourapp.supportlayer.io', 'sl_live_...', 60);
```

## Usage

### Tickets

```php
// List tickets with filters
$tickets = $client->tickets->list([
    'status'   => 'open',       // status machine key, or "overdue"
    'assignee' => 'unassigned', // user ID, "me", or "unassigned"
    'category' => 2,            // category ID
    'product'  => 1,            // product ID
    'sla'      => 'at_risk',    // "at_risk" for tickets due within 2 hours
    'search'   => 'login',      // search subject or ticket ID
]);

// Get a single ticket
$ticket = $client->tickets->get(42);

// Create a ticket
$ticket = $client->tickets->create([
    'subject'        => 'Cannot export report',
    'body'           => 'The export button returns a 500 error in Safari.',
    'urgency_id'     => 3,
    'category_id'    => 2,
    'product_id'     => 1,
    'assignee_id'    => 12,
    'reporter_email' => 'sam@example.com',
    'tag_ids'        => [1, 3],
]);

// Update a ticket (only include fields you want to change)
$client->tickets->update(42, [
    'status_id'   => 2,
    'assignee_id' => 12,
    'product_id'  => 3,
]);

// Close a ticket
$client->tickets->close(42);

// Assign or unassign
$client->tickets->assign(42, 12);   // assign to user 12
$client->tickets->assign(42, null); // unassign

// Escalate to a target user
$client->tickets->escalate(42, 8);
```

### Messages

```php
// List messages on a ticket
$messages = $client->messages->list(42);

// Add a public reply
$client->messages->create(42, [
    'body' => 'This has been fixed in the latest release.',
]);

// Add an internal note
$client->messages->create(42, [
    'body'     => 'Escalating to engineering — this is a regression.',
    'internal' => true,
]);
```

### Lookups

Fetch the configuration data for your workspace. Use these to get valid IDs before creating or updating tickets.

```php
$statuses  = $client->lookups->statuses();
$categories = $client->lookups->categories();
$urgencies = $client->lookups->urgencies();
$tags      = $client->lookups->tags();
$products  = $client->lookups->products();
```

### Users

```php
// List all active users in the organization
$users = $client->users->list();

// Get a single user
$user = $client->users->get(5);
```

### End users

```php
// List all end users
$endUsers = $client->endUsers->list();

// Create an end user
$endUser = $client->endUsers->create([
    'email'      => 'sam@example.com',
    'first_name' => 'Sam',
    'last_name'  => 'Kim',
]);
```

### Statistics

```php
// Dashboard counters (open, my_open, unassigned, overdue, at_risk, closed_today)
$dashboard = $client->stats->dashboard();

// Reporting data (by_status, avg response time, SLA compliance, by_agent, volume)
$reporting = $client->stats->reporting();
```

## Error handling

The SDK throws typed exceptions for API errors. All exceptions extend `SupportLayer\Exception\SupportLayerException`.

```php
use SupportLayer\Exception\AuthenticationException;
use SupportLayer\Exception\NotFoundException;
use SupportLayer\Exception\ValidationException;
use SupportLayer\Exception\ApiException;

try {
    $ticket = $client->tickets->get(999);
} catch (AuthenticationException $e) {
    // 401 — invalid, expired, or revoked API key
    echo 'Auth failed: ' . $e->getMessage();
} catch (NotFoundException $e) {
    // 404 — resource not found
    echo 'Not found: ' . $e->getMessage();
} catch (ValidationException $e) {
    // 422 — missing or invalid fields
    echo 'Validation error: ' . $e->getMessage();
} catch (ApiException $e) {
    // Any other API error
    echo $e->getHttpStatus() . ': ' . $e->getErrorCode() . ' — ' . $e->getMessage();
}
```

| Exception | HTTP Status | When |
|---|---|---|
| `AuthenticationException` | 401 | Missing, invalid, expired, or revoked API key |
| `NotFoundException` | 404 | Resource does not exist or is not in your organization |
| `ValidationException` | 422 | Missing required fields or invalid data |
| `ApiException` | Any 4xx/5xx | All other API errors |

## Response format

All methods return associative arrays matching the JSON structure from the API. List endpoints return `data` (array of items) and `total`. Single-resource endpoints return `data` (the object).

```php
$response = $client->tickets->list(['status' => 'open']);

$response['data'];  // array of ticket objects
$response['total']; // total count

$response = $client->tickets->get(42);

$response['data'];            // the ticket object
$response['data']['id'];      // 42
$response['data']['subject']; // "Cannot export report"
$response['data']['product']; // {"id": 1, "name": "Dashboard", "slug": "dashboard"} or null
```

## License

MIT
