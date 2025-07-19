<?php

use App\Models\Product;
use App\Models\User;
use App\Enums\ProductStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Producto', function () {
    test('lista productos con paginación', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        // Crear 3 productos de prueba
        for ($i = 1; $i <= 3; $i++) {
            Product::create([
                'name' => "Producto Test $i",
                'description' => "Descripción del producto $i",
                'slug' => "producto-test-$i",
                'price' => 99.99 + $i,
                'stock' => 10 + $i,
                'status' => ProductStatus::ACTIVE->value,
                'user_id' => $user->id
            ]);
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson('/api/products');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'slug',
                        'price',
                        'stock',
                        'status', 'status_label',
                        'user_id',
                        'user',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta',
                'links'
            ])
            ->assertJsonCount(3, 'data');
    });

    test('requiere autenticación', function () {
        $response = $this->getJson('/api/products');
        $response->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'message' => 'No autenticado'
            ]);
    });

    test('filtra productos por búsqueda', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        Product::create([
            'name' => 'iPhone 13',
            'description' => 'Smartphone Apple',
            'slug' => 'iphone-13',
            'price' => 999.99,
            'stock' => 10,
            'status' => ProductStatus::ACTIVE->value,
            'user_id' => $user->id
        ]);

        Product::create([
            'name' => 'Samsung Galaxy',
            'description' => 'Smartphone Android',
            'slug' => 'samsung-galaxy',
            'price' => 899.99,
            'stock' => 15,
            'status' => ProductStatus::ACTIVE->value,
            'user_id' => $user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson('/api/products?search=iphone');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'iPhone 13');
    });

    test('permite especificar límite de paginación', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        for ($i = 1; $i <= 15; $i++) {
            Product::create([
                'name' => "Producto $i",
                'description' => "Descripción $i",
                'slug' => "producto-$i",
                'price' => 99.99,
                'stock' => 10,
                'status' => ProductStatus::ACTIVE->value,
                'user_id' => $user->id
            ]);
        }

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->getJson('/api/products?per_page=10');

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.per_page', 10)
            ->assertJsonPath('meta.total', 15);
    });
});
