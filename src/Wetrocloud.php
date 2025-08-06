<?php

declare(strict_types=1);

namespace Wetrocloud\WetrocloudSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;

class Wetrocloud
{
    private Client $client;
    private string $baseUrl;

    /**
     * Initialize the Wetrocloud SDK client
     *
     * @param string $apiKey Your Wetrocloud API key
     * @param string $baseUrl The base URL for the API 
     * @throws \InvalidArgumentException If the API key is empty
     */

    public function __construct(private string $apiKey, string $baseUrl = "https://api.wetrocloud.com")
    {
        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException('API key cannot be empty');
        }

        $this->baseUrl = rtrim($baseUrl, '/');

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'headers' => [
                'Authorization' => "Token {$this->apiKey}",
                'User-Agent' => 'WetroSDK-PHP/1.0',
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'http_errors' => false
        ]);
    }


    /**
     * Get the HTTP client instance
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Get the base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }


    /**
     * Create a new collection
     * 
     * @param string|null $collectionId Optional unique identifier for the collection
     * @return array<string, mixed> Response from the API
     * @throws \RuntimeException
     */

    public function createCollection(?string $collectionId = null): array
    {
        try {
            $payload = [];

            if ($collectionId !== null) {
                $payload['collection_id'] = $collectionId;
            }

            $response = $this->client->post('/v1/collection/create/', [
                'json' => $payload,
            ]);

            $body = (string) $response->getBody();
            return json_decode($body, true);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to create collection: " . $e->getMessage());
        }
    }
    
}
