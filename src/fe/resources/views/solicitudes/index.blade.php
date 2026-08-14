@extends('adminlte::page')

@section('title', 'Solicitudes')

@section('content_header')
    <h1>Solicitudes municipales</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Mis solicitudes</span>
                <span class="info-box-number">{{ $dashboard['mis_solicitudes'] ?? 0 }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-user-tie"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pend. Directivo</span>
                <span class="info-box-number">{{ $dashboard['pendientes']['directivo'] ?? 0 }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pend. RRHH</span>
                <span class="info-box-number">{{ $dashboard['pendientes']['rrhh'] ?? 0 }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-landmark"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pend. Alcalde</span>
                <span class="info-box-number">{{ $dashboard['pendientes']['alcalde'] ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>

@if(!empty($dashboard['saldo']))
<div class="card card-outline card-secondary mb-3">
    <div class="card-header"><strong>Saldos {{ $dashboard['saldo']['anio'] ?? date('Y') }}</strong></div>
    <div class="card-body">
        Admin: <b>{{ $dashboard['saldo']['dias_administrativos'] ?? 0 }}</b> |
        Feriados: <b>{{ $dashboard['saldo']['feriados_legales'] ?? 0 }}</b> |
        Compensatorios: <b>{{ $dashboard['saldo']['dias_compensatorios'] ?? 0 }}</b>
        @if(!empty($dashboard['rol']['rol']))
            <span class="badge badge-info ml-2">Rol módulo: {{ $dashboard['rol']['rol'] }}</span>
        @endif
    </div>
</div>
@endif

<div class="mb-3">
    <a href="{{ route('solicitudes.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva solicitud</a>
    <a href="{{ route('solicitudes.rrhh') }}" class="btn btn-outline-secondary">RRHH / Saldos</a>
    <a href="{{ route('solicitudes.admin') }}" class="btn btn-outline-dark">Administración</a>
</div>

<div class="card">
    <div class="card-header">
        <form class="form-inline" method="get">
            <select name="estado" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">Todos los estados</option>
                @foreach(['pendiente_directivo','pendiente_rrhh','pendiente_alcalde','completada','rechazada'] as $e)
                    <option value="{{ $e }}" @if(request('estado')===$e) selected @endif>{{ $e }}</option>
                @endforeach
            </select>
            <select name="tipo" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">Todos los tipos</option>
                @foreach(['dias_administrativos','feriados_legales','dias_compensatorios','licencia_medica','viaticos'] as $t)
                    <option value="{{ $t }}" @if(request('tipo')===$t) selected @endif>{{ $t }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tipo</th>
                    <th>Solicitante</th>
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
                    <td>{{ $s['tipo_solicitud'] }}</td>
                    <td>{{ ($s['usuario']['nombres'] ?? '') . ' ' . ($s['usuario']['primer_apellido'] ?? '') }}</td>
                    <td>{{ $s['fecha_inicio'] }} → {{ $s['fecha_termino'] }}</td>
                    <td>{{ $s['total_dias'] }}</td>
                    <td><span class="badge badge-secondary">{{ $s['estado'] }}</span></td>
                    <td><a class="btn btn-sm btn-info" href="{{ route('solicitudes.show', $s['id']) }}">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">Sin solicitudes</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
