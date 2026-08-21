@extends('adminlte::page')

@section('title', 'RRHH Saldos')

@section('content_header')
    <h1 class="mb-0">Saldos de días</h1>
    <p class="text-muted mb-0">Cargue o descuente días administrativos, feriados o compensatorios.</p>
@stop

@section('content')
@if(!empty($error))
    <div class="alert alert-warning">{{ $error }}</div>
@endif

<div class="card card-outline card-primary mb-3">
    <div class="card-header"><strong>Registrar movimiento ({{ $anio }})</strong></div>
    <div class="card-body">
        <form method="post" action="{{ route('solicitudes.rrhh.movimiento') }}" class="form-row">@csrf
            <input type="hidden" name="anio" value="{{ $anio }}">
            <div class="form-group col-md-3">
                <label>Usuario</label>
                <select name="user_id" class="form-control select2" required>
                    @foreach($usuarios as $u)
                        <option value="{{ $u['id'] }}">{{ $u['nombres'] }} {{ $u['primer_apellido'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2">
                <label>Tipo</label>
                <select name="tipo" class="form-control">
                    <option value="carga">Carga</option>
                    <option value="descuento">Descuento</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label>Permiso</label>
                <select name="permiso_tipo" class="form-control">
                    <option value="dias_administrativos">Días administrativos</option>
                    <option value="feriados_legales">Feriados legales</option>
                    <option value="dias_compensatorios">Compensatorios</option>
                </select>
            </div>
            <div class="form-group col-md-2">
                <label>Días</label>
                <input type="number" min="0.5" step="0.5" name="dias" class="form-control" value="1" required>
                <small class="text-muted">Puede usar medios días (0,5).</small>
            </div>
            <div class="form-group col-md-2">
                <label>&nbsp;</label>
                <button class="btn btn-primary btn-block">Guardar</button>
            </div>
            <div class="form-group col-md-12">
                <input type="text" name="motivo" class="form-control" placeholder="Motivo">
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="get" class="form-inline">
            <label class="mr-2">Año</label>
            <input type="number" name="anio" class="form-control mr-2" value="{{ $anio }}">
            <button class="btn btn-default">Filtrar</button>
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th class="text-center">Administrativos</th>
                    <th class="text-center">Feriados</th>
                    <th class="text-center">Compensatorios</th>
                </tr>
            </thead>
            <tbody>
            @php
                $fmtDias = function ($v) {
                    $n = round((float) $v, 1);
                    if (abs($n - (int) $n) < 0.05) {
                        return (string) (int) $n;
                    }
                    return rtrim(rtrim(number_format($n, 1, ',', ''), '0'), ',');
                };
            @endphp
            @forelse($saldos as $s)
                <tr>
                    <td class="align-middle">
                        <strong>{{ ($s['user']['nombres'] ?? '') }} {{ ($s['user']['primer_apellido'] ?? '') }}</strong>
                    </td>
                    <td class="text-center text-primary" style="font-size:2rem;font-weight:800;line-height:1.1">{{ $fmtDias($s['dias_administrativos'] ?? 0) }}</td>
                    <td class="text-center text-info" style="font-size:2rem;font-weight:800;line-height:1.1">{{ $fmtDias($s['feriados_legales'] ?? 0) }}</td>
                    <td class="text-center text-success" style="font-size:2rem;font-weight:800;line-height:1.1">{{ $fmtDias($s['dias_compensatorios'] ?? 0) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted">Sin saldos</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop

@section('js')
<script>
$(function () { $('.select2').select2({ width: '100%' }); });
</script>
@stop
