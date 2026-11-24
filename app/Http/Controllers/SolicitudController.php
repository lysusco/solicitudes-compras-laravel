<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
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
            'items' => 'required|array',
            'items.*.producto_id' => 'required|integer',
            'items.*.cantidad' => 'required|integer|min:1',
        ]);

        $solicitud = Solicitud::create([
            'usuario_id' => auth()->id(),
            'estado' => Solicitud::ESTADO_PENDIENTE,
            'observacion' => $data['observacion'] ?? null,
            'total' => 0,
        ]);

        return redirect()->route('solicitudes.index')
            ->with('status', 'Solicitud creada correctamente.');
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
}
