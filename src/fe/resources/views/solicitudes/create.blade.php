@extends('adminlte::page')

@section('title', 'Nueva solicitud')

@section('content_header')
    <h1>Nueva solicitud</h1>
@stop

@section('content')
<div class="card card-primary">
    <form method="post" action="{{ route('solicitudes.store') }}">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label>Tipo</label>
                <select name="tipo_solicitud" class="form-control" required>
                    @foreach($tipos as $k => $label)
                        <option value="{{ $k }}" @if(old('tipo_solicitud')===$k) selected @endif>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Fecha inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Fecha término</label>
                    <input type="date" name="fecha_termino" class="form-control" value="{{ old('fecha_termino') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Directivo asignado</label>
                    <select name="directivo_asignado_id" class="form-control">
                        <option value="">— Automático / sin asignar —</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u['id'] }}" @if(old('directivo_asignado_id')==$u['id']) selected @endif>
                                {{ $u['nombres'] }} {{ $u['primer_apellido'] }} ({{ $u['email'] }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Motivo</label>
                <input type="text" name="motivo" class="form-control" value="{{ old('motivo') }}">
            </div>
            <div class="form-group">
                <label>Explicación / mensaje</label>
                <textarea name="explicacion" class="form-control" rows="3">{{ old('explicacion') }}</textarea>
            </div>
            <div class="form-group">
                <label>Mensaje para directivo</label>
                <textarea name="mensaje_para_directivo" class="form-control" rows="2">{{ old('mensaje_para_directivo') }}</textarea>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Viáticos destino</label>
                    <input type="text" name="viaticos_destino" class="form-control" value="{{ old('viaticos_destino') }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Hora inicio</label>
                    <input type="time" name="viaticos_hora_inicio" class="form-control" value="{{ old('viaticos_hora_inicio') }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Hora término</label>
                    <input type="time" name="viaticos_hora_termino" class="form-control" value="{{ old('viaticos_hora_termino') }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Licencia folio</label>
                    <input type="text" name="licencia_folio" class="form-control" value="{{ old('licencia_folio') }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Licencia tipo</label>
                    <input type="text" name="licencia_tipo" class="form-control" value="{{ old('licencia_tipo') }}">
                </div>
                <div class="form-group col-md-4">
                    <label>Licencia emisor</label>
                    <input type="text" name="licencia_emisor" class="form-control" value="{{ old('licencia_emisor') }}">
                </div>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" name="usar_firmagob" id="usar_firmagob" value="1" checked>
                <label class="form-check-label" for="usar_firmagob">Firmar con FirmaGob al crear</label>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-primary" type="submit">Crear y enviar</button>
            <a href="{{ route('solicitudes.index') }}" class="btn btn-default">Cancelar</a>
        </div>
    </form>
</div>
@stop
