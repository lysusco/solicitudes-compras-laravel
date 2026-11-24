# Flujo completo de solicitud de compra — Plan de implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Cerrar el flujo completo de una solicitud de compra (crear con items y total, ver detalle, aprobar/rechazar, finalizar) para que el proyecto deje de ser scaffolding a medias.

**Architecture:** Se mantiene el scaffolding Laravel existente. Se añade el modelo `SolicitudDetalle`, se completa `store` (items + total), se añaden `show` y `finalizar` al controlador, dos vistas Blade (`create`, `show`) y la ruta `finalizar`. La migración se ajusta para `usuario_id` nullable y `producto_nombre` + `producto_id` nullable.

**Tech Stack:** PHP 8.2, Laravel 11, Blade + Tailwind (CDN), MySQL/MariaDB. Sin build step. Sin autenticación real.

**Spec:** `docs/superpowers/specs/2026-11-24-solicitud-compra-flujo-design.md`

## Global Constraints

- PHP ^8.2, Laravel ^11.0 (composer.json).
- Sin Composer instalado en el entorno: verificar sintaxis con `php -l`; ejecución real requiere `composer install` + migraciones.
- Complejidad básica–intermedia: sin Breeze, sin colas, sin eventos, sin API.
- Convención de nombres existente: `Solicitud`, `SolicitudController`, vistas en `resources/views/solicitudes/`.
- Tailwind vía CDN (sin build step).

## Review Focus

1. `store` crea la solicitud pero olvida persistir items/total (bug actual) → se espera total > 0 y detalles guardados.
2. `finalizar` permitido sobre solicitud no aprobada → debe rechazar el cambio.
3. `producto_id` NOT NULL rompe la migración al enviar solo nombre → debe ser nullable.
4. Vistas `create`/`show` inexistentes causan 500 → deben existir y responder 200.
5. `auth()->id()` null en demo rompe la FK → `usuario_id` nullable.

---

### Task 1: Ajustar la migración

**Files:**
- Modify: `database/migrations/2024_01_01_000000_create_solicitudes_table.php`

**Interfaces:**
- Consumes: esquema existente.
- Produces: `solicitudes.usuario_id` nullable; `solicitud_detalles.producto_id` nullable; nueva columna `solicitud_detalles.producto_nombre` string.

- [ ] **Step 1: Editar la migración**

Cambiar `foreignId('usuario_id')->constrained('users')` por `foreignId('usuario_id')->nullable()->constrained('users')`.
En `solicitud_detalles`: cambiar `unsignedBigInteger('producto_id')` por `unsignedBigInteger('producto_id')->nullable()` y añadir `$table->string('producto_nombre');` después.

- [ ] **Step 2: Verificar sintaxis**

Run: `php -l database/migrations/2024_01_01_000000_create_solicitudes_table.php`
Expected: `No syntax errors detected`

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2024_01_01_000000_create_solicitudes_table.php
git commit -m "feat: migracion usuario_id nullable y producto_nombre en detalles"
```

---

### Task 2: Modelo SolicitudDetalle

**Files:**
- Create: `app/Models/SolicitudDetalle.php`

**Interfaces:**
- Consumes: tabla `solicitud_detalles`.
- Produces: `App\Models\SolicitudDetalle` con `fillable` y relación `solicitud()`.

- [ ] **Step 1: Crear el modelo**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudDetalle extends Model
{
    protected $fillable = ['solicitud_id', 'producto_id', 'producto_nombre', 'cantidad', 'precio_unitario'];

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }
}
```

- [ ] **Step 2: Verificar sintaxis**

Run: `php -l app/Models/SolicitudDetalle.php`
Expected: `No syntax errors detected`

- [ ] **Step 3: Commit**

```bash
git add app/Models/SolicitudDetalle.php
git commit -m "feat: modelo SolicitudDetalle"
```

---

### Task 3: Completar SolicitudController (store, show, finalizar)

**Files:**
- Modify: `app/Http/Controllers/SolicitudController.php`

**Interfaces:**
- Consumes: `App\Models\Solicitud`, `App\Models\SolicitudDetalle`, request `items[]`.
- Produces: métodos `store`, `show`, `finalizar` con firmas:
  - `store(Request $request): RedirectResponse`
  - `show(Solicitud $solicitud): View`
  - `finalizar(Solicitud $solicitud): RedirectResponse`
- `aprobar`/`rechazar` se mantienen.

- [ ] **Step 1: Escribir test de feature (opcional, requiere composer)**

Crear `tests/Feature/SolicitudFlowTest.php`:

```php
public function test_crear_solicitud_calcula_total()
{
    $response = $this->post(route('solicitudes.store'), [
        'items' => [['producto_nombre' => 'Lapiz', 'cantidad' => 2, 'precio_unitario' => 1000]],
    ]);
    $response->assertRedirect(route('solicitudes.index'));
    $this->assertDatabaseHas('solicitudes', ['total' => 2000]);
    $this->assertDatabaseHas('solicitud_detalles', ['producto_nombre' => 'Lapiz', 'cantidad' => 2]);
}

public function test_finalizar_solo_si_aprobada()
{
    $s = Solicitud::factory()->create(['estado' => Solicitud::ESTADO_PENDIENTE]);
    $this->post(route('solicitudes.finalizar', $s))->assertStatus(302);
    $this->assertEquals(Solicitud::ESTADO_PENDIENTE, $s->fresh()->estado);
}
```

(Requiere factory de Solicitud; si no existe, omitir o crear factory mínima. Se ejecuta con `php artisan test` tras composer install.)

- [ ] **Step 2: Implementar controller**

Reemplazar el archivo completo por:

```php
<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\SolicitudDetalle;
use Illuminate\Http\Request;

class SolicitudController extends Controller
{
    public function index()
    {
        $solicitudes = Solicitud::with('usuario')->latest()->paginate(15);
        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        return view('solicitudes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'observacion' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.producto_nombre' => 'required|string',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $solicitud = Solicitud::create([
            'usuario_id' => auth()->id(),
            'estado' => Solicitud::ESTADO_PENDIENTE,
            'observacion' => $data['observacion'] ?? null,
            'total' => 0,
        ]);

        $total = 0;
        foreach ($data['items'] as $item) {
            $subtotal = $item['cantidad'] * $item['precio_unitario'];
            $total += $subtotal;
            SolicitudDetalle::create([
                'solicitud_id' => $solicitud->id,
                'producto_nombre' => $item['producto_nombre'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
            ]);
        }
        $solicitud->update(['total' => $total]);

        return redirect()->route('solicitudes.index')
            ->with('status', 'Solicitud creada correctamente.');
    }

    public function show(Solicitud $solicitud)
    {
        $solicitud->load(['usuario', 'detalles']);
        return view('solicitudes.show', compact('solicitud'));
    }

    public function aprobar(Solicitud $solicitud)
    {
        $solicitud->update(['estado' => Solicitud::ESTADO_APROBADA]);
        return back()->with('status', 'Solicitud aprobada.');
    }

    public function rechazar(Solicitud $solicitud)
    {
        $solicitud->update(['estado' => Solicitud::ESTADO_RECHAZADA]);
        return back()->with('status', 'Solicitud rechazada.');
    }

    public function finalizar(Solicitud $solicitud)
    {
        if ($solicitud->estado !== Solicitud::ESTADO_APROBADA) {
            return back()->with('status', 'Solo se pueden finalizar solicitudes aprobadas.');
        }
        $solicitud->update(['estado' => Solicitud::ESTADO_FINALIZADA]);
        return back()->with('status', 'Solicitud finalizada.');
    }
}
```

- [ ] **Step 3: Verificar sintaxis**

Run: `php -l app/Http/Controllers/SolicitudController.php`
Expected: `No syntax errors detected`

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/SolicitudController.php
git commit -m "feat: store guarda items/total, show y finalizar"
```

---

### Task 4: Rutas (finalizar)

**Files:**
- Modify: `routes/web.php`

**Interfaces:**
- Consumes: `SolicitudController::finalizar`.
- Produces: ruta `solicitudes.finalizar`.

- [ ] **Step 1: Añadir ruta finalizar**

Después de la línea de rechazar, añadir:
```php
Route::post('solicitudes/{solicitud}/finalizar', [SolicitudController::class, 'finalizar'])->name('solicitudes.finalizar');
```

- [ ] **Step 2: Verificar sintaxis**

Run: `php -l routes/web.php`
Expected: `No syntax errors detected`

- [ ] **Step 3: Commit**

```bash
git add routes/web.php
git commit -m "feat: ruta finalizar solicitud"
```

---

### Task 5: Vista create.blade.php

**Files:**
- Create: `resources/views/solicitudes/create.blade.php`

**Interfaces:**
- Consumes: ruta `solicitudes.store`.
- Produces: formulario con items dinámicos (JS vanilla) y campo observacion.

- [ ] **Step 1: Crear vista**

Formulario POST a `route('solicitudes.store')` con @csrf. Campo observacion textarea. Tabla de items con inputs `items[i][producto_nombre]`, `items[i][cantidad]`, `items[i][precio_unitario]`. Botón "Agregar item" en JS. Botón submit. Link volver.

- [ ] **Step 2: Commit**

```bash
git add resources/views/solicitudes/create.blade.php
git commit -m "feat: vista create con items dinamicos"
```

---

### Task 6: Vista show.blade.php

**Files:**
- Create: `resources/views/solicitudes/show.blade.php`

**Interfaces:**
- Consumes: `$solicitud` con `usuario`, `detalles`.
- Produces: detalle de cabecera + tabla de items + total.

- [ ] **Step 1: Crear vista**

Mostrar ID, usuario, estado (badge), observacion, total. Tabla de detalles: producto, cantidad, precio unitario, subtotal. Botones según estado: si pendiente aprobar/rechazar; si aprobada finalizar. Link volver al listado.

- [ ] **Step 2: Commit**

```bash
git add resources/views/solicitudes/show.blade.php
git commit -m "feat: vista show detalle solicitud"
```

---

### Task 7: Ajustar index.blade.php

**Files:**
- Modify: `resources/views/solicitudes/index.blade.php`

**Interfaces:**
- Consumes: rutas `solicitudes.show`, `solicitudes.finalizar`, `solicitudes.create`.

- [ ] **Step 1: Añadir enlace "Ver" y botón "Finalizar"**

En la columna Acciones: añadir `<a href="{{ route('solicitudes.show', $s) }}">Ver</a>`. Si estado === 'aprobada', añadir form POST a `solicitudes.finalizar`. Añadir botón/link "Nueva solicitud" hacia `solicitudes.create` en el encabezado.

- [ ] **Step 2: Commit**

```bash
git add resources/views/solicitudes/index.blade.php
git commit -m "feat: index con ver, finalizar y nueva solicitud"
```

---

### Task 8: Verificación final

- [ ] **Step 1: Lint de todos los PHP**

Run: `php -l app/Models/Solicitud.php && php -l app/Models/SolicitudDetalle.php && php -l app/Http/Controllers/SolicitudController.php && php -l routes/web.php && php -l database/migrations/2024_01_01_000000_create_solicitudes_table.php`
Expected: todos `No syntax errors detected`

- [ ] **Step 2: (Opcional) ejecutar tests si composer instalado**

Run: `composer install && php artisan key:generate && php artisan migrate && php artisan test`
Expected: PASS (solo si el entorno lo permite).

---

## Self-Review (notas del autor del plan)

- Cobertura de spec: crear con items/total (T3), ver detalle (T6), aprobar/rechazar (existente), finalizar (T3/T4), migración (T1), modelo detalle (T2), vistas (T5/T6/T7). Cubierto.
- Tipos consistentes: `Solicitud`, `SolicitudDetalle`, rutas con nombres acordados.
- Review Focus cubierto por tareas correspondientes.