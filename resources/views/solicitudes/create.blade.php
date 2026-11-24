<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Nueva Solicitud de Compra</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto py-8 px-4">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold">Nueva Solicitud de Compra</h1>
      <a href="{{ route('solicitudes.index') }}" class="text-blue-600 hover:underline">Volver al listado</a>
    </div>

    @if ($errors->any())
      <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
        <ul class="list-disc pl-5">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('solicitudes.store') }}" class="bg-white shadow rounded-lg p-6 space-y-6">
      @csrf

      <div>
        <label class="block text-sm font-medium mb-1">Observación</label>
        <textarea name="observacion" rows="3" class="w-full border rounded px-3 py-2">{{ old('observacion') }}</textarea>
      </div>

      <div>
        <div class="flex items-center justify-between mb-2">
          <h2 class="text-lg font-semibold">Items</h2>
          <button type="button" id="addItem" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Agregar item</button>
        </div>
        <table class="w-full text-sm border" id="itemsTable">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-3 py-2 text-left">Producto</th>
              <th class="px-3 py-2 text-left">Cantidad</th>
              <th class="px-3 py-2 text-left">Precio unitario</th>
              <th class="px-3 py-2"></th>
            </tr>
          </thead>
          <tbody id="itemsBody"></tbody>
        </table>
      </div>

      <div class="flex justify-end">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Guardar solicitud</button>
      </div>
    </form>
  </div>

  <script>
    let idx = 0;
    const body = document.getElementById('itemsBody');
    function addRow() {
      const tr = document.createElement('tr');
      tr.className = 'border-t';
      tr.innerHTML = `
        <td class="px-3 py-2"><input name="items[${idx}][producto_nombre]" required class="w-full border rounded px-2 py-1"></td>
        <td class="px-3 py-2"><input type="number" name="items[${idx}][cantidad]" min="1" required class="w-full border rounded px-2 py-1"></td>
        <td class="px-3 py-2"><input type="number" step="0.01" name="items[${idx}][precio_unitario]" min="0" required class="w-full border rounded px-2 py-1"></td>
        <td class="px-3 py-2"><button type="button" class="text-red-600 removeItem">Eliminar</button></td>
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
