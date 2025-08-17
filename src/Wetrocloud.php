<?php

declare(strict_types=1);

namespace Wetrocloud\WetrocloudSdk;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;


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
     * Central JSON decoder for all API responses.
     *
     * @param ResponseInterface $response
     * @return array<string, mixed>
     * @throws \RuntimeException
     */
    private function decodeResponse(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();
        $decoded = json_decode($body, true);

        if (!is_array($decoded)) {
            throw new \RuntimeException(
                "Invalid API response: expected JSON object, got " . $body
            );
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
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

            return $this->decodeResponse($response);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to create collection: " . $e->getMessage(), 0, $e);
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

            return $this->decodeResponse($response);
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

            return $this->decodeResponse($response);
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

            return $this->decodeResponse($response);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to fetch collections: " . $e->getMessage());
        }
    }

    /**
     * Chat with a collection
     *
     * @param string $collectionId The ID of the collection to which the query is being submitted
     * @param string $message The message being submitted to the collection
     * @param string|null $chatHistory History of chat between user and system (JSON string)
     * @return array<string, mixed> Response from the API containing response, tokens, and success status
     * @throws \RuntimeException
     */
    public function chatCollection(
        string $collectionId,
        string $message,
        ?string $chatHistory = null
    ): array {
        try {
            $payload = [
                'collection_id' => $collectionId,
                'message' => $message,
            ];

            if ($chatHistory !== null) {
                $payload['chat_history'] = $chatHistory;
            }

            $response = $this->client->post('/v1/collection/chat/', [
                'json' => $payload,
            ]);

            return $this->decodeResponse($response);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to chat with collection: " . $e->getMessage());
        }
    }


    /**
     * Categorize a resource
     *
     * @param string $resource Description of the resource to categorize
     * @param string $type Type of the category
     * @param string $jsonSchema JSON schema for the category
     * @param string $categories Comma-separated list of categories
     * @param string $prompt Overall command or instruction for categorization
     * @return array<string, mixed> Response from the API containing label, tokens, and success status
     * @throws \RuntimeException
     */
    public function categorizeResource(
        string $resource,
        string $type,
        string $jsonSchema,
        string $categories,
        string $prompt
    ): array {
        try {
            $payload = [
                'resource' => $resource,
                'type' => $type,
                'json_schema' => $jsonSchema,
                'categories' => $categories,
                'prompt' => $prompt,
            ];

            $response = $this->client->post('/v1/categorize/', [
                'json' => $payload,
            ]);

            return $this->decodeResponse($response);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to categorize resource: " . $e->getMessage());
        }
    }

    /**
     * Remove a resource from a collection.
     *
     * @param string $collectionId The ID of the collection the resource belongs to
     * @param string $resourceId The unique ID of the resource to delete
     * @return array<string, mixed> Response from the API
     * @throws \RuntimeException
     */
    public function removeResource(string $collectionId, string $resourceId): array
    {
        try {
            $payload = [
                'collection_id' => $collectionId,
                'resource_id'   => $resourceId,
            ];

            $response = $this->client->delete('/v1/resource/remove/', [
                'json' => $payload,
            ]);

            return $this->decodeResponse($response);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to remove resource: " . $e->getMessage());
        }
    }

    /**
     * Delete a collection.
     *
     * @param string $collectionId The ID of the collection to be deleted
     * @return array<string, mixed> Response from the API
     * @throws \RuntimeException
     */
    public function deleteCollection(string $collectionId): array
    {
        try {
            $payload = [
                'collection_id' => $collectionId,
            ];

            $response = $this->client->delete('/v1/collection/delete/', [
                'json' => $payload,
            ]);

            return $this->decodeResponse($response);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to delete collection: " . $e->getMessage());
        }
    }

    /**
     * Generate text without retrieval-augmented generation (RAG).
     *
     * @param array<int, array<string, string>> $messages Array of message objects (role + content)
     * @param string $model The model identifier (e.g., "llama-3.3-70b")
     * @return array<string, mixed> Response from the API
     * @throws \RuntimeException
     */
    public function textGeneration(array $messages, string $model): array
    {
        try {
            $payload = [
                [
                    'name' => 'messages',
                    'contents' => json_encode($messages, JSON_THROW_ON_ERROR),
                ],
                [
                    'name' => 'model',
                    'contents' => $model,
                ],
            ];

            $response = $this->client->post('/v1/text-generation/', [
                'multipart' => $payload,
            ]);

            return $this->decodeResponse($response);
        } catch (\JsonException $e) {
            throw new \RuntimeException("Failed to encode messages: " . $e->getMessage());
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to generate text: " . $e->getMessage());
        }
    }

    /** 
     * Extracts text from an image.
     * @param string $imageUrl  The URL of the image.
     * @param string $requestQuery  The query to process the image.
     * @return array <string,  mixed> Response fro the API
     * @throws \RuntimeException
     * **/

    public function imageToText(string $imageUrl, string $requestQuery): array
    {
        try {
            $payload = [
                'image_url' => $imageUrl,
                'request_query' => $requestQuery,
            ];

            $response = $this->client->post('/v1/image-to-text/', [
                'json' => $payload,
            ]);

            return $this->decodeResponse($response);
        } catch (\JsonException $e) {
            throw new \RuntimeException("Failed to encode messages: " . $e->getMessage());
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to extract text: " . $e->getMessage());
        }
    }

    /**
     * Convert a resource (file, web, image) to Markdown
     *
     * @param string $resource The resource URL (file, web page, or image)
     * @param string $resourceType The type of resource: "file", "web", or "image"
     * @return array<string, mixed> Response from the API containing response, tokens, and success status
     * @throws \RuntimeException
     */
    public function markdownConverter(string $resource, string $resourceType): array
    {
        try {
            $payload = [
                'link'       => $resource,
                'resource_type'  => $resourceType,
            ];

            $response = $this->client->post('/v2/markdown-converter/', [
                'json' => $payload,
            ]);

            return $this->decodeResponse($response);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to convert resource to Markdown: " . $e->getMessage());
        }
    }

    /**
     * Generate transcript from a resource (e.g., YouTube video).
     *
     * @param string $link The URL of the resource (e.g., YouTube video link).
     * @param string $resourceType The type of resource (e.g., "youtube").
     * @return array<string, mixed> Response from the API containing transcript data, tokens, and success status.
     * @throws \RuntimeException
     */
    public function transcript(string $link, string $resourceType = 'youtube'): array
    {
        try {
            $payload = [
                'link'          => $link,
                'resource_type' => $resourceType,
            ];

            $response = $this->client->post('/v2/transcript/', [
                'json' => $payload,
            ]);

            return $this->decodeResponse($response);
        } catch (GuzzleException $e) {
            throw new \RuntimeException("Failed to generate transcript: " . $e->getMessage());
        }
    }
}
