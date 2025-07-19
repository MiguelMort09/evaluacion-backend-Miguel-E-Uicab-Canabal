<?php
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('GET /api/external/users retorna lista de users', function () {
    Http::fake([
        'jsonplaceholder.typicode.com/users' => Http::response([
            ['id' => 1, 'title' => 'Post de prueba']
        ], 200),
    ]);

    $response = $this->getJson('/api/external/users');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id',]
            ]
        ])->assertJson([
            'data' => [
                ['id' => 1, 'title' => 'Post de prueba']
            ]
        ])->assertJsonMissing([
            'errors'
        ])->assertJsonCount(1, 'data');
});

test('GET /api/external/posts retorna lista de posts', function () {
    // Opcional: mock de la API externa si no quieres depender del servicio real
    Http::fake([
        'jsonplaceholder.typicode.com/posts' => Http::response([
            ['id' => 1, 'title' => 'Post de prueba']
        ], 200),
    ]);

    $response = $this->getJson('/api/external/posts');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title']
            ]
        ]);
});
