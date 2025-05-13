<?php
return [
    //N° registros por tabla (buzon)
    'ndocs_perpage' => (int) env('NDOCS_PERPAGE', 25),
    //habilitar visado masivo
    'visarmasivo_enabled' => (boolean) env('VISARMASIVO', false),
    //habilitar filtro por defecto pendientes
    'recibidos_solo_pendientes'=>(boolean) env('FILTRO_PENDINTES', false),

];