<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Variable global para el token
$userToken = null;

describe('User Creation API', function () {

    test('puede crear un usuario exitosamente con datos válidos', function () {
        global $userToken;

        $userData = [
            'name' => 'Juan Pérez',
            'email' => 'juan@ejemplo.com',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ];

        $response = $this->postJson('/api/users', $userData);

        dump('=== RESPUESTA EXITOSA - CREAR USUARIO ===');
        dump('JSON:', $response->json());
        dump('Status:', $response->getStatusCode());

        $response->assertStatus(201)
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

        // Guardar el token para otros tests
        $userToken = $response->json('token');

        $this->assertDatabaseHas('users', [
            'name' => 'Juan Pérez',
            'email' => 'juan@ejemplo.com'
        ]);

        // Verificar que la contraseña esté hasheada
        $user = User::where('email', 'juan@ejemplo.com')->first();
        expect($user->password)->not->toBe('contraseña123');
        expect(password_verify('contraseña123', $user->password))->toBeTrue();
    });

    // PAR 1: NOMBRE REQUERIDO
    test('falla cuando el nombre es requerido - test normal', function () {
        $response = $this->postJson('/api/users', [
            'email' => 'test@ejemplo.com',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJson([
                'errors' => [
                    'name' => ['El nombre es obligatorio.']
                ]
            ]);
    });

    test('falla cuando el nombre es requerido - response JSON normal', function () {
        $response = $this->postJson('/api/users', [
            'email' => 'test@ejemplo.com',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ]);

        dump('=== NOMBRE REQUERIDO - RESPUESTA LARAVEL NORMAL ===');
        dump('JSON:', $response->json());
        dump('Status:', $response->getStatusCode());

        expect($response->getStatusCode())->toBe(422);
    });

    test('falla cuando el nombre es requerido - test ApiException', function () {
        $response = $this->postJson('/api/users', [
            'email' => 'test1@ejemplo.com',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
                'timestamp',
                'debug'
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => [
                    'name' => ['El nombre es obligatorio.']
                ]
            ]);

        $responseData = $response->json();
        expect($responseData['timestamp'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/');
    });

    test('falla cuando el nombre es requerido - response JSON ApiException', function () {
        $response = $this->postJson('/api/users', [
            'email' => 'test2@ejemplo.com',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ]);

        dump('=== NOMBRE REQUERIDO - RESPUESTA APIEXCEPTION ===');
        dump('JSON:', $response->json());
        dump('Status:', $response->getStatusCode());

        expect($response->getStatusCode())->toBe(422);
    });

    // PAR 2: EMAIL REQUERIDO
    test('falla cuando el email es requerido - test normal', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJson([
                'errors' => [
                    'email' => ['El correo electrónico es obligatorio.']
                ]
            ]);
    });

    test('falla cuando el email es requerido - response JSON normal', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ]);

        dump('=== EMAIL REQUERIDO - RESPUESTA LARAVEL NORMAL ===');
        dump('JSON:', $response->json());
        dump('Status:', $response->getStatusCode());

        expect($response->getStatusCode())->toBe(422);
    });

    test('falla cuando el email es requerido - test ApiException', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
                'timestamp',
                'debug'
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => [
                    'email' => ['El correo electrónico es obligatorio.']
                ]
            ]);

        $responseData = $response->json();
        expect($responseData['timestamp'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/');
    });

    test('falla cuando el email es requerido - response JSON ApiException', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ]);

        dump('=== EMAIL REQUERIDO - RESPUESTA APIEXCEPTION ===');
        dump('JSON:', $response->json());
        dump('Status:', $response->getStatusCode());

        expect($response->getStatusCode())->toBe(422);
    });

    // PAR 3: EMAIL INVÁLIDO
    test('falla cuando el email no es válido - test normal', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'email' => 'email-invalido',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email'])
            ->assertJson([
                'errors' => [
                    'email' => ['El correo electrónico debe ser una dirección de correo válida.']
                ]
            ]);
    });

    test('falla cuando el email no es válido - response JSON normal', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'email' => 'email-invalido2',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ]);

        dump('=== EMAIL INVÁLIDO - RESPUESTA LARAVEL NORMAL ===');
        dump('JSON:', $response->json());
        dump('Status:', $response->getStatusCode());

        expect($response->getStatusCode())->toBe(422);
    });

    test('falla cuando el email no es válido - test ApiException', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'email' => 'email-invalido3',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
                'timestamp',
                'debug'
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => [
                    'email' => ['El correo electrónico debe ser una dirección de correo válida.']
                ]
            ]);

        $responseData = $response->json();
        expect($responseData['timestamp'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/');
    });

    test('falla cuando el email no es válido - response JSON ApiException', function () {
        $response = $this->postJson('/api/users', [
            'name' => 'Juan Pérez',
            'email' => 'email-invalido4',
            'password' => 'contraseña123',
            'password_confirmation' => 'contraseña123'
        ]);

        dump('=== EMAIL INVÁLIDO - RESPUESTA APIEXCEPTION ===');
        dump('JSON:', $response->json());
        dump('Status:', $response->getStatusCode());

        expect($response->getStatusCode())->toBe(422);
    });

    // PAR 4: MÚLTIPLES ERRORES
    test('falla con múltiples errores de validación - test normal', function () {
        $response = $this->postJson('/api/users', [
            'name' => '',
            'email' => 'email-invalido',
            'password' => '123'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    });

    test('falla con múltiples errores de validación - response JSON normal', function () {
        $response = $this->postJson('/api/users', [
            'name' => '',
            'email' => 'email-invalido2',
            'password' => '123'
        ]);

        dump('=== MÚLTIPLES ERRORES - RESPUESTA LARAVEL NORMAL ===');
        dump('JSON:', $response->json());
        dump('Status:', $response->getStatusCode());

        expect($response->getStatusCode())->toBe(422);
    });

    test('falla con múltiples errores de validación - test ApiException', function () {
        $response = $this->postJson('/api/users', [
            'name' => '',
            'email' => 'email-invalido3',
            'password' => '123'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors',
                'timestamp',
                'debug'
            ])
            ->assertJson([
                'success' => false,
                'message' => 'Error de validación'
            ]);

        $responseData = $response->json();
        expect($responseData['errors'])->toHaveKeys(['name', 'email', 'password']);
        expect($responseData['timestamp'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/');
    });

    test('falla con múltiples errores de validación - response JSON ApiException', function () {
        $response = $this->postJson('/api/users', [
            'name' => '',
            'email' => 'email-invalido4',
            'password' => '123'
        ]);

        dump('=== MÚLTIPLES ERRORES - RESPUESTA APIEXCEPTION ===');
        dump('JSON:', $response->json());
        dump('Status:', $response->getStatusCode());

        expect($response->getStatusCode())->toBe(422);
    });

});
