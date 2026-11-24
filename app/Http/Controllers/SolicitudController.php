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
