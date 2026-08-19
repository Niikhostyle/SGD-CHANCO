@extends('adminlte::page')

@section('title', 'Solicitudes')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">Mis solicitudes</h1>
            <p class="text-muted mb-0">Pida días, permisos o viáticos. El trámite sigue en el buzón, como un documento normal.</p>
        </div>
        <a href="{{ route('solicitudes.create') }}" class="btn btn-success btn-lg"><i class="fas fa-plus"></i> Nueva solicitud</a>
    </div>
@stop

@section('content')
@php
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
    ];
    $fmt = function ($d) {
        if (!$d) return '—';
        $t = strtotime($d);
        return $t ? date('d-m-Y', $t) : $d;
    };
@endphp

@if(!empty($dashboard['saldo']))
    @include('solicitudes._saldos', ['saldo' => $dashboard['saldo']])
@endif

<div class="card">
    <div class="card-header">
        <form class="form-inline" method="get">
            <select name="estado" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                @foreach(['pendiente' => 'En trámite', 'completada' => 'Completada', 'rechazada' => 'Rechazada'] as $e => $lab)
                    <option value="{{ $e }}" @if(request('estado')===$e) selected @endif>{{ $lab }}</option>
                @endforeach
            </select>
            <select name="tipo" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">Todos los tipos</option>
                @foreach(($tiposFiltro ?? []) as $t)
                    <option value="{{ $t['tipo_solicitud'] }}" @if(request('tipo')===$t['tipo_solicitud']) selected @endif>{{ $t['nombre'] ?? $t['tipo_solicitud'] }}</option>
                @endforeach
            </select>
            @if(request('bandeja'))
                <input type="hidden" name="bandeja" value="1">
                <span class="badge badge-warning ml-2">Solo las de mis buzones</span>
                <a href="{{ route('solicitudes.index') }}" class="btn btn-sm btn-default ml-2">Ver todas</a>
            @endif
            <div class="ml-auto">
                @if(!empty($esAdmin))
                    <a href="{{ route('solicitudes.tipos') }}" class="btn btn-sm btn-outline-secondary">Plantillas</a>
                    <a href="{{ route('solicitudes.admin') }}" class="btn btn-sm btn-outline-secondary">Admin</a>
                @endif
                @if(!empty($puedeSaldos))
                    <a href="{{ route('solicitudes.rrhh') }}" class="btn btn-sm btn-outline-secondary">Saldos</a>
                @endif
            </div>
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Qué pidió</th>
                    <th>Quién</th>
                    <th>Buzón</th>
                    <th>Período</th>
                    <th>Días</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($solicitudes as $s)
                <tr>
                    <td>{{ $s['id'] }}</td>
                    <td>{{ $s['tipo_documento']['nombre'] ?? $s['tipo_solicitud'] }}</td>
                    <td>{{ trim(($s['usuario']['nombres'] ?? '') . ' ' . ($s['usuario']['primer_apellido'] ?? '')) }}</td>
                    <td>{{ $s['buzon_destino']['nombre'] ?? '—' }}</td>
                    <td>{{ $fmt($s['fecha_inicio']) }} → {{ $fmt($s['fecha_termino']) }}</td>
                    <td>{{ $s['total_dias'] }}</td>
                    <td>
                        <span class="badge badge-{{ ($s['estado'] ?? '') === 'completada' ? 'success' : (($s['estado'] ?? '') === 'rechazada' ? 'danger' : 'warning') }}">
                            {{ $labelsEstado[$s['estado']] ?? $s['estado'] }}
                        </span>
                    </td>
                    <td><a class="btn btn-sm btn-info" href="{{ route('solicitudes.show', $s['id']) }}">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Aún no hay solicitudes. Use <b>Nueva solicitud</b> para crear la primera.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
