@extends('adminlte::page')

@section('title', 'Solicitud #'.$solicitud['id'])

@section('content_header')
    <h1>Solicitud #{{ $solicitud['id'] }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <span class="badge badge-info">{{ $solicitud['tipo_solicitud'] }}</span>
        <span class="badge badge-secondary">{{ $solicitud['estado'] }}</span>
        <a class="btn btn-sm btn-outline-dark float-right" href="{{ route('solicitudes.pdf', $solicitud['id']) }}" target="_blank">Ver PDF</a>
    </div>
    <div class="card-body">
        <p><strong>Solicitante:</strong> {{ ($solicitud['usuario']['nombres'] ?? '') }} {{ ($solicitud['usuario']['primer_apellido'] ?? '') }} ({{ $solicitud['usuario']['email'] ?? '' }})</p>
        <p><strong>Período:</strong> {{ $solicitud['fecha_inicio'] }} → {{ $solicitud['fecha_termino'] }} ({{ $solicitud['total_dias'] }} días)</p>
        <p><strong>Motivo:</strong> {{ $solicitud['motivo'] ?? '—' }}</p>
        <p><strong>Explicación:</strong> {{ $solicitud['explicacion'] ?? '—' }}</p>
        <hr>
        <div>{!! $solicitud['documento_cuerpo_html'] !!}</div>
        <hr>
        <p><strong>Directivo:</strong> {{ optional($solicitud['directivo'])['nombres'] ?? '—' }} {{ $solicitud['directivo_observaciones'] ?? '' }}</p>
        <p><strong>RRHH:</strong> {{ optional($solicitud['rrhh'])['nombres'] ?? '—' }} {{ $solicitud['rrhh_observaciones'] ?? '' }}</p>
        <p><strong>Alcalde:</strong> {{ optional($solicitud['alcalde'])['nombres'] ?? '—' }} {{ $solicitud['alcalde_observaciones'] ?? '' }}</p>
    </div>
    <div class="card-footer">
        @php $estado = $solicitud['estado']; @endphp
        @if($estado === 'pendiente_directivo')
            <form class="d-inline" method="post" action="{{ route('solicitudes.accion', [$solicitud['id'], 'aprobar-directivo']) }}">@csrf
                <input type="text" name="observaciones" class="form-control mb-2" placeholder="Observaciones (opcional)">
                <button class="btn btn-success">Aprobar Directivo + FirmaGob</button>
            </form>
            <form class="d-inline" method="post" action="{{ route('solicitudes.accion', [$solicitud['id'], 'rechazar-directivo']) }}">@csrf
                <button class="btn btn-danger">Rechazar</button>
            </form>
        @endif
        @if($estado === 'pendiente_rrhh')
            <form class="d-inline" method="post" action="{{ route('solicitudes.accion', [$solicitud['id'], 'firmar-rrhh']) }}">@csrf
                <input type="text" name="observaciones" class="form-control mb-2" placeholder="Observaciones (opcional)">
                <button class="btn btn-success">Firmar RRHH + FirmaGob</button>
            </form>
            <form class="d-inline" method="post" action="{{ route('solicitudes.accion', [$solicitud['id'], 'rechazar-rrhh']) }}">@csrf
                <button class="btn btn-danger">Rechazar</button>
            </form>
        @endif
        @if($estado === 'pendiente_alcalde')
            <form class="d-inline" method="post" action="{{ route('solicitudes.accion', [$solicitud['id'], 'firmar-alcalde']) }}">@csrf
                <input type="text" name="observaciones" class="form-control mb-2" placeholder="Observaciones (opcional)">
                <button class="btn btn-success">Firmar Alcalde + FirmaGob</button>
            </form>
            <form class="d-inline" method="post" action="{{ route('solicitudes.accion', [$solicitud['id'], 'rechazar-alcalde']) }}">@csrf
                <button class="btn btn-danger">Rechazar</button>
            </form>
        @endif
        <a href="{{ route('solicitudes.index') }}" class="btn btn-default">Volver</a>
        @if($estado === 'pendiente_directivo')
            <form class="d-inline float-right" method="post" action="{{ route('solicitudes.destroy', $solicitud['id']) }}" onsubmit="return confirm('¿Eliminar?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger">Eliminar</button>
            </form>
        @endif
    </div>
</div>
@stop
