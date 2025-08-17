<?php

use Wetrocloud\WetrocloudSdk\Wetrocloud;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
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

// Test textGeneration 
test('can generate text without rag', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'response' => 'Generated text response',
        'tokens' => 50,
        'model' => 'llama-3.3-70b'
    ]));

    $messages = [
        ['role' => 'user', 'content' => 'Hello, how are you?'],
        ['role' => 'assistant', 'content' => 'I am doing well, thank you!']
    ];

    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('/v1/text-generation/', [
            'multipart' => [
                [
                    'name' => 'messages',
                    'contents' => json_encode($messages, JSON_THROW_ON_ERROR),
                ],
                [
                    'name' => 'model',
                    'contents' => 'llama-3.3-70b',
                ],
            ]
        ])
        ->once()
        ->andReturn($mockResponse);

    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);

    $result = $wetrocloud->textGeneration($messages, 'llama-3.3-70b');

    expect($result)->toBe([
        'success' => true,
        'response' => 'Generated text response',
        'tokens' => 50,
        'model' => 'llama-3.3-70b'
    ]);
});


// Test imageToText method
test('can extract text from image', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'text' => 'Extracted text from image',
        'tokens' => 20
    ]));

    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('/v1/image-to-text/', [
            'json' => [
                'image_url' => 'https://example.com/image.jpg',
                'request_query' => 'What text is in this image?'
            ]
        ])
        ->once()
        ->andReturn($mockResponse);

    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);

    $result = $wetrocloud->imageToText('https://example.com/image.jpg', 'What text is in this image?');

    expect($result)->toBe([
        'success' => true,
        'text' => 'Extracted text from image',
        'tokens' => 20
    ]);
});

// Test markdownConverter 
test('can convert file to markdown', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'markdown' => '# Converted Document\n\nThis is the converted content.',
        'tokens' => 30
    ]));

    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('/v2/markdown-converter/', [
            'json' => [
                'link' => 'https://example.com/document.pdf',
                'resource_type' => 'file'
            ]
        ])
        ->once()
        ->andReturn($mockResponse);

    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);

    $result = $wetrocloud->markdownConverter('https://example.com/document.pdf', 'file');

    expect($result)->toBe([
        'success' => true,
        'markdown' => '# Converted Document\n\nThis is the converted content.',
        'tokens' => 30
    ]);
});

test('can convert web page to markdown', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'markdown' => '# Web Page Title\n\nContent from the web page.',
        'tokens' => 25
    ]));

    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('/v2/markdown-converter/', [
            'json' => [
                'link' => 'https://example.com/webpage',
                'resource_type' => 'web'
            ]
        ])
        ->once()
        ->andReturn($mockResponse);

    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);

    $result = $wetrocloud->markdownConverter('https://example.com/webpage', 'web');

    expect($result)->toBe([
        'success' => true,
        'markdown' => '# Web Page Title\n\nContent from the web page.',
        'tokens' => 25
    ]);
});

test('can convert image to markdown', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'markdown' => '![Image Description](image-url)\n\nDescription of the image content.',
        'tokens' => 15
    ]));
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('/v2/markdown-converter/', [
            'json' => [
                'link' => 'https://example.com/image.jpg',
                'resource_type' => 'image'
            ]
        ])
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->markdownConverter('https://example.com/image.jpg', 'image');
    
    expect($result)->toBe([
        'success' => true,
        'markdown' => '![Image Description](image-url)\n\nDescription of the image content.',
        'tokens' => 15
    ]);
});

// Test transcript method
test('can generate transcript from youtube video', function () {
    $mockResponse = new Response(200, [], json_encode([
        'success' => true,
        'transcript' => 'Hello, welcome to this video tutorial...',
        'tokens' => 45,
        'duration' => '10:30'
    ]));
    
    $mockClient = Mockery::mock(Client::class);
    $mockClient->shouldReceive('post')
        ->with('/v2/transcript/', [
            'json' => [
                'link' => 'https://www.youtube.com/watch?v=example123',
                'resource_type' => 'youtube'
            ]
        ])
        ->once()
        ->andReturn($mockResponse);
    
    $wetrocloud = new Wetrocloud('test-api-key');
    $wetrocloudReflection = new ReflectionClass($wetrocloud);
    $clientProperty = $wetrocloudReflection->getProperty('client');
    $clientProperty->setAccessible(true);
    $clientProperty->setValue($wetrocloud, $mockClient);
    
    $result = $wetrocloud->transcript('https://www.youtube.com/watch?v=example123');
    
    expect($result)->toBe([
        'success' => true,
        'transcript' => 'Hello, welcome to this video tutorial...',
        'tokens' => 45,
        'duration' => '10:30'
    ]);
});
