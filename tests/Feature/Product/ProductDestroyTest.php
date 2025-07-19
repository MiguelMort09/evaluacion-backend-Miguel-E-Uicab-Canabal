<?php

use App\Models\Product;
use App\Models\User;
use App\Enums\ProductStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Producto', function () {
    test('puede eliminar un producto propio', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $product = Product::factory()->create([
            'user_id' => $user->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->deleteJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Producto eliminado exitosamente'
            ])
            ->assertJsonStructure([
                'success', 'message'
            ]);
    });

    test('requiere autenticación', function () {
        $user = User::factory()->create();
        $product = Product::create([
            'name' => 'Producto a Eliminar',
            'description' => 'Descripción del producto',
            'slug' => 'producto-a-eliminar',
            'price' => 99.99,
            'stock' => 10,
            'status' => ProductStatus::ACTIVE->value,
            'user_id' => $user->id
        ]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertUnauthorized()
            ->assertJson([
                'success' => false,
                'message' => 'No autenticado'
            ]);

        // Verificar que el producto no fue eliminado
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null
        ]);
    });

    test('retorna error si producto no existe', function () {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json'
        ])->deleteJson('/api/products/999999');

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Recurso no encontrado'
            ])
            ->assertJsonStructure([
                'success', 'message'
            ]);
    });

});
