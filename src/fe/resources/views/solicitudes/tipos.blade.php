@extends('adminlte::page')

@section('title', 'Plantillas de solicitud')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-0">Plantillas de solicitud</h1>
            <p class="text-muted mb-0">Igual que un tipo de documento: se redacta con el editor Word y se envía a buzones del SGD.</p>
        </div>
        <button type="button" class="btn btn-success" id="btn-nuevo"><i class="fas fa-plus"></i> Nueva plantilla</button>
    </div>
@stop

@section('content')
@if(!empty($error))
    <div class="alert alert-danger">{{ $error }}</div>
@endif

<div class="card">
    <div class="card-header"><strong>¿A quién sigue el trámite después del primer buzón?</strong></div>
    <div class="card-body">
        <p class="mb-3">El funcionario elige el primer buzón (su jefatura). Después el documento sigue a estos buzones, como un documento controlado.</p>
        <form method="post" action="{{ route('solicitudes.tipos.config') }}" class="form-row align-items-end">
            @csrf
            <div class="form-group col-md-5">
                <label>2.º buzón (RRHH / Personal)</label>
                <select name="buzon_rrhh_id" id="cfg_rrhh" class="form-control">
                    <option value="">— Elegir —</option>
                    @foreach($buzones as $b)
                        <option value="{{ $b['id_buzon'] }}" @if(($config['buzon_rrhh_id'] ?? null) == $b['id_buzon']) selected @endif>{{ $b['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-5">
                <label>3.º buzón (Alcalde)</label>
                <select name="buzon_alcalde_id" id="cfg_alcalde" class="form-control">
                    <option value="">— Elegir —</option>
                    @foreach($buzones as $b)
                        <option value="{{ $b['id_buzon'] }}" @if(($config['buzon_alcalde_id'] ?? null) == $b['id_buzon']) selected @endif>{{ $b['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2">
                <button class="btn btn-primary btn-block" type="submit">Guardar</button>
            </div>
        </form>
    </div>
</div>

<div class="card" id="card_grilla">
    <div class="card-header"><strong>Plantillas disponibles</strong></div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Para qué sirve</th>
                    <th>Descuenta días</th>
                    <th>Firmas</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @php
                $catNom = [
                    'dias' => 'Días administrativos',
                    'compensatorios' => 'Días compensatorios',
                    'vacaciones' => 'Feriado legal',
                    'viaticos' => 'Viáticos',
                    'licencias' => 'Licencia médica',
                    'otro' => 'Otro',
                ];
            @endphp
            @forelse($tipos as $t)
                <tr>
                    <td><b>{{ $t['nombre'] }}</b></td>
                    <td>{{ $catNom[$t['categoria'] ?? ''] ?? ($t['categoria'] ?? '—') }}</td>
                    <td>{{ !empty($t['consume_saldo']) ? 'Sí' : 'No' }}</td>
                    <td>@if(!empty($t['numero_firmas'])){{ (int) $t['numero_firmas'] }}@else—@endif</td>
                    <td class="text-right text-nowrap">
                        <button type="button" class="btn btn-sm btn-info btn-editar" data-id="{{ $t['id'] }}">Editar</button>
                        <form method="post" action="{{ route('solicitudes.tipos.delete', $t['id']) }}" class="d-inline" onsubmit="return confirm('¿Borrar esta plantilla?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Borrar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted">Aún no hay plantillas. Cree la primera.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card card-outline card-success" id="card-form" style="display:none">
    <div class="card-header">
        <strong id="form-title">Nueva plantilla</strong>
        <button type="button" class="btn btn-sm btn-default float-right" id="btn-cerrar-form">Volver al listado</button>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('solicitudes.tipos.save') }}" id="form-tipo">
            @csrf
            <input type="hidden" name="id" id="tipo_id" value="">
            <input type="hidden" name="tipo_solicitud" id="tipo_solicitud" value="">

            <h5 class="mb-3">1. Datos de la plantilla</h5>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Nombre que verá el funcionario</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required placeholder="Ej. Días administrativos">
                </div>
                <div class="form-group col-md-6">
                    <label>Qué tipo de trámite es</label>
                    <select name="categoria" id="categoria" class="form-control">
                        <option value="dias">Días administrativos</option>
                        <option value="compensatorios">Días compensatorios</option>
                        <option value="vacaciones">Feriado legal / vacaciones</option>
                        <option value="viaticos">Viáticos</option>
                        <option value="licencias">Licencia médica</option>
                        <option value="otro">Otro</option>
                    </select>
                    <small class="text-muted">Administrativos y compensatorios son trámites distintos y descuentan saldos distintos.</small>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="consume_saldo" value="0">
                        <input type="checkbox" class="custom-control-input" name="consume_saldo" id="consume_saldo" value="1" checked>
                        <label class="custom-control-label" for="consume_saldo">Descuenta del saldo de días</label>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="requiere_fe" value="0">
                        <input type="checkbox" class="custom-control-input" name="requiere_fe" id="requiere_fe" value="1" checked>
                        <label class="custom-control-label" for="requiere_fe">Requiere firma electrónica</label>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <div class="custom-control custom-switch">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" class="custom-control-input" name="activo" id="activo" value="1" checked>
                        <label class="custom-control-label" for="activo">Visible para los funcionarios</label>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Número de firmas (incluye la del solicitante)</label>
                    <select name="numero_firmas" id="numero_firmas" class="form-control">
                        <option value="2">2</option>
                        <option value="3" selected>3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                    <small class="text-muted">Al crear, el funcionario ya firma. Luego firman los buzones del recorrido. Con 1 firma el trámite se cerraría en la jefatura.</small>
                </div>
            </div>
            <input type="hidden" name="primer_buzon_editable" value="1">

            <hr>
            <h5 class="mb-2">2. Texto del documento</h5>
            <p class="text-muted">Redacte como en Word. Use los botones para insertar datos que se rellenan solos al crear la solicitud: nombre, fechas, motivo, etc.</p>

            <div class="mb-2" id="btns-campos">
                <span class="text-muted mr-2">Insertar dato:</span>
                <button type="button" class="btn btn-xs btn-outline-secondary btn-campo" data-campo="@{{nombre}}">Nombre</button>
                <button type="button" class="btn btn-xs btn-outline-secondary btn-campo" data-campo="@{{run}}">RUN</button>
                <button type="button" class="btn btn-xs btn-outline-secondary btn-campo" data-campo="@{{cargo}}">Cargo</button>
                <button type="button" class="btn btn-xs btn-outline-secondary btn-campo" data-campo="@{{fecha_inicio}}">Fecha inicio</button>
                <button type="button" class="btn btn-xs btn-outline-secondary btn-campo" data-campo="@{{fecha_termino}}">Fecha término</button>
                <button type="button" class="btn btn-xs btn-outline-secondary btn-campo" data-campo="@{{total_dias}}">N. de dias</button>
                <button type="button" class="btn btn-xs btn-outline-secondary btn-campo" data-campo="@{{motivo}}">Motivo</button>
                <button type="button" class="btn btn-xs btn-outline-secondary btn-campo" data-campo="@{{viaticos_destino}}">Destino viatico</button>
                <button type="button" class="btn btn-xs btn-outline-secondary btn-campo" data-campo="@{{departamento}}">Departamento</button>
                <button type="button" class="btn btn-xs btn-outline-secondary btn-campo" data-campo="@{{fecha}}">Fecha de hoy</button>
            </div>

            <div class="form-group">
                <label>Encabezado</label>
                <textarea name="plantilla_encabezado_html" id="plantilla_encabezado_html" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Cuerpo del documento</label>
                <textarea name="plantilla_cuerpo_html" id="plantilla_cuerpo_html" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Distribución (opcional)</label>
                <textarea name="plantilla_distribucion_html" id="plantilla_distribucion_html" class="form-control"></textarea>
            </div>

            <hr>
            <h5 class="mb-2">3. Recorrido de buzones (derivaciones)</h5>
            <p class="text-muted">Igual que un tipo de documento controlado. El funcionario elige el primer buzón al crear. Estos pasos son los siguientes (visar / firmar / cerrar).</p>
            <div class="form-row align-items-end mb-2">
                <div class="col-md-8">
                    <label>Agregar buzón al recorrido</label>
                    <select id="sel-buzon" class="form-control">
                        <option value="">Escriba para buscar el buzón</option>
                        @foreach($buzones as $b)
                            <option value="{{ $b['id_buzon'] }}">{{ $b['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-outline-success btn-block" id="btn-add-buzon">Agregar al recorrido</button>
                </div>
            </div>
            <table class="table table-bordered table-sm" id="tabla-flujo">
                <thead>
                    <tr>
                        <th style="width:50px">Paso</th>
                        <th>Buzón</th>
                        <th class="text-center">Visar</th>
                        <th class="text-center">Firmar</th>
                        <th class="text-center">Cierra trámite</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <button type="button" class="btn btn-outline-primary" id="btn-vista-previa"><i class="fas fa-eye"></i> Ver vista previa</button>
            <button class="btn btn-success" type="submit"><i class="fas fa-save"></i> Guardar plantilla</button>
            <button class="btn btn-default" type="button" id="btn-reset">Limpiar</button>
        </form>
    </div>
</div>

<div class="modal fade" id="modal-vista-previa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document" style="max-width:900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista previa del documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="background:#cfcfcf">
                <p class="text-center text-muted mb-2" style="font-size:12px">Datos de ejemplo (como al crear una solicitud de días)</p>
                <div id="sol-hoja-preview" style="max-width:794px;min-height:800px;margin:0 auto;background:#fff;padding:48px 56px;box-shadow:0 2px 12px rgba(0,0,0,.25);font-family:DejaVu Sans, Arial, sans-serif;font-size:13px;color:#222;line-height:1.45"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>window.SOL_TIPOS = @json($tipos);</script>
<script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ url('js/ckfinder/ckfinder.js') }}"></script>
<script>
(function () {
    var idx = 0;
    var ckCfg = {
        language: 'es',
        removePlugins: 'exportpdf',
        filebrowserBrowseUrl: "{{ route('ckfinder_browser') }}",
        filebrowserImageBrowseUrl: "{{ route('ckfinder_browser') }}?type=Images&token=123",
        filebrowserImageUploadUrl: "{{ route('ckfinder_connector') }}?command=QuickUpload&type=Images"
    };
    var editorActivo = 'plantilla_cuerpo_html';
    var ckListo = false;
    var ckIniciando = false;
    var ckPendientes = [];
    function initCk(cb) {
        if (cb) ckPendientes.push(cb);
        if (ckListo) {
            var fns = ckPendientes.slice();
            ckPendientes = [];
            fns.forEach(function (fn) { if (fn) fn(); });
            return;
        }
        if (ckIniciando) return;
        ckIniciando = true;
        var edEnc = CKEDITOR.replace('plantilla_encabezado_html', Object.assign({ height: 280 }, ckCfg));
        var edCuerpo = CKEDITOR.replace('plantilla_cuerpo_html', Object.assign({ height: 280 }, ckCfg));
        var edDist = CKEDITOR.replace('plantilla_distribucion_html', Object.assign({ height: 100 }, ckCfg));
        var left = 3;
        function listoUno() {
            left--;
            if (left > 0) return;
            ckListo = true;
            var fns = ckPendientes.slice();
            ckPendientes = [];
            fns.forEach(function (fn) { if (fn) fn(); });
        }
        [edEnc, edCuerpo, edDist].forEach(function (ed) {
            ed.on('focus', function () { editorActivo = ed.name; });
            ed.on('instanceReady', listoUno);
        });
    }

    var cuerpoDefault = '<p>Yo, @{{nombre}}, RUN @{{run}}, solicito @{{tipo_solicitud}} desde @{{fecha_inicio}} hasta @{{fecha_termino}} (@{{total_dias}} días).</p><p>Motivo: @{{motivo}}.</p>';

    $('#sel-buzon').select2({ width: '100%', placeholder: 'Busque buzón' });
    $('#cfg_rrhh, #cfg_alcalde').select2({ width: '100%' });

    function slugify(s) {
        return (s || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '').substring(0, 40);
    }
    function slugPorCategoria(cat) {
        var map = {
            dias: 'dias_administrativos',
            compensatorios: 'dias_compensatorios',
            vacaciones: 'feriados_legales',
            viaticos: 'viaticos',
            licencias: 'licencia_medica'
        };
        return map[cat] || slugify($('#nombre').val());
    }
    $('#categoria').on('change', function () {
        var c = $(this).val();
        $('#tipo_solicitud').val(slugPorCategoria(c));
        $('#consume_saldo').prop('checked', c === 'dias' || c === 'compensatorios' || c === 'vacaciones');
    });
    $('#nombre').on('input', function () {
        if (!$('#tipo_id').val() && $('#categoria').val() === 'otro') $('#tipo_solicitud').val(slugify(this.value));
    });

    $('.btn-campo').on('click', function () {
        var inst = (CKEDITOR.instances[editorActivo] || CKEDITOR.instances.plantilla_cuerpo_html);
        if (!inst) return;
        inst.insertText($(this).attr('data-campo'));
        inst.focus();
    });

    function setEditor(name, html) {
        if (CKEDITOR.instances[name]) CKEDITOR.instances[name].setData(html || '');
        else $('#' + name).val(html || '');
    }
    function addRow(id, nombre, acciones) {
        acciones = acciones || ['firmar'];
        var i = idx++;
        var tr = $('<tr/>');
        tr.append('<td><input type="hidden" name="flujo_id_buzon[]" value="' + id + '">' +
            '<input type="hidden" name="flujo_nombre_buzon[]" value="' + $('<div>').text(nombre).html() + '">' +
            '<span class="orden-num"></span></td>');
        tr.append('<td>' + $('<div>').text(nombre).html() + '</td>');
        tr.append('<td class="text-center"><input type="checkbox" name="flujo_acciones[' + i + '][]" value="visar"' + (acciones.indexOf('visar') >= 0 ? ' checked' : '') + '></td>');
        tr.append('<td class="text-center"><input type="checkbox" name="flujo_acciones[' + i + '][]" value="firmar"' + (acciones.indexOf('firmar') >= 0 ? ' checked' : '') + '></td>');
        tr.append('<td class="text-center"><input type="checkbox" name="flujo_acciones[' + i + '][]" value="finalizar"' + (acciones.indexOf('finalizar') >= 0 ? ' checked' : '') + '></td>');
        tr.append('<td><button type="button" class="btn btn-xs btn-outline-danger btn-del">Quitar</button></td>');
        $('#tabla-flujo tbody').append(tr);
        renum();
    }
    function renum() {
        $('#tabla-flujo tbody tr').each(function (i) { $(this).find('.orden-num').text(i + 1); });
    }
    function mostrarForm(after) {
        $('#card-form').show();
        initCk(function () {
            setTimeout(function () {
                for (var n in CKEDITOR.instances) {
                    try { CKEDITOR.instances[n].resize('100%', CKEDITOR.instances[n].config.height); } catch (e) {}
                }
                if (after) after();
            }, 80);
        });
        $('html, body').animate({ scrollTop: $('#card-form').offset().top - 70 }, 250);
    }
    function ocultarForm() {
        $('#card-form').hide();
    }

    $('#form-tipo').on('submit', function () {
        for (var n in CKEDITOR.instances) {
            if (CKEDITOR.instances.hasOwnProperty(n)) CKEDITOR.instances[n].updateElement();
        }
        $('#tabla-flujo tbody tr').each(function (i) {
            $(this).find('input[type=checkbox]').attr('name', 'flujo_acciones[' + i + '][]');
        });
        if (!$('#tipo_solicitud').val()) $('#tipo_solicitud').val(slugPorCategoria($('#categoria').val()) || slugify($('#nombre').val()));
    });
    $('#btn-add-buzon').on('click', function () {
        var sel = $('#sel-buzon');
        if (!sel.val()) return;
        addRow(sel.val(), sel.find('option:selected').text(), ['firmar']);
    });
    $('#tabla-flujo').on('click', '.btn-del', function () {
        $(this).closest('tr').remove();
        renum();
    });
    function limpiarCampos() {
        $('#form-title').text('Nueva plantilla');
        $('#tipo_id').val('');
        $('#form-tipo')[0].reset();
        $('#requiere_fe, #activo, #consume_saldo').prop('checked', true);
        $('#categoria').val('dias');
        $('#tipo_solicitud').val('dias_administrativos');
        $('#tabla-flujo tbody').empty();
        idx = 0;
        $('#numero_firmas').val('3');
    }
    $('#btn-nuevo').on('click', function () {
        limpiarCampos();
        mostrarForm(function () {
            setEditor('plantilla_encabezado_html', '');
            setEditor('plantilla_cuerpo_html', cuerpoDefault);
            setEditor('plantilla_distribucion_html', '');
        });
    });
    $('#btn-reset').on('click', function () {
        limpiarCampos();
        setEditor('plantilla_encabezado_html', '');
        setEditor('plantilla_cuerpo_html', cuerpoDefault);
        setEditor('plantilla_distribucion_html', '');
    });
    $('#btn-cerrar-form').on('click', ocultarForm);

    function editorHtml(name) {
        return CKEDITOR.instances[name] ? CKEDITOR.instances[name].getData() : ($('#' + name).val() || '');
    }
    function fillEjemplo(html) {
        var hoy = new Date();
        var map = {
            '@{{nombre}}': 'JUAN PEREZ GONZALEZ',
            '@{{run}}': '12.345.678-9',
            '@{{cargo}}': 'Administrativo',
            '@{{departamento}}': 'Dirección de Administración',
            '@{{tipo_solicitud}}': $('#nombre').val() || 'Días administrativos',
            '@{{fecha_inicio}}': '19-08-2026',
            '@{{fecha_termino}}': '26-08-2026',
            '@{{total_dias}}': '6',
            '@{{motivo}}': 'Asuntos personales',
            '@{{explicacion}}': 'Asuntos personales',
            '@{{viaticos_destino}}': 'Talca',
            '@{{fecha}}': pad2(hoy.getDate()) + '-' + pad2(hoy.getMonth() + 1) + '-' + hoy.getFullYear()
        };
        var out = html || '';
        Object.keys(map).forEach(function (k) { out = out.split(k).join(map[k]); });
        return out;
    }
    function pad2(n) { return (n < 10 ? '0' : '') + n; }
    $('#btn-vista-previa').on('click', function () {
        if (!ckListo) {
            alert('Espere a que cargue el editor.');
            return;
        }
        var enc = fillEjemplo(editorHtml('plantilla_encabezado_html'));
        var cuerpo = fillEjemplo(editorHtml('plantilla_cuerpo_html') || cuerpoDefault);
        var dist = fillEjemplo(editorHtml('plantilla_distribucion_html'));
        var html = '';
        if (enc && enc.replace(/<[^>]+>/g, '').trim()) html += '<div class="mb-4">' + enc + '</div>';
        html += '<div>' + (cuerpo || '<p class="text-muted">Sin texto</p>') + '</div>';
        if (dist && dist.replace(/<[^>]+>/g, '').trim()) {
            html += '<hr><p class="mb-1"><strong>Distribución</strong></p><div>' + dist + '</div>';
        }
        $('#sol-hoja-preview').html(html);
        $('#modal-vista-previa').modal('show');
    });

    $('.btn-editar').on('click', function () {
        var id = Number($(this).data('id'));
        var t = (window.SOL_TIPOS || []).find(function (x) { return Number(x.id) === id; });
        if (!t) return;
        limpiarCampos();
        $('#form-title').text('Editar: ' + t.nombre);
        $('#tipo_id').val(t.id);
        $('#nombre').val(t.nombre);
        $('#tipo_solicitud').val(t.tipo_solicitud);
        $('#categoria').val(t.categoria || 'dias');
        $('#numero_firmas').val(t.numero_firmas && Number(t.numero_firmas) >= 2 ? t.numero_firmas : 3);
        $('#consume_saldo').prop('checked', !!t.consume_saldo);
        $('#requiere_fe').prop('checked', t.requiere_fe !== false);
        $('#activo').prop('checked', t.activo !== false);
        mostrarForm();
        setEditor('plantilla_encabezado_html', t.plantilla_encabezado_html || '');
        setEditor('plantilla_cuerpo_html', t.plantilla_cuerpo_html || cuerpoDefault);
        setEditor('plantilla_distribucion_html', t.plantilla_distribucion_html || '');
        (t.buzones_flujo || []).forEach(function (p) {
            addRow(p.id_buzon, p.nombre_buzon, p.acciones || ['firmar']);
        });
    });
})();
</script>
@stop
