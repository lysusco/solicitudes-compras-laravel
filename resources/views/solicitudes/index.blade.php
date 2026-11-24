<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Solicitudes de Compra</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-5xl mx-auto py-8 px-4">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Solicitudes de Compra</h1>
      <a href="{{ route('solicitudes.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Nueva solicitud</a>
    </div>

    @if (session('status'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left">ID</th>
            <th class="px-4 py-3 text-left">Usuario</th>
            <th class="px-4 py-3 text-left">Estado</th>
            <th class="px-4 py-3 text-left">Total</th>
            <th class="px-4 py-3 text-left">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($solicitudes as $s)
            <tr class="border-t">
              <td class="px-4 py-3">#{{ $s->id }}</td>
              <td class="px-4 py-3">{{ $s->usuario->name ?? '—' }}</td>
              <td class="px-4 py-3">
                <span class="px-2 py-1 rounded text-xs
                  @if($s->estado === 'pendiente') bg-yellow-100 text-yellow-800
                  @elseif($s->estado === 'aprobada') bg-blue-100 text-blue-800
                  @elseif($s->estado === 'rechazada') bg-red-100 text-red-800
                  @else bg-green-100 text-green-800 @endif">
                  {{ ucfirst($s->estado) }}
                </span>
              </td>
              <td class="px-4 py-3">${{ number_format($s->total, 0, ',', '.') }}</td>
              <td class="px-4 py-3 space-x-2">
                <a href="{{ route('solicitudes.show', $s) }}" class="text-blue-600 hover:underline">Ver</a>
                @if ($s->estado === 'pendiente')
                  <form method="POST" action="{{ route('solicitudes.aprobar', $s) }}" class="inline">
                    @csrf
                    <button class="text-green-600 hover:underline">Aprobar</button>
                  </form>
                  <form method="POST" action="{{ route('solicitudes.rechazar', $s) }}" class="inline">
                    @csrf
                    <button class="text-red-600 hover:underline">Rechazar</button>
                  </form>
                @elseif ($s->estado === 'aprobada')
                  <form method="POST" action="{{ route('solicitudes.finalizar', $s) }}" class="inline">
                    @csrf
                    <button class="text-blue-600 hover:underline">Finalizar</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Sin solicitudes.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $solicitudes->links() }}</div>
  </div>
</body>
</html>
