<?php
use App\Http\Controllers\PLCController;

Route::middleware(['auth:sanctum', 'verified'])->get('descargar_docto',[PLCController::class,'getDoc'])->name('buscador.descargar_plc');
