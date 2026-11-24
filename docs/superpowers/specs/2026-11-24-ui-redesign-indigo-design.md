# Diseño: Rediseño visual (tema índigo suave)

Fecha: 2026-11-24
Proyecto: Sistema de Solicitudes y Órdenes de Compra (Laravel 11)
Autor: Luis Ernesto Corredor Jiménez (con asistencia de OpenCode)

## Intención y por qué

El diseño actual es funcional pero genérico (Tailwind por defecto: grises, azul/verde/rojo saturados, tabla simple). El objetivo es darle una identidad visual atractiva y cómoda para uso prolongado por un usuario común, sin añadir dependencias ni build step. Se eligió un tema claro y suave con acento índigo, tarjetas en lugar de tabla y un encabezado fijo.

## Objetivos

- Tema claro y suave, de bajo cansancio visual.
- Acento índigo (`#6366f1`) para botones, enlaces y foco.
- Listado de solicitudes en tarjetas (no tabla).
- Encabezado superior fijo con título y botón "Nueva solicitud".
- Badges de estado en colores pastel.
- Formularios con inputs redondeados y anillo de foco índigo.
- Responsive (tarjetas apiladas en móvil).
- Mantener Tailwind por CDN (sin build step).

## No-objetivos

- Modo oscuro o adaptativo al sistema.
- Dependencias nuevas (Inter, Alpine, etc.).
- Animaciones complejas.
- Rediseño de la estructura de datos o rutas.
- Cambios en la lógica del controlador.

## Anti-objetivos

- Colores saturados o de alto contraste que cansen la vista.
- Introducir un framework CSS o JS adicional.
- Romper el flujo funcional existente (crear, ver, aprobar, rechazar, finalizar).

## Restricciones

- Tailwind vía CDN (sin build step) — se usan clases utilitarias y, si hace falta, CSS inline.
- Vistas Blade existentes: `index`, `create`, `show`. Se reescriben conservando las rutas/variables.
- Complejidad básica–intermedia: sin sobre-ingeniería.

## Paleta (decisiones)

- Fondo de página: `#f5f3ff` (lavanda muy claro).
- Superficie (tarjetas/contenedores): `#ffffff` con sombra `0 1px 3px rgba(0,0,0,.06)`.
- Texto principal: `#312e81` (índigo-900) en títulos; `#334155` (slate-700) en cuerpo.
- Texto secundario: `#64748b` (slate-500).
- Acento primario: `#6366f1` (índigo-500) para botones/enlaces/foco.
- Acento hover: `#4f46e5` (índigo-600).
- Badges pastel:
  - Pendiente: `#fef9c3` / `#854d0e`
  - Aprobada: `#dbeafe` / `#1e40af`
  - Rechazada: `#fee2e2` / `#991b1b`
  - Finalizada: `#dcfce7` / `#166534`
- Bordes redondeados: `12px` tarjetas, `8px` botones/inputs, `999px` badges.

## Tipografía

- Pila del sistema: `ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif`.
- Títulos: `font-bold`, color `#312e81`.
- Cuerpo: `text-sm`/`text-base`, color `#334155`.

## Estructura de las vistas

### `index.blade.php`
- Encabezado fijo (sticky top) con título "Solicitudes de Compra" y botón "Nueva solicitud" (índigo).
- Lista de tarjetas (grid 1 columna, gap 12px). Cada tarjeta: fila superior con ID + usuario a la izquierda y badge de estado a la derecha; fila inferior con total y acciones (Ver, Aprobar/Rechazar/Finalizar según estado).
- Paginación centrada al pie.

### `create.blade.php`
- Encabezado con título y enlace "Volver".
- Tarjeta contenedora con formulario: observación (textarea) e items (tabla compacta de inputs dentro de la tarjeta) con botón "Agregar item".
- Botón "Guardar solicitud" índigo, ancho completo en móvil.

### `show.blade.php`
- Encabezado con título y enlace "Volver".
- Tarjeta de cabecera: usuario, estado (badge), observación, total.
- Tarjeta de items: tabla compacta con producto, cantidad, precio, subtotal.
- Acciones según estado (Aprobar/Rechazar/Finalizar) como botones índigo.

## Lo que queda al builder

- Ajuste fino de espaciados y tamaños.
- Estados de foco/hover exactos (Tailwind).
- Manejo de mensajes de error (`@error`) con estilo rojo suave.
- Comportamiento responsive (breakpoints Tailwind).

## Criterios de éxito

- Las tres vistas usan la paleta y componentes descritos.
- No hay dependencias nuevas ni build step.
- El flujo funcional (crear, ver, aprobar, rechazar, finalizar) sigue funcionando.
- Visualmente consistente y cómodo para uso prolongado.