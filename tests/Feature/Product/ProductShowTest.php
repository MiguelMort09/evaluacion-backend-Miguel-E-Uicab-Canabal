<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Producto', function () {
    test('usuario autenticado puede ver un producto', function () {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->getJson('/api/products/' . $product->id);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'description',
                    'slug',
                    'price',
                    'stock',
                    'status',
                    'status_label',
                    'user_id',
                    'user',
                    'created_at',
                    'updated_at'
                ],
                'message'
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Producto obtenido exitosamente',
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                ]
            ]);
    });

    test('retorna 404 si el producto no existe', function () {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->getJson('/api/products/999999');

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Recurso no encontrado'
            ])
            ->assertJsonStructure([
                'success',
                'message'
            ]);
    });

    test('usuario no autenticado no puede ver un producto', function () {
        $product = Product::factory()->create();
        $response = $this->getJson('/api/products/' . $product->id);

        $response->assertUnauthorized();
    });
});
