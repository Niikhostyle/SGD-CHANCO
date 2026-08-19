@extends('adminlte::page')

@section('title', 'Nueva solicitud')

@section('content_header')
    <h1 class="mb-0">Nueva solicitud</h1>
    <p class="text-muted mb-0">Complete los 3 pasos. El documento se envía al buzón como un oficio normal.</p>
@stop

@section('content')
@if(empty($tipos))
    <div class="alert alert-warning">Aún no hay plantillas. Un administrador debe crearlas en <a href="{{ route('solicitudes.tipos') }}">Plantillas</a>.</div>
@endif
@if(empty($buzones))
    <div class="alert alert-danger">No hay buzones. Primero asigne buzones en el SGD.</div>
@endif

<form method="post" action="{{ route('solicitudes.store') }}" id="form-solicitud">
    @csrf
    <input type="hidden" name="tipo_solicitud" id="tipo_solicitud" value="{{ old('tipo_solicitud') }}">
    <input type="hidden" name="id_buzon_destino" id="id_buzon_destino_val" value="{{ old('id_buzon_destino') }}">
    <input type="hidden" name="usar_firmagob" value="1">

    <div class="card card-outline card-primary">
        <div class="card-header"><strong>1. ¿Qué pide?</strong></div>
        <div class="card-body">
            <div class="form-group mb-0">
                <label>Tipo de solicitud</label>
                <select name="sol_tipo_documento_id" id="sol_tipo_documento_id" class="form-control" required>
                    <option value="">— Elija —</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t['id'] }}"
                            data-slug="{{ $t['tipo_solicitud'] }}"
                            data-categoria="{{ $t['categoria'] ?? 'dias' }}"
                            @if((string) old('sol_tipo_documento_id') === (string) $t['id']) selected @endif>
                            {{ $t['nombre'] }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header"><strong>2. Fechas y motivo</strong></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Desde</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Hasta</label>
                    <input type="date" name="fecha_termino" id="fecha_termino" class="form-control" value="{{ old('fecha_termino') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Días</label>
                    <input type="text" id="total_dias_vista" class="form-control" readonly placeholder="—" tabindex="-1">
                </div>
            </div>
            <div class="form-group mb-0">
                <label>Motivo o comentario</label>
                <textarea name="motivo" id="motivo" class="form-control" rows="2" placeholder="Ej. trámite personal, feriado programado…">{{ old('motivo') }}</textarea>
            </div>
            <div id="campos-viaticos" class="form-row mt-3" style="display:none">
                <div class="form-group col-md-4 mb-0">
                    <label>Destino del viático</label>
                    <input type="text" name="viaticos_destino" id="viaticos_destino" class="form-control" value="{{ old('viaticos_destino') }}">
                </div>
                <div class="form-group col-md-4 mb-0">
                    <label>Hora inicio</label>
                    <input type="time" name="viaticos_hora_inicio" class="form-control" value="{{ old('viaticos_hora_inicio') }}">
                </div>
                <div class="form-group col-md-4 mb-0">
                    <label>Hora término</label>
                    <input type="time" name="viaticos_hora_termino" class="form-control" value="{{ old('viaticos_hora_termino') }}">
                </div>
            </div>
            <div id="campos-licencia" class="form-row mt-3" style="display:none">
                <div class="form-group col-md-4 mb-0">
                    <label>Folio de la licencia</label>
                    <input type="text" name="licencia_folio" class="form-control" value="{{ old('licencia_folio') }}">
                </div>
                <div class="form-group col-md-4 mb-0">
                    <label>Tipo de licencia</label>
                    <input type="text" name="licencia_tipo" class="form-control" value="{{ old('licencia_tipo') }}">
                </div>
                <div class="form-group col-md-4 mb-0">
                    <label>Emisor</label>
                    <input type="text" name="licencia_emisor" class="form-control" value="{{ old('licencia_emisor') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card card-outline card-primary">
        <div class="card-header"><strong>3. ¿A qué buzón se envía?</strong></div>
        <div class="card-body">
            <div class="form-group mb-2">
                <label>Buzón de su jefatura o unidad</label>
                <select id="id_buzon_destino" class="form-control">
                    <option value="">— Busque el nombre del buzón —</option>
                    @foreach($buzones as $b)
                        <option value="{{ $b['id_buzon'] }}" @if((string) old('id_buzon_destino') === (string) $b['id_buzon']) selected @endif>
                            {{ $b['nombre'] }}@if(!empty($b['nombre_corto'])) ({{ $b['nombre_corto'] }})@endif
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Queda en <b>Por Recibir</b> de ese buzón. Allí se recibe, visa, firma y deriva, igual que cualquier documento.</small>
            </div>
        </div>
    </div>

    <div class="card card-outline card-success">
        <div class="card-header"><strong>Documento</strong> <small class="text-muted">— puede revisar o ajustar el texto antes de enviar</small></div>
        <div class="card-body">
            <div id="preview-encabezado" class="border rounded p-2 mb-3 bg-light" style="display:none"></div>
            <textarea name="documento_cuerpo_html" id="documento_cuerpo_html" class="form-control">{{ old('documento_cuerpo_html') }}</textarea>
            <div id="preview-distribucion" class="border rounded p-2 mt-3 bg-light" style="display:none"></div>
        </div>
        <div class="card-footer">
            <button class="btn btn-success btn-lg" type="submit"><i class="fas fa-paper-plane"></i> Firmar y enviar al buzón</button>
            <a href="{{ route('solicitudes.index') }}" class="btn btn-default btn-lg">Cancelar</a>
        </div>
    </div>
</form>
@stop

@section('js')
<script>window.SOL_TIPOS = @json($tipos); window.SOL_YO = @json($yoDatos ?? ['nombre'=>'','run'=>'','cargo'=>'']);</script>
<script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ url('js/ckfinder/ckfinder.js') }}"></script>
<script>
(function () {
    var ckCfg = {
        language: 'es',
        height: 280,
        removePlugins: 'exportpdf',
        filebrowserBrowseUrl: "{{ route('ckfinder_browser') }}",
        filebrowserImageBrowseUrl: "{{ route('ckfinder_browser') }}?type=Images&token=123",
        filebrowserImageUploadUrl: "{{ route('ckfinder_connector') }}?command=QuickUpload&type=Images"
    };
    var editor = CKEDITOR.replace('documento_cuerpo_html', ckCfg);
    var usuarioEdito = false;
    var silenciar = false;
    var oldCuerpo = @json(old('documento_cuerpo_html'));
    editor.on('change', function () { if (!silenciar) usuarioEdito = true; });

    $('#id_buzon_destino').select2({ placeholder: 'Busque el nombre del buzón', width: '100%', allowClear: true });
    $('#sol_tipo_documento_id').select2({ width: '100%', placeholder: 'Elija el tipo' });

    function tipoActual() {
        var id = Number($('#sol_tipo_documento_id').val() || 0);
        return (window.SOL_TIPOS || []).find(function (t) { return Number(t.id) === id; }) || null;
    }
    function fmtFecha(iso) {
        if (!iso) return '';
        var p = String(iso).split('-');
        return p.length === 3 ? (p[2] + '-' + p[1] + '-' + p[0]) : iso;
    }
    function dias() {
        var a = $('#fecha_inicio').val(), b = $('#fecha_termino').val();
        if (!a || !b) return '';
        var d1 = new Date(a + 'T00:00:00'), d2 = new Date(b + 'T00:00:00');
        if (d2 < d1) return '';
        return Math.round((d2 - d1) / 86400000) + 1;
    }
    function fill(html) {
        var t = tipoActual();
        var yo = window.SOL_YO || {};
        var map = {
            '@{{nombre}}': yo.nombre || '',
            '@{{run}}': yo.run || '',
            '@{{cargo}}': yo.cargo || '',
            '@{{departamento}}': '',
            '@{{tipo_solicitud}}': t ? (t.nombre || '') : '',
            '@{{fecha_inicio}}': fmtFecha($('#fecha_inicio').val()),
            '@{{fecha_termino}}': fmtFecha($('#fecha_termino').val()),
            '@{{total_dias}}': String(dias() || ''),
            '@{{motivo}}': $('#motivo').val() || '',
            '@{{explicacion}}': $('#motivo').val() || '',
            '@{{viaticos_destino}}': $('#viaticos_destino').val() || '',
            '@{{fecha}}': (function () {
                var d = new Date();
                return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear();
            })()
        };
        var out = html || '';
        Object.keys(map).forEach(function (k) {
            out = out.split(k).join(map[k]);
        });
        return out;
    }
    function setSide(id, html) {
        var el = $(id);
        if (html && html.replace(/<[^>]+>/g, '').trim()) {
            el.html(html).show();
        } else {
            el.empty().hide();
        }
    }
    function refrescarDoc(forzar) {
        var t = tipoActual();
        $('#tipo_solicitud').val(t ? (t.tipo_solicitud || '') : '');
        var cat = t ? (t.categoria || '') : '';
        $('#campos-viaticos').toggle(cat === 'viaticos');
        $('#campos-licencia').toggle(cat === 'licencias');
        $('#total_dias_vista').val(dias() ? (dias() + ' día(s)') : '—');
        setSide('#preview-encabezado', fill(t ? (t.plantilla_encabezado_html || '') : ''));
        setSide('#preview-distribucion', fill(t ? (t.plantilla_distribucion_html || '') : ''));
        if (!forzar && usuarioEdito) return;
        var cuerpo = t && t.plantilla_cuerpo_html
            ? t.plantilla_cuerpo_html
            : '<p>Yo, @{{nombre}}, solicito @{{tipo_solicitud}} desde @{{fecha_inicio}} hasta @{{fecha_termino}} (@{{total_dias}} días).</p><p>Motivo: @{{motivo}}.</p>';
        silenciar = true;
        editor.setData(fill(cuerpo), function () {
            silenciar = false;
            usuarioEdito = false;
        });
    }

    $('#sol_tipo_documento_id').on('change', function () { usuarioEdito = false; refrescarDoc(true); });
    $('#fecha_inicio, #fecha_termino, #motivo, #viaticos_destino').on('change input', function () { refrescarDoc(false); });
    $('#id_buzon_destino').on('change', function () {
        $('#id_buzon_destino_val').val($(this).val() || '');
    }).trigger('change');

    $('#form-solicitud').on('submit', function (e) {
        editor.updateElement();
        $('#id_buzon_destino_val').val($('#id_buzon_destino').val() || '');
        if (!$('#id_buzon_destino_val').val()) {
            e.preventDefault();
            alert('Elija el buzón de destino.');
        }
    });
    editor.on('instanceReady', function () {
        if (oldCuerpo) {
            editor.setData(oldCuerpo);
            usuarioEdito = true;
            refrescarDoc(false);
        } else {
            refrescarDoc(true);
        }
    });
})();
</script>
@stop
