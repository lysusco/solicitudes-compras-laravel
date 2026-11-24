# Rediseño visual índigo suave — Plan de implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Reescribir las vistas Blade con el tema índigo suave (tarjetas, encabezado fijo, badges pastel) sin tocar lógica ni dependencias.

**Architecture:** Se reescriben `index`, `create` y `show` manteniendo variables/rutas existentes. Tailwind por CDN con clases utilitarias y CSS inline para la paleta exacta. Sin layout compartido (YAGNI).

**Tech Stack:** Blade + Tailwind CSS (CDN). Sin build step. Sin dependencias nuevas.

**Spec:** `docs/superpowers/specs/2026-11-24-ui-redesign-indigo-design.md`

## Global Constraints

- Tailwind vía CDN (sin build step).
- Vistas en `resources/views/solicitudes/`.
- Variables disponibles: `$solicitudes` (paginado), `$solicitud` (con `usuario`, `detalles`).
- Rutas: `solicitudes.index`, `.create`, `.store`, `.show`, `.aprobar`, `.rechazar`, `.finalizar`.
- Paleta: fondo `#f5f3ff`, superficie `#ffffff`, texto `#312e81`/`#334155`/`#64748b`, acento `#6366f1` (hover `#4f46e5`), badges pastel.
- Radios: 12px tarjetas, 8px botones/inputs, 999px badges.
- Tipografía del sistema.

## Review Focus

1. Vistas dejan de referenciar rutas/variables existentes (rotura de flujo) → verificar que `route(...)` y `$s`/`$solicitud` se mantengan.
2. Badge de estado con color incorrecto o falta de contraste → verificar pastel por estado.
3. Formulario de creación pierde inputs `items[i][...]` (store falla) → verificar nombres exactos.
4. Encabezado fijo tapa contenido en móvil → verificar padding-top.
5. Paginación o acciones (Aprobar/Rechazar/Finalizar) desaparecen → verificar presencia.

---

### Task 1: Rediseñar index.blade.php

**Files:**
- Modify: `resources/views/solicitudes/index.blade.php`

**Interfaces:**
- Consumes: `$solicitudes` paginado; rutas `solicitudes.create`, `.show`, `.aprobar`, `.rechazar`, `.finalizar`.
- Produces: listado en tarjetas, encabezado fijo, paginación.

- [ ] **Step 1: Reescribir la vista**

Encabezado fijo (sticky) con título y botón "Nueva solicitud" índigo. Lista de tarjetas (1 columna, gap 12px). Cada tarjeta: fila superior ID+usuario (izq) y badge estado (der); fila inferior total (izq) y acciones (der): Ver siempre; Aprobar/Rechazar si pendiente; Finalizar si aprobada. Estado vacío "Sin solicitudes." Paginación centrada al pie. Paleta índigo suave.

- [ ] **Step 2: Verificar archivo existe y contiene tokens clave**

Run: `grep -q "6366f1" resources/views/solicitudes/index.blade.php && grep -q "route('solicitudes.create')" resources/views/solicitudes/index.blade.php && echo OK`
Expected: `OK`

- [ ] **Step 3: Commit**

```bash
git add resources/views/solicitudes/index.blade.php
git commit -m "feat(ui): index con tarjetas y tema indigo"
```

---

### Task 2: Rediseñar create.blade.php

**Files:**
- Modify: `resources/views/solicitudes/create.blade.php`

**Interfaces:**
- Consumes: ruta `solicitudes.store`; campos `observacion`, `items[i][producto_nombre]`, `items[i][cantidad]`, `items[i][precio_unitario]`.
- Produces: formulario estilizado con items dinámicos.

- [ ] **Step 1: Reescribir la vista**

Encabezado con título y enlace "Volver". Tarjeta contenedora con formulario: observación (textarea) e items (tabla compacta de inputs) con botón "Agregar item". Botón "Guardar solicitud" índigo. Mantener nombres de inputs exactos. Paleta índigo suave.

- [ ] **Step 2: Verificar tokens clave**

Run: `grep -q "items\[" resources/views/solicitudes/create.blade.php && grep -q "route('solicitudes.store')" resources/views/solicitudes/create.blade.php && echo OK`
Expected: `OK`

- [ ] **Step 3: Commit**

```bash
git add resources/views/solicitudes/create.blade.php
git commit -m "feat(ui): create estilizado tema indigo"
```

---

### Task 3: Rediseñar show.blade.php

**Files:**
- Modify: `resources/views/solicitudes/show.blade.php`

**Interfaces:**
- Consumes: `$solicitud` con `usuario`, `detalles`; rutas `solicitudes.index`, `.aprobar`, `.rechazar`, `.finalizar`.
- Produces: detalle estilizado.

- [ ] **Step 1: Reescribir la vista**

Encabezado con título y enlace "Volver". Tarjeta cabecera: usuario, estado (badge), observación, total. Tarjeta items: tabla compacta (producto, cantidad, precio, subtotal). Acciones según estado. Paleta índigo suave.

- [ ] **Step 2: Verificar tokens clave**

Run: `grep -q "route('solicitudes.index')" resources/views/solicitudes/show.blade.php && grep -q "solicitud->detalles" resources/views/solicitudes/show.blade.php && echo OK`
Expected: `OK`

- [ ] **Step 3: Commit**

```bash
git add resources/views/solicitudes/show.blade.php
git commit -m "feat(ui): show estilizado tema indigo"
```

---

### Task 4: Verificación final

- [ ] **Step 1: Comprobar que las tres vistas existen y contienen la paleta**

Run: `for f in index create show; do grep -q "6366f1" resources/views/solicitudes/$f.blade.php || echo "MISSING 6366f1 in $f"; done; echo "checked"`
Expected: `checked` (sin MISSING).

- [ ] **Step 2: (Opcional) revisar visualmente en navegador**

Abrir las vistas renderizadas si hay servidor disponible; si no, inspección manual del HTML.

---

## Self-Review

- Cobertura de spec: paleta (T1/T2/T3), tarjetas (T1), encabezado (T1), badges (T1/T3), formularios (T2), responsive (T1/T2/T3 implícito). Cubierto.
- Tipos consistentes: rutas y variables se mantienen.
- Review Focus cubierto por verificaciones.