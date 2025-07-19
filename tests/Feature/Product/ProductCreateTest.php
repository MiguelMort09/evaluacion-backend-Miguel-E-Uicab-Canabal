<?php

use App\Models\Product;
use App\Models\User;
use App\Enums\ProductStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Producto', function () {
    test('puede crear un producto exitosamente', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $productData = [
            'name' => 'Producto de Prueba',
            'description' => 'Descripción del producto',
            'slug' => 'producto-prueba',
            'price' => 99.99,
            'stock' => 10,
            'status' => ProductStatus::ACTIVE->value
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/products', $productData);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'Producto creado exitosamente'
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'price',
                    'stock',
                    'status',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('products', array_merge(
            $productData,
            ['user_id' => $user->id]
        ));
    });

    test('requiere autenticación', function () {
        $productData = [
            'name' => 'Producto de Prueba',
            'description' => 'Descripción del producto',
            'slug' => 'producto-prueba',
            'price' => 99.99,
            'stock' => 10,
            'status' => ProductStatus::ACTIVE->value
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'message' => 'No autenticado'
            ]);
    });

    test('valida nombre requerido', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/products', [
            'description' => 'Descripción del producto',
            'slug' => 'producto-prueba',
            'price' => 99.99,
            'stock' => 10,
            'status' => ProductStatus::ACTIVE->value
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Error de validación')
            ->assertJsonValidationErrors(['name']);
    });

    test('valida precio válido', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/products', [
            'name' => 'Producto de Prueba',
            'description' => 'Descripción del producto',
            'slug' => 'producto-prueba',
            'price' => -10,
            'stock' => 10,
            'status' => ProductStatus::ACTIVE->value
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Error de validación')
            ->assertJsonValidationErrors(['price']);
    });

    test('valida stock válido', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/products', [
            'name' => 'Producto de Prueba',
            'description' => 'Descripción del producto',
            'slug' => 'producto-prueba',
            'price' => 99.99,
            'stock' => -1,
            'status' => ProductStatus::ACTIVE->value
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Error de validación')
            ->assertJsonValidationErrors(['stock']);
    });

    test('valida estado válido', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->postJson('/api/products', [
            'name' => 'Producto de Prueba',
            'description' => 'Descripción del producto',
            'slug' => 'producto-prueba',
            'price' => 99.99,
            'stock' => 10,
            'status' => 'invalid_status'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Error de validación')
            ->assertJsonValidationErrors(['status']);
    });
});
