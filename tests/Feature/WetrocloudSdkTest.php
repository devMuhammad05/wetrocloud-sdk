<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use Mockery;

// Test constructor and basic setup
test('can create wetrocloud instance with api key', function () {
    $wetrocloud = new Wetrocloud('test-api-key');
    
    expect($wetrocloud)->toBeInstanceOf(Wetrocloud::class);
    expect($wetrocloud->getBaseUrl())->toBe('https://api.wetrocloud.com');
});

test('can create wetrocloud instance with custom base url', function () {
    $wetrocloud = new Wetrocloud('test-api-key', 'https://custom-api.example.com');
    
    expect($wetrocloud->getBaseUrl())->toBe('https://custom-api.example.com');
});

test('can get http client instance', function () {
    $wetrocloud = new Wetrocloud('test-api-key');
    
    expect($wetrocloud->getClient())->toBeInstanceOf(Client::class);
});

test('throws exception when api key is empty', function () {
    expect(fn() => new Wetrocloud(''))->toThrow(\InvalidArgumentException::class, 'API key cannot be empty');
});


// Test createCollection method
test('can create collection without collection id', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'collection_id' => 'test-collection-123',
        'message' => 'Collection created successfully'
    ]));
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('/v1/collection/create/', ['json' => []])
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->createCollection();
    
    expect($result)->toBe([
        'success' => true,
        'collection_id' => 'test-collection-123',
        'message' => 'Collection created successfully'
    ]);
});

test('can create collection with collection id', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'collection_id' => 'custom-collection-id',
        'message' => 'Collection created successfully'
    ]));
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('/v1/collection/create/', ['json' => ['collection_id' => 'custom-collection-id']])
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->createCollection('custom-collection-id');
    
    expect($result)->toBe([
        'success' => true,
        'collection_id' => 'custom-collection-id',
        'message' => 'Collection created successfully'
    ]);
});

// Test listAllCollections method
test('can list all collections', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'collections' => [
            ['id' => 'collection-1', 'name' => 'Test Collection 1'],
            ['id' => 'collection-2', 'name' => 'Test Collection 2']
        ]
    ]));
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('get')
        ->with('/v1/collection/all/')
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->listAllCollections();
    
    expect($result)->toBe([
        'success' => true,
        'collections' => [
            ['id' => 'collection-1', 'name' => 'Test Collection 1'],
            ['id' => 'collection-2', 'name' => 'Test Collection 2']
        ]
    ]);
});

// Test insertResource method
test('can insert resource into collection', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'resource_id' => 'resource-123',
        'message' => 'Resource inserted successfully'
    ]));
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('/v1/resource/insert/', [
            'json' => [
                'collection_id' => 'test-collection',
                'resource' => 'Sample document content',
                'type' => 'document'
            ]
        ])
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->insertResource('test-collection', 'Sample document content', 'document');
    
    expect($result)->toBe([
        'success' => true,
        'resource_id' => 'resource-123',
        'message' => 'Resource inserted successfully'
    ]);
});

// Test queryCollection method
test('can query collection without schema', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'results' => ['result1', 'result2'],
        'query' => 'test query'
    ]));
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('v1/collection/query/', [
            'json' => [
                'collection_id' => 'test-collection',
                'request_query' => 'test query',
                'json_schema' => null,
                'json_schema_rules' => null
            ]
        ])
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->queryCollection('test-collection', 'test query');
    
    expect($result)->toBe([
        'success' => true,
        'results' => ['result1', 'result2'],
        'query' => 'test query'
    ]);
});

test('can query collection with schema and rules', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'results' => ['structured result'],
        'query' => 'test query'
    ]));
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('v1/collection/query/', [
            'json' => [
                'collection_id' => 'test-collection',
                'request_query' => 'test query',
                'json_schema' => '{"type": "object"}',
                'json_schema_rules' => '{"required": ["name"]}'
            ]
        ])
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->queryCollection(
        'test-collection', 
        'test query', 
        '{"type": "object"}', 
        '{"required": ["name"]}'
    );
    
    expect($result)->toBe([
        'success' => true,
        'results' => ['structured result'],
        'query' => 'test query'
    ]);
});

// Test chatCollection method
test('can chat with collection without history', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'response' => 'Hello! How can I help you?',
        'tokens' => 15
    ]));
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('/v1/collection/chat/', [
            'json' => [
                'collection_id' => 'test-collection',
                'message' => 'Hello'
            ]
        ])
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->chatCollection('test-collection', 'Hello');
    
    expect($result)->toBe([
        'success' => true,
        'response' => 'Hello! How can I help you?',
        'tokens' => 15
    ]);
});

test('can chat with collection with history', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'response' => 'Based on our conversation...',
        'tokens' => 25
    ]));
    
    $chatHistory = json_encode([
        ['role' => 'user', 'content' => 'Previous message'],
        ['role' => 'assistant', 'content' => 'Previous response']
    ]);
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('/v1/collection/chat/', [
            'json' => [
                'collection_id' => 'test-collection',
                'message' => 'Follow up question',
                'chat_history' => $chatHistory
            ]
        ])
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->chatCollection('test-collection', 'Follow up question', $chatHistory);
    
    expect($result)->toBe([
        'success' => true,
        'response' => 'Based on our conversation...',
        'tokens' => 25
    ]);
});

// Test categorizeResource method
test('can categorize resource', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'label' => 'Technology',
        'tokens' => 10
    ]));
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('/v1/categorize/', [
            'json' => [
                'resource' => 'This is a technology article about AI',
                'type' => 'article',
                'json_schema' => '{"type": "string"}',
                'categories' => 'Technology,Science,Health',
                'prompt' => 'Categorize this content'
            ]
        ])
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->categorizeResource(
        'This is a technology article about AI',
        'article',
        '{"type": "string"}',
        'Technology,Science,Health',
        'Categorize this content'
    );
    
    expect($result)->toBe([
        'success' => true,
        'label' => 'Technology',
        'tokens' => 10
    ]);
});

// Test removeResource method
test('can remove resource from collection', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'message' => 'Resource removed successfully'
    ]));
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('delete')
        ->with('/v1/resource/remove/', [
            'json' => [
                'collection_id' => 'test-collection',
                'resource_id' => 'resource-123'
            ]
        ])
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->removeResource('test-collection', 'resource-123');
    
    expect($result)->toBe([
        'success' => true,
        'message' => 'Resource removed successfully'
    ]);
});

// Test deleteCollection method
test('can delete collection', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'message' => 'Collection deleted successfully'
    ]));
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('delete')
        ->with('/v1/collection/delete/', [
            'json' => [
                'collection_id' => 'test-collection'
            ]
        ])
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->deleteCollection('test-collection');
    
    expect($result)->toBe([
        'success' => true,
        'message' => 'Collection deleted successfully'
    ]);
});

// Test error handling
test('handles invalid json response', function () {
    $mockResponse = new Response(200, [], 'invalid json');
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('get')
        ->with('/v1/collection/all/')
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    expect(fn() => $wetrocloud->listAllCollections())
        ->toThrow(\RuntimeException::class, 'Invalid API response: expected JSON object, got invalid json');
});

test('handles guzzle exception', function () {
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('get')
        ->with('/v1/collection/all/')
        ->once()
        ->andThrow(new \GuzzleHttp\Exception\ConnectException('Connection failed', new \GuzzleHttp\Psr7\Request('GET', 'test')));
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    expect(fn() => $wetrocloud->listAllCollections())
        ->toThrow(\RuntimeException::class, 'Failed to fetch collections: Connection failed');
});
