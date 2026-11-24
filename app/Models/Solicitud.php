<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    protected $fillable = ['usuario_id', 'estado', 'observacion', 'total'];

    public const ESTADO_PENDIENTE = 'pendiente';
    public const ESTADO_APROBADA = 'aprobada';
    public const ESTADO_RECHAZADA = 'rechazada';
    public const ESTADO_FINALIZADA = 'finalizada';

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(\App\Models\SolicitudDetalle::class);
    }
}
