@extends('adminlte::page')

@section('title', 'Solicitud #'.$solicitud['id'])

@section('content_header')
@php
    $estadoHdr = $solicitud['estado'] ?? '';
    $labelsHdr = [
        'pendiente' => 'En trámite', 'completada' => 'Completada', 'rechazada' => 'Rechazada',
        'pendiente_directivo' => 'En trámite', 'pendiente_rrhh' => 'En trámite', 'pendiente_alcalde' => 'En trámite',
        'por_recibir' => 'Por recibir', 'visado' => 'Visado', 'firmado' => 'Firmado', 'derivado' => 'Derivado', 'rechazado' => 'Rechazado',
    ];
    $badgeHdr = $estadoHdr === 'completada' ? 'success' : ($estadoHdr === 'rechazada' ? 'danger' : 'warning');
@endphp
<div class="d-flex flex-wrap justify-content-between align-items-start">
    <div class="mb-2">
        <h1 class="mb-1">{{ $solicitud['tipo_documento']['nombre'] ?? $solicitud['tipo_solicitud'] }}</h1>
        <p class="text-muted mb-1">Solicitud #{{ $solicitud['id'] }}</p>
        <span class="badge badge-{{ $badgeHdr }}">{{ $labelsHdr[$estadoHdr] ?? $estadoHdr }}</span>
        @if(!empty($solicitud['sgd']['carpeta']))
            <span class="badge badge-info">{{ $solicitud['sgd']['carpeta'] }}</span>
        @endif
    </div>
    <div class="btn-group mb-2">
        @if(!empty($solicitud['puede_abrir_buzon']))
            <a class="btn btn-primary" href="{{ url('buzonesCarpetas/'.$solicitud['sgd']['id_buzon']) }}">
                <i class="fas fa-inbox"></i> Abrir buzón
            </a>
        @endif
        <a class="btn btn-outline-secondary" href="{{ route('solicitudes.pdf', $solicitud['id']) }}" target="_blank">
            <i class="fas fa-file-pdf"></i> Ver PDF
        </a>
        <a class="btn btn-default" href="{{ route('solicitudes.index') }}">Volver</a>
    </div>
</div>
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
    $nombreSol = trim(($solicitud['usuario']['nombres'] ?? '') . ' ' . ($solicitud['usuario']['primer_apellido'] ?? '') . ' ' . ($solicitud['usuario']['segundo_apellido'] ?? ''));
    $labelsEstado = [
        'pendiente' => 'En trámite', 'completada' => 'Completada', 'rechazada' => 'Rechazada',
        'pendiente_directivo' => 'En trámite', 'pendiente_rrhh' => 'En trámite', 'pendiente_alcalde' => 'En trámite',
        'por_recibir' => 'Por recibir', 'visado' => 'Visado', 'firmado' => 'Firmado', 'derivado' => 'Derivado', 'rechazado' => 'Rechazado',
    ];
    $fmt = function ($d) {
        if (!$d) return '—';
        $t = strtotime($d);
        return $t ? date('d-m-Y', $t) : $d;
    };
    $ji = strtolower((string) ($solicitud['jornada_inicio'] ?? ''));
    $jt = strtolower((string) ($solicitud['jornada_termino'] ?? ''));
    $esMedia = $ji && $ji === $jt && (float) ($solicitud['total_dias'] ?? 0) == 0.5;
@endphp

<div class="row">
    <div class="col-lg-4">
        <div class="card card-outline card-primary h-100">
            <div class="card-header"><strong>Resumen</strong></div>
            <div class="card-body p-0">
                <dl class="row mb-0 px-3 py-2">
                    <dt class="col-sm-4 text-muted">Funcionario</dt>
                    <dd class="col-sm-8 mb-2">{{ $nombreSol ?: '—' }}</dd>
                    <dt class="col-sm-4 text-muted">Período</dt>
                    <dd class="col-sm-8 mb-2">
                        {{ $fmt($solicitud['fecha_inicio']) }} → {{ $fmt($solicitud['fecha_termino']) }}
                    </dd>
                    <dt class="col-sm-4 text-muted">Días</dt>
                    <dd class="col-sm-8 mb-2">
                        {{ $solicitud['total_dias'] }}
                        @if($esMedia)
                            <span class="badge badge-info ml-1">Media jornada {{ strtoupper($ji) }}</span>
                        @elseif(!empty($solicitud['jornada_inicio']) || !empty($solicitud['jornada_termino']))
                            <small class="text-muted">({{ strtoupper($solicitud['jornada_inicio'] ?? '') }}{{ !empty($solicitud['jornada_termino']) ? '–'.strtoupper($solicitud['jornada_termino']) : '' }})</small>
                        @endif
                    </dd>
                    <dt class="col-sm-4 text-muted">Motivo</dt>
                    <dd class="col-sm-8 mb-0">{{ $solicitud['motivo'] ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        @if(!empty($solicitud['documento_cuerpo_html']))
            <div class="card card-outline card-secondary h-100">
                <div class="card-header"><strong>Documento</strong></div>
                <div class="card-body bg-white p-3">
                    <div class="sol-doc-preview mx-auto">{!! $solicitud['documento_cuerpo_html'] !!}</div>
                </div>
            </div>
        @else
            <div class="card card-outline card-secondary h-100">
                <div class="card-body d-flex align-items-center justify-content-center text-muted">
                    <p class="mb-0">Sin vista previa del documento.</p>
                </div>
            </div>
        @endif
    </div>
</div>

@if(!empty($solicitud['saldo']) && (!empty($solicitud['puede_ver_saldos']) || !empty($solicitud['es_solicitante'])))
    <div class="mt-3">
        @include('solicitudes._saldos', ['saldo' => $solicitud['saldo'], 'class' => 'mb-0', 'compact' => true])
    </div>
@endif

@if($usaFlujo && !empty($pasos))
<div class="card card-outline card-info mt-3">
    <div class="card-header"><strong>Recorrido</strong></div>
    <div class="card-body py-2">
        <div class="table-responsive">
            <table class="table table-sm table-borderless mb-0">
                <tbody>
                @foreach($pasos as $p)
                    @php $st = $p['estado'] ?? ''; @endphp
                    <tr class="{{ $st === 'pendiente' ? 'font-weight-bold' : 'text-muted' }}">
                        <td style="width:2rem">{{ $loop->iteration }}.</td>
                        <td>{{ $p['nombre_buzon'] ?? '—' }}</td>
                        <td class="text-right">
                            <span class="badge badge-{{ $st === 'firmado' || $st === 'completada' ? 'success' : ($st === 'rechazado' ? 'danger' : 'secondary') }} badge-pill">
                                {{ $labelsEstado[$st] ?? $st }}
                            </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@if(!empty($bitacora))
<div class="card mt-3">
    <div class="card-header"><strong>Historial</strong></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-sm table-striped mb-0">
            <thead>
                <tr>
                    <th style="width:7rem">Fecha</th>
                    <th>Acción</th>
                    <th>Usuario</th>
                    <th>Detalle</th>
                </tr>
            </thead>
            <tbody>
            @foreach($bitacora as $b)
                <tr>
                    <td><small>{{ $fmt($b['created_at'] ?? '') }}</small></td>
                    <td>{{ $b['accion'] }}</td>
                    <td>{{ trim(($b['usuario']['nombres'] ?? '') . ' ' . ($b['usuario']['primer_apellido'] ?? '')) }}</td>
                    <td class="text-muted">{{ $b['comentario'] ?? '' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if($usaFlujo && $estado === 'pendiente' && $puede && empty($solicitud['sgd']['id_documento']))
<div class="card card-outline card-warning mt-3">
    <div class="card-body">
        <form method="post" action="{{ route('solicitudes.accion', [$solicitud['id'], 'firmar']) }}">@csrf
            <input type="text" name="observaciones" class="form-control mb-2" placeholder="Observaciones (opcional)">
            <div class="btn-group">
                @if(in_array('visar', $accionesPaso, true) || empty($accionesPaso))
                    <button type="submit" class="btn btn-warning" formaction="{{ route('solicitudes.accion', [$solicitud['id'], 'visar']) }}">Visar y derivar</button>
                @endif
                @if(in_array('firmar', $accionesPaso, true) || empty($accionesPaso))
                    <button type="submit" class="btn btn-success">Firmar y derivar</button>
                @endif
                <button type="submit" class="btn btn-danger" formaction="{{ route('solicitudes.accion', [$solicitud['id'], 'rechazar']) }}">Rechazar</button>
            </div>
        </form>
    </div>
</div>
@endif

@if(in_array($estado, ['pendiente_directivo', 'pendiente'], true))
    <form class="mt-3 text-right" method="post" action="{{ route('solicitudes.destroy', $solicitud['id']) }}" onsubmit="return confirm('¿Eliminar esta solicitud?')">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar solicitud</button>
    </form>
@endif
@stop

@section('css')
<style>
    .sol-doc-preview {
        max-width: 794px;
        font-family: DejaVu Sans, Arial, sans-serif;
        font-size: 13px;
        line-height: 1.45;
        color: #222;
    }
    .sol-doc-preview table { max-width: 100%; }
    .sol-doc-preview img { max-width: 120px; height: auto; }
    .sol-doc-preview p[style*="margin-left"] {
        margin-left: 0 !important;
        text-align: right !important;
    }
</style>
@stop
