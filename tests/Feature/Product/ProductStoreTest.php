<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use App\Enums\ProductStatus;

uses(RefreshDatabase::class);

describe('Producto', function () {
    test('usuario autenticado puede crear un producto', function () {
        $user = User::factory()->create();
        Auth::login($user);

        $productData = [
            'name' => 'Producto de prueba',
            'price' => 100.50,
            'slug' => 'producto-de-prueba',
            'stock' => 10,
            'status' => ProductStatus::ACTIVE->value,
        ];

        $response = $this->actingAs($user)->postJson('/api/products', $productData);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'name',
                    'price',
                    'status',
                    'created_at',
                    'updated_at'
                ]
            ])
            ->assertJson([
                'success' => true,
                'message' => 'Producto creado exitosamente',
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Producto de prueba',
            'user_id' => $user->id,
        ]);
    });

    test('falla al crear producto sin nombre', function () {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/products', [
            'price' => 100.50,
            'slug' => 'producto-sin-nombre',
            'stock' => 10,
            'status' => ProductStatus::ACTIVE->value,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    });

    test('falla al crear producto sin precio', function () {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/products', [
            'name' => 'Producto sin precio',
            'slug' => 'producto-sin-precio',
            'stock' => 10,
            'status' => ProductStatus::ACTIVE->value,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    });

    test('falla al crear producto con precio inválido', function () {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/products', [
            'name' => 'Producto de prueba',
            'price' => 'invalido',
            'slug' => 'producto-precio-invalido',
            'stock' => 10,
            'status' => ProductStatus::ACTIVE->value,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    });

    test('usuario no autenticado no puede crear un producto', function () {
        $response = $this->postJson('/api/products', [
            'name' => 'Producto de prueba',
            'price' => 100.50,
            'slug' => 'producto-no-autenticado',
            'stock' => 10,
            'status' => ProductStatus::ACTIVE->value,
        ]);

        $response->assertUnauthorized();
    });
});
