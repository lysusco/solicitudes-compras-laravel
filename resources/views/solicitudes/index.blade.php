<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Solicitudes de Compra</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body { font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
  </style>
</head>
<body style="background:#f5f3ff;min-height:100vh;">
  <header style="position:sticky;top:0;z-index:10;background:#ffffffcc;backdrop-filter:blur(8px);border-bottom:1px solid #e0e7ff;">
    <div class="max-w-5xl mx-auto py-4 px-4 flex items-center justify-between">
      <h1 style="font-size:1.5rem;font-weight:700;color:#312e81;">Solicitudes de Compra</h1>
      <a href="{{ route('solicitudes.create') }}" style="background:#6366f1;color:#fff;padding:8px 16px;border-radius:8px;font-weight:600;font-size:14px;" class="hover:opacity-90">Nueva solicitud</a>
    </div>
  </header>

  <main class="max-w-5xl mx-auto py-8 px-4">
    @if (session('status'))
      <div class="mb-4 p-3 rounded-lg" style="background:#dcfce7;color:#166534;">{{ session('status') }}</div>
    @endif

    <div class="space-y-3">
      @forelse ($solicitudes as $s)
        <div style="background:#ffffff;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
          <div class="flex items-center justify-between gap-4">
            <div>
              <span style="font-weight:700;color:#312e81;font-size:14px;">#{{ $s->id }}</span>
              <span style="color:#64748b;font-size:13px;margin-left:8px;">{{ $s->usuario->name ?? '—' }}</span>
            </div>
            <span class="px-2 py-1 rounded-full text-xs font-medium
              @if($s->estado === 'pendiente') bg-yellow-100 text-yellow-800
              @elseif($s->estado === 'aprobada') bg-blue-100 text-blue-800
              @elseif($s->estado === 'rechazada') bg-red-100 text-red-800
              @else bg-green-100 text-green-800 @endif">
              {{ ucfirst($s->estado) }}
            </span>
          </div>
          <div class="flex items-center justify-between mt-3">
            <span style="color:#334155;font-size:14px;font-weight:600;">Total: ${{ number_format($s->total, 0, ',', '.') }}</span>
            <div class="space-x-3 text-sm">
              <a href="{{ route('solicitudes.show', $s) }}" style="color:#6366f1;font-weight:500;">Ver</a>
              @if ($s->estado === 'pendiente')
                <form method="POST" action="{{ route('solicitudes.aprobar', $s) }}" class="inline">
                  @csrf
                  <button style="color:#10b981;font-weight:500;background:none;border:none;cursor:pointer;padding:0;">Aprobar</button>
                </form>
                <form method="POST" action="{{ route('solicitudes.rechazar', $s) }}" class="inline">
                  @csrf
                  <button style="color:#e11d48;font-weight:500;background:none;border:none;cursor:pointer;padding:0;">Rechazar</button>
                </form>
              @elseif ($s->estado === 'aprobada')
                <form method="POST" action="{{ route('solicitudes.finalizar', $s) }}" class="inline">
                  @csrf
                  <button style="color:#6366f1;font-weight:500;background:none;border:none;cursor:pointer;padding:0;">Finalizar</button>
                </form>
              @endif
            </div>
          </div>
        </div>
      @empty
        <div class="text-center py-12" style="color:#64748b;">Sin solicitudes.</div>
      @endforelse
    </div>

    <div class="mt-6 flex justify-center">{{ $solicitudes->links() }}</div>
  </main>
</body>
</html>
