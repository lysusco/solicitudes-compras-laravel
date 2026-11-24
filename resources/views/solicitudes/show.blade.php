<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detalle Solicitud #{{ $solicitud->id }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
    html { scroll-padding-top: 4rem; }
    :focus-visible { outline: 2px solid #6366f1; outline-offset: 2px; border-radius: 4px; }
  </style>
</head>
<body style="background:#f5f3ff;min-height:100vh;">
  <header style="background:#ffffffcc;backdrop-filter:blur(8px);border-bottom:1px solid #e0e7ff;">
    <div class="max-w-4xl mx-auto py-4 px-4 flex items-center justify-between">
      <h1 style="font-size:1.5rem;font-weight:700;color:#312e81;">Solicitud #{{ $solicitud->id }}</h1>
      <a href="{{ route('solicitudes.index') }}" style="color:#6366f1;font-weight:500;font-size:14px;">Volver al listado</a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto py-8 px-4 space-y-4">
    @if (session('status'))
      <div class="p-3 rounded-lg" style="background:#dcfce7;color:#166534;">{{ session('status') }}</div>
    @endif

    <div style="background:#ffffff;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
      <div class="space-y-2 text-sm">
        <p><span style="font-weight:600;color:#312e81;">Usuario:</span> <span style="color:#334155;">{{ $solicitud->usuario->name ?? '—' }}</span></p>
        <p class="flex items-center gap-2">
          <span style="font-weight:600;color:#312e81;">Estado:</span>
          <span class="px-2 py-1 rounded-full text-xs font-medium
            @if($solicitud->estado === 'pendiente') bg-yellow-100 text-yellow-800
            @elseif($solicitud->estado === 'aprobada') bg-blue-100 text-blue-800
            @elseif($solicitud->estado === 'rechazada') bg-red-100 text-red-800
            @else bg-green-100 text-green-800 @endif">
            {{ ucfirst($solicitud->estado) }}
          </span>
        </p>
        <p><span style="font-weight:600;color:#312e81;">Observación:</span> <span style="color:#334155;">{{ $solicitud->observacion ?? '—' }}</span></p>
        <p><span style="font-weight:600;color:#312e81;">Total:</span> <span style="color:#334155;font-weight:600;">${{ number_format($solicitud->total, 0, ',', '.') }}</span></p>
      </div>
    </div>

    <div style="background:#ffffff;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
      <h2 style="font-weight:600;color:#312e81;margin-bottom:12px;font-size:16px;">Items</h2>
      <div style="overflow-x:auto;">
        <table style="width:100%;font-size:13px;border-collapse:collapse;">
          <thead>
            <tr style="color:#6366f1;text-align:left;border-bottom:1px solid #e0e7ff;">
              <th style="padding:8px 4px;">Producto</th>
              <th style="padding:8px 4px;">Cantidad</th>
              <th style="padding:8px 4px;">Precio unitario</th>
              <th style="padding:8px 4px;">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($solicitud->detalles as $d)
              <tr style="border-bottom:1px solid #f1f5f9;color:#334155;">
                <td style="padding:8px 4px;">{{ $d->producto_nombre }}</td>
                <td style="padding:8px 4px;">{{ $d->cantidad }}</td>
                <td style="padding:8px 4px;">${{ number_format($d->precio_unitario, 0, ',', '.') }}</td>
                <td style="padding:8px 4px;font-weight:600;">${{ number_format($d->cantidad * $d->precio_unitario, 0, ',', '.') }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center py-6" style="color:#64748b;">Sin items.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="space-x-2">
      @if ($solicitud->estado === 'pendiente')
        <form method="POST" action="{{ route('solicitudes.aprobar', $solicitud) }}" class="inline">
          @csrf
          <button style="background:#10b981;color:#fff;padding:10px 16px;border-radius:8px;font-weight:600;border:none;cursor:pointer;">Aprobar</button>
        </form>
        <form method="POST" action="{{ route('solicitudes.rechazar', $solicitud) }}" class="inline">
          @csrf
          <button style="background:#e11d48;color:#fff;padding:10px 16px;border-radius:8px;font-weight:600;border:none;cursor:pointer;">Rechazar</button>
        </form>
      @elseif ($solicitud->estado === 'aprobada')
        <form method="POST" action="{{ route('solicitudes.finalizar', $solicitud) }}" class="inline">
          @csrf
          <button style="background:#6366f1;color:#fff;padding:10px 16px;border-radius:8px;font-weight:600;border:none;cursor:pointer;">Finalizar</button>
        </form>
      @endif
    </div>
  </main>
</body>
</html>
