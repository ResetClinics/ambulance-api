<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class OneCAmbulanceApiClient
{
    public function __construct(
        private HttpClientInterface $client,
        private string $baseUrl,
        private string $username,
        private string $password,
    ) {}

    /**
     * @param array<string, mixed> $queryParams
     * @param array<string, mixed>|null $body
     *
     * @throws TransportExceptionInterface
     */
    public function request(
        string $endpoint,
        array $queryParams = [],
        string $method = 'GET',
        ?array $body = null,
    ): ResponseInterface {
        $method = strtoupper($method);
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $options = [
            'auth_basic' => [$this->username, $this->password],
        ];

        $normalizedQuery = $this->normalizeParams($queryParams);
        if ($normalizedQuery !== []) {
            $options['query'] = $normalizedQuery;
        }

        if ($body !== null && \in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            $options['json'] = $body;
            $options['headers'] = [
                'Content-Type' => 'application/json',
            ];
        }

        return $this->client->request($method, $url, $options);
    }

    /**
     * @param array<string, mixed> $queryParams
     * @param array<string, mixed>|null $body
     *
     * @return array{statusCode: int, data: mixed}
     *
     * @throws TransportExceptionInterface
     */
    public function requestAndGetResponse(
        string $endpoint,
        array $queryParams = [],
        string $method = 'GET',
        ?array $body = null,
    ): array {
        $response = $this->request($endpoint, $queryParams, $method, $body);
        $statusCode = $response->getStatusCode();

        try {
            $data = $response->toArray(false);
        } catch (DecodingExceptionInterface) {
            $data = $response->getContent(false);
        }

        return [
            'statusCode' => $statusCode,
            'data' => $data,
        ];
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function normalizeParams(array $params): array
    {
        $normalized = [];

        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (\is_bool($value)) {
                $normalized[$key] = $value ? 'true' : 'false';
                continue;
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }
}
