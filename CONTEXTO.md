# Contexto del Proyecto 3: Sistema de Solicitudes y Órdenes de Compra (Laravel)

> Archivo de contexto para iniciar un nuevo chat en OpenCode trabajando específicamente en este proyecto.

## Identidad del desarrollador
- **Nombre:** Luis Ernesto Corredor Jiménez
- **Perfil:** Desarrollador pragmático, nivel básico–intermedio, experiencia en ERP (GeneXus, Java, JS, PHP, Spring Boot, Laravel).
- **GitHub:** https://github.com/lysusco
- **Objetivo:** Demostrar manejo de Laravel (PHP) con complejidad limitada pero funcional.

## Proyecto
- **Nombre:** Sistema de Solicitudes y Órdenes de Compra
- **Ruta local:** `C:\Users\jimen\OneDrive\Documentos\InformacionTrabajo\solicitudes-compras-laravel`
- **Repo GitHub:** https://github.com/lysusco/solicitudes-compras-laravel
- **Complejidad objetivo:** básica–intermedia (CRUD con estados y vistas Blade, sin sobre-ingeniería).

## Stack
- PHP 8.2 (XAMPP)
- Laravel 11 (estructura scaffolding; sin Composer instalado en el entorno)
- Blade + Tailwind CSS (CDN)
- MySQL (MariaDB 10.4, vía XAMPP) — base de datos `solicitudes_compras`
- Puerto del servidor: **8000** (php artisan serve)

## Estructura de archivos
```
solicitudes-compras-laravel/
├── composer.json
├── artisan (placeholder)
├── .env.example
├── routes/web.php
├── app/Http/Controllers/SolicitudController.php
├── app/Models/Solicitud.php
├── resources/views/solicitudes/index.blade.php
├── database/migrations/2024_01_01_000000_create_solicitudes_table.php
└── README.md
```

## Rutas disponibles
- `GET  /` — redirige a solicitudes.index
- `GET  /solicitudes` — listar solicitudes (paginado)
- `GET  /solicitudes/create` — (reservado) formulario de creación
- `POST /solicitudes` — crear solicitud
- `POST /solicitudes/{solicitud}/aprobar` — aprobar
- `POST /solicitudes/{solicitud}/rechazar` — rechazar

## Estados de una solicitud
- `pendiente` → `aprobada` / `rechazada` → `finalizada`

## Tablas
- `solicitudes` (id, usuario_id, estado, observacion, total, timestamps)
- `solicitud_detalles` (id, solicitud_id, producto_id, cantidad, precio_unitario, timestamps)

## Cómo ejecutar (cuando se instale Composer)
```bash
cd C:\Users\jimen\OneDrive\Documentos\InformacionTrabajo\solicitudes-compras-laravel
copy .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
# Abre http://127.0.0.1:8000
```
MySQL debe estar corriendo (XAMPP).

## Herramientas del entorno
- PHP: `php -v` (8.2.12)
- MySQL: `C:\xampp\mysql\bin\mysql.exe`
- Composer: NO instalado (pendiente de instalar para ejecutar Laravel real).
- Git/GitHub CLI (`gh`) autenticado como `lysusco`.

## Notas importantes
- El proyecto es scaffolding: los archivos son plantillas válidas pero requieren `composer install` y migraciones para funcionar.
- Tailwind vía CDN (sin build step) para mantenerlo simple.
- Complejidad limitada: sin autenticación real, sin notificaciones por correo, sin API. Priorizar flujo de estados y vistas Blade.
- El README y el portafolio ya referencian este proyecto.

## Prompt sugerido para el nuevo chat
> "Trabajo en el proyecto Laravel de solicitudes de compra ubicado en `C:\Users\jimen\OneDrive\Documentos\InformacionTrabajo\solicitudes-compras-laravel`. Lee el archivo `CONTEXTO.md` para tener todo el contexto. Quiero [describe aquí el cambio o adición]."
