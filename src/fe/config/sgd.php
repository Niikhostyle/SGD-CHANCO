<?php
return [
    //API Interna
    'api_documento'=>env('API_SGD_DOCUMENTO','http://sgd_ms_documentos:3333'),
    'api_tipo_documento'=>env('API_SGD_TIPO_DOCUMENTOS','http://sgd_ms_tipos_documentos:3333'),
    'api_buzones'=>env('API_SGD_BUZONES','http://sgd_ms_buzones:3333'),
    'api_usuarios'=>env('API_SGD_USUARIOS','http://sgd_ms_usuarios:3333'),
    'api_firma'=>env('API_SGD_FIRMA','http://sgd_ms_firma:3333'),
    'api_archivos'=>env('API_SGD_ARCHIVOS','http://sgd_ms_archivos:3333'),
    'api_folios'=>env('API_SGD_FOLIOS','http://sgd_ms_folios:3333'),

    //opciones
    //N° registros por tabla (buzon)
    'ndocs_perpage' => (int) env('NDOCS_PERPAGE', 25),
    //habilitar visado masivo
    'visarmasivo_enabled' => (boolean) env('VISARMASIVO', false),
    //habilitar filtro por defecto pendientes
    'recibidos_solo_pendientes'=>(boolean) env('FILTRO_PENDINTES', false),
    //habilitar clave unica
    'claveunica_enabled'=>(boolean) env('CLAVEUNICA_ENABLED', false),
    //clave unica clientid
    'claveunica_clientid'=>env("CLAVEUNICA_CLIENT_ID","123456"),
    //clave unica secret id
    'claveunica_secretid'=>env("CLAVEUNICA_SECRET_ID","123456"),
    

];