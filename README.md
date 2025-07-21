<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Evaluación Técnica

Este proyecto es una API desarrollada en Laravel que incluye:
- API REST de productos (CRUD)
- Consumo de servicio externo JSONPlaceholder
- Notificaciones en tiempo real tipo chat (Livewire + Broadcast)

## Ejercicios realizados

1. **API REST de productos**
   - Endpoints CRUD para productos.
   - Validaciones y migraciones.
2. **Consumo de servicio JSONPlaceholder**
   - Servicio y endpoints para obtener datos externos.
3. **Notificaciones en tiempo real**
   - Componente Livewire y eventos broadcast.
   - Vista tipo chat con animaciones y límite de 5 mensajes.

## Pruebas Feature realizadas

- CRUD de productos: crear, listar, mostrar, actualizar, eliminar.
- Consumo de servicio externo JSONPlaceholder.
- Autenticación y creación de usuarios.
- No se han implementado pruebas Feature para notificaciones en tiempo real ni Livewire.

## Estructura de Carpetas

- **app/**: Modelos, controladores, servicios, eventos, Livewire.
- **database/**: Migraciones, seeders, factories.
- **resources/**: Vistas Blade, CSS, JS.
- **routes/**: Rutas API, web y canales.
- **tests/**: Pruebas Feature y Unit.

---

## Requerimientos técnicos y ejecución

1. **Requisitos previos**
   - PHP >= 8.1
   - Composer
   - Node.js y npm
   - SQLite (por defecto) o configurar MySQL/Postgres en `.env`

2. **Instalación**
   ```bash
   composer install
   npm install && npm run build
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   ```

3. **Ejecución**
   ```bash
   php artisan serve
   # O usar Valet, Sail o Docker según preferencia
   ```

4. **Pruebas**
   ```bash
   php artisan test
   ```

5. **Probar notificaciones tipo chat**
   ```bash
   curl -k -X POST https://api.test/api/webhooks/notifications -H "Content-Type: application/json"  -d '{"event":"message.received","channel":"notifications","message":{"body":"MORTa"}}'
   ```

---

Para más información, revisa la documentación de cada servicio y los archivos de configuración en el proyecto.
