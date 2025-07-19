<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Usuario', function () {
    test('puede registrarse exitosamente con datos válidos', function () {
        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@ejemplo.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/users', $userData);

        $response->assertCreated()
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at'
                ],
                'token_type',
                'token'
            ])
            ->assertJson([
                'message' => 'Usuario creado exitosamente',
                'token_type' => 'Bearer'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Juan Pérez',
            'email' => 'juan@ejemplo.com'
        ]);
    });

    test('requiere nombre para registro', function () {
        $response = $this->postJson('/api/users', [
            'email' => 'juan@ejemplo.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Error de validación')
            ->assertJsonValidationErrors(['name']);
    });

    test('requiere email para registro', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Error de validación')
            ->assertJsonValidationErrors(['email']);
    });

    test('requiere email válido', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'email' => 'correo-invalido',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Error de validación')
            ->assertJsonValidationErrors(['email']);
    });

    test('requiere contraseña', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'email' => 'juan@ejemplo.com'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Error de validación')
            ->assertJsonValidationErrors(['password']);
    });

    test('requiere confirmación de contraseña', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'email' => 'juan@ejemplo.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Error de validación')
            ->assertJsonValidationErrors(['password']);
    });

    test('valida email único', function () {
        // Crear usuario existente
        User::create([
            'name' => 'Usuario Existente',
            'email' => 'juan@ejemplo.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'email' => 'juan@ejemplo.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Error de validación')
            ->assertJsonValidationErrors(['email']);
    });
});
