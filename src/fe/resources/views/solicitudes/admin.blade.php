@extends('adminlte::page')

@section('title', 'Admin Solicitudes')

@section('content_header')
    <h1>Administración del módulo Solicitudes</h1>
@stop

@section('content')
@if(!empty($error))
    <div class="alert alert-danger">{{ $error }}</div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header"><strong>Asignar rol de módulo</strong></div>
            <div class="card-body">
                <form method="post" action="{{ route('solicitudes.admin.rol') }}">@csrf
                    <div class="form-group">
                        <label>Usuario</label>
                        <select name="user_id" class="form-control" required>
                            @foreach($usuarios as $u)
                                <option value="{{ $u['id'] }}">{{ $u['nombres'] }} {{ $u['primer_apellido'] }} — {{ $u['email'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rol</label>
                        <select name="rol" class="form-control">
                            @foreach(['usuario','directivo','rrhh','alcalde','admin_solicitudes'] as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Régimen</label>
                        <select name="regimen_laboral" class="form-control">
                            <option value="">—</option>
                            <option value="administrativo">administrativo</option>
                            <option value="honorarios">honorarios</option>
                            <option value="codigo_trabajo">codigo_trabajo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Departamento</label>
                        <select name="departamento_id" class="form-control">
                            <option value="">—</option>
                            @foreach($departamentos as $d)
                                <option value="{{ $d['id'] }}">{{ $d['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cargo</label>
                        <select name="cargo_id" class="form-control">
                            <option value="">—</option>
                            @foreach($cargos as $c)
                                <option value="{{ $c['id'] }}">{{ $c['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check mb-2">
                        <input type="checkbox" name="firmagob_enabled" value="1" class="form-check-input" id="fg">
                        <label for="fg" class="form-check-label">FirmaGob habilitado</label>
                    </div>
                    <button class="btn btn-primary">Guardar rol</button>
                </form>
            </div>
            <div class="card-body table-responsive p-0" style="max-height:240px;overflow:auto">
                <table class="table table-sm">
                    <thead><tr><th>Usuario</th><th>Rol</th><th>FG</th></tr></thead>
                    <tbody>
                    @foreach($roles as $r)
                        <tr>
                            <td>{{ $r['user']['email'] ?? $r['user_id'] }}</td>
                            <td>{{ $r['rol'] }}</td>
                            <td>{{ !empty($r['firmagob_enabled']) ? 'Sí' : 'No' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-outline card-secondary">
            <div class="card-header"><strong>Departamentos</strong></div>
            <div class="card-body">
                <form method="post" action="{{ route('solicitudes.admin.departamento') }}">@csrf
                    <input type="text" name="nombre" class="form-control mb-2" placeholder="Nombre dirección" required>
                    <select name="directivo_id" class="form-control mb-2">
                        <option value="">Directivo titular</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u['id'] }}">{{ $u['nombres'] }} {{ $u['primer_apellido'] }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-secondary">Crear departamento</button>
                </form>
                <ul class="mt-3">
                    @foreach($departamentos as $d)
                        <li>{{ $d['nombre'] }} — Directivo: {{ $d['directivo']['nombres'] ?? '—' }}</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="card card-outline card-info">
            <div class="card-header"><strong>Cargos</strong></div>
            <div class="card-body">
                <form method="post" action="{{ route('solicitudes.admin.cargo') }}" class="form-inline">@csrf
                    <input type="text" name="nombre" class="form-control mr-2" required>
                    <button class="btn btn-info">Agregar</button>
                </form>
                <p class="mt-2">{{ collect($cargos)->pluck('nombre')->implode(', ') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card card-outline card-success">
    <div class="card-header"><strong>Plantillas de documento</strong></div>
    <div class="card-body">
        <form method="post" action="{{ route('solicitudes.admin.tipo') }}">@csrf
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Tipo solicitud</label>
                    <select name="tipo_solicitud" class="form-control" required>
                        @foreach(['dias_administrativos','feriados_legales','dias_compensatorios','licencia_medica','viaticos'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Régimen (opcional)</label>
                    <input type="text" name="regimen_laboral" class="form-control" placeholder="administrativo">
                </div>
                <div class="form-group col-md-6">
                    <label>Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Cuerpo HTML (placeholders: {{nombre}}, {{fecha_inicio}}, {{motivo}}, …)</label>
                <textarea name="plantilla_cuerpo_html" class="form-control" rows="4" required><p>Yo, {{nombre}}, solicito {{tipo_solicitud}} desde {{fecha_inicio}} hasta {{fecha_termino}} ({{total_dias}} días). Motivo: {{motivo}}</p></textarea>
            </div>
            <button class="btn btn-success">Guardar plantilla</button>
        </form>
        <hr>
        <ul>
            @foreach($tipos as $t)
                <li><b>{{ $t['nombre'] }}</b> — {{ $t['tipo_solicitud'] }} / {{ $t['regimen_laboral'] ?? 'general' }}</li>
            @endforeach
        </ul>
    </div>
</div>
@stop
