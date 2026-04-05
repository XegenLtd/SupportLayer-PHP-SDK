<?php

declare(strict_types=1);

namespace SupportLayer;

use SupportLayer\Exception\ApiException;
use SupportLayer\Exception\AuthenticationException;
use SupportLayer\Exception\NotFoundException;
use SupportLayer\Exception\SupportLayerException;
use SupportLayer\Exception\ValidationException;

/**
 * @internal Thin cURL wrapper used by the SDK. Not part of the public API.
 */
final class HttpClient
{
    private string $baseUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct(string $baseUrl, string $apiKey, int $timeout = 30)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
        $this->timeout = $timeout;
    }

    /**
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        $url = $this->buildUrl($path, $query);

        return $this->request('GET', $url);
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    public function post(string $path, array $body = []): array
    {
        $url = $this->buildUrl($path);

        return $this->request('POST', $url, $body);
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    public function put(string $path, array $body = []): array
    {
        $url = $this->buildUrl($path);

        return $this->request('PUT', $url, $body);
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    public function patch(string $path, array $body = []): array
    {
        $url = $this->buildUrl($path);

        return $this->request('PATCH', $url, $body);
    }

    /**
     * @param array<string, mixed>|null $body
     * @return array<string, mixed>
     */
    private function request(string $method, string $url, ?array $body = null): array
    {
        $ch = curl_init();

        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'Accept: application/json',
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if ($body !== null) {
            $json = json_encode($body, JSON_THROW_ON_ERROR);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            $headers[] = 'Content-Type: application/json';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new SupportLayerException('HTTP request failed: ' . $curlError);
        }

        $data = json_decode((string) $response, true);
        if (!is_array($data)) {
            $data = [];
        }

        if ($httpCode >= 400) {
            $this->throwApiException($httpCode, $data);
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function throwApiException(int $httpCode, array $data): never
    {
        $errorCode = $data['error']['code'] ?? 'unknown';
        $message = $data['error']['message'] ?? 'An API error occurred.';

        $exception = match ($httpCode) {
            401 => new AuthenticationException($httpCode, $errorCode, $message),
            404 => new NotFoundException($httpCode, $errorCode, $message),
            422 => new ValidationException($httpCode, $errorCode, $message),
            default => new ApiException($httpCode, $errorCode, $message),
        };

        throw $exception;
    }

    /**
     * @param array<string, mixed> $query
     */
    private function buildUrl(string $path, array $query = []): string
    {
        $url = $this->baseUrl . '/api/v1' . $path;

        $filtered = array_filter($query, fn($v) => $v !== null && $v !== '');

        if ($filtered !== []) {
            $url .= '?' . http_build_query($filtered);
        }

        return $url;
    }
}
