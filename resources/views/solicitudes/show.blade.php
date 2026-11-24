<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Detalle Solicitud #{{ $solicitud->id }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Solicitud #{{ $solicitud->id }}</h1>
      <a href="{{ route('solicitudes.index') }}" class="text-blue-600 hover:underline">Volver al listado</a>
    </div>

    @if (session('status'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
    @endif

    <div class="bg-white shadow rounded-lg p-6 mb-6 space-y-2">
      <p><span class="font-semibold">Usuario:</span> {{ $solicitud->usuario->name ?? '—' }}</p>
      <p><span class="font-semibold">Estado:</span>
        <span class="px-2 py-1 rounded text-xs
          @if($solicitud->estado === 'pendiente') bg-yellow-100 text-yellow-800
          @elseif($solicitud->estado === 'aprobada') bg-blue-100 text-blue-800
          @elseif($solicitud->estado === 'rechazada') bg-red-100 text-red-800
          @else bg-green-100 text-green-800 @endif">
          {{ ucfirst($solicitud->estado) }}
        </span>
      </p>
      <p><span class="font-semibold">Observación:</span> {{ $solicitud->observacion ?? '—' }}</p>
      <p><span class="font-semibold">Total:</span> ${{ number_format($solicitud->total, 0, ',', '.') }}</p>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left">Producto</th>
            <th class="px-4 py-3 text-left">Cantidad</th>
            <th class="px-4 py-3 text-left">Precio unitario</th>
            <th class="px-4 py-3 text-left">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($solicitud->detalles as $d)
            <tr class="border-t">
              <td class="px-4 py-3">{{ $d->producto_nombre }}</td>
              <td class="px-4 py-3">{{ $d->cantidad }}</td>
              <td class="px-4 py-3">${{ number_format($d->precio_unitario, 0, ',', '.') }}</td>
              <td class="px-4 py-3">${{ number_format($d->cantidad * $d->precio_unitario, 0, ',', '.') }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Sin items.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="space-x-2">
      @if ($solicitud->estado === 'pendiente')
        <form method="POST" action="{{ route('solicitudes.aprobar', $solicitud) }}" class="inline">
          @csrf
          <button class="px-4 py-2 bg-green-600 text-white rounded">Aprobar</button>
        </form>
        <form method="POST" action="{{ route('solicitudes.rechazar', $solicitud) }}" class="inline">
          @csrf
          <button class="px-4 py-2 bg-red-600 text-white rounded">Rechazar</button>
        </form>
      @elseif ($solicitud->estado === 'aprobada')
        <form method="POST" action="{{ route('solicitudes.finalizar', $solicitud) }}" class="inline">
          @csrf
          <button class="px-4 py-2 bg-blue-600 text-white rounded">Finalizar</button>
        </form>
      @endif
    </div>
  </div>
</body>
</html>
