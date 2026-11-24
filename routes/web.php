<?php

use App\Http\Controllers\SolicitudController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('solicitudes.index');
});

Route::resource('solicitudes', SolicitudController::class);
Route::post('solicitudes/{solicitud}/aprobar', [SolicitudController::class, 'aprobar'])->name('solicitudes.aprobar');
Route::post('solicitudes/{solicitud}/rechazar', [SolicitudController::class, 'rechazar'])->name('solicitudes.rechazar');
Route::post('solicitudes/{solicitud}/finalizar', [SolicitudController::class, 'finalizar'])->name('solicitudes.finalizar');
