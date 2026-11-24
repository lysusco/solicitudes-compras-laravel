# Diseño: Flujo completo de solicitud de compra

Fecha: 2026-11-24
Proyecto: Sistema de Solicitudes y Órdenes de Compra (Laravel 11)
Autor: Luis Ernesto Corredor Jiménez (con asistencia de OpenCode)

## Intención y por qué

Cerrar el flujo principal de una solicitud de compra de punta a punta para que el proyecto deje de ser scaffolding a medias y se convierta en una pieza de portafolio funcional y demostrable: un empleado crea una solicitud con productos y cantidades, un administrador la aprueba o rechaza, y al recibir la mercancía se finaliza. Hoy el código crea la solicitud pero no guarda los items ni el total, no tiene vista de creación ni de detalle, y no permite finalizar. El objetivo es que, una vez instalado Composer y corridas las migraciones, el flujo completo funcione sin huecos.

## Objetivos

- Crear una solicitud con lista de productos (cantidad y precio unitario) y total calculado.
- Ver el detalle de una solicitud (cabecera + items).
- Aprobar / rechazar una solicitud pendiente (ya existe, se mantiene).
- Finalizar una solicitud aprobada.
- Mantener el listado paginado existente, mostrando totales y estado.

## No-objetivos (fuera de alcance)

- Autenticación real (login, roles, permisos).
- Catálogo de productos (tabla propia).
- Generación de orden de compra a proveedor.
- Actualización de inventario.
- Notificaciones por correo.
- Tests automatizados exhaustivos (solo lo mínimo para no romper).
- API REST.

## Anti-objetivos (lo que haría que esto falle aunque "funcione")

- Que `store` siga creando solicitudes sin items ni total (flujo a medias).
- Que la vista de creación referencie rutas/modelos inexistentes.
- Que `auth()->id()` rompa la ejecución en entorno demo sin login.
- Que se introduzca complejidad (Breeze, colas, eventos) que contradiga el objetivo de complejidad básica-intermedia.

## Restricciones

- Stack fijo: PHP 8.2, Laravel 11, Blade + Tailwind (CDN), MySQL/MariaDB. Sin build step.
- Sin Composer instalado en el entorno: el código debe ser válido y coherente; la ejecución real depende de instalar dependencias y migrar.
- Complejidad básica-intermedia: CRUD con estados y vistas Blade, sin sobre-ingeniería.
- Convención de nombres existente: modelo `Solicitud`, controlador `SolicitudController`, vistas en `resources/views/solicitudes/`, rutas resource + aprobar/rechazar.
- Migración ya define `solicitudes` y `solicitud_detalles` (con `producto_id` como entero libre, sin FK a productos).

## Decisiones tomadas (cómo se hace)

1. **Usuario demo fijo:** como no hay autenticación, `usuario_id` se asigna a un usuario existente o se permite nulo. Decisión: la migración permite `usuario_id` nullable y el controlador usa un usuario demo (id 1) si existe, o null. Marcado como decisión mía; el builder puede ajustar.
2. **Productos a mano:** los detalles guardan `producto_nombre` (texto) y `producto_id` se vuelve nullable (el usuario solo escribe nombre/código; no se pide un código numérico aparte). Como no hay catálogo, el usuario escribe el nombre/código del producto. Nota: esto requiere añadir la columna `producto_nombre` a `solicitud_detalles` y hacer `producto_id` nullable en la migración.
3. **Cálculo del total:** se calcula en el controlador al guardar (suma de cantidad × precio unitario) y se persiste en `solicitudes.total`.
4. **Transición a finalizada:** solo desde estado `aprobada`, mediante ruta `POST solicitudes/{solicitud}/finalizar`. Se añade método `finalizar` al controlador.
5. **Vistas nuevas:** `create.blade.php` (formulario con items dinámicos en JS vanilla) y `show.blade.php` (detalle). Se mantiene `index.blade.php`.
6. **Modelo `SolicitudDetalle`:** se crea con fillable y relación inversa a `Solicitud`.
7. **Validación:** `observacion` nullable string; `items` required array; `items.*.producto_nombre` required string; `items.*.cantidad` required integer min 1; `items.*.precio_unitario` required numeric min 0.
8. **Rutas:** se mantiene el resource y se añade `POST solicitudes/{solicitud}/finalizar` con nombre `solicitudes.finalizar`.

## Lo que queda al builder (decisiones menores)

- Estilo exacto de los formularios (Tailwind), siempre dentro del CDN ya usado.
- Manejo de items dinámicos en el formulario (JS vanilla vs. campos fijos). Se sugiere JS vanilla con botón "agregar item".
- Mensajes de error de validación (Blade `@error`).
- Si `usuario_id` es null, la vista muestra "—".
- Paginación: mantener 15 por página.

## Criterios de éxito (cómo sabremos que está bien)

- Se puede crear una solicitud con al menos un item; el total se calcula y se muestra en el listado.
- Se puede ver el detalle con cabecera e items.
- Se puede aprobar, rechazar y finalizar (finalizar solo si aprobada).
- No hay referencias a vistas/modelos/rutas inexistentes.
- El código es coherente con Laravel 11 y pasa `php -l` sobre cada archivo PHP.
- Complejidad se mantiene básica-intermedia.