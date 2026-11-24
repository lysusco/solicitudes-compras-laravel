<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Nueva Solicitud de Compra</title>
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
      <h1 style="font-size:1.5rem;font-weight:700;color:#312e81;">Nueva Solicitud de Compra</h1>
      <a href="{{ route('solicitudes.index') }}" style="color:#6366f1;font-weight:500;font-size:14px;">Volver al listado</a>
    </div>
  </header>

  <main class="max-w-4xl mx-auto py-8 px-4">
    @if ($errors->any())
      <div class="mb-4 p-3 rounded-lg" style="background:#fee2e2;color:#991b1b;">
        <ul class="list-disc pl-5 space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('solicitudes.store') }}" style="background:#ffffff;border-radius:12px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,.06);">
      @csrf

      <div class="mb-6">
        <label style="display:block;font-weight:600;color:#312e81;margin-bottom:6px;font-size:14px;">Observación</label>
        <textarea name="observacion" rows="3" style="width:100%;border:1px solid #c7d2fe;border-radius:8px;padding:10px 12px;font-size:14px;color:#334155;" placeholder="Notas opcionales...">{{ old('observacion') }}</textarea>
      </div>

      <div class="mb-6">
        <div class="flex items-center justify-between mb-3">
          <h2 style="font-weight:600;color:#312e81;font-size:16px;">Items</h2>
          <button type="button" id="addItem" style="background:#6366f1;color:#fff;padding:6px 12px;border-radius:8px;font-size:13px;font-weight:600;border:none;cursor:pointer;">Agregar item</button>
        </div>
        <div style="overflow-x:auto;">
          <table style="width:100%;font-size:13px;border-collapse:collapse;">
            <thead>
              <tr style="color:#6366f1;text-align:left;border-bottom:1px solid #e0e7ff;">
                <th style="padding:8px 4px;">Producto</th>
                <th style="padding:8px 4px;">Cantidad</th>
                <th style="padding:8px 4px;">Precio unitario</th>
                <th style="padding:8px 4px;"></th>
              </tr>
            </thead>
            <tbody id="itemsBody"></tbody>
          </table>
        </div>
      </div>

      <button type="submit" style="width:100%;background:#6366f1;color:#fff;padding:12px;border-radius:8px;font-weight:600;font-size:15px;border:none;cursor:pointer;" class="hover:opacity-90">Guardar solicitud</button>
    </form>
  </main>

  <script>
    let idx = 0;
    const body = document.getElementById('itemsBody');
    function addRow() {
      const tr = document.createElement('tr');
      tr.style.borderBottom = '1px solid #f1f5f9';
      tr.innerHTML = `
        <td style="padding:8px 4px;"><input name="items[${idx}][producto_nombre]" required style="width:100%;border:1px solid #c7d2fe;border-radius:8px;padding:8px 10px;font-size:13px;color:#334155;"></td>
        <td style="padding:8px 4px;"><input type="number" name="items[${idx}][cantidad]" min="1" required style="width:100%;border:1px solid #c7d2fe;border-radius:8px;padding:8px 10px;font-size:13px;color:#334155;"></td>
        <td style="padding:8px 4px;"><input type="number" step="0.01" name="items[${idx}][precio_unitario]" min="0" required style="width:100%;border:1px solid #c7d2fe;border-radius:8px;padding:8px 10px;font-size:13px;color:#334155;"></td>
        <td style="padding:8px 4px;"><button type="button" class="removeItem" style="color:#e11d48;background:none;border:none;cursor:pointer;font-size:13px;">Eliminar</button></td>
      `;
      body.appendChild(tr);
      idx++;
    }
    document.getElementById('addItem').addEventListener('click', addRow);
    body.addEventListener('click', (e) => {
      if (e.target.classList.contains('removeItem')) {
        e.target.closest('tr').remove();
      }
    });
    addRow();
  </script>
</body>
</html>
