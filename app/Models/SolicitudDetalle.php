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
