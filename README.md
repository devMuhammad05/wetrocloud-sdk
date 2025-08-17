# WetroCloud SDK for PHP

> **⚠️ Unofficial SDK**: This is an **unofficial** PHP SDK for the WetroCloud API.

## Introduction

The **WetroCloud SDK for PHP** provides an easy way to interact with the WetroCloud API, allowing developers to create collections, insert resources, and query data effortlessly using PHP.

## Installation

```bash
composer require wetrocloud/wetrocloud-sdk
```

## Quick Start

```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

// Initialize the SDK
$wetrocloud = new Wetrocloud('your-api-key');

// Create a collection
$response = $wetrocloud->createCollection('my-collection');

// Insert a resource
$wetrocloud->insertResource('my-collection', 'https://example.com/article', 'web');

// Query the collection
$results = $wetrocloud->queryCollection('my-collection', 'What is this about?');
```

## Available Methods

### Collection Management

#### 1. `createCollection()`

Creates a new collection.

**Parameters:**
- `?string $collectionId` - (Optional) The unique ID of the collection.

**Returns:** `array<string, mixed>`

**Example:**
```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

$wetrocloud = new Wetrocloud('your-api-key');

// Create collection with auto-generated ID
$response = $wetrocloud->createCollection();

// Create collection with custom ID
$response = $wetrocloud->createCollection('unique-collection-id');

echo "Creating Collection: " . json_encode($response, JSON_PRETTY_PRINT);
```

#### 2. `listAllCollections()`

Retrieves a list of available collections.

**Returns:** `array<string, mixed>`

**Example:**
```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

$wetrocloud = new Wetrocloud('your-api-key');
$response = $wetrocloud->listAllCollections();

echo "Listing Collections: " . json_encode($response, JSON_PRETTY_PRINT);
```

#### 3. `deleteCollection()`

Deletes an entire collection.

**Parameters:**
- `string $collectionId` - The ID of the collection.

**Returns:** `array<string, mixed>`

**Example:**
```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

$wetrocloud = new Wetrocloud('your-api-key');
$response = $wetrocloud->deleteCollection('your-collection-id');

echo "Deleting collection: " . json_encode($response, JSON_PRETTY_PRINT);
```

### Resource Management

#### 4. `insertResource()`

Inserts a resource into a collection.

**Parameters:**
- `string $collectionId` - The ID of the collection.
- `string $resource` - The resource to insert.
- `string $type` - The type of resource.

**Returns:** `array<string, mixed>`

**Example:**
```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

$wetrocloud = new Wetrocloud('your-api-key');

$response = $wetrocloud->insertResource(
    'your-collection-id',
    'https://medium.com/@AlexanderObregon/a-brief-history-of-artificial-intelligence-1656693721f9',
    'web'
);

echo "Insert a resource: " . json_encode($response, JSON_PRETTY_PRINT);
```

#### 5. `removeResource()`

Deletes a resource from a collection.

**Parameters:**
- `string $collectionId` - The ID of the collection.
- `string $resourceId` - The ID of the resource to delete.

**Returns:** `array<string, mixed>`

**Example:**
```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

$wetrocloud = new Wetrocloud('your-api-key');
$response = $wetrocloud->removeResource('your-collection-id', 'your-resource-id');

echo "Deleting resource: " . json_encode($response, JSON_PRETTY_PRINT);
```

### Querying & Chat

#### 6. `queryCollection()`

Queries resources from a collection.

**Parameters:**
- `string $collectionId` - The ID of the collection.
- `string $requestQuery` - The query string.
- `?string $jsonSchema` - Optional JSON schema.
- `?string $jsonSchemaRules` - Optional JSON schema rules.

**Returns:** `array<string, mixed>`

**Example:**
```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

$wetrocloud = new Wetrocloud('your-api-key');

$collectionId = 'your-collection-id';
$query = 'What are the sales trends for Q1?';

$response = $wetrocloud->queryCollection($collectionId, $query);

// With schema and rules
$response = $wetrocloud->queryCollection(
    $collectionId,
    $query,
    '{"type": "object", "properties": {"answer": {"type": "string"}}}',
    '{"required": ["answer"]}'
);

echo "Querying resource: " . json_encode($response, JSON_PRETTY_PRINT);
```

#### 7. `chatCollection()`

Chat with a collection using message history.

**Parameters:**
- `string $collectionId` - The ID of the collection.
- `string $message` - The message to send.
- `?string $chatHistory` - Optional chat history as JSON string.

**Returns:** `array<string, mixed>`

**Example:**
```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

$wetrocloud = new Wetrocloud('your-api-key');

// Simple chat without history
$response = $wetrocloud->chatCollection('your-collection-id', 'Tell me more');

// Chat with history
$chatHistory = json_encode([
    ['role' => 'user', 'content' => 'What is this all about?'],
    ['role' => 'assistant', 'content' => 'This is about Queen Elizabeth II of England']
]);

$response = $wetrocloud->chatCollection(
    'your-collection-id',
    'Tell me more',
    $chatHistory
);

echo "Chat with collection: " . json_encode($response, JSON_PRETTY_PRINT);
```

### AI & Content Processing

#### 8. `categorizeResource()`

Categorizes a resource using predefined categories.

**Parameters:**
- `string $resource` - The resource to categorize.
- `string $type` - The type of resource.
- `string $jsonSchema` - JSON schema of the resource.
- `string $categories` - Comma-separated list of categories.
- `string $prompt` - An overall command of your request.

**Returns:** `array<string, mixed>`

**Example:**
```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

$wetrocloud = new Wetrocloud('your-api-key');

$response = $wetrocloud->categorizeResource(
    'match review: John Cena vs. The Rock',
    'text',
    '{"label": ""}',
    'football,coding,entertainment,basketball,wrestling,information',
    'Where does this fall under?'
);

echo "Categorizing resource: " . json_encode($response, JSON_PRETTY_PRINT);
```

#### 9. `textGeneration()`

Generates text without retrieval-augmented generation (RAG).

**Parameters:**
- `array $messages` - Array of message objects with role and content.
- `string $model` - The model to use.

**Returns:** `array<string, mixed>`

**Example:**
```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

$wetrocloud = new Wetrocloud('your-api-key');

$messages = [
    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
    ['role' => 'user', 'content' => 'Write a short poem about technology.']
];

$response = $wetrocloud->textGeneration($messages, 'llama-3.3-70b');

echo "Generation without RAG: " . json_encode($response, JSON_PRETTY_PRINT);
```

#### 10. `imageToText()`

Extracts text from an image.

**Parameters:**
- `string $imageUrl` - The URL of the image.
- `string $requestQuery` - The query to process the image.

**Returns:** `array<string, mixed>`

**Example:**
```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

$wetrocloud = new Wetrocloud('your-api-key');

$imageUrl = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTQBQcwHfud1w3RN25Wgys6Btt_Y-4mPrD2kg&s';
$query = 'What animal is this?';

$response = $wetrocloud->imageToText($imageUrl, $query);

echo "Image to text: " . json_encode($response, JSON_PRETTY_PRINT);
```

### Content Conversion

#### 11. `markdownConverter()`

Converts a resource (file, web, image) to Markdown.

**Parameters:**
- `string $resource` - The resource URL (file, web page, or image).
- `string $resourceType` - The type of resource: "file", "web", or "image".

**Returns:** `array<string, mixed>`

**Example:**
```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

$wetrocloud = new Wetrocloud('your-api-key');

// Convert web page to markdown
$response = $wetrocloud->markdownConverter(
    'https://www.forbes.com/real-time-billionaires/',
    'web'
);

// Convert file to markdown
$response = $wetrocloud->markdownConverter(
    'https://example.com/document.pdf',
    'file'
);

// Convert image to markdown
$response = $wetrocloud->markdownConverter(
    'https://example.com/image.jpg',
    'image'
);

echo "Converted Markdown: " . json_encode($response, JSON_PRETTY_PRINT);
```

#### 12. `transcript()`

Retrieves transcript data from a resource (e.g., YouTube video).

**Parameters:**
- `string $link` - The URL of the resource (e.g., YouTube video link).
- `string $resourceType` - The type of resource (e.g., "youtube").

**Returns:** `array<string, mixed>`

**Example:**
```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

$wetrocloud = new Wetrocloud('your-api-key');

$response = $wetrocloud->transcript(
    'https://www.youtube.com/watch?v=m4qBwGnubew',
    'youtube'
);

echo "Transcript result: " . json_encode($response, JSON_PRETTY_PRINT);
```


## Configuration

### Custom Base URL

```php
<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;

// Use custom API endpoint
$wetrocloud = new Wetrocloud('your-api-key', 'https://custom-api.wetrocloud.com');
```

### HTTP Client Configuration

The SDK uses Guzzle HTTP client with the following default configuration:

- **Timeout**: 30 seconds
- **Headers**: 
  - `Authorization: Token your-api-key`
  - `User-Agent: WetroSDK-PHP/1.0`
  - `Content-Type: application/json`
  - `Accept: application/json`

## Requirements

- PHP 8.3 or higher
- Guzzle HTTP Client 7.9 or higher

## Testing

Run the test suite using:

```bash
./vendor/bin/pest
```

The test suite uses Pest PHP testing framework.

## Support

For additional support, please contact support@wetrocloud.com or visit the website [WetroCloud Docs](https://docs.wetrocloud.com/introduction).

## License

This SDK is licensed under the MIT License.

## Disclaimer

This is an **unofficial** PHP SDK for the WetroCloud API. It is not officially maintained by WetroCloud and is provided as-is for community use. For official support and documentation, please visit the [official WetroCloud documentation](https://docs.wetrocloud.com/).
