@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')


    <div class="row">
        <div class="col-10">
            <h1>Buzón: {{$nombre_buzon}}</h1>
        </div>
        <div class="col-2">
            <button type="button" class="btn btn-success nuevo_documento">Nuevo Documento</button>
        </div>
    </div>
    <div class="linea_content_header"></div>

@stop

@section('content')
<!--<div class="container">-->

    <div class="row">
        <div class="col-12">

            <div class="accordion" id="carpetas">
                <div class="card">
                  <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                      <button class="btn btn-block text-left buzones_carpetas_btn_colapsable_text" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <span id="boton_carpetas_texto"> Carpetas - Por Recibir </span>
                        <i class="fa fa-chevron-circle-up" style="float:right;margin-top: 8px;"></i>
                        <i class="fa fa-chevron-circle-down" style="float:right;margin-top: 8px;"></i>
                      </button>
                    </h2>

                  </div>

                  <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#carpetas">
                    <div class="card-body">
                        <nav class="text-center">
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                              <a style="width: 33%" class="nav-item nav-link active" id="nav-por-recibir-tab" data-toggle="tab" href="#nav-por-recibir" role="tab" aria-controls="nav-home" aria-selected="true" onclick="cambio_texto_boton_carpetas('Por Recibir');">
                                Por Recibir
                                @if($n_docs_por_recibir>0)
                                <span class="badge badge-success right">
                                    {{$n_docs_por_recibir}}
                                </span>
                                @endif
                            </a>
                              <a style="width: 33%" class="nav-item nav-link" id="nav-recibidos-tab" data-toggle="tab" href="#nav-recibidos" role="tab" aria-controls="nav-profile" aria-selected="false" onclick="cambio_texto_boton_carpetas('Recibidos');">
                                Recibidos
                                @if($n_docs_recibidos_pendientes>0)
                                <span class="badge badge-success right">
                                    {{$n_docs_recibidos_pendientes}}
                                </span>
                                @endif
                              </a>
                              <a style="width: 33%" class="nav-item nav-link" id="nav-despachados-tab" data-toggle="tab" href="#nav-despachados" role="tab" aria-controls="nav-contact" aria-selected="false" onclick="cambio_texto_boton_carpetas('Despachados');">
                                Despachados</a>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-por-recibir" role="tabpanel" aria-labelledby="nav-por-recibir-tab">
                                <table id="grilla_por_recibir"  class="table dt-responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID Doc.</th>
                                            <th>F. Entrada</th>
                                            <th>Contestar Hasta</th>
                                            <th>TD</th>
                                            <th>TE</th>
                                            <th>Origen</th>
                                            <th>Materia</th>
                                        </tr>
                                    </thead>
                                </table>
                                {{-- Pagination --}}

                            </div>
                            </div>



                            <div class="tab-pane fade" id="nav-recibidos" role="tabpanel" aria-labelledby="nav-recibidos-tab">
                                <table id="grilla_recibidos"  class="table dt-responsive nowrap no-footer dtr-inline dataTable collapsed" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>E</th>
                                            <th>ID Doc</th>
                                            <th>Fecha Recepción</th>
                                            <th>Contestar Hasta</th>
                                            <th>TD</th>
                                            <th>TE</th>
                                            <th>Origen</th>
                                            <th>Materia</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                </table>


                            </div>
                            <div class="tab-pane fade" id="nav-despachados" role="tabpanel" aria-labelledby="nav-despachados-tab"  style="width: 100%;">
                                    <table id="grilla_despachados"  class="table dt-responsive nowrap no-footer dtr-inline dataTable collapsed" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>E</th>
                                                <th>ID Doc</th>
                                                <th width="100px">Fecha Despacho</th>
                                                <th width="50px">Fecha Recepción</th>
                                                <th>TD</th>
                                                <th>Destinatario</th>
                                                <th>Materia</th>
                                                <th>Rpta a</th>
                                                <th>Fecha Doc</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                    </table>
                            </div>
                        </div>



                    </div>
                  </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row" id="card_ver_documento" style="display:none">
        <div class="col-12">

            <div class="card" id="documento" style="display: none">
                <div class="card-header">
                    <h4>Documento</h4>
                    <div class="linea_content_header"></div>
                </div>
                <div class="card-body">

                </div>
              </div>

        </div>
    </div>

    <!-- **DOCUMENTOS** GRILLA CREAR DOCUMENTOS -->
    <div class="row" id="card_crear_documento" style="display:none">
        <div class="col-12">
            <div class="card">
                <div class="card-header" >
                    <h4>Nuevo Documento</h4>
                    <div class="linea_content_header"></div>
                </div>
                <div class="card-body">

                    <form class="needs-validation" id="form_crear_editar" method="POST" action="">
                        @csrf
                        <div class="container">
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
                                <div class="form-control">Buzón Origen: <i>{{ $nombre_buzon }}</i></div>
                                <div class="form-control">ID: <i><span id="idAsignado">No Asignado</span></i></div>
                                <div class="form-control">Folio: <i>No Asignado</i></div>
                                <div class="form-control">Fecha: <i>No Asignado</i></div>
                            </div>
                        </div>
                        <br>
                        <div class="form-row">
                            <div class="col-md-3 mb-3">
                                <label for="inputState">Tipo Documento:</label>
                                <select id="form_tipo_documento" name="tipo_documento" class="form-control">
                                    <option selected>Seleccionar</option>
                                    @foreach($listado_tiposdoc as $list)
                                    <option value="{{$list['id_tipo_documento']}}">{{$list['nombre']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="inputState">Nivel Acceso</label>
                                <select class="form-control" id="form_nivel_acceso" name="nivel_acceso" required>
                                    <option value="">Seleccionar</option>
                                    @foreach($nivel_acceso as $dato)
                                    <option value="{{$dato['id_nivel_acceso']}}">{{$dato['nombre']}}</option>
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="inputState">Efectos sobre terceros</label>
                                <select id="form_efectos_terceros" name="efectos_terceros" class="form-control">
                                    <option selected>Seleccionar</option>
                                    <option value="true">Si</option>
                                    <option value="false">No</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="inputState">Contestar/Hasta</label>
                                <input type="date" class="form-control" id="form_contestar_hasta" name="contestar_hasta">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4 mb-3">
                                <label for="inputState">Respuesta a:</label>
                                <select id="form_respuesta_a" name="respuesta_a" class="form-control">
                                    <option selected>Seleccionar</option>
                                    <option>...</option>
                                </select>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="inputState">Materia:</label>
                                <input type="text" class="form-control" id="form_materia" name="materia">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="inputState">Anterior:</label>
                                <input type="text" class="form-control" id="form_anterior" name="anterior">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="floatingTextarea">Descripción o Extracto</label>
                                <textarea class="form-control" id="form_descripcion" name="descripcion"></textarea>
                            </div>
                        </div>

                        <!--los campos cuerpo y anexo son los unicos que varian segun el documento por eso estan desactivado-->
                        <div class="form-row row_cuerpo" style="display:none">
                            <div class="col-md-12">
                                <label for="exampleFormControlTextarea1">Cuerpo:</label>
                                <textarea class="form-control" id="form_cuerpo" name="cuerpo"></textarea>
                                <input type="hidden" id="form_encabezado" name="encabezado">
                            </div>
                        </div>

                        <div class="form-group row_anexo">
                            <label for="exampleFormControlTextarea1">Anexo:</label>
                            <textarea class="form-control" id="form_anexo" rows="4" disabled="disabled"></textarea>
                            <div class="card-body" id="cargar_anexo" style="display:none">
                                <form action="{{route('files.store')}}"
                                    method="POST"
                                    class="dropzone"
                                    id="dropzone-anexo">
                                </form>
                            </div>

                        </div>

                        <div class="form-group row_arch_ppal">
                            <label for="exampleFormControlTextarea1">Archivo Principal</label>
                            <textarea class="form-control" id="form_archivo_principal_el" rows="4" disabled="disable"></textarea>

                            <div class="card-body" id="cargar_archivo_principal_el" style="display:none">
                                <form action="{{route('files.store')}}"
                                    method="POST"
                                    class="dropzone"
                                    id="dropzone-archivo-ppal">
                                </form>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">Otros Archivos</label>
                            <textarea class="form-control" id="form_otros_archivos_el" rows="4" disabled="disabled"></textarea>

                            <div class="card-body" id="cargar_otros_archivos" style="display:none">
                                <form action="{{route('files.store')}}"
                                    method="POST"
                                    class="dropzone"
                                    id="dropzone-otros-archivos">
                                </form>
                            </div>
                        </div>

                        <div class="form-row">

                            <div class="col-md-8 mb-3">
                                <label for="inputState">Destinatario Principal:</label>
                                <input type="text" class="form-control" id="form_destinatario_principal_el" data-role="tagsinput" disabled="false">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="inputState">Acciones Solicitadas:</label>
                                <select id="form_acciones_solicitadas_el" class="form-control" disabled="false">
                                    <option>Seleccionar acciones</option>
                                </select>
                                </div>
                        </div>
                        <div class="form-floating">
                            <label for="floatingTextarea">Comentario a Destinatario Principal:</label>
                            <textarea class="form-control"  id="form_comentario_el" disabled="false"></textarea>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="inputState">Otro(s) Destinatario(s):</label>
                                <input type="text" class="form-control" id="form_otros_destinatarios_el" data-role="tagsinput"  disabled="false">
                            </div>
                        </div>
                        <div class="form-floating">
                            <label for="floatingTextarea">Comentario(s) Otro(s) Destinatario(s)</label>
                            <textarea class="form-control"  id="form_comentario_otro_el" disabled="false"></textarea>
                        </div>
                        <br>

                        <div class="form-row">
                            <div class="col-md-9"> </div>
                            <div class="col-md-1">
                                <button type="button"  class="btn btn-secondary w-100 btn_cerrar_guardar">Cerrar</button>
                            </div>
                            <div class="col-md-2 btn-toolbar">
                                <button type="button" class="btn btn-success btn-guardar-submit w-50">Guardar</button>
                                <button type="button" class="btn btn-success btn-enviar-submit w-50" style="display:none">Enviar</button>  
                                <input type="hidden" name="hiddIdDocumento" id="hiddIdDocumento" value="">
                                <input type="hidden" name="hiddIdDocumentoBuzon" id="hiddIdDocumentoBuzon" value="">
                                <input type="hidden" name="hiddIdBuzon" id="hiddIdBuzon" value="{{$id_buzon}}">
                                <input type="hidden" name="hiddIdUsuario" id="hiddIdUsuario" value="">
                                <input type="hidden" name="hiddIdOrigen" id="hiddIdOrigen" value="">
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
    <!-- **DOCUMENTOS** GRILLA CREAR DOCUMENTOS -->

@stop

@section('css')

    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" integrity="sha512-jU/7UFiaW5UBGODEopEqnbIAHOI8fO6T99m7Tsmqs2gkdujByJfkCbbfPSN4Wlqlb9TGnsuC0YgUgWkRBK7B9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">

    <style type="text/css">

        .flex-container {
            display: flex;
            flex-wrap: nowrap;
            background-color: rgb(208, 228, 191);
            border: 1px solid #5a8fc7;
        }

        .flex-container > div {
            background-color: #b5e4b9;
            border: 1px solid #5a8fc7;
            width: 100px;
            margin: 15px;
            text-align: center;
            line-height: 20px;
            font-size: 15px;
        }

        .label-info {
            background-color:#5bc0de
        }
        .label-info[href]:focus,
        .label-info[href]:hover {
            background-color:#31b0d5
        }
        .label {
            display: inline-block;
            padding: .25em .4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;

        }

     </style>
@stop

@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js" integrity="sha512-oQq8uth41D+gIH/NJvSJvVB85MFk1eWpMK6glnkg6I7EdMqC1XVkW7RxLheXwmFdG03qScCM7gKS/Cx3FYt7Tg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('/vendor/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>

<script>
    //globales
    
    var grilla_por_recibir;
    var grilla_recibidos;
    var grilla_despachados;

    const accionesFlujo2 = @json($acciones_tipoflujo2);
    const accionesFlujo3 = @json($acciones_tipoflujo3);
    var allBuzones = @json($allBuzones);
    const listadoBuzones = @json($listadoBuzones);

    var idTipoFlujo = "";
    //tags input

    var allBuzones = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: allBuzones
    });
    allBuzones.initialize();

    $('#form_destinatario_principal_el').tagsinput({
        maxTags: 1,
        itemValue: 'value',
        itemText: 'text',
        typeaheadjs: {
            name: 'allBuzones',
            displayKey: 'text',
            source: allBuzones.ttAdapter()
        }
    });

    $('#form_otros_destinatarios_el').tagsinput({
        itemValue: 'value',
        itemText: 'text',
        typeaheadjs: {
            name: 'allBuzones',
            displayKey: 'text',
            source: allBuzones.ttAdapter()
        }
    });

    //dropzone

    //desactivar opciones de el formulario documento

    form_anexo.disabled=true;
    form_archivo_principal_el.disabled=true;
    form_otros_archivos_el.disabled=true;
    form_destinatario_principal_el.disabled=true;
    form_acciones_solicitadas_el.disabled=true;
    form_comentario_el.disabled=true;
    form_otros_destinatarios_el.disabled=true;
    form_comentario_otro_el.disabled=true;


    //dropzone

    var idDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

    Dropzone.options.dropzoneAnexo = {
        headers:{
            'X-CSRF-TOKEN' : "{{csrf_token()}}"
        },
        dictDefaultMessage: "Arrastre y suelte archivos aquí",
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        params: {id_documento_buzon: idDocumentoBuzon, 'id_tipo_archivo' : 2},
        createImageThumbnails: true,
        timeout: 50000,
    };

    Dropzone.options.dropzoneOtrosArchivos = {
        headers:{
            'X-CSRF-TOKEN' : "{{csrf_token()}}"
        },
        dictDefaultMessage: "Arrastre y suelte archivos aquí",
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        params: {id_documento_buzon: idDocumentoBuzon, 'id_tipo_archivo' : 3},
        createImageThumbnails: true,
        timeout: 50000,
    };

    Dropzone.options.dropzoneArchivoPpal = {
        headers:{
            'X-CSRF-TOKEN' : "{{csrf_token()}}"
        },
        maxFiles: 1,
        dictDefaultMessage: "Arrastre y suelte archivos aquí",
        acceptedFiles: "image/*",
        addRemoveLinks: true,
        params: {'id_documento_buzon': idDocumentoBuzon, 'id_tipo_archivo' : 1},
        createImageThumbnails: true,
            timeout: 50000,
    };


    $(".boton_desplegar_versiones_anteriores").click(function(e){
        $('#card_ocultar_versiones').show();
        $('#card_desplegar_versiones').hide();
    });

    $(".boton_ocultar_versiones_anteriores").click(function(e){
        $('#card_ocultar_versiones').hide();
        $('#card_desplegar_versiones').show();

    });

    /* **DOCUMENTOS** SCRIPT */

    const editor_cuerpo = CKEDITOR.replace('form_cuerpo');

    $(".nuevo_documento").click(function(e){

        $('#card_crear_documento').show();
        $('#form_crear_editar').trigger("reset");
    });

    $("#form_tipo_documento").change(function(){
        datosTipoDoc($(this).val());
    });

    function datosTipoDoc(id)
    {
        $.ajax({
                url: "../tipos_documentos/"+id,
                type:'GET',
                dataType: 'json',
                success: function(data) {
                    if(data.status=='400') {
                        toastr.error(data.data.comentario,"Aviso!");
                    }
                    else {
                        if(data.status=='200' || data.status=='201')
                        {
                            $("input[name='encabezado']").val(data.data.plantilla_encabezado);
                            $("input[name='hiddIdOrigen']").val(data.data.id_tipo_origen);

                            idTipoFlujo = data.data.id_tipo_flujo;

                            console.log(idTipoFlujo);
                            //editor_encabezado.setData(data.data.plantilla_encabezado);
                            editor_cuerpo.setData(data.data.plantilla_cuerpo);

                            if (data.data.id_tipo_origen == 1) //interno
                            {
                                $('.row_cuerpo').show();
                                $('.row_arch_ppal').hide();
                                $('.row_anexo').show();
                            }
                            if (data.data.id_tipo_origen == 2) //externo
                            {
                                $('.row_cuerpo').hide();
                                $('.row_arch_ppal').show();
                                $('.row_anexo').hide();
                            }
                        }
                    }
                    $('.btn-guardar-submit').prop("disabled", false);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    toastr.error("Falla al obtener datos","Aviso!");
                    $('.btn-guardar-submit').prop("disabled", false);

                }
            });
    }

    //SUBMIT
    $(".btn-guardar-submit").click(function(e)
    {
        e.preventDefault();

        $('.btn-guardar-submit').html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardar'
        );

        $(".print-error-msg").hide();

        guarda_documento();

    });

    $(".btn-enviar-submit").click(function(e)
    {
        e.preventDefault();

        $('.btn-enviar-submit').html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviar'
        );

        $(".print-error-msg").hide();

        guarda_documento();
        enviar_documento();

    });

    function guarda_documento()
    {
        var _token = $("input[name='_token']").val();
        var tipo_documento = $("select[name='tipo_documento']").val();
        var nivel_acceso = $("select[name='nivel_acceso']").val();
        var efectos_terceros = $("select[name='efectos_terceros']").val();
        var contestar_hasta = $("input[name='contestar_hasta']").val();
        var materia = $("input[name='materia']").val();
        var anterior = $("input[name='anterior']").val();
        var descripcion = $("textarea[name='descripcion']").val();

        var encabezado = $("input[name='encabezado']").val();
        var cuerpo = editor_cuerpo.getData();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();


        var destinatarioPrincipal = $('#form_destinatario_principal_el').val();
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();//.tagsinput('items')
        var comentarioPrincipal = $('#form_comentario_el').val();
        var comentarioOtros = $('#form_comentario_otro_el').val();

        if (hiddIdDocumento == '') //crear
        {
            var urlAccion = "{{route('buzones.store_documento')}}";
            var typeAccion = 'POST';
        }
        else //editar
        {
            var urlAccion = "{{route('buzones.update_documento')}}";
            var typeAccion = 'PUT';
        }

        $.ajax({
            url: urlAccion,
            type: typeAccion,
            dataType: 'json',
            data: {
                _token:_token,
                tipo_documento:tipo_documento,
                nivel_acceso:nivel_acceso,
                descripcion:descripcion,
                efectos_terceros:efectos_terceros,
                contestar_hasta:contestar_hasta,
                materia:materia,
                anterior:anterior,
                encabezado:encabezado,
                cuerpo:cuerpo,
                buzon:hiddIdBuzon,
                destinatarioPrincipal:destinatarioPrincipal,
                destinatarioOtros:otrosDestinatarios,
                comentarioPrincipal:comentarioPrincipal,
                comentarioOtros:comentarioOtros,
                hiddIdDocumento:hiddIdDocumento,
                hiddIdDocumentoBuzon:hiddIdDocumentoBuzon
            },
            success: function(data)
            {

                if(data.status == '200')
                {
                    toastr.success("Tipo de Documento actualizado","Aviso!");

                }
                else if(data.status == '201')
                {
                    Swal.fire({
                    icon: 'info',
                    title: 'Borrador guardado',
                    html: "Se ha guardado exitosamente el borrador del documento: <br>" +
                          "<b>ID: " + data.data.identificador + "</b><br>" +
                          "<b>Materia: " + data.data.materia + "</b>",
                    });

                    $('#form_tipo_documento').prop("disabled", true);

                    $("input[name='hiddIdDocumentoBuzon']").val(data.data.rel_documento_buzon[0]['id_documento_buzon']);
                    $("input[name='hiddIdDocumento']").val(data.data.id_documento);
                    $("#idAsignado").text(data.data.identificador);

                    //habilita para guardar documentos

                    if ($("input[name='hiddIdOrigen']").val() == 1)
                    {
                        $('#form_anexo').hide();
                        $('#cargar_anexo').show();
                    }

                    if ($("input[name='hiddIdOrigen']").val() == 2)
                    {
                        $('#form_archivo_principal_el').hide();
                        $('#cargar_archivo_principal_el').show();
                    }


                    $('#form_otros_archivos_el').hide();
                    $('#cargar_otros_archivos').show();

                    $('#form_destinatario_principal_el').prop("disabled", false);
                    //$('#form_acciones_solicitadas_el').prop("disabled", false);
                    $('#form_comentario_el').prop("disabled", false);
                    $('#form_otros_destinatarios_el').prop("disabled", false);
                    $('#form_comentario_otro_el').prop("disabled", false);

                    //habilita botón enviar

                    if ($("input[name='hiddIdDocumento']").val() != '')
                        $('.btn-enviar-submit').show();

                    //completar select form_acciones_solicitadas_el - se obtienen las acciones del orden 1 del flujo de buzones

                    var json_tipo_doc = $.parseJSON(data.data.json_tipo_documento);
                    var jsonAcciones = json_tipo_doc['buzones_flujo'];

                    for (let i in jsonAcciones) {
                        if (jsonAcciones[i].orden == 1)
                        {
                            var aAcciones = jsonAcciones[i].acciones;
                            var idBuzonAccion = jsonAcciones[i].id_buzon;
                        }
                    }

                    //obtener todas las acciones asociadas al tipo de flujo asociado al tipo de documento => idTipoFlujo

                    if (idTipoFlujo == 2)
                    {
                        for (let i in accionesFlujo2)
                        {
                            if (accionesFlujo2[i][0] == aAcciones[0]['id_accion'])
                                $('#form_acciones_solicitadas_el').append("<option selected value='"+accionesFlujo2[i][0]+"' >"+accionesFlujo2[i][1]+"</option>");
                        }
                    }
                    if (idTipoFlujo == 3)
                    {
                        for (let i in accionesFlujo3)
                        {
                            if (accionesFlujo3[i][0] == aAcciones[0]['id_accion'])
                                $('#form_acciones_solicitadas_el').append("<option selected value='"+accionesFlujo3[i][0]+"' >"+accionesFlujo3[i][1]+"</option>");
                        }
                    }

                    //agrega primer elemento en destinatario principal
                    var nombreBuzon = listadoBuzones[idBuzonAccion];
                    $('#form_destinatario_principal_el').tagsinput('add', {"value": idBuzonAccion, "text": nombreBuzon});

                    //actualiza grilla despachados

                    fn_grilla_despachados();


                }
                else
                {
                    toastr.error(data.data.comentario,"Aviso!");
                }

                $('.btn-guardar-submit').html( 'Guardar' );
            },
            error: function (jqXHR, textStatus, errorThrown) {

                toastr.error("Falla en el tipo de documento","Aviso!");

                $('.btn-guardar-submit').html( 'Guardar' );
            }

        });


    }

    function enviar_documento()
    {
        console.log('envia documento');
    }

    /* **DOCUMENTOS** SCRIPT */




    function cambio_texto_boton_carpetas(texto){
        $('#documento').hide();

        if(texto.length>20 || texto.length==0 ){
            texto='';
        }
        $('#boton_carpetas_texto').html('Carpetas - '+texto);
        if(texto=='Recibidos'){
            $('#grilla_recibidos').DataTable().draw();
        }
        if(texto=='Despachados'){
            $('#grilla_despachados').DataTable().draw();
            $(".nuevo_documento").removeAttr('disabled');
        }else{
            $(".nuevo_documento").prop("disabled", true);
        }
    }

    function mostrar_documento(texto){
        $('#documento .card-title').html('Documento: '+texto);
        $('#documento').show();
    }

    function ver_recibidos(identificador){
        alert(identificador)
    }
    function responder_recibidos(identificador){
        alert(identificador)
    }
    function derivar_recibidos(identificador){
        alert(identificador)
    }
    function archivar_recibidos(identificador){
        alert(identificador)
    }
    function bitacora_recibidos(identificador){
        alert(identificador)
    }
    function favorito_recibidos(identificador){
        alert(identificador)
    }

    function ver_despachados(identificador){
        alert(identificador)
    }
    function editar_despachados(identificador){
        alert(identificador)
    }
    function eliminar_despachados(identificador){
        alert(identificador)
    }



    async function fn_grilla_por_recibir(){
            $('#documento').hide();
            if ( $.fn.DataTable.isDataTable('#grilla_por_recibir') ) {
                $('#grilla_por_recibir').DataTable().destroy();
            }
        $('#grilla_por_recibir tbody').empty();

        grilla_por_recibir=  $('#grilla_por_recibir').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/buzonesListar?id_buzon={{$id_buzon}}&id_carpeta=1',
                type:'json',
                responsive: true,
                language: lenguaje_datatable,
                columns: [
                    { data: 'id_documento', name: 'id_documento' },
                    { data: 'fecha_despacho', name: 'fecha_despacho' },
                    { data: 'contestas_hasta', name: 'contestas_hasta' },
                    { data: 'tipo_documento', name: 'tipo_documento' },
                    { data: 'tipo_envio', name: 'tipo_envio' },
                    { data: 'origen', name: 'origen' },
                    { data: 'materia', name: 'materia' },
                ]
            });
    }




    async function fn_grilla_recibidos(){

        $('#documento').hide();
        if ( $.fn.DataTable.isDataTable('#grilla_recibidos') ) {
                $('#grilla_recibidos').DataTable().destroy();
        }
        $('#grilla_recibidos tbody').empty();

        grilla_recibidos = $('#grilla_recibidos').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/buzonesListar?id_buzon={{$id_buzon}}&id_carpeta=2',
                type:'json',
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true,
                language: lenguaje_datatable,
                columns: [
                    { data: 'recibido',
                    render: function(data, type) {
                        if (type === 'display') {
                            if(data==null){
                                return '';
                            }else{
                                if(data==true){
                                    return '<span class="fas fa-check text-green"></span>';
                                }
                            }
                        }
                        return '';
                    }
                    },
                    { data: 'estado_documento', name: 'estado_documento' },
                    { data: 'id_documento', name: 'id_documento' },
                    { data: 'fecha_recepcion', name: 'fecha_recepcion' },
                    { data: 'contestas_hasta', name: 'contestas_hasta' },
                    { data: 'tipo_documento', name: 'tipo_documento' },
                    { data: 'tipo_envio', name: 'tipo_envio' },
                    { data: 'origen', name: 'origen' },
                    { data: 'materia', name: 'materia' },
                    { data: 'id_buzon',
                    render: function(data, type) {
                        if (type === 'display') {
                            if(data==null){
                                return '';
                            }else{
                                let botonera = '<div class="dropdown">';
                                    botonera += '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                        botonera +=' <i class="fas fa-bars"></i>';
                                        botonera +=' </button>';
                                        botonera +='<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos('+data+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                            botonera +=' <a class="dropdown-item btn-menu-editar" onclick="responder_recibidos('+data+')"  href="#"><i class="fas fa-reply text-orange"></i> Responder</a>';
                                            botonera +=' <a class="dropdown-item btn-menu-editar" onclick="derivar_recibidos('+data+')"  href="#"><i class="fas fa-share text-green"></i> Derivar</a>';
                                            botonera +=' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos('+data+')"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>';
                                            botonera +=' <a class="dropdown-item btn-menu-editar" onclick="bitacora_recibidos('+data+')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';
                                            botonera +='<a class="dropdown-item btn-menu-deshabilitar" onclick="favorito_recibidos('+data+')" href="#">';
                                                // if($list['id_doc']==1)
                                                botonera +='<i class="far fa-star text-green"></i> ( + ) Favoritos';
                                                // endif
                                                // if($list['id_doc']==2)
                                                    //  <i class="fas fa-star text-green"></i>  ( - ) Favoritos
                                                    //endif
                                            botonera +='</a>';


                                    botonera += '</div>';
                                    botonera += '</div>';
                                return botonera;
                            }
                        }
                        return '';
                    }
                    }
                ]
        });
    }



    async function fn_grilla_despachados(){
    $('#documento').hide();
    if ( $.fn.DataTable.isDataTable('#grilla_despachados') ) {
            $('#grilla_despachados').DataTable().destroy();
    }
    $('#grilla_despachados tbody').empty();
    grilla_despachados=  $('#grilla_despachados').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/buzonesListar?id_buzon={{$id_buzon}}&id_carpeta=3',
            type:'json',
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            language: lenguaje_datatable,
            columns: [
                { data: 'recibido',
                  render: function(data, type) {
                    if (type === 'display') {
                        if(data==null){
                            return '';
                        }else{
                            if(data==true){
                                return '<span class="fas fa-check text-green"></span>';
                            }
                        }
                    }
                    return '';
                  }
                },
                { data: 'estado_documento', name: 'estado_documento' },
                { data: 'id_documento', name: 'id_documento' },
                { data: 'fecha_despacho', name: 'fecha_despacho' },
                { data: 'fecha_recepcion', name: 'fecha_recepcion' },
                { data: 'tipo_documento', name: 'tipo_documento' },
                { data: 'destinatario', name: 'destinatario' },
                { data: 'materia', name: 'materia' },
                { data: 'respuesta_a', name: 'respuesta_a' },
                { data: 'fecha_documento', name: 'fecha_documento' },
                { data: 'id_buzon',
                  render: function(data, type) {
                    if (type === 'display') {
                        if(data==null){
                            return '';
                        }else{
                            let botonera = '<div class="dropdown">';
                                botonera += '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                    botonera +=' <i class="fas fa-bars"></i>';
                                    botonera +='                 </button>';
                                    botonera +='                 <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                                    botonera +='                     <a class="dropdown-item btn-menu-ver" onclick="ver_despachados('+data+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                    botonera +='                    <a class="dropdown-item btn-menu-editar" onclick="editar_despachados('+data+')"  href="#"><i class="fas fa-edit text-blue"></i> Editar</a>';
                                    botonera +='                     <a class="dropdown-item btn-menu-editar" onclick="eliminar_despachados('+data+')"  href="#"><i class="fas fa-trash-alt text-red"></i> Eliminar</a>';
                                    botonera +='</div>';
                                botonera += '</div>';
                            return botonera;
                        }
                    }
                    return '';
                  }
                }
            ]
        });
    }

$(document).ready(function () {
    $(".nuevo_documento").prop("disabled", true);

    //var div_por_recibir_width = document.getElementById('nav-por-recibir').getBoundingClientRect().width;
    //$('#nav-despachados').attr("style","width:"+div_por_recibir_width+'px');
    //$('#nav-recibidos').attr("style","width:"+div_por_recibir_width+'px');

    $(function() {

        fn_grilla_por_recibir();

        $('#grilla_por_recibir tbody').on( 'click', 'tr', function () {
            td_seleccionado=grilla_por_recibir.row( this ).data();
            mostrar_documento(td_seleccionado['materia']);
        });

        fn_grilla_recibidos();

        fn_grilla_despachados();

    });
 });


</script>
@stop
