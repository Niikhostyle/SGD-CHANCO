<?php
use App\Http\Controllers\PLCController;

Route::middleware(['auth:sanctum', 'verified'])->get('descargar_documento_plc',[PLCController::class,'getDoc'])->name('buscador.descargar_plc');
