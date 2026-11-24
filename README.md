# Sistema de Solicitudes y Órdenes de Compra

Módulo ERP para la gestión de solicitudes de compra y órdenes de compra: un empleado solicita productos, el administrador aprueba/rechaza y genera la orden de compra al proveedor. Incluye estados, historial y notificaciones por correo.

## Idea del proyecto

Automatizar el flujo de compras: desde la solicitud interna hasta la orden de compra al proveedor, con trazabilidad y control de estados (pendiente, aprobada, rechazada, finalizada). Reduce tiempos y evita compras no autorizadas.

## Stack tecnológico

- PHP 8.2
- Laravel 11
- Blade + Tailwind CSS
- MySQL (configurable a PostgreSQL, SQL Server u Oracle)
- (Opcional) Laravel Breeze para autenticación

## Estructura del proyecto

```
solicitudes-compras-laravel/
├── composer.json
├── artisan
├── .env.example
├── routes/web.php
├── app/Http/Controllers/SolicitudController.php
├── app/Models/Solicitud.php
├── resources/views/solicitudes/index.blade.php
└── database/migrations/..._create_solicitudes_table.php
```

## Cómo ejecutar

1. Instala PHP 8.2 y Composer.
2. Copia `.env.example` a `.env` y configura la base de datos.
3. Instala dependencias:
   ```bash
   composer install
   ```
4. Genera la key y migra:
   ```bash
   php artisan key:generate
   php artisan migrate
   ```
5. Ejecuta:
   ```bash
   php artisan serve
   ```
6. Abre `http://127.0.0.1:8000`.

## Flujo principal

- Empleado crea una solicitud con lista de productos y cantidades.
- Administrador revisa y aprueba/rechaza.
- Al aprobar, se genera la orden de compra al proveedor.
- Al recibir la mercancía, se finaliza y actualiza el inventario.

## Metodología

Scrum: sprints de 2 semanas con revisiones al final de cada sprint.

## Autor

Luis Ernesto Corredor Jiménez — https://github.com/lysusco
