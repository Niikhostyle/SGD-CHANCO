@extends('adminlte::page')

@section('title', 'Solicitud #'.$solicitud['id'])

@section('content_header')
    <h1 class="mb-0">{{ $solicitud['tipo_documento']['nombre'] ?? $solicitud['tipo_solicitud'] }}</h1>
    <p class="text-muted mb-0">Solicitud #{{ $solicitud['id'] }}</p>
@stop

@section('content')
@php
    $estado = $solicitud['estado'] ?? '';
    $usaFlujo = !empty($solicitud['usa_flujo_buzones']);
    $paso = $solicitud['paso_actual_detalle'] ?? null;
    $accionesPaso = is_array($paso) ? ($paso['acciones'] ?? []) : [];
    if (is_string($accionesPaso)) { $accionesPaso = json_decode($accionesPaso, true) ?: []; }
    $puede = !empty($solicitud['puede_actuar']);
    $pasos = $solicitud['pasos'] ?? [];
    $bitacora = $solicitud['bitacora'] ?? [];
    $buzonNom = $solicitud['buzon_destino']['nombre'] ?? (is_array($paso) ? ($paso['nombre_buzon'] ?? '—') : '—');
    $labelsEstado = [
        'pendiente' => 'En trámite',
        'completada' => 'Completada',
        'rechazada' => 'Rechazada',
        'pendiente_directivo' => 'En trámite',
        'pendiente_rrhh' => 'En trámite',
        'pendiente_alcalde' => 'En trámite',
        'por_recibir' => 'Por recibir',
        'visado' => 'Visado',
        'firmado' => 'Firmado',
        'derivado' => 'Derivado',
        'rechazado' => 'Rechazado',
    ];
    $fmt = function ($d) {
        if (!$d) return '—';
        $t = strtotime($d);
        return $t ? date('d-m-Y', $t) : $d;
    };
@endphp

<div class="card">
    <div class="card-header">
        <span class="badge badge-{{ $estado === 'completada' ? 'success' : ($estado === 'rechazada' ? 'danger' : 'warning') }}">{{ $labelsEstado[$estado] ?? $estado }}</span>
        @if(!empty($solicitud['sgd']['carpeta']))
            <span class="badge badge-info">{{ $solicitud['sgd']['carpeta'] }}</span>
        @endif
        <a class="btn btn-sm btn-outline-dark float-right" href="{{ route('solicitudes.pdf', $solicitud['id']) }}" target="_blank">Ver PDF</a>
    </div>
    <div class="card-body">
        <p class="mb-1"><b>Quién:</b> {{ trim(($solicitud['usuario']['nombres'] ?? '') . ' ' . ($solicitud['usuario']['primer_apellido'] ?? '')) }}</p>
        <p class="mb-1"><b>Período:</b> {{ $fmt($solicitud['fecha_inicio']) }} → {{ $fmt($solicitud['fecha_termino']) }} ({{ $solicitud['total_dias'] }} días)</p>
        <p class="mb-3"><b>Motivo:</b> {{ $solicitud['motivo'] ?? '—' }}</p>

        @if(!empty($solicitud['saldo']) && (!empty($solicitud['puede_ver_saldos']) || !empty($solicitud['es_solicitante'])))
            <h5 class="mt-2">Días del funcionario</h5>
            @include('solicitudes._saldos', ['saldo' => $solicitud['saldo']])
        @endif

        @php
            $dec = $solicitud['alcalde_decision'] ?? null;
        @endphp
        <div class="border rounded p-3 mb-3 {{ $dec === 'autorizado' ? 'border-success' : ($dec === 'denegado' ? 'border-danger' : 'border-secondary') }}">
            <h5 class="mb-2">Uso exclusivo del alcalde</h5>
            <p class="mb-1"><b>Autorizado:</b> {{ $dec === 'autorizado' ? 'X' : '______' }}
                &nbsp;&nbsp;&nbsp;<b>Denegado:</b> {{ $dec === 'denegado' ? 'X' : '______' }}</p>
            <p class="mb-2"><b>Observaciones:</b> {{ $solicitud['alcalde_observaciones'] ?: '________________' }}</p>
            <small class="text-muted">En el buzón del alcalde: <b>Firmar</b> autoriza el permiso; <b>Rechazar</b> lo deniega. El comentario del buzón queda como observación. La firma electrónica del alcalde es la autorización oficial (el PDF no se reescribe después de firmado).</small>
        </div>

        @if(!empty($solicitud['documento_cuerpo_html']))
            <div class="border rounded p-3 bg-light mb-3">{!! $solicitud['documento_cuerpo_html'] !!}</div>
        @endif

        @if(!empty($solicitud['sgd']['id_documento']))
            <div class="alert alert-success">
                <p class="mb-2"><b>Cómo tramitarlo</b> — ya está en el buzón <b>{{ $solicitud['sgd']['nombre_buzon'] ?? $buzonNom }}</b>.</p>
                <ol class="mb-3 pl-3">
                    <li>Ábralo en el buzón.</li>
                    <li>Recíbalo (pasa a Recibidos).</li>
                    <li>Personal visa (quedan las iniciales en el PDF, p. ej. ABC/nff) y se genera la firma del funcionario.</li>
                    <li>Deriva al director; el director firma y deriva al alcalde.</li>
                </ol>
                @if(!empty($solicitud['sgd']['id_buzon']))
                    <a class="btn btn-primary" href="{{ url('buzonesCarpetas/'.$solicitud['sgd']['id_buzon']) }}">Abrir el buzón</a>
                @endif
            </div>
        @endif

        @if($usaFlujo && !empty($pasos))
            <h5>Recorrido</h5>
            <ol class="pl-3">
            @foreach($pasos as $p)
                <li class="{{ ($p['estado'] ?? '') === 'pendiente' ? 'font-weight-bold' : 'text-muted' }}">
                    {{ $p['nombre_buzon'] }}
                    — {{ $labelsEstado[$p['estado']] ?? $p['estado'] }}
                </li>
            @endforeach
            </ol>
        @endif

        @if(!empty($bitacora))
            <h5>Historial</h5>
            <ul class="list-unstyled mb-0">
            @foreach($bitacora as $b)
                <li class="mb-1">
                    <small class="text-muted">{{ $fmt($b['created_at'] ?? '') }}</small>
                    — {{ $b['accion'] }}
                    {{ $b['usuario']['nombres'] ?? '' }}
                    {{ $b['comentario'] ?? '' }}
                </li>
            @endforeach
            </ul>
        @endif
    </div>
    <div class="card-footer">
        @if($usaFlujo && $estado === 'pendiente' && $puede && empty($solicitud['sgd']['id_documento']))
            <form method="post" action="{{ route('solicitudes.accion', [$solicitud['id'], 'firmar']) }}">@csrf
                <input type="text" name="observaciones" class="form-control mb-2" placeholder="Observaciones (opcional)">
                @if(in_array('visar', $accionesPaso, true) || empty($accionesPaso))
                    <button class="btn btn-warning" formaction="{{ route('solicitudes.accion', [$solicitud['id'], 'visar']) }}">Visar y derivar</button>
                @endif
                @if(in_array('firmar', $accionesPaso, true) || empty($accionesPaso))
                    <button class="btn btn-success">Firmar y derivar</button>
                @endif
                <button class="btn btn-danger" formaction="{{ route('solicitudes.accion', [$solicitud['id'], 'rechazar']) }}">Rechazar</button>
            </form>
        @elseif(!empty($solicitud['sgd']['id_documento']) && $estado === 'pendiente')
            <p class="text-muted mb-0">Las acciones (recibir, visar, firmar, derivar) se hacen en el buzón, no aquí.</p>
        @endif
        <a href="{{ route('solicitudes.index') }}" class="btn btn-default mt-2">Volver</a>
        @if(in_array($estado, ['pendiente_directivo', 'pendiente'], true))
            <form class="d-inline float-right mt-2" method="post" action="{{ route('solicitudes.destroy', $solicitud['id']) }}" onsubmit="return confirm('¿Eliminar esta solicitud?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">Eliminar</button>
            </form>
        @endif
    </div>
</div>
@stop
