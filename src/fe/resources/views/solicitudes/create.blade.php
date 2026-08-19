@extends('adminlte::page')

@section('title', 'Nueva solicitud')

@section('content_header')
    <h1 class="mb-0">Nueva solicitud</h1>
    <p class="text-muted mb-0">Complete los 3 pasos. El documento se envía al buzón como un oficio normal.</p>
@stop

@section('content')
@if(empty($tipos))
    <div class="alert alert-warning">Aún no hay plantillas. Un administrador debe crearlas.</div>
@endif
@if(empty($buzones))
    <div class="alert alert-danger">No hay buzones. Primero asigne buzones en el SGD.</div>
@endif

@if(!empty($saldo))
    @include('solicitudes._saldos', ['saldo' => $saldo])
@endif
<div id="aviso-sin-dias" class="alert alert-danger" style="display:none">
    No le quedan días de este tipo. No puede crear la solicitud. Elija otro tipo o pida a Personal que le cargue saldo.
</div>

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
                            data-consume="{{ !empty($t['consume_saldo']) ? '1' : '0' }}"
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
                    <label>Días hábiles a pedir</label>
                    <input type="text" id="total_dias_vista" class="form-control" readonly placeholder="—" tabindex="-1">
                    <div id="resto-tipo" class="mt-2 font-weight-bold" style="font-size:1.15rem"></div>
                    <small class="text-muted">Lunes a viernes, sin feriados de Chile.</small>
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
        <div class="card-header"><strong>3. Buzón del director (después de Personal)</strong></div>
        <div class="card-body">
            <div class="form-group mb-2">
                <label>Buzón de su jefatura o dirección</label>
                <select id="id_buzon_destino" class="form-control">
                    <option value="">— Busque el nombre del buzón —</option>
                    @foreach($buzones as $b)
                        <option value="{{ $b['id_buzon'] }}" @if((string) old('id_buzon_destino') === (string) $b['id_buzon']) selected @endif>
                            {{ $b['nombre'] }}@if(!empty($b['nombre_corto'])) ({{ $b['nombre_corto'] }})@endif
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Usted firma al enviar. Luego visa <b>Departamento de Personal</b>, firma el <b>director</b> de este buzón y al final el <b>alcalde</b>.</small>
            </div>
        </div>
    </div>

    <div class="card card-outline card-success">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><strong>Documento</strong> <small class="text-muted">— puede revisar o ajustar el texto antes de enviar</small></span>
            <button type="button" class="btn btn-outline-primary" id="btn-vista-previa">
                <i class="fas fa-eye"></i> Ver vista previa
            </button>
        </div>
        <div class="card-body">
            <div id="preview-encabezado" class="border rounded p-2 mb-3 bg-light" style="display:none"></div>
            <textarea name="documento_cuerpo_html" id="documento_cuerpo_html" class="form-control">{{ old('documento_cuerpo_html') }}</textarea>
            <div id="preview-distribucion" class="border rounded p-2 mt-3 bg-light" style="display:none"></div>
        </div>
        <div class="card-footer">
            <button class="btn btn-success btn-lg" type="submit" id="btn-enviar-sol"><i class="fas fa-paper-plane"></i> Firmar y enviar al buzón</button>
            <button type="button" class="btn btn-outline-primary btn-lg" id="btn-vista-previa-2"><i class="fas fa-eye"></i> Ver vista previa</button>
            <a href="{{ route('solicitudes.index') }}" class="btn btn-default btn-lg">Cancelar</a>
        </div>
    </div>
</form>

<div class="modal fade" id="modal-vista-previa" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document" style="max-width:900px">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vista previa del documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="background:#cfcfcf">
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
<script>window.SOL_TIPOS = @json($tipos); window.SOL_YO = @json($yoDatos ?? ['nombre'=>'','run'=>'','cargo'=>'']); window.SOL_SALDO = @json($saldo ?? []);</script>
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
    function pad(n) { return (n < 10 ? '0' : '') + n; }
    function ymd(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }
    function parseIso(iso) {
        var p = String(iso).split('-');
        return new Date(Number(p[0]), Number(p[1]) - 1, Number(p[2]));
    }
    function domingoPascua(anio) {
        var a = anio % 19, b = Math.floor(anio / 100), c = anio % 100;
        var d = Math.floor(b / 4), e = b % 4, f = Math.floor((b + 8) / 25);
        var g = Math.floor((b - f + 1) / 3);
        var h = (19 * a + b - d - g + 15) % 30;
        var i = Math.floor(c / 4), k = c % 4;
        var l = (32 + 2 * e + 2 * i - h - k) % 7;
        var m = Math.floor((a + 11 * h + 22 * l) / 451);
        var mes = Math.floor((h + l - 7 * m + 114) / 31);
        var dia = ((h + l - 7 * m + 114) % 31) + 1;
        return new Date(anio, mes - 1, dia);
    }
    function addDays(d, n) {
        return new Date(d.getFullYear(), d.getMonth(), d.getDate() + n);
    }
    function trasladoLunes(d) {
        var dow = d.getDay();
        if (dow === 2) return addDays(d, -1);
        if (dow === 3 || dow === 4 || dow === 5) return addDays(d, (8 - dow) % 7 || 7);
        return d;
    }
    function feriadosChile(anio) {
        var set = {};
        function add(d) { set[ymd(d)] = true; }
        add(new Date(anio, 0, 1));
        var pascua = domingoPascua(anio);
        add(addDays(pascua, -2));
        add(addDays(pascua, -1));
        add(new Date(anio, 4, 1));
        add(new Date(anio, 4, 21));
        add(trasladoLunes(new Date(anio, 5, 20)));
        add(trasladoLunes(new Date(anio, 5, 29)));
        add(trasladoLunes(new Date(anio, 6, 16)));
        add(trasladoLunes(new Date(anio, 7, 15)));
        add(new Date(anio, 8, 18));
        add(new Date(anio, 8, 19));
        var d18 = new Date(anio, 8, 18);
        if (d18.getDay() === 2) add(new Date(anio, 8, 17));
        if (new Date(anio, 8, 19).getDay() === 1) add(new Date(anio, 8, 20));
        add(trasladoLunes(new Date(anio, 9, 12)));
        add(trasladoLunes(new Date(anio, 9, 31)));
        add(trasladoLunes(new Date(anio, 10, 1)));
        add(new Date(anio, 11, 8));
        add(new Date(anio, 11, 25));
        return set;
    }
    var feriadosCache = {};
    function esHabil(d) {
        var dow = d.getDay();
        if (dow === 0 || dow === 6) return false;
        var y = d.getFullYear();
        if (!feriadosCache[y]) feriadosCache[y] = feriadosChile(y);
        return !feriadosCache[y][ymd(d)];
    }
    function dias() {
        var a = $('#fecha_inicio').val(), b = $('#fecha_termino').val();
        if (!a || !b) return '';
        var d1 = parseIso(a), d2 = parseIso(b);
        if (d2 < d1) return '';
        var t = tipoActual();
        if (t && t.categoria === 'licencias') {
            return Math.round((d2 - d1) / 86400000) + 1;
        }
        var n = 0, cur = new Date(d1.getTime());
        while (cur <= d2) {
            if (esHabil(cur)) n++;
            cur = addDays(cur, 1);
        }
        return n;
    }
    function rrhh() {
        var t = tipoActual();
        var s = window.SOL_SALDO || {};
        var campo = campoSaldo(t) || 'dias_administrativos';
        var asig = Number((s.asignados && s.asignados[campo]) != null ? s.asignados[campo] : (s[campo] || 0));
        var usado = Number((s.usados && s.usados[campo]) != null ? s.usados[campo] : 0);
        var pide = Number(dias() || 0);
        return {
            ha: usado,
            solicita: pide,
            saldo: Math.max(0, asig - usado - pide),
            total: asig
        };
    }
    function fill(html) {
        var t = tipoActual();
        var yo = window.SOL_YO || {};
        var map = {
            '@{{nombre}}': yo.nombre || '',
            '@{{run}}': yo.run || '',
            '@{{cargo}}': yo.cargo || '',
            '@{{departamento}}': yo.departamento || '',
            '@{{tipo_solicitud}}': t ? (t.nombre || '') : '',
            '@{{fecha_inicio}}': fmtFecha($('#fecha_inicio').val()),
            '@{{fecha_termino}}': fmtFecha($('#fecha_termino').val()),
            '@{{total_dias}}': String(dias() || ''),
            '@{{motivo}}': $('#motivo').val() || '',
            '@{{explicacion}}': $('#motivo').val() || '',
            '@{{viaticos_destino}}': $('#viaticos_destino').val() || '',
            '@{{fecha}}': (function () {
                var meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
                var d = new Date();
                return ('0' + d.getDate()).slice(-2) + ' de ' + meses[d.getMonth()] + ' del ' + d.getFullYear();
            })(),
            '@{{anio}}': String(new Date().getFullYear()),
            '{t_anio}': String(new Date().getFullYear()),
            '{t_fecha}': (function () {
                var meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
                var d = new Date();
                return ('0' + d.getDate()).slice(-2) + ' de ' + meses[d.getMonth()] + ' del ' + d.getFullYear();
            })(),
            '{t_folio}': 'SIN FOLIO',
            '@{{ha_solicitado}}': String(rrhh().ha),
            '@{{solicita}}': String(rrhh().solicita),
            '@{{saldo}}': String(rrhh().saldo),
            '@{{total}}': String(rrhh().total),
            '@{{alcalde_autorizado}}': '______',
            '@{{alcalde_denegado}}': '______',
            '@{{alcalde_observaciones}}': '________________'
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
        var nDias = dias();
        var tVista = tipoActual();
        var esLic = tVista && tVista.categoria === 'licencias';
        if (nDias === '' || nDias === null) {
            $('#total_dias_vista').val('—');
        } else if (esLic) {
            $('#total_dias_vista').val(nDias + ' día(s) corridos');
        } else {
            $('#total_dias_vista').val(nDias + ' día(s) hábil(es)');
        }
        actualizarSaldoTipo();
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

    function abrirVistaPrevia() {
        var t = tipoActual();
        if (!t) {
            alert('Elija primero el tipo de solicitud.');
            return;
        }
        var enc = fill(t.plantilla_encabezado_html || '');
        var cuerpo = editor.getData() || fill(t.plantilla_cuerpo_html || '');
        var dist = fill(t.plantilla_distribucion_html || '');
        var html = '';
        if (enc && enc.replace(/<[^>]+>/g, '').trim()) html += '<div class="mb-4">' + enc + '</div>';
        html += '<div>' + (cuerpo || '<p class="text-muted">Sin texto</p>') + '</div>';
        if (dist && dist.replace(/<[^>]+>/g, '').trim()) {
            html += '<hr><p class="mb-1"><strong>Distribución</strong></p><div>' + dist + '</div>';
        }
        $('#sol-hoja-preview').html(html);
        $('#modal-vista-previa').modal('show');
    }
    $('#btn-vista-previa, #btn-vista-previa-2').on('click', abrirVistaPrevia);
    $('#sol_tipo_documento_id').on('change', function () { usuarioEdito = false; refrescarDoc(true); });
    $('#fecha_inicio, #fecha_termino, #motivo, #viaticos_destino').on('change input', function () { refrescarDoc(false); });
    $('#id_buzon_destino').on('change', function () {
        $('#id_buzon_destino_val').val($(this).val() || '');
    }).trigger('change');

    function campoSaldo(t) {
        if (!t) return null;
        var cat = t.categoria || '';
        var slug = t.tipo_solicitud || '';
        if (slug === 'dias_compensatorios' || cat === 'compensatorios') return 'dias_compensatorios';
        if (slug === 'feriados_legales' || cat === 'vacaciones') return 'feriados_legales';
        if (slug === 'dias_administrativos' || cat === 'dias') return 'dias_administrativos';
        if (t.consume_saldo) return 'dias_administrativos';
        return null;
    }
    function restanteTipo(t) {
        var campo = campoSaldo(t);
        if (!campo) return null;
        var s = window.SOL_SALDO || {};
        return Number(s[campo] || 0);
    }
    function consumeSaldo(t) {
        if (!t) return false;
        if (String(t.consume_saldo) === '1' || t.consume_saldo === true || t.consume_saldo === 1) return true;
        return campoSaldo(t) !== null && t.categoria !== 'licencias' && t.categoria !== 'viaticos';
    }
    function actualizarSaldoTipo() {
        var t = tipoActual();
        var resto = restanteTipo(t);
        var nDias = Number(dias() || 0);
        var box = $('#resto-tipo');
        var aviso = $('#aviso-sin-dias');
        var btn = $('#btn-enviar-sol');
        if (!t || !consumeSaldo(t)) {
            box.text('');
            aviso.hide();
            btn.prop('disabled', false);
            return true;
        }
        box.text('Le quedan ' + resto + ' día(s) de este tipo');
        if (resto <= 0) {
            aviso.show();
            btn.prop('disabled', true);
            return false;
        }
        aviso.hide();
        if (nDias > resto) {
            box.append(' — está pidiendo más de las que tiene');
            btn.prop('disabled', true);
            return false;
        }
        btn.prop('disabled', false);
        return true;
    }

    $('#form-solicitud').on('submit', function (e) {
        editor.updateElement();
        $('#id_buzon_destino_val').val($('#id_buzon_destino').val() || '');
        if (!$('#id_buzon_destino_val').val()) {
            e.preventDefault();
            alert('Elija el buzón del director.');
            return;
        }
        if (!actualizarSaldoTipo()) {
            e.preventDefault();
            alert('No tiene días disponibles de este tipo.');
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
