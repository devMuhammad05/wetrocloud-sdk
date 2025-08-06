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

    /**
     * Retrieve all collections
     *
     * @return array<string, mixed> Response from the API
     * @throws \RuntimeException
     */
    public function listAllCollections(): array
    {
        try {
            $response = $this->client->get('/v1/collection/all/');

            $body = (string) $response->getBody();
            return json_decode($body, true);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to fetch collections: " . $e->getMessage());
        }
    }

    /**
     * Insert resource
     *
     * @param string $collectionId The ID of the collection to which the data will be added.
     * @param string $resource The resource to be added to the collection.
     * @param string $type The resource to be added to the collection.
     * @return array<string, mixed> Response from the API
     * @throws \RuntimeException
     */

    public function insertResource(string $collectionId, string $resource, string $type): array
    {
        try {

            $payload = [
                'collection_id' => $collectionId,
                'resource' => $resource,
                'type' => $type,
            ];

            $response = $this->client->post('/v1/resource/insert/', [
                'json' => $payload,
            ]);

            $body = (string) $response->getBody();
            return json_decode($body, true);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to fetch collections: " . $e->getMessage());
        }
    }

    /**
     * Query collection 
     *
     * @param string $collectionId The ID of the collection to which the data will be added.
     * @param string $requestQuery The query being submitted to the collection.
     * @param string $jsonSchema   The JSON schema for the query response.
     * @param string $jsonSchemaRules  Rules for the JSON schema.
     * @return array<string, mixed> Response from the API
     * @throws \RuntimeException
     */

    public function queryCollection(
        string $collectionId,
        string $requestQuery,
        ?string $jsonSchema = null,
        ?string $jsonSchemaRules = null
    ): array {
        try {

            $payload = [
                'collection_id' => $collectionId,
                'request_query' => $requestQuery,
                'json_schema' => $jsonSchema,
                'json_schema_rules' => $jsonSchemaRules,
            ];

            $response = $this->client->post('v1/collection/query/', [
                'json' => $payload,
            ]);

            $body = (string) $response->getBody();
            return json_decode($body, true);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to fetch collections: " . $e->getMessage());
        }
    }
}
