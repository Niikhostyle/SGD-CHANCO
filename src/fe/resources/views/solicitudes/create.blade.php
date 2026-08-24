@extends('adminlte::page')

@section('title', 'Nueva solicitud')

@section('content_header')
    <h1 class="mb-0">Nueva solicitud</h1>
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
            <div class="mb-3" id="wrap-media-jornada" style="display:none">
                <input type="hidden" name="media_jornada" id="media_jornada" value="{{ old('media_jornada', '0') }}">
                <button type="button" class="btn btn-outline-primary" id="btn-media-jornada">
                    <i class="fas fa-clock"></i> Permiso por media jornada
                </button>
                <small class="text-muted d-block mt-1">
                    Jornada laboral: <b>08:30 a 17:30</b>. Puede pedir solo la mañana o solo la tarde (0,5 día).
                </small>
            </div>

            <div class="form-row" id="fila-fechas-completo">
                <div class="form-group col-md-4">
                    <label>Desde</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}" required>
                </div>
                <div class="form-group col-md-4" id="wrap-fecha-termino">
                    <label>Hasta</label>
                    <input type="date" name="fecha_termino" id="fecha_termino" class="form-control" value="{{ old('fecha_termino') }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Días a pedir</label>
                    <input type="text" id="total_dias_vista" class="form-control" readonly placeholder="—" tabindex="-1">
                    <div id="resto-tipo" class="mt-2 font-weight-bold" style="font-size:1.15rem"></div>
                    <small class="text-muted" id="ayuda-dias">Lunes a viernes, sin feriados.</small>
                </div>
            </div>

            <div id="panel-media-jornada" class="border rounded p-3 mb-3 bg-light" style="display:none">
                <p class="mb-2 font-weight-bold">¿Qué media jornada pide permiso?</p>
                <div class="form-row">
                    <div class="form-group col-md-6 mb-2">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="media_am" name="media_franja" value="am" class="custom-control-input"
                                @if(old('media_franja', old('jornada_inicio', 'am')) === 'am') checked @endif>
                            <label class="custom-control-label" for="media_am">
                                <b>Mañana (AM)</b> — 08:30 a 13:00<br>
                                <small class="text-muted">Pide la mañana y trabaja la tarde (13:00 a 17:30).</small>
                            </label>
                        </div>
                    </div>
                    <div class="form-group col-md-6 mb-2">
                        <div class="custom-control custom-radio">
                            <input type="radio" id="media_pm" name="media_franja" value="pm" class="custom-control-input"
                                @if(old('media_franja', old('jornada_inicio')) === 'pm') checked @endif>
                            <label class="custom-control-label" for="media_pm">
                                <b>Tarde (PM)</b> — 13:00 a 17:30<br>
                                <small class="text-muted">Pide la tarde y trabaja la mañana (08:30 a 13:00).</small>
                            </label>
                        </div>
                    </div>
                </div>
                <div id="resumen-media" class="alert alert-info mb-0 py-2"></div>
            </div>

            <input type="hidden" name="jornada_inicio" id="jornada_inicio" value="{{ old('jornada_inicio', 'am') }}">
            <input type="hidden" name="jornada_termino" id="jornada_termino" value="{{ old('jornada_termino', 'pm') }}">
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
        <div class="card-header"><strong>3. Buzón del director de área</strong></div>
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
            </div>
        </div>
    </div>

    <div class="card card-outline card-success">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><strong>Documento</strong></span>
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
            <button class="btn btn-success btn-lg" type="submit" id="btn-enviar-sol"><i class="fas fa-paper-plane"></i> Firmar y enviar al director</button>
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
    function permiteMedioDia(t) {
        if (!t) return false;
        var cat = t.categoria || '';
        return cat === 'dias' || cat === 'compensatorios'
            || t.tipo_solicitud === 'dias_administrativos'
            || t.tipo_solicitud === 'dias_compensatorios';
    }
    function esMediaJornada() {
        return $('#media_jornada').val() === '1';
    }
    function franjaMedia() {
        return ($('input[name=media_franja]:checked').val() || 'am').toLowerCase();
    }
    function horarioFranja(franja) {
        return franja === 'pm'
            ? { permiso: '13:00 a 17:30', trabaja: '08:30 a 13:00', label: 'tarde (PM)' }
            : { permiso: '08:30 a 13:00', trabaja: '13:00 a 17:30', label: 'mañana (AM)' };
    }
    function sincronizarMediaJornada() {
        var t = tipoActual();
        var medioOk = permiteMedioDia(t);
        $('#wrap-media-jornada').toggle(!!medioOk);
        if (!medioOk) {
            $('#media_jornada').val('0');
            $('#panel-media-jornada').hide();
            $('input[name=media_franja]').prop('disabled', true);
            $('#btn-media-jornada').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#wrap-fecha-termino').show();
            $('#fecha_termino').prop('required', true);
            $('#jornada_inicio').val('am');
            $('#jornada_termino').val('pm');
            return;
        }
        if (esMediaJornada()) {
            $('#btn-media-jornada').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#panel-media-jornada').show();
            $('#wrap-fecha-termino').hide();
            $('input[name=media_franja]').prop('disabled', false);
            var f = $('#fecha_inicio').val();
            if (f) $('#fecha_termino').val(f);
            $('#fecha_termino').prop('required', false);
            var franja = franjaMedia();
            $('#jornada_inicio').val(franja);
            $('#jornada_termino').val(franja);
            var h = horarioFranja(franja);
            var fechaTxt = fmtFecha($('#fecha_inicio').val()) || '—';
            $('#resumen-media').html(
                'El <b>' + fechaTxt + '</b> pide permiso en la <b>' + h.label + '</b> (' + h.permiso + ') ' +
                'y trabaja en la otra media jornada (' + h.trabaja + '). Descuenta <b>0,5 día</b>.'
            );
        } else {
            $('#btn-media-jornada').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#panel-media-jornada').hide();
            $('#wrap-fecha-termino').show();
            $('input[name=media_franja]').prop('disabled', true);
            $('#fecha_termino').prop('required', true);
            $('#jornada_inicio').val('am');
            $('#jornada_termino').val('pm');
            $('#resumen-media').empty();
        }
    }
    function listarHabilesJs(d1, d2) {
        var out = [], cur = new Date(d1.getTime());
        while (cur <= d2) {
            if (esHabil(cur)) out.push(new Date(cur.getTime()));
            cur = addDays(cur, 1);
        }
        return out;
    }
    function dias() {
        var a = $('#fecha_inicio').val();
        if (!a) return '';
        if (esMediaJornada() && permiteMedioDia(tipoActual())) {
            var d = parseIso(a);
            if (!esHabil(d)) return '';
            return 0.5;
        }
        var b = $('#fecha_termino').val();
        if (!b) return '';
        var d1 = parseIso(a), d2 = parseIso(b);
        if (d2 < d1) return '';
        var t = tipoActual();
        if (t && t.categoria === 'licencias') {
            return Math.round((d2 - d1) / 86400000) + 1;
        }
        var habiles = listarHabilesJs(d1, d2);
        var n = habiles.length;
        if (!n) return '';
        if (!permiteMedioDia(t)) return n;
        var ji = ($('#jornada_inicio').val() || 'am').toLowerCase();
        var jt = ($('#jornada_termino').val() || 'pm').toLowerCase();
        if (n === 1) {
            return ji === jt ? 0.5 : 1;
        }
        var total = 0;
        for (var i = 0; i < n; i++) {
            if (i === 0) total += (ji === 'pm') ? 0.5 : 1;
            else if (i === n - 1) total += (jt === 'am') ? 0.5 : 1;
            else total += 1;
        }
        return Math.round(total * 10) / 10;
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
    function normalizarLlavesToken(html) {
        return String(html || '').replace(/\{([^{}]+)\}/g, function (_, inner) {
            var t = String(inner).replace(/<[^>]+>/g, '').replace(/&nbsp;/gi, '').replace(/\s+/g, '');
            var ta = document.createElement('textarea');
            ta.innerHTML = t;
            return '{' + ta.value + '}';
        });
    }
    function normalizarEstilosDoc(html) {
        var s = String(html || '');
        s = s.replace(/\bstyle="([^"]*)"/gi, function (_, style) {
            var m = /margin-left\s*:\s*(\d+)px/i.exec(style);
            if (m && parseInt(m[1], 10) >= 200) {
                style = style.replace(/margin-left\s*:\s*\d+px\s*;?/gi, '');
                if (!/text-align\s*:/i.test(style)) style = 'text-align:right;' + style;
            }
            return 'style="' + style.replace(/;\s*$/, '') + ';"';
        });
        return s.replace(/<img\b[^>]*src="file:[^"]*"[^>]*>/gi, '');
    }
    function deduplicarDiaFecha(s, patMes) {
        return String(s || '').replace(new RegExp('(\\d{1,2})\\s+\\1\\s+(' + patMes + ')', 'gi'), '$1 $2');
    }
    function completarDiaFecha(html, dia) {
        var meses = 'enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre';
        var patMes = 'de\\s+(' + meses + ')\\s+del?\\s+\\d{4}';
        var fechaCompleta = new RegExp('\\d{1,2}\\s+(' + meses + ')\\s+del?\\s+\\d{4}', 'i');
        var s = String(html || '');
        s = deduplicarDiaFecha(s, patMes);
        s = s.replace(new RegExp('(\\d{1,2})\\s*<br\\s*/?>\\s*(' + patMes + ')', 'gi'), '$1 $2');
        if (fechaCompleta.test(s)) {
            return deduplicarDiaFecha(s, patMes);
        }
        var re = new RegExp('(^|>|&nbsp;|[\\s\\u00A0_\\.·…]+)(' + patMes + ')', 'gi');
        s = s.replace(re, function (all, pref, resto, offset) {
            var before = s.slice(Math.max(0, offset - 200), offset);
            before = before.replace(/<[^>]+>/g, '').replace(/&nbsp;/gi, ' ').replace(/\s+/g, ' ').trim();
            if (/\d{1,2}\s*$/i.test(before)) return all;
            return pref + dia + ' ' + resto;
        });
        return deduplicarDiaFecha(s, patMes);
    }
    function fill(html) {
        var t = tipoActual();
        var yo = window.SOL_YO || {};
        var meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
        var d = new Date();
        var dia = ('0' + d.getDate()).slice(-2);
        var mes = meses[d.getMonth()];
        var anio = String(d.getFullYear());
        var fechaLarga = dia + ' de ' + mes + ' del ' + anio;
        var map = {};
        map['@{{nombre}}'] = yo.nombre || '';
        map['@{{run}}'] = yo.run || '';
        map['@{{cargo}}'] = yo.cargo || '';
        map['@{{departamento}}'] = yo.departamento || '';
        map['@{{tipo_solicitud}}'] = t ? (t.nombre || '') : '';
        map['@{{fecha_inicio}}'] = fmtFecha($('#fecha_inicio').val());
        map['@{{fecha_termino}}'] = fmtFecha($('#fecha_termino').val());
        map['@{{total_dias}}'] = String(dias() || '');
        map['@{{jornada_inicio}}'] = String(($('#jornada_inicio').val() || '').toUpperCase());
        map['@{{jornada_termino}}'] = String(($('#jornada_termino').val() || '').toUpperCase());
        map['@{{jornada}}'] = (function () {
            if (esMediaJornada()) {
                var h = horarioFranja(franjaMedia());
                return 'media jornada ' + h.label + ' (' + h.permiso + ')';
            }
            var a = ($('#jornada_inicio').val() || '').toUpperCase();
            var b = ($('#jornada_termino').val() || '').toUpperCase();
            if (!a && !b) return '';
            if (a === b) return 'jornada ' + a;
            return (a || '—') + ' a ' + (b || '—');
        })();
        map['@{{horario_permiso}}'] = esMediaJornada() ? horarioFranja(franjaMedia()).permiso : '08:30 a 17:30';
        map['@{{horario_trabaja}}'] = esMediaJornada() ? horarioFranja(franjaMedia()).trabaja : '';
        map['@{{motivo}}'] = $('#motivo').val() || '';
        map['@{{explicacion}}'] = $('#motivo').val() || '';
        map['@{{viaticos_destino}}'] = $('#viaticos_destino').val() || '';
        map['@{{fecha}}'] = fechaLarga;
        map['@{{dia}}'] = dia;
        map['@{{mes}}'] = mes;
        map['@{{anio}}'] = anio;
        map['{t_anio}'] = anio;
        map['{t_dia}'] = dia;
        map['{t_mes}'] = mes;
        map['{dia}'] = dia;
        map['{mes}'] = mes;
        map['{t_fecha}'] = fechaLarga;
        map['{t_folio}'] = 'SIN FOLIO';
        map['@{{ha_solicitado}}'] = String(rrhh().ha);
        map['@{{solicita}}'] = String(rrhh().solicita);
        map['@{{saldo}}'] = String(rrhh().saldo);
        map['@{{total}}'] = String(rrhh().total);
        map['@{{alcalde_autorizado}}'] = '______';
        map['@{{alcalde_denegado}}'] = '______';
        map['@{{alcalde_observaciones}}'] = '________________';
        var out = normalizarLlavesToken(html || '');
        out = out.replace(/https?:\/\/[^"'\/\s>]+\/files\//gi, (window.location.origin || '') + '/files/');
        Object.keys(map).sort(function (a, b) {
            function rank(k) {
                if (/fecha/i.test(k)) return 0;
                if (/dia/i.test(k)) return 2;
                return 1;
            }
            return rank(a) - rank(b) || b.length - a.length;
        }).forEach(function (k) {
            out = out.split(k).join(map[k]);
        });
        out = completarDiaFecha(out, dia);
        return normalizarEstilosDoc(out);
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
        sincronizarMediaJornada();
        $('#ayuda-dias').text(permiteMedioDia(tVista)
            ? (esMediaJornada()
                ? 'Media jornada: 0,5 día. Horario completo 08:30 a 17:30.'
                : 'Día completo o active “Permiso por media jornada”.')
            : 'Lunes a viernes, sin feriados.');
        if (nDias === '' || nDias === null) {
            $('#total_dias_vista').val('—');
        } else if (esLic) {
            $('#total_dias_vista').val(nDias + ' día(s) corridos');
        } else if (nDias === 0.5) {
            $('#total_dias_vista').val('0,5 día (media jornada)');
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
    $('#btn-media-jornada').on('click', function () {
        if (!permiteMedioDia(tipoActual())) return;
        $('#media_jornada').val(esMediaJornada() ? '0' : '1');
        if (esMediaJornada() && !$('input[name=media_franja]:checked').length) {
            $('#media_am').prop('checked', true);
        }
        refrescarDoc(false);
    });
    $('input[name=media_franja]').on('change', function () { refrescarDoc(false); });
    $('#fecha_inicio').on('change', function () {
        if (esMediaJornada()) $('#fecha_termino').val($(this).val() || '');
    });
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
        sincronizarMediaJornada();
        if (esMediaJornada()) {
            var f = $('#fecha_inicio').val();
            $('#fecha_termino').val(f || '');
            var franja = franjaMedia();
            $('#jornada_inicio').val(franja);
            $('#jornada_termino').val(franja);
        }
        $('#id_buzon_destino_val').val($('#id_buzon_destino').val() || '');
        if (!$('#id_buzon_destino_val').val()) {
            e.preventDefault();
            alert('Elija el buzón del director.');
            return;
        }
        if (esMediaJornada() && dias() === '') {
            e.preventDefault();
            alert('Elija un día hábil para la media jornada.');
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
