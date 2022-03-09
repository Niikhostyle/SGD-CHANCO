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
                        <span id="boton_carpetas_texto"> Carpetas - <i><b>Por Recibir</b></i> </span>
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
                            <div class="tab-pane fade" id="nav-recibidos" role="tabpanel" aria-labelledby="nav-recibidos-tab">
                                <table border="0" cellspacing="5" cellpadding="5">
                                    <tbody>
                                        <tr>
                                            <td>ID Doc:</td>
                                            <td>Tipo Documento:</td>
                                            <td>Estado:</td>
                                            <td>Texto Libre:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><input class="form-control"  type="text" id="gr_buscar_id_doc" name="gr_buscar_id_doc"></td>
                                            <td>
                                                <select id="gr_buscar_tipo_doc" name="gr_buscar_tipo_doc" class="form-control">
                                                    <option value=''>Todos</option>
                                                    @foreach($listado_tiposdoc as $list)
                                                    <option value="{{$list['id_tipo_documento']}}">{{$list['nombre']}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control"  id="gr_buscar_estado" name="gr_buscar_estado" >
                                                    <option value=''> Todos </option>
                                                    @foreach($listado_parametros['estado_documento'] as $estado_documento)
                                                        <option value='{{$estado_documento['nombre_corto']}}'> {{$estado_documento['nombre']}} </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="search" aria-controls="grilla_recibidos" class="form-control"  id="gr_buscar_origen_materia" name="gr_buscar_origen_materia"></td>
                                            <td id="botones_grilla_recibidos">
                                            </td>
                                        </tr>
                                </tbody></table>
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
                                <table border="0" cellspacing="5" cellpadding="5">
                                    <tbody>
                                        <tr>
                                            <td>ID Doc:</td>
                                            <td>Tipo Documento:</td>
                                            <td>Estado:</td>
                                            <td>Texto Libre:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><input class="form-control"  type="text" id="gd_buscar_id_doc" name="gd_buscar_id_doc"></td>
                                            <td>
                                                <select id="gd_buscar_tipo_doc" name="gd_buscar_tipo_doc" class="form-control">
                                                    <option value=''>Todos</option>
                                                    @foreach($listado_tiposdoc as $list)
                                                    <option value="{{$list['id_tipo_documento']}}">{{$list['nombre']}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control"  id="gd_buscar_estado" name="gd_buscar_estado" >
                                                    <option value=''> Todos </option>
                                                    @foreach($listado_parametros['estado_documento'] as $estado_documento)
                                                        <option value='{{$estado_documento['nombre_corto']}}'> {{$estado_documento['nombre']}} </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="search" aria-controls="grilla_despachados" class="form-control"  id="gd_buscar_destino_materia" name="gd_buscar_destino_materia"></td>
                                            <td id="botones_grilla_despachados">
                                            </td>
                                        </tr>
                                    </tbody></table>
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

    <!-- **DOCUMENTOS** GRILLA CREAR DOCUMENTOS -->
    <div class="row" id="card_crear_documento" style="display:none">
        <div class="col-12">
            <div class="card">
                <div class="card-header" >
                    <h4 id="titulo_accion">Nuevo Documento</h4>
                    <div class="linea_content_header"></div>
                </div>
                <div class="card-body">

                    <form class="needs-validation" id="form_crear_editar" method="POST" action="">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12">
                                <ul class="list-group list-group-horizontal">
                                    <li class="list-group-item col-md-6"><b>Buzón Origen:</b> <i>{{ $nombre_buzon }}</i></li>
                                    <li class="list-group-item col-md-2"><b>ID:</b> <i><span id="idAsignado">No Asignado</span></i></li>
                                    <li class="list-group-item col-md-2"><b>Folio:</b> <i>No Asignado</i></li>
                                    <li class="list-group-item col-md-2"><b>Fecha:</b> <i>No Asignado</i></li>
                                </ul>
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
                                <select id="form_respuesta_a" name="respuesta_a" class="form-control" multiple="multiple" style="text-align:left !important">
                                    @foreach($listDocPendientesBuzon as $doc)
                                        <option value="{{$doc['value']}}">{{$doc['title']}}</option>
                                    @endforeach
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
                        <div style="display:none">
                            <div class="col-md-12">
                                <form> </form>
                            </div>
                        </div>
                        <div class="form-group row_anexo">
                            <label for="exampleFormControlTextarea1">Anexos:</label>
                            
                            <div class="card-body card-archivos" id="cargar_anexo">
                                <div id="dropzone-anexo-view" class="dropzone-view"></div>
                                <div id="dropzone-anexo" class="dropzone dropzone-files"></div>                                                          
                            </div>

                        </div>

                        <div class="form-group row_arch_ppal">
                            <label for="exampleFormControlTextarea1">Archivo Principal</label>
                            
                            <div class="card-body card-archivos" id="cargar_principal">
                                <div id="dropzone-principal-view" class="dropzone-view"></div>
                                <div id="dropzone-principal" class="dropzone dropzone-files"></div>                                                          
                            </div>

                        <!--    <div class="card-body" id="cargar_archivo_principal_el" style="display:none">
                                <form action=""
                                    method="POST"
                                    class="dropzone"
                                    id="dropzone-archivo-ppal">
                                </form>
                            </div>-->
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">Otros Archivos</label>
                            
                            <div class="card-body card-archivos" id="cargar_otros">
                                <div id="dropzone-otros-view" class="dropzone-view"></div>
                                <div id="dropzone-otros" class="dropzone dropzone-files"></div>                                                          
                            </div>
                            <!--
                            <textarea class="form-control" id="form_otros_archivos_el" rows="4" disabled="disabled"></textarea>

                            <div class="card-body" id="cargar_otros_archivos" style="display:none">
                                <form action=""
                                    method="POST"
                                    class="dropzone"
                                    id="dropzone-otros-archivos">
                                </form>
                            </div> -->
                        </div>

                        <div class="form-row">
                            <div class="col-md-8 mb-3">
                                <label for="inputState">Destinatario Principal:</label><br>
                                <!--<input type="text" class="form-control" id="form_destinatario_principal_el" data-role="tagsinput" disabled="false">-->
                                <select class="form-control" style="width: 100%" id="form_destinatario_principal" name="form_destinatario_principal" multiple="multiple">
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="inputState">Acciones Solicitadas:</label><br>
                                <select id="form_acciones_solicitadas_el" class="form-control" multiple="multiple" style="text-align:left !important" disabled="false">                                    
                                    @foreach($listadoAcciones as $accion)
                                        @if($accion['id_tipo_accion'] == 1)
                                            <option value="{{$accion['id_accion']}}">{{$accion['nombre']}}</option>
                                        @endif    
                                    @endforeach    
                                </select>
                                </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="floatingTextarea">Comentario a Destinatario Principal:</label>
                                <textarea class="form-control"  id="form_comentario_el" disabled="false"></textarea>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="inputState">Otro(s) Destinatario(s):</label>
                                <input type="text" class="form-control" id="form_otros_destinatarios_el" data-role="tagsinput"  disabled="false">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <label for="floatingTextarea">Comentario(s) Otro(s) Destinatario(s)</label>
                                <textarea class="form-control" id="form_comentario_otro_el" disabled="false"></textarea>
                            </div>
                        </div>
                        <div class="form-row row_archivar">
                            <div class="col-md-12">
                                <label for="floatingTextarea">Ingrese fundamentación para archivar</label>
                                <textarea class="form-control" id="form_comentario_archivar"></textarea>
                            </div>
                        </div>
                        
                        <div class="form-row">                                
                                <div class="col-md-12 group-button-align">                                    
                                    <button type="button"  class="btn btn-secondary w-10 btn_cerrar_guardar">Cerrar</button>
                                    <button type="button" id="submit-all" class="btn btn-success btn-guardar-submit w-10">Guardar</button>
                                    <button type="button" class="btn btn-success btn-enviar-submit w-10" style="display:none">Enviar</button>
                                    <span class="w-10" id="addButton"></span>
                                    <input type="hidden" name="hiddIdDocumento" id="hiddIdDocumento" value="">
                                    <input type="hidden" name="hiddIdDocumentoBuzon" id="hiddIdDocumentoBuzon" value="">
                                    <input type="hidden" name="hiddIdBuzon" id="hiddIdBuzon" value="{{$id_buzon}}">
                                    <input type="hidden" name="hiddIdOrigen" id="hiddIdOrigen" value="">
                                    <input type="hidden" name="hiddIdFileDelete" id="hiddIdFileDelete" value="">

                                </div>                          
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
    <!-- **DOCUMENTOS** GRILLA CREAR DOCUMENTOS -->

    <!-- Bitacora-->  
        
    <div class="card" id="card_bitacora"  style="display:none">
        <div class="card-header" >
            <h4 id="titulo_accion">Bitácora</h4>
            <div class="linea_content_header"></div>
        </div>
    <div class="card-body">
        <div class="col"><b>ID: <span id="idAsignado2"></span></b></div>
        <div class="col"><b>Materia: <span id="textMateria"></span></b></div>
        <br>
      
        <div class="form-check" style="padding-right: 5px;">
            <input class="form-check-input" type="checkbox" value="DDP" name="buscar_accion" id="accion_ddp">
            <label class="form-check-label" for="defaultCheck1" >
                Derivaciones destinatarios principales (DDP)
            </label>
        </div>
            <div class="form-check" >
            <input class="form-check-input" type="checkbox" value="DOO" name="buscar_accion" id="accion_doo">
            <label class="form-check-label" for="defaultCheck1">
                Derivaciones otros destinatarios (DOO)
            </label>
            </div>
            <div class="form-check" >
            <input class="form-check-input" type="checkbox" value="CAP" name="buscar_accion" id="accion_cap">
            <label class="form-check-label" for="defaultCheck1">
                Cambios Archivos Principal (CAP)
            </label>
            </div>
           
            <div class="card-body">
                <table id="tabla_bitacora_grilla" class="table dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Buzón Origen</th>
                            <th>Acción </th>
                            <th>Mensaje</th>
                            
                        </tr>

                    </thead>                    
                </table>
            </div> 
            
            <div class="form-row">                                
                <div class="col-md-12 group-button-align">                                    
                    <button type="button" class="btn btn-secondary w-10 btn_cerrar_bitacora">Cerrar</button>                   
                </div>                          
        </div>
        </div>
    </div>
   
    <!-- Bitacora fin-->   


@stop

@section('css')

    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" integrity="sha512-jU/7UFiaW5UBGODEopEqnbIAHOI8fO6T99m7Tsmqs2gkdujByJfkCbbfPSN4Wlqlb9TGnsuC0YgUgWkRBK7B9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
    <link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css"/>

    <style type="text/css">
   
        .card {
            overflow: visible !important;
        }

        .dropzone {
            border: 2px dashed #ced4da;
        }

        .card-archivos {
            display: flex;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .dropzone-files {
            flex:1;
        }

        .dz-max-files-reached {
            pointer-events: none;
            cursor: default;
        }
        .dz-remove { 
            pointer-events: all; cursor: default; 
        }

        .disabled {
            background-color: #e9ecef !important;
        }

        .row_archivar {
            display:none;
        }

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
            font-size: 90%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;

        }

        .label-danger {
            background-color: #d9534f;
        }

        .label-warning{background-color:#f0ad4e;}


        .file-container-all { display: flex; }
        .file-container { position: relative; }
        .file-container img { display: block; }
        .fa-icon1 { position: absolute; bottom:0; left:0; }
        .fa-icon2 { position: absolute; bottom:0; left:30px; }

        .odd:hover, .even:hover{
            background: whitesmoke;
        }

     </style>
@stop

@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js" integrity="sha512-oQq8uth41D+gIH/NJvSJvVB85MFk1eWpMK6glnkg6I7EdMqC1XVkW7RxLheXwmFdG03qScCM7gKS/Cx3FYt7Tg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('/vendor/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script src="/js/fglobales.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>


<script>
    //globales

    var grilla_por_recibir;
    var grilla_recibidos;
    var grilla_despachados;

    const accionesFlujo1 = @json($acciones_tipoflujo1);
    const accionesFlujo2 = @json($acciones_tipoflujo2);
    const accionesFlujo3 = @json($acciones_tipoflujo3);
    const listadoBuzones = @json($listadoBuzones);
    const pathFiles = "";

    var allBuzonesT2 = @json($allBuzonesT2);
    var allBuzones = @json($allBuzones);
    var allBuzones2 = @json($allBuzones2);
    var listadoDocPendientes = @json($listDocPendientesBuzon);
    var idTipoFlujo = "";   

    $('#form_acciones_solicitadas_el').multiselect({
        nonSelectedText: 'Seleccione Acciones',
        numberDisplayed: 6,
        buttonWidth: '100%'
    });

    $('#form_respuesta_a').multiselect({
        nonSelectedText: 'Seleccione Documentos',
        numberDisplayed: 4,
        buttonWidth: '100%'        
    });

    var allBuzones = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: allBuzones
    });
    allBuzones.initialize();    

    var allBuzonesT2 = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: allBuzonesT2
    });
    allBuzonesT2.initialize();    
    
    $('#form_destinatario_principal').select2({
        data: allBuzones2,
        maximumSelectionLength: 1,
        placeholder: '',
        tags: false,
        language: {
            maximumSelected: function (args) {
                var message = 'Sólo puede seleccionar ' + args.maximum + ' elemento';
                if (args.maximum != 1) {
                    message += 's';
                }
                return message;
            },
            noResults: function () {
                return 'No se encontraron resultados';
            }
        }
    }).on('select2:unselect', function (e) {
        var data = e.params.data;

        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);

    }).on('select2:select', function (e) {
       
        $('#form_acciones_solicitadas_el').multiselect('select', 6);
       
    });

    $('#form_otros_destinatarios_el').tagsinput({
        tagClass: function(item) {
            return (item.tipo == 2 ? 'label label-info' : 'label label-warning');            
        },
        itemValue: 'value',
        itemText: 'text',
        typeaheadjs: {
            name: 'allBuzones',
            displayKey: 'text',
            source: allBuzones.ttAdapter()
        }
    });

    form_acciones_solicitadas_el.disabled=true;
    form_comentario_el.disabled=true;
    form_otros_destinatarios_el.disabled=true;
    form_comentario_otro_el.disabled=true;

    $(".bootstrap-tagsinput").addClass("disabled");   

    //dropzone

    idDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

    Dropzone.options.dropzonePrincipal = {
        headers:{
            'X-CSRF-TOKEN' : "{{csrf_token()}}"
        },
        url: "{{route('files.store')}}",
        autoProcessQueue: false,
        uploadMultiple: true,
        maxFilesize: 10, //MB
        maxFiles: 1,
        dictDefaultMessage: "Arrastre y suelte archivos pdf aquí",
        acceptedFiles: "application/pdf",
        addRemoveLinks: true,
        params: {'id_tipo_archivo' : 1},
        createImageThumbnails: true,
        timeout: 50000,
        init: function() {
            dropzonePrincipal = this; // closure              

            if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) 
            {
                var _this = this;
                // Remove all files
                _this.removeAllFiles();
            }

            $(".btn-delete").click(function () {
                console.log('delete');
                //Dropzone.forElement("#dropzoneAnexo").removeAllFiles(true);
                dropzoneAnexo.removeAllFiles(true);
                //console.log(Dropzone.forElement("#dropzoneAnexo"));
                        }
                ); 

            this.on("queuecomplete", function (file) {
                console.log('completado');
            });     
        },        
        sending: function(file, xhr, formData){
            var idb = $("input[name='hiddIdDocumentoBuzon']").val();
            var idoc = $("input[name='hiddIdDocumento']").val();
            formData.append('id_documento_buzon', idb);
            formData.append('id_documento', idoc);
        }        
    };

    Dropzone.options.dropzoneAnexo = {
        headers:{
            'X-CSRF-TOKEN' : "{{csrf_token()}}"
        },
        url: "{{route('files.store')}}",
        autoProcessQueue: false,
        uploadMultiple: true,
        maxFilesize: 10, //MB
        //maxFiles: 2,
        dictDefaultMessage: "Arrastre y suelte archivos pdf aquí",
        //acceptedFiles: "image/*",
        acceptedFiles: "application/pdf",
        addRemoveLinks: true,
        params: {'id_tipo_archivo' : 2},
        createImageThumbnails: true,
        timeout: 50000,
        init: function() {
            dropzoneAnexo = this; // closure              

            if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) 
            {
                var _this = this;
                // Remove all files
                _this.removeAllFiles();
            }

            $(".btn-delete").click(function () {
                console.log('delete');
                //Dropzone.forElement("#dropzoneAnexo").removeAllFiles(true);
                dropzoneAnexo.removeAllFiles(true);
                //console.log(Dropzone.forElement("#dropzoneAnexo"));
                        }
                ); 

            this.on("queuecomplete", function (file) {
                console.log('completado');
            });     
        },        
        sending: function(file, xhr, formData){
            var idb = $("input[name='hiddIdDocumentoBuzon']").val();
            var idoc = $("input[name='hiddIdDocumento']").val();
            formData.append('id_documento_buzon', idb);
            formData.append('id_documento', idoc);
        }        
    };

    Dropzone.options.dropzoneOtros = {
        headers:{
            'X-CSRF-TOKEN' : "{{csrf_token()}}"
        },
        url: "{{route('files.store')}}",
        autoProcessQueue: false,
        uploadMultiple: true,
        maxFilesize: 10, //MB
        //maxFiles: 2,
        dictDefaultMessage: "Arrastre y suelte archivos pdf aquí",
        //acceptedFiles: "image/*",
        acceptedFiles: "application/pdf",
        addRemoveLinks: true,
        params: {'id_tipo_archivo' : 3},
        createImageThumbnails: true,
        timeout: 50000,
        init: function() {
            dropzoneOtros = this; // closure              

            if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) 
            {
                var _this = this;
                // Remove all files
                _this.removeAllFiles();

            }           

            this.on("queuecomplete", function (file) {
                console.log('completado');
            }); 
               
        },        
        sending: function(file, xhr, formData){
            var idb = $("input[name='hiddIdDocumentoBuzon']").val();
            var idoc = $("input[name='hiddIdDocumento']").val();
            formData.append('id_documento_buzon', idb);
            formData.append('id_documento', idoc);


           // formData.append('ids_buzon_archivo', $("input[name='hiddIdFileDelete']").val());
        }        
    };

    /* **DOCUMENTOS** SCRIPT */
/*
    //si se elimina destinatario principal se quita selecció a las acciones asociadas
    $('#form_destinatario_principal_el').on('itemRemoved', function(event) {
        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
    });

    $('#form_destinatario_principal_el').on('itemAdded', function(event) {
        var js_tipo_doc = datoTipoJson;
                        
        var jsAcciones = js_tipo_doc['buzones_flujo'];    
        var jsTipoAvance = js_tipo_doc['id_tipo_avance'];  
        var jsTipoflujo = js_tipo_doc['id_tipo_flujo'];
        var jsFlujoActual = js_tipo_doc['flujo_actual'];
        
        if (jsTipoflujo != 1)
        {
            if (jsFlujoActual == 0 || jsFlujoActual == 1)
                $('#form_acciones_solicitadas_el').multiselect('select', 6);    
        }

        //obtener datos de json_tipo_documento
        //if flujo controlado/mixto 
        //if se agrega destinatario en orden 0
        //si no es orden 0 ver tipo de avance y validar segun corresponda

    });

    $('#form_destinatario_principal_el').on('itemRemoved', function(event) {
        console.log(event.item);
        console.log(aBuzonesReinicio);

        var allBuzonesReset = new Bloodhound({
                            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
                            queryTokenizer: Bloodhound.tokenizers.whitespace,
                            local: aBuzonesReinicio
        });
        allBuzonesReset.initialize();
        
        $('#form_destinatario_principal_el').tagsinput('destroy'); 
        
        $('#form_destinatario_principal_el').tagsinput({
            maxTags: 1,
            itemValue: 'value',
            itemText: 'text',
            typeaheadjs: {
                name: 'allBuzonesReset',
                displayKey: 'text',
                source: allBuzonesReset.ttAdapter()
            }
        });  




    });
*/
    const editor_cuerpo = CKEDITOR.replace('form_cuerpo');

    $(".nuevo_documento").click(function(e)
    {
        $("#collapseOne").collapse('hide');
        $("#titulo_accion").html("Nuevo Documento");
        $('#card_crear_documento').show();  
        $('#card_bitacora').hide();	        
        
        clear_form();

        deshabilita_campos();
        $('#form_tipo_documento').prop("disabled", false);
        $("#form_crear_editar :input").prop("disabled", false);
        editor_cuerpo.setReadOnly(false);

        $('.btn-guardar-submit').show();   
        
    });

    function deshabilita_campos()
    {
        $('#form_tipo_documento').prop("disabled", true);
        $("#form_crear_editar :input").prop("disabled", true);
        editor_cuerpo.setReadOnly(true);
        $('#form_destinatario_principal').prop("disabled", true);
        $('#form_comentario_el').prop("disabled", true);        
        $('#form_otros_destinatarios_el').prop("disabled", true);
        $('#form_comentario_otro_el').prop("disabled", true);
        $(".bootstrap-tagsinput-max").addClass("disabled");
        $(".bootstrap-tagsinput").addClass("disabled");  
        $('#form_acciones_solicitadas_el').multiselect('disable');
        $('#dropzone-principal').prop("disabled", true);
        $('#dropzone-otros').prop("disabled", true);
        $('#dropzone-anexo').prop("disabled", true);
        
        $(".dz-hidden-input").prop("disabled",true);

        //form_destinatario_principal_el.disabled=true;
        form_acciones_solicitadas_el.disabled=true;
        form_comentario_el.disabled=true;
        form_otros_destinatarios_el.disabled=true;
        form_comentario_otro_el.disabled=true;
    }

    function habilita_campos()
    {
        $('#form_tipo_documento').prop("disabled", false);
        $("#form_crear_editar :input").prop("disabled", false);
        editor_cuerpo.setReadOnly(false);
        $('#form_destinatario_principal').prop("disabled", false);
        $('#form_acciones_solicitadas_el').multiselect('enable');
        $('#form_comentario_el').prop("disabled", false);        
        $('#form_otros_destinatarios_el').prop("disabled", false);
        $('#form_comentario_otro_el').prop("disabled", false);
        $(".bootstrap-tagsinput-max").removeClass("disabled");
        $(".bootstrap-tagsinput").removeClass("disabled"); 

        $('#dropzone-principal').prop("disabled", false);
        $('#dropzone-anexo').prop("disabled", false);
        $('#dropzone-otros').prop("disabled", false);
        $(".dz-hidden-input").prop("disabled", false); 
    }
 
    function clear_form()
    {
        ///botones
        $('.btn-guardar-submit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

        //inicializa formulario

        $('#form_crear_editar').trigger("reset");
        $("input[name='encabezado']").val('');
        editor_cuerpo.setData('');
        $("textarea[id='form_comentario_el']").val('');
        $("textarea[id='form_comentario_otro_el']").val('');

        $('#row_cuerpo').hide();
        $('#row_anexo').hide();     
        $(".row_archivar").hide();  

        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
        $('#form_respuesta_a').multiselect('deselectAll', true);

        $("#form_destinatario_principal").val(null);
        $("#form_destinatario_principal").trigger('change');  

        //$('form_destinatario_principal').select2().val(null).trigger("change");
        $('#form_otros_destinatarios_el').tagsinput('removeAll');
     
        $("input[name='hiddIdDocumentoBuzon']").val('');
        $("input[name='hiddIdDocumento']").val('');
        $("input[name='hiddIdOrigen']").val('');
        $("input[name='hiddIdFileDelete']").val('');

        $("#idAsignado").text('No Asignado');

        //vaciar archivos pre cargados

        $('#dropzone-anexo-view').html('');
        $('#dropzone-otros-view').html('');
        $('#dropzone-principal-view').html('');

        dropzoneAnexo.removeAllFiles();
        dropzoneAnexo.removeAllFiles(true);
        dropzoneOtros.removeAllFiles();
        dropzoneOtros.removeAllFiles(true);
        dropzonePrincipal.removeAllFiles();
        dropzonePrincipal.removeAllFiles(true);

    }

    $(".btn_cerrar_guardar").click(function(e){
        $('#card_crear_documento').hide();
        $('#form_crear_editar').trigger("reset");
        $("#collapseOne").collapse('show');
    });

    $(".btn_cerrar_bitacora").click(function(e){
        $('#card_bitacora').hide();
        $("#collapseOne").collapse('show');
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

                            editor_cuerpo.setData(data.data.plantilla_cuerpo);

                            //habilita respuesta a: solo a flujo libre

                             $('#form_respuesta_a').multiselect('deselectAll', true);
                            if (idTipoFlujo != 1)
                                $('#form_respuesta_a').multiselect('disable');
                            else
                                $('#form_respuesta_a').multiselect('enable');

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

        guarda_documento(1);
    });

    $(".btn-enviar-submit").click(function(e)
    {
        e.preventDefault();

        $('.btn-enviar-submit').html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviar'
        );

        $(".print-error-msg").hide();

        //guarda_documento();
        enviar_documento();

    });

    function guarda_documento(accion)
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
        var responder = $('#form_respuesta_a').val();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        var hiddIdFileDelete = $("input[name='hiddIdFileDelete']").val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        var comentarioPrincipal = $('#form_comentario_el').val();
        var comentarioOtros = $('#form_comentario_otro_el').val();
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();

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
                responder:responder,
                buzon:hiddIdBuzon,
                destinatarioPrincipal:destinatarioPrincipal,
                destinatarioOtros:otrosDestinatarios,
                comentarioPrincipal:comentarioPrincipal,
                comentarioOtros:comentarioOtros,
                acciones_solicitadas:acciones_solicitadas,
                hiddIdDocumento:hiddIdDocumento,
                hiddIdDocumentoBuzon:hiddIdDocumentoBuzon,
                hiddIdFileDelete:hiddIdFileDelete,
                carpeta:3
            },
            success: function(data)
            {
                if(data.status == '200')
                {                
                    if (accion == 1) //guarda
                    {
                        dropzonePrincipal.processQueue();   
                        dropzoneOtros.processQueue();   
                        dropzoneAnexo.processQueue();  

                        toastr.success("Documento actualizado","Aviso!");
                        
                        $('#card_crear_documento').hide();
                        $("#collapseOne").collapse('show');     
                        fn_grilla_despachados();
                    }
                }
                else if(data.status == '201')
                {
                    Swal.fire({
                    //icon: 'info',
                    title: 'Borrador guardado',
                    html: "Se ha guardado exitosamente el borrador del documento: <br>" +
                          "<b>ID: " + data.data.identificador + "</b><br>" +
                          "<b>Materia: " + data.data.materia + "</b>",
                    });

                    $('#form_tipo_documento').prop("disabled", true);
                    
                    habilita_campos();
                    cargar_datos_grilla(data.data.id_documento,data.data.rel_documento_buzon[0]['id_documento_buzon'],data.data.rel_documento_buzon[0]['id_documento_buzon_padre'],3,1);

                    //habilita botón enviar y guardar
                    $('.btn-guardar-submit').show();   

                    if(data.data.id_documento != '')
                        $('.btn-enviar-submit').show();
    
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

                toastr.error("Falla en el documento","Aviso!");

                $('.btn-guardar-submit').html( 'Guardar' );
            }

        });
    }

    function guarda_destinatarios_documento(accion) 
    {

        var _token = $("input[name='_token']").val();
        var contestar_hasta = $("input[name='contestar_hasta']").val();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        var comentarioPrincipal = $('#form_comentario_el').val();
        var comentarioOtros = $('#form_comentario_otro_el').val();
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();

        var opcionGuardarDestinatarios = 1;

        $.ajax({
            url: "{{route('buzones.update_documento')}}",
            type: 'PUT',
            dataType: 'json',
            data: {
                _token:_token,                
                contestar_hasta:contestar_hasta,                
                buzon:hiddIdBuzon,
                destinatarioPrincipal:destinatarioPrincipal,
                destinatarioOtros:otrosDestinatarios,
                comentarioPrincipal:comentarioPrincipal,
                comentarioOtros:comentarioOtros,
                acciones_solicitadas:acciones_solicitadas,
                hiddIdDocumento:hiddIdDocumento,
                hiddIdDocumentoBuzon:hiddIdDocumentoBuzon,
                carpeta:2,
                opcionGuardar:opcionGuardarDestinatarios
            },
            success: function(data)
            {
                if(data.status == '200')
                {
                    if (accion == 2) //derivar
                        derivar_documento();
                    
                    if (accion == 6) //visar
                        visar_documento();

                    if (accion == 7) //firmar
                        firmar_documento();    
                }
                else
                {
                    toastr.error("Falla al guardar destinatarios","Aviso!");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en la actualización del documento","Aviso!");
            }

        });
    }

    function accion_editar_guardar() //**** revisar si se puede usar funcion que guarda documento ****//
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
        var hiddIdFileDelete = $("input[name='hiddIdFileDelete']").val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        var comentarioPrincipal = $('#form_comentario_el').val();
        var comentarioOtros = $('#form_comentario_otro_el').val();
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();

        $.ajax({
            url: "{{route('buzones.update_documento')}}",
            type: 'PUT',
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
                acciones_solicitadas:acciones_solicitadas,
                hiddIdDocumento:hiddIdDocumento,
                hiddIdDocumentoBuzon:hiddIdDocumentoBuzon,
                hiddIdFileDelete:hiddIdFileDelete,
                carpeta:2
            },
            success: function(data)
            {
                if(data.status == '200')
                {
                    dropzoneAnexo.processQueue(); 
                    dropzoneOtros.processQueue(); 
                    dropzonePrincipal.processQueue(); 
                    
                    toastr.success("Documento actualizado","Aviso!");

                    fn_grilla_recibidos();
                    $('#card_crear_documento').hide();
                    $("#collapseOne").collapse('show');

                }
                else
                {
                    toastr.error(data.data.comentario,"Aviso!");
                }

                $('.btn-guardar-submit').html( 'Guardar' );
            },
            error: function (jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en el documento","Aviso!");

                $('.btn-guardar-submit').html( 'Guardar' );
            }

        });
    }

    function enviar_documento()
    {
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        var responder = $('#form_respuesta_a').val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();

        Swal.fire({
            title: 'Enviar Documento',
            text: "¿Está seguro(a) que desea enviar este documento?",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
            }).then((result) => {
                console.log(result);
            if (result.value==true) 
            {
                guarda_documento(2);                
                
                $.ajax({
                    url: "../buzonesCarpetas/"+hiddIdDocumento,
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        _token:_token,
                        hiddIdDocumento:hiddIdDocumento,
                        hiddIdDocumentoBuzon:hiddIdDocumentoBuzon,
                        buzon:hiddIdBuzon,
                        destinatarioPrincipal:destinatarioPrincipal,
                        destinatarioOtros:otrosDestinatarios,
                        responder:responder,
                        carpeta:3                
                    },
                    success: function(data)
                    {
                        if(data.status == '200')
                        {
                            toastr.success("Documento enviado","Aviso!");

                            $('#card_crear_documento').hide();        
                            $("#collapseOne").collapse('show');
                            clear_form();
                            fn_grilla_despachados();        

                            //location.reload();
                        }
                        else
                        {
                            toastr.error(data.data.comentario,"Aviso!");
                        }

                        $('.btn-enviar-submit').html( 'Enviar' );
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        toastr.error("Falla en el envío del documento","Aviso!");

                        $('.btn-enviar-submit').html( 'Enviar' );
                    }
                });
                
            }
        })               
    }

    function derivar_documento()
    {
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();

            Swal.fire({
                title: 'Derivar',
                html: "Se realizará la derivación del documento: <br>" +
                    "<b>" + $("input[name='materia']").val() + "</b><br>",
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
                }).then((result) => {
                    console.log(result);
                if (result.value==true) 
                {
                    $.ajax({
                        url: "../buzonesCarpetas/"+hiddIdDocumento,
                        type: 'PUT',
                        dataType: 'json',
                        data: {
                            _token:_token,
                            hiddIdDocumento:hiddIdDocumento,
                            hiddIdDocumentoBuzon:hiddIdDocumentoBuzon,
                            buzon:hiddIdBuzon,
                            destinatarioPrincipal:destinatarioPrincipal,
                            destinatarioOtros:otrosDestinatarios,
                            carpeta:2                
                        },
                        success: function(data)
                        {
                            if(data.status == '200')
                            {
                                toastr.success("Documento Derivado","Aviso!");

                                $('#card_crear_documento').hide();        
                                clear_form();
                                fn_grilla_despachados();
                                location.reload();
                            }
                            else
                            {
                                toastr.error(data.data.comentario,"Aviso!");
                            }

                            $('.btn-enviar-submit').html( 'Enviar' );
                        },
                        error: function (jqXHR, textStatus, errorThrown) {

                            toastr.error("Falla en la derivación del documento","Aviso!");

                            $('.btn-enviar-submit').html( 'Enviar' );
                        }
                    });
                }
            }) 
    }

    function visar_documento()
    {
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        Swal.fire({
                title: 'Visar',
                html: "Se realizará la visación del documento: <br>" +
                    "<b>" + $("input[name='materia']").val() + "</b><br>",
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
                }).then((result) => {
                    console.log(result);
                if (result.value==true) 
                {
                    $.ajax({
                        url: "/actualizar_estado_documento/"+hiddIdDocumentoBuzon,
                        type: 'PUT',
                        dataType: 'json',
                        data: {
                            _token:_token,
                            hiddIdDocumento:hiddIdDocumento,
                            buzon:hiddIdBuzon,
                            accion:6                
                        },
                        success: function(data)
                        {
                            if(data.status == '200')
                            {
                                toastr.success("Documento Visado","Aviso!");
                                
                                fn_grilla_recibidos();
                                $('#card_crear_documento').hide();        
                                $("#collapseOne").collapse('show');                   
                            }
                            else
                            {
                                toastr.error(data.data.comentario,"Aviso!");
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            toastr.error("Falla en el documento","Aviso!");
                        }
                    }); 
                }
            })                 
    }

    function firmar_documento()
    {
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        Swal.fire({
                title: 'Firmar',
                html: "Se realizará la firma del documento: <br>" +
                    "<b>" + $("input[name='materia']").val() + "</b><br>",
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
                }).then((result) => {
                    console.log(result);
                if (result.value==true) 
                {       
                    $.ajax({
                        url: "/actualizar_estado_documento/"+hiddIdDocumentoBuzon,
                        type: 'PUT',
                        dataType: 'json',
                        data: {
                            _token:_token,
                            hiddIdDocumento:hiddIdDocumento,
                            buzon:hiddIdBuzon,
                            accion:7                
                        },
                        success: function(data)
                        {
                            if(data.status == '200')
                            {
                                toastr.success("Documento Firmado","Aviso!");

                                $('#card_crear_documento').hide();        
                                fn_grilla_recibidos();
                                $("#collapseOne").collapse('show');
                            }
                            else
                            {
                                toastr.error(data.data.comentario,"Aviso!");
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            toastr.error("Falla en el documento","Aviso!");
                        }
                    });
                }
            }) 
    }

    function finalizar_documento()
    {
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        Swal.fire({
                title: 'Finalizar',
                html: "Se realizará la finalización del documento: <br>" +
                    "<b>" + $("input[name='materia']").val() + "</b><br>",
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
                }).then((result) => {
                    console.log(result);
                if (result.value==true) 
                {
        
                    $.ajax({
                        url: "/actualizar_estado_documento/"+hiddIdDocumentoBuzon,
                        type: 'PUT',
                        dataType: 'json',
                        data: {
                            _token:_token,
                            hiddIdDocumento:hiddIdDocumento,
                            buzon:hiddIdBuzon,
                            accion:10                
                        },
                        success: function(data)
                        {
                            if(data.status == '200')
                            {
                                toastr.success("Documento Finalizado","Aviso!");

                                $('#card_crear_documento').hide();        
                                fn_grilla_recibidos();
                                $("#collapseOne").collapse('show');
                            }
                            else
                            {
                                toastr.error(data.data.comentario,"Aviso!");
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            toastr.error("Falla en el documento","Aviso!");
                        }
                    });
                }
            })
    }

    function recibir_documento(destino)
    {
        var _token = $("input[name='_token']").val();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        Swal.fire({
            title: 'Recibir',
            html: "Se recepcionará el documento: <br>" +
                  "<b>" + $("input[name='materia']").val() + "</b><br>",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
            }).then((result) => {
                console.log(result);
            if (result.value==true) 
            {
                $.ajax({
                    url: "/actualizar_estado_documento/"+hiddIdDocumentoBuzon,
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        _token:_token,
                        hiddIdDocumento:hiddIdDocumento,
                        buzon:hiddIdBuzon,
                        destino:destino,
                        accion:3                
                    },
                    success: function(data)
                    {
                        if(data.status == '200')
                        {
                            toastr.success("Documento Recepcionado","Aviso!");

                            $('#card_crear_documento').hide();        
                            clear_form();
                            fn_grilla_recibidos();
                            location.reload();
                        }
                        else
                        {
                            toastr.error(data.data.comentario,"Aviso!");
                        }

                        $('.btn-enviar-submit').html( 'Enviar' );
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        toastr.error("Falla en el documento","Aviso!");

                        $('.btn-enviar-submit').html( 'Enviar' );
                    }
                });
            }
        })        
       
    }

    function archivar_documento()
    {
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        var comentario = $("textarea[id='form_comentario_archivar']").val();

        Swal.fire({
            title: 'Archivar',
            html: "Se archivará el documento: <br><br>" +
                  "<b>" + $("input[name='materia']").val() + "</b><br>",            
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
            }).then((result) => {
                console.log(result);
            if (result.value==true) 
            {
                $.ajax({
                    url: "/archivar_documento/"+hiddIdDocumentoBuzon,
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        _token:_token,
                        hiddIdDocumento:hiddIdDocumento,
                        buzon:hiddIdBuzon,
                        comentario:comentario                
                    },
                    success: function(data)
                    {
                        if(data.status == '200')
                        {
                            toastr.success("Documento Archivado","Aviso!");

                            $('#card_crear_documento').hide();        
                            clear_form();
                            fn_grilla_recibidos();
                            location.reload();
                        }
                        else
                        {
                            toastr.error(data.data.comentario,"Aviso!");
                        }

                        $('.btn-enviar-submit').html( 'Enviar' );
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        toastr.error("Falla en el documento","Aviso!");

                        $('.btn-enviar-submit').html( 'Enviar' );
                    }
                });
            }
        })        
       
    }


    /* **DOCUMENTOS** SCRIPT */

    function cambio_texto_boton_carpetas(texto){
        $('#documento').hide();
        $('#card_crear_documento').hide();
        $('#card_bitacora').hide();	        

        if(texto.length>20 || texto.length==0 ){
            texto='';
        }
        $('#boton_carpetas_texto').html('Carpetas - <i><b>'+texto+'</b></i>');
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

    function ver_recibidos(id_documento, id_documento_buzon,id_documento_buzon_padre)
    {           
        $('#titulo_accion').html('Ver Documento');         

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,2); 

        $('.btn-guardar-submit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

    }

    function responder_recibidos(identificador){
        alert(identificador)
    }

    function accion_visar(id_documento,id_documento_buzon,id_documento_buzon_padre){
        $('#titulo_accion').html('Editar Documento');         

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,2);         
        
        $('.btn-guardar-submit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html(''); 

        var buttonVisar = '<button onClick="guarda_destinatarios_documento(6)" type="button" class="btn btn-success btn-recibir-submit w-10">Visar</button> ';
        $('#addButton').append(buttonVisar);
        
    }

    function accion_pdf(id_documento,id_documento_buzon){

        var _token = $("input[name='_token']").val();

        Swal.fire({
            title: 'Generar Pdf',
            html: "Se generará archivo pdf",                        
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
            }).then((result) => {
            
                if (result.value==true) 
                {
                    $.ajax({
                        url: "/generar_archivo/",
                        type: 'PUT',
                        dataType: 'json',
                        data: {
                            _token:_token,
                            idDocumento:id_documento,
                            idDocumentoBuzon:id_documento_buzon             
                        },
                        success: function(data)
                        {
                            if(data.status == '200')
                            {
                                toastr.success("Archivo Pdf generado","Aviso!");
                            }
                            else
                            {
                                toastr.error(data.data.comentario,"Aviso!");
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {

                            toastr.error("Falla en la generación del archivo","Aviso!");
                        }
                    });
            }
        })      
    }

    function accion_firmar(id_documento,id_documento_buzon,id_documento_buzon_padre){
        $('#titulo_accion').html('Ver Documento');         

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,2);         

        $('.btn-guardar-submit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html(''); 

        var buttonFirmar = '<button onClick="guarda_destinatarios_documento(7)" type="button" class="btn btn-success btn-recibir-submit w-10">Firmar</button> ';
        $('#addButton').append(buttonFirmar);
        
    }

    function accion_finalizar(id_documento,id_documento_buzon,id_documento_buzon_padre){
        $('#titulo_accion').html('Ver Documento');         

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,2);

        $('.btn-guardar-submit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html(''); 

        var buttonFirmar = '<button onClick="finalizar_documento()" type="button" class="btn btn-success btn-recibir-submit w-10">Finalizar</button> ';
        $('#addButton').append(buttonFirmar);
        
    }

    function derivar_recibidos(id_documento,id_documento_buzon,id_documento_buzon_padre){
        $('#titulo_accion').html('Ver Documento');         

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,2,1);         
        
        $('#form_comentario_el').prop("disabled", false); 

        $('#form_otros_destinatarios_el').prop("disabled", false);
        $('#form_comentario_otro_el').prop("disabled", false);
        $(".bootstrap-tagsinput").removeClass("disabled");          

        $('.btn-guardar-submit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html(''); 

        var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn btn-success btn-recibir-submit w-10">Enviar</button> ';
        $('#addButton').append(buttonDerivar);
        
    }

    function archivar_recibidos(id_documento,id_documento_buzon,id_documento_buzon_padre){

        $('#titulo_accion').html('Ver Documento');         

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre); 

        $('.btn-guardar-submit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');
        
        var buttonArchivar = '<button onClick="archivar_documento()" type="button" class="btn btn-success btn-recibir-submit w-10">Archivar</button> ';
        $('#addButton').append(buttonArchivar);

        $(".row_archivar").show();       
        
    }

    function bitacora(id_documento){

        $("#collapseOne").collapse('hide');
        $('#card_crear_documento').hide();  
        $('#card_bitacora').show();	

        $('input[name="buscar_accion"]').on('change', function () 
        {
            var types = $('input:checkbox[name="buscar_accion"]:checked').map(function() {
                return '^' + this.value + '\$';
            }).get().join('|');

            gridBitacora.fnFilter(types, 0, true, false, false, false);
        });

        cargar_datos_bitacora(id_documento);
    }

    function cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,carpeta,accion)
    {
        clear_form();
        $("#collapseOne").collapse('hide');
        $('#card_crear_documento').show(); 
        $('#card_bitacora').hide();    

        if (carpeta == 2)
            var docBuzon = id_documento_buzon_padre;
        else
            var docBuzon = id_documento_buzon; 

        $.ajax({
            url: "/documentos/"+id_documento,
            type:'GET',
            dataType: 'json',
            //async: false,
            data: {
                    hiddIdDocumentoBuzon:docBuzon
                  },
            success: function(data) {
                if(data.status=='400') {
                    toastr.error(data.data.comentario,"Aviso!");
                }
                else
                {
                    if(data.status=='200')
                    {
                        var json_tipo_doc = $.parseJSON(data.data.json_tipo_documento);
                        var fechaContestarHasta = data.data.rel_documento_buzon[0]['contestar_hasta'].split(' ');
                        var idBuzon = $("input[name='hiddIdBuzon']").val();
                        var nFlujo = json_tipo_doc['id_tipo_flujo'];
                        var jsonAcciones = json_tipo_doc['buzones_flujo'];    
                        var jsonTipoAvance = json_tipo_doc['id_tipo_avance'];    
                        var jsonRespuesta = $.parseJSON(data.data.json_respuesta_a);
                        
                        datoTipoJson = json_tipo_doc;

                        $("select[name='tipo_documento']").val(data.data.id_tipo_documento);
                        $("select[name='nivel_acceso']").val(data.data.id_nivel_acceso);
                        $("select[name='efectos_terceros']").val(""+data.data.efectos_terceros+"");
                        $("input[name='contestar_hasta']").val(fechaContestarHasta[0]);
                        $("input[name='materia']").val(data.data.materia);
                        $("input[name='anterior']").val(data.data.anterior);
                        $("textarea[name='descripcion']").val(data.data.descripcion);

                        if (nFlujo != 1)
                            $('#form_respuesta_a').multiselect('disable');
                         
                        $("input[name='encabezado']").val(json_tipo_doc['plantilla_encabezado']);
                        $("input[name='hiddIdOrigen']").val(json_tipo_doc['id_tipo_origen']);                        

                        editor_cuerpo.setData(data.data.cuerpo);

                        $("input[name='hiddIdDocumento']").val(data.data.id_documento);
                        $("input[name='hiddIdDocumentoBuzon']").val(id_documento_buzon);

                        $("#idAsignado").text(data.data.identificador);

                        if (json_tipo_doc['id_tipo_origen'] == 1) //interno
                        {
                            $('.row_cuerpo').show();
                            $('.row_arch_ppal').hide();
                            $('.row_anexo').show();
                            $('#cargar_anexo').show();
                        }
                        if (json_tipo_doc['id_tipo_origen'] == 2) //externo
                        {
                            $('.row_cuerpo').hide();
                            $('.row_arch_ppal').show();
                            $('.row_anexo').hide();
                            $('#form_archivo_principal_el').hide();
                            $('#cargar_archivo_principal_el').show();
                        }
                        
                        //selecciona documentos en respuesta a
                       
                        $('#form_respuesta_a').multiselect({numberDisplayed: 6});
                        $('#form_respuesta_a').multiselect('deselectAll', true);
                        //$('#form_respuesta_a').empty();                        
 
                        if (carpeta != 3 || (carpeta == 3 && accion == 0))
                            $('#form_respuesta_a').empty();
                        
                        for (let j in jsonRespuesta) 
                        {                           
                            if (carpeta == 3 && accion != 0)
                                $('#form_respuesta_a').multiselect('select', jsonRespuesta[j]['id_documento']);
                            else
                                $('#form_respuesta_a').append("<option selected value='"+jsonRespuesta[j]['id_documento']+"' >"+jsonRespuesta[j]['identificador'] +"-"+jsonRespuesta[j]['materia']+"</option>");
                        }

                        $('#form_respuesta_a').multiselect('rebuild');

                        $('#form_otros_archivos_el').hide();
                        $('#cargar_otros_archivos').show();   

                        var relDocumentoBuzon = data.data.rel_documento_buzon;

                        if (carpeta == 3 || carpeta == 2)
                        {
                            var buzon_padre = id_documento_buzon;
                            var flujoSgte = json_tipo_doc['flujo_actual'] + 1; //solo aplica cuando está en carpeta = 2
                        }
                        else
                        {
                            var buzon_padre = id_documento_buzon_padre; 
                            var flujoSgte = json_tipo_doc['flujo_actual'];
                        }
                         
                        //agrega las acciones correspondientes al tipo de flujo
                        if (nFlujo == 3)
                            var accionesFlujo = accionesFlujo3;
                                                
                        //flujo controlado
                        if(nFlujo == 2 || nFlujo == 3) //SE AGREGÓ MIXTO PERO SIN BUZONES PERSONALES - PENDIENTE
                        {                        
                            //agrega las acciones correspondientes al tipo de flujo
                            var accionesFlujo = accionesFlujo2; 

                            //habilitar en carpeta = 3 agregar item extra

                            if (carpeta == 3 && accion == 1)
                            {
                                $('#form_destinatario_principal').prop("disabled", false);                            
                                $(".bootstrap-tagsinput").removeClass("disabled");
                            }

                            var aBuzonesDerivaciones = [];

                            //obtener accion, buzon en orden siguiente dentro del flujo definido
                            for (let i in jsonAcciones) 
                            { 
                                //revisar valores de flujo cuando está en carpeta 3
                                if (carpeta == 3)
                                {
                                    if (jsonAcciones[i].orden < 2)
                                    {
                                        var aAcciones = jsonAcciones[i].acciones;
                                        var idBuzonAccion = jsonAcciones[i].id_buzon;

                                        if(jsonAcciones[i].orden == 0)                                        
                                            break;
                                    }                                        
                                }
                                else
                                {
                                    if (jsonAcciones[i].orden == flujoSgte)
                                    {
                                        var aAcciones = jsonAcciones[i].acciones;
                                        var idBuzonAccion = jsonAcciones[i].id_buzon;

                                        aBuzonesDerivaciones.push({"id":idBuzonAccion, "text":listadoBuzones[idBuzonAccion], "accion":aAcciones});
                                    }
                                }

                                //guarda buzon y acciones flujo anterior
                                if ((jsonAcciones[i].orden == json_tipo_doc['flujo_actual'] - 1) && jsonAcciones[i].orden != 0 && ((jsonTipoAvance == 2 || jsonTipoAvance == 4) && jsonAcciones[i].orden != 1)) //validar que no se repita 
                                {
                                    aBuzonesDerivaciones.push({"id":jsonAcciones[i].id_buzon, "text":listadoBuzones[jsonAcciones[i].id_buzon], "accion":jsonAcciones[i].acciones});
                                }

                                //guardar buzon y acciones de flujo 1 para reinicio
                                if (jsonAcciones[i].orden == 1 && jsonAcciones[i].orden != json_tipo_doc['flujo_actual'] && (jsonTipoAvance == 2 || jsonTipoAvance == 4))
                                {
                                    aBuzonesDerivaciones.push({"id":jsonAcciones[i].id_buzon, "text":listadoBuzones[jsonAcciones[i].id_buzon], "accion":jsonAcciones[i].acciones});
                                }
                            }                         

                            $('#form_acciones_solicitadas_el').empty();
                            for (let i in accionesFlujo) 
                            {
                                //var aEncontrado = aAcciones.filter(function (a) { return a.id_accion == accionesFlujo[i][0] });                                
                                //if (aEncontrado.length > 0)
                                //    $('#form_acciones_solicitadas_el').append("<option selected value='"+accionesFlujo[i][0]+"' >"+accionesFlujo[i][1]+"</option>");
                                //else
                                    $('#form_acciones_solicitadas_el').append("<option value='"+accionesFlujo[i][0]+"' >"+accionesFlujo[i][1]+"</option>");

                            }

                            $('#form_acciones_solicitadas_el').multiselect('rebuild');
                            $('#form_acciones_solicitadas_el').multiselect('disable');

                            var bFlujo = false;
                            $.each(relDocumentoBuzon, function(i, item)
                            {                       
                                if (item.id_tipo_destino == 1 && item.id_documento_buzon_padre == buzon_padre)
                                {
                                    bFlujo = true;

                                    //agrega buzon q corresponde al flujo actual segun carpeta
                                    //$('#form_destinatario_principal_el').tagsinput('add', {"value": item.id_buzon, "text": listadoBuzones[item.id_buzon]});                                    
                                    $("#form_destinatario_principal").val(item.id_buzon);
                                    $("#form_destinatario_principal").trigger('change');                                     

                                    var accionesSolicitadas = $.parseJSON(item.json_acciones);
                                    $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                    for (let i in accionesSolicitadas) {
                                        $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                    }

                                    $("textarea[id='form_comentario_el']").val(item.comentario_principal);

                                }
                                
                                if (item.id_tipo_destino == 2 && item.id_documento_buzon_padre == buzon_padre)
                                {
                                    $('#form_otros_destinatarios_el').tagsinput('add', {"value": item.id_buzon, "text": listadoBuzones[item.id_buzon]});
                                    $("textarea[id='form_comentario_otro_el']").val(item.comentario_secundario);
                                }  
                            });

                            if (bFlujo == false)
                            {
                                if (idBuzonAccion != null && idBuzonAccion != '')
                                {
                                    //agrega buzon q corresponde al flujo actual segun carpeta
                                    $("#form_destinatario_principal").val(idBuzonAccion);
                                    $("#form_destinatario_principal").trigger('change'); 

                                    $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                    for (let i in aAcciones) 
                                        $('#form_acciones_solicitadas_el').multiselect('select', aAcciones[i]['id_accion']);
                                }
                                else
                                    deshabilita_campos();
                            }

                            if (carpeta == 2)
                            {
                                if (jsonTipoAvance == 1) //unidireccional
                                    $('#form_destinatario_principal').prop("disabled", true);
                                
                                if (jsonTipoAvance != 1 && accion == 1) //unidireccional con reinicio
                                {
                                    $('#form_destinatario_principal').prop("disabled", false);
                                    $('#form_destinatario_principal').empty();
                                    $('#form_destinatario_principal').select2({
                                        data: aBuzonesDerivaciones,
                                        maximumSelectionLength: 1,
                                        tags: false,
                                        language: {
                                            maximumSelected: function (args) {
                                                var message = 'Sólo puede seleccionar ' + args.maximum + ' elemento';
                                                if (args.maximum != 1) {
                                                    message += 's';
                                                }
                                                return message;
                                            },
                                        }
                                    }).on('select2:unselect', function (e) {
                                        var data = e.params.data;

                                        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);

                                    }).on('select2:select', function (e) {
                                        var aAcciones = e.params.data.accion;
                                        
                                        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                        for (let i in aAcciones) {
                                            $('#form_acciones_solicitadas_el').multiselect('select', aAcciones[i]['id_accion']);
                                        }
                                    });
                                   
                                    $('#form_destinatario_principal').val(idBuzonAccion).trigger('change');                                   

                                }   
/*
                                if (jsonTipoAvance == 3 && accion == 1) //bidireccional con reinicio
                                {
                                    $('#form_destinatario_principal').prop("disabled", false);    
                                }

                                if (jsonTipoAvance == 4 && accion == 1) //bidireccional con reinicio
                                {}
*/
                            }
                            //habilitar si tipo avance = 1
                            


                        }
                        else if(nFlujo == 1)  //flujo libre
                        {                           
                            if (accion == 1)
                            {
                                $('#form_destinatario_principal').prop("disabled", false);
                                $('#form_acciones_solicitadas_el').multiselect('enable');
                            } 

                            $.each(relDocumentoBuzon, function(i, item)
                            {                       
                                
                                if (item.id_tipo_destino == 1 && item.id_documento_buzon_padre == buzon_padre) //PENDIENTE: agregar carpeta 
                                {
                                    $("#form_destinatario_principal").val(item.id_buzon);
                                    $("#form_destinatario_principal").trigger('change');    
                                    $("textarea[id='form_comentario_el']").val(item.comentario_principal);

                                    //seleccionar acciones
                                    var accionesSolicitadas = $.parseJSON(item.json_acciones);
                                    
                                    $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                    for (let i in accionesSolicitadas) 
                                        $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                }

                                if (item.id_tipo_destino == 2 && item.id_documento_buzon_padre == buzon_padre)
                                {
                                    $('#form_otros_destinatarios_el').tagsinput('add', {"value": item.id_buzon, "text": listadoBuzones[item.id_buzon]});
                                    $("textarea[id='form_comentario_otro_el']").val(item.comentario_secundario);
                                }  
                            });
                        }
 
                        var relDocumentoBuzonArchivo = data.data.rel_archivos;

                        let htmlFile = "";
                        let htmlFileAnexo = '<div class="col-md-12 group-button-alig file-container-all">';
                        let htmlFileOtros = '<div class="col-md-12 group-button-align file-container-all">';
                        let htmlFilePrincipal = '<div class="col-md-12 group-button-align file-container-all">';
                        
                        aFilesPrincipal = [];
                        aFilesDelete = [];                  

                        $.each(relDocumentoBuzonArchivo, function(key,value){
                            
                          /*  let mockFile = { name: value.nombre_archivo_original, size: 1024 };

                            //dropzoneAnexo.displayExistingFile(mockFile, "{{ asset('/vendor/ckeditor/ckeditor.js') }}");
                            
                            dropzoneAnexo.options.addedfile.call(dropzoneAnexo, mockFile);
                            dropzoneAnexo.options.thumbnail.call(dropzoneAnexo, mockFile, "https://upload.wikimedia.org/wikipedia/commons/8/87/PDF_file_icon.svg");
                            //dropzoneAnexo.emit('complete', mockFile);
                            mockFile.previewElement.classList.add('dz-complete');

                            var a = document.createElement('a');
                            a.setAttribute('href',"#");
                            a.setAttribute('target',"_blank");
                            a.innerHTML = "<i class='fas fa-download'></i>";
                            //a.innerHTML = "Descargar";
                            mockFile.previewTemplate.appendChild(a);
                           
*/                            
                            
                            htmlFile = '<div class="file-container '+value.id_documento_buzon_archivo+'">'+
                                       ' <img src="https://upload.wikimedia.org/wikipedia/commons/8/87/PDF_file_icon.svg" width="75" height=75" style="height:75px;" />'+
                                           '<a href="/imagenes/'+value.nombre_archivo_codificado+'" class="btn-descargar" target="_blank"><i class="fas fa-download fa-icon1"></i></a>';
                            
                          // htmlFile = '<a href="/imagenes/'+value.nombre_archivo_original+'" target="_blank"><img src="https://upload.wikimedia.org/wikipedia/commons/8/87/PDF_file_icon.svg" width="75" height=75" style="height:75px;" /></a>';

                           /// habilitar cuando esté operativo el eliminar
                            if (carpeta == 2 && value.id_documento_buzon == id_documento_buzon && accion == 1)               
                                htmlFile += '<a href="javascript:deleteFile('+value.id_documento_buzon_archivo+')"><i class="fas fa-trash-alt fa-icon2"></i></a>';
                            
                            if (carpeta == 3 && accion == 1)               
                                htmlFile += '<a href="#"><i class="fas fa-trash-alt fa-icon2"></i></a>';
                            
                            if (carpeta == 3 && value.id_documento_buzon != id_documento_buzon)
                                htmlFile = "";                                 
                             
                            if (value.id_tipo_archivo == 2) //anexo
                                htmlFileAnexo += htmlFile + '</div>';       

                            if (value.id_tipo_archivo == 3) //otros
                                htmlFileOtros += htmlFile + '</div>'; 
                            
                            if (value.id_tipo_archivo == 1 && value.version == 1) //principal
                                htmlFilePrincipal += htmlFile + '</div>'; 

                        });
                       

                        $('#dropzone-principal-view').html(htmlFilePrincipal + '</div>');
                        $('#dropzone-anexo-view').html(htmlFileAnexo + '</div>');
                        $('#dropzone-otros-view').html(htmlFileOtros + '</div>');

                    }
                }
            },
            error: function (e) {
                data = e.responseJSON;
                if (typeof data.errors !== 'undefined') {
                    printErrorMsg(data.errors);
                }
            }
        });
    }

    function deleteFile(codFile){
        //obtener datos y eliminar el seleccionado
        
        var listDelete = [];
        var valDelete = $('#hiddIdFileDelete').val();

        if (valDelete.length != 0)
            listDelete = valDelete.split(",");
        
        listDelete.push(codFile);

        $('#hiddIdFileDelete').val(listDelete.join(","));

        $('.'+codFile+'').hide();
    }

    function ver_despachados(id_documento,id_documento_buzon,id_documento_buzon_padre)
    {
        $('#titulo_accion').html('Ver Documento');         

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,3,0); 

        $('.btn-guardar-submit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

    
    }

    function editar_despachados(id_documento,id_documento_buzon,id_documento_buzon_padre)
    {       
        $('#titulo_accion').html('Editar Documento'); 
        
        habilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,3,1); 
        
        $('#form_tipo_documento').prop("disabled", true); 
       
        $('.btn-guardar-submit').show();
        $('.btn-enviar-submit').show();
        $('#addButton').html('');

    }

    function accion_editar(id_documento, id_documento_buzon,id_documento_buzon_padre)
    {
        $('#titulo_accion').html('Editar Documento'); 
        
        habilita_campos();
        cargar_datos_grilla(id_documento, id_documento_buzon,id_documento_buzon_padre,2,1);

        $('#form_tipo_documento').prop("disabled", true);
        $('#form_respuesta_a').multiselect('disable');
       
        $('.btn-guardar-submit').hide();
        $('.btn-enviar-submit').hide();

        var buttonGuardar = '<button onClick="accion_editar_guardar()" type="button" class="btn btn-success btn-recibir-submit w-10">Guardar</button> ';
        $('#addButton').html('');
        $('#addButton').append(buttonGuardar);
    }

    function visualizar_documento_por_recibir(id_documento,id_documento_buzon,id_documento_buzon_padre, destino)
    {           
        $('#titulo_accion').html('Ver Documento'); 

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,1,0);       
        
        $('.btn-guardar-submit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

        var buttonRecibir = '<button onClick="recibir_documento('+destino+')" type="button" class="btn btn-success btn-recibir-submit w-10">Recibir</button>';
        $('#addButton').append(buttonRecibir);
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
                select: true,
                language: lenguaje_datatable,
                columns: [
                    { data: 'identificador', name: 'documento.identificador' },                    
                    { data: 'fecha_envio_recepcion', 
                            render: function(data)
                            {
                                if(data == null)
                                    return '';
                                else                                 
                                    return moment(data).format('DD-MM-YYYY HH:mm');
                            }
                    },
                    { data: 'contestas_hasta', 
                            render: function(data)
                            {
                                if(data == null)
                                    return '';
                                else                                 
                                    return moment(data).format('DD-MM-YYYY');
                            }
                    },
                    { data: 'tipo_documento', name: 'tipo_documento.nombre' },
                    { data: 'tipo_envio', name: 'tipo_destino.nombre' },                   
                    { data: 'buzon_origen',
                            render: function(data, type, row) {
                                if (type === 'display') 
                                {
                                    if(data != null)
                                        return listadoBuzones[data];
                                    else                           
                                        return '';
                                }
                                return '';
                            }     
                    },
                    { data: 'materia', name: 'documento.materia' },
                ],
                rowCallback: function (row, data, index ) {
                    $(row).on("click", function (e) {
                        visualizar_documento_por_recibir(data['id_documento'],data['id_documento_buzon'],data['id_documento_buzon_padre'], data['id_tipo_destino']);
                    });                
                }
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
                    { data: 'estado_documento', name: 'estado_documento.nombre_corto' },
                    { data: 'identificador', name: 'documento.identificador' },
                    { data: 'fecha_envio_recepcion', 
                            render: function(data)
                            {
                                return moment(data).format('DD-MM-YYYY HH:mm');
                            }
                    },
                    { data: 'contestas_hasta', 
                            render: function(data)
                            {
                                if(data == null)
                                    return '';
                                else                                 
                                    return moment(data).format('DD-MM-YYYY');
                            }
                    },
                    { data: 'tipo_documento', name: 'tipo_documento.id_tipo_documento' },
                    { data: 'tipo_envio', name: 'tipo_destino.nombre' },
                   // { data: 'buzon_origen', name: 'tipo_origen.nombre' },
                    { data: 'buzon_origen',
                            render: function(data, type, row) {
                                if (type === 'display') 
                                {
                                    if(data != null)
                                        return listadoBuzones[data];
                                    else                           
                                        return '';
                                }
                                return '';
                            }     
                    },
                    { data: 'materia', name: 'documento.materia' },
                    { data: 'id_documento',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if(data==null){
                                return '';
                            }else{
                                let botonera = '<div class="dropdown">';
                                    botonera += '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                        botonera +=' <i class="fas fa-bars"></i>';
                                        botonera +=' </button>';
                                        botonera +='<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

                                        if (row.id_tipo_destino == 1) //principal
                                        {
                                            //agrega listado de acciones

                                            if(row.id_estado_documento != 5 && row.id_estado_documento != 7 && row.id_estado_documento != 10 && row.id_estado_documento != 12 && row.id_estado_documento != 13)
                                            {
                                                if (row.json_acciones != null)
                                                {
                                                    var accionesSolicitadas = row.json_acciones
                                                    
                                                    accionesSolicitadas = $.parseJSON(accionesSolicitadas.replace(/(&quot\;)/g,"\""));                                                
                                                    jsonTipoDoc = $.parseJSON(row.json_tipo_documento.replace(/(&quot\;)/g,"\""));                                                

                                                    for (let i in accionesSolicitadas) {
                                                        if (accionesSolicitadas[i]['id_accion'] == 4) //editar  
                                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="accion_editar('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-edit text-blue"></i> '+accionesFlujo1[accionesSolicitadas[i]['id_accion']]+'</a>';
                                                        if (accionesSolicitadas[i]['id_accion'] == 6) //visar                                                   
                                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="accion_visar('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-check-circle text-blue"></i> '+accionesFlujo1[accionesSolicitadas[i]['id_accion']]+'</a>';
                                                        if (accionesSolicitadas[i]['id_accion'] == 7) //Firmar                                                   
                                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="accion_firmar('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-file text-blue"></i> '+accionesFlujo1[accionesSolicitadas[i]['id_accion']]+'</a>';
                                                        if (accionesSolicitadas[i]['id_accion'] == 8) //Generar pdf                                                   
                                                            //botonera +=' <a class="dropdown-item btn-menu-ver" href="#"><i class="fas fa-file text-blue"></i> '+accionesFlujo1[accionesSolicitadas[i]['id_accion']]+'</a>';
                                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="accion_pdf('+data+','+row.id_documento_buzon+')"  href="#"><i class="fas fa-file text-blue"></i> '+accionesFlujo1[accionesSolicitadas[i]['id_accion']]+'</a>';
                                                        if (accionesSolicitadas[i]['id_accion'] == 10) //finalizar                                                   
                                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="accion_finalizar('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-file text-blue"></i> '+accionesFlujo1[accionesSolicitadas[i]['id_accion']]+'</a>';
                                                    } 
                                                }                                                
                                                
                                                if (jsonTipoDoc['id_tipo_flujo'] == 1)
                                                    botonera +=' <a class="dropdown-item btn-menu-editar" onclick="responder_recibidos('+data+')"  href="#"><i class="fas fa-reply text-orange"></i> Responder</a>';
                                                
                                                botonera +=' <a class="dropdown-item btn-menu-editar" onclick="derivar_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-share text-green"></i> Derivar</a>';
                                            }  

                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                                                                        
                                            if(row.id_estado_documento != 6 && row.id_estado_documento != 7 && row.id_estado_documento != 13)
                                                botonera +=' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>';
                                           
                                            botonera +=' <a class="dropdown-item btn-menu-editar" onclick="bitacora('+data+')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';
                                        }
                                        else
                                        {
                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                            
                                            if(row.id_estado_documento != 6)
                                                botonera +=' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>';
                                            
                                            botonera +=' <a class="dropdown-item btn-menu-editar" onclick="bitacora('+data+')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';
                                        }

                                        if (row.favorito == null)
                                            botonera +='<a class="dropdown-item btn-menu-deshabilitar" onclick="add_favorito('+data+')" href="#"><i class="far fa-star text-green"></i> ( + ) Favoritos</a>';
                                        else
                                            botonera +='<a class="dropdown-item btn-menu-deshabilitar" onclick="del_favorito('+data+')" href="#"><i class="fas fa-star text-green"></i> ( - ) Favoritos</a>';

                                    botonera += '</div>';
                                    botonera += '</div>';
                                return botonera;
                            }
                        }
                        return '';
                    }
                    }
                ],
                initComplete : function() {
                    var input = $('#gr_buscar_origen_materia input').unbind(),
                    self = this.api(),
                    $clearButton = $('<button class="btn btn-light buscar_btn_limpiar">')
                            .text('Limpiar')
                            .click(function() {
                                $('#gr_buscar_origen_materia').val('');
                                $('#gr_buscar_estado').find('option:eq(0)').prop('selected', true);
                                $('#gr_buscar_tipo_doc').find('option:eq(0)').prop('selected', true);
                                $('#gr_buscar_id_doc').val('');
                                $searchButton.click();
                            }),
                    $searchButton = $('<button class="btn btn-success buscar_btn_buscar">')
                            .text('Buscar')
                            .click(function() {
                                grilla_recibidos.columns(1).search($('#gr_buscar_estado').val()).draw();
                                grilla_recibidos.columns(2).search($('#gr_buscar_id_doc').val()).draw();
                                grilla_recibidos.columns(5).search($('#gr_buscar_tipo_doc').val()).draw();
                                self.search($('#gr_buscar_origen_materia').val()).draw();
                            })
                    $('#botones_grilla_recibidos').html('');
                    $('#botones_grilla_recibidos').append($clearButton,$searchButton);
                    $('#grilla_recibidos_filter').html('');
                }
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
                { data: 'estado_documento', name: 'estado_documento.nombre_corto' },
                { data: 'identificador', name: 'documento.identificador' },
                { data: 'fecha_envio_recepcion', 
                            render: function(data)
                            {
                                if(data == null)
                                    return '';
                                else 
                                    return moment(data).format('DD-MM-YYYY HH:mm');
                            }
                },
                { data: 'estado_documento',
                            render: function(data)
                            {
                                return '';                                
                            }
                }, //cambiar, prueba
                { data: 'tipo_documento', name: 'tipo_documento.id_tipo_documento' },
                { data: 'destinatario', 
                            render: function(data, type, row) {
                                if (type === 'display') 
                                {
                                    if(data != null)
                                        return listadoBuzones[data];
                                    else                           
                                        return '';
                                }
                                return '';
                            }     
                },
                { data: 'materia', name: 'documento.materia' },
                { data: 'respuesta_a', 
                            render: function(data, type, row) {
                                if (type === 'display') 
                                {
                                    var docsRespuesta = row.respuesta_a;
                                    var docs = '';   
                                    
                                    if (docsRespuesta != null)
                                    {
                                        docsRespuesta = $.parseJSON(docsRespuesta.replace(/(&quot\;)/g,"\""));                                                
                                        
                                        if (docsRespuesta.length > 0) 
                                        {                                 
                                            for (let i in docsRespuesta) 
                                            {
                                                docs += docsRespuesta[i]['identificador'] + ' - '; 
                                            }

                                            return docs.substring(0, docs.length - 2);                                    
                                        }
                                        else
                                            return '--';
                                    }
                                }
                                return '--';
                            }     
                },
                { data: 'fecha_creacion', 
                            render: function(data)
                            {
                                return moment(data).format('DD-MM-YYYY HH:mm');
                            }
                },
                { data: 'id_documento',
                  render: function(data, type, row) {
                    if (type === 'display') {
                        if(data==null){
                            return '';
                        }else{
                            let botonera = '<div class="dropdown">';
                                botonera += '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                    botonera +=' <i class="fas fa-bars"></i>';
                                    botonera +=' </button>';
                                    botonera +=' <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

                                    if (row.id_estado_documento == 1) //B
                                    {
                                        botonera +='<a class="dropdown-item btn-menu-editar" onclick="editar_despachados('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-edit text-blue"></i> Editar</a>';
                                        botonera +='<a class="dropdown-item btn-menu-editar" onclick="eliminar_despachados('+data+','+row.id_documento_buzon+')"  href="#"><i class="fas fa-trash-alt text-red"></i> Eliminar</a>';
                                    }    

                                    if (row.id_estado_documento == 2) //E
                                    {
                                        botonera +='<a class="dropdown-item btn-menu-ver" onclick="ver_despachados('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                        botonera +='<a class="dropdown-item btn-menu-editar" onclick="bitacora('+data+')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';

                                        if (row.favorito == null)
                                            botonera +='<a class="dropdown-item btn-menu-deshabilitar" onclick="add_favorito('+data+')" href="#"><i class="far fa-star text-green"></i> ( + ) Favoritos</a>';
                                        else
                                            botonera +='<a class="dropdown-item btn-menu-deshabilitar" onclick="del_favorito('+data+')" href="#"><i class="fas fa-star text-green"></i> ( - ) Favoritos</a>';

                                    }  


                                    botonera +='</div>';
                                botonera += '</div>';
                            return botonera;
                        }
                    }
                    return '';
                  }
                }
            ],
            initComplete : function() {
                    var input = $('#gd_buscar_destino_materia input').unbind(),
                    self = this.api(),
                    $clearButton = $('<button class="btn btn-light buscar_btn_limpiar">')
                            .text('Limpiar')
                            .click(function() {
                                $('#gd_buscar_destino_materia').val('');
                                $('#gd_buscar_estado').find('option:eq(0)').prop('selected', true);
                                $('#gd_buscar_tipo_doc').find('option:eq(0)').prop('selected', true);
                                $('#gd_buscar_id_doc').val('');
                                $searchButton.click();
                            }),
                    $searchButton = $('<button class="btn btn-success buscar_btn_buscar">')
                            .text('Buscar')
                            .click(function() {
                                grilla_despachados.columns(1).search($('#gd_buscar_estado').val()).draw();
                                grilla_despachados.columns(2).search($('#gd_buscar_id_doc').val()).draw();
                                grilla_despachados.columns(5).search($('#gd_buscar_tipo_doc').val()).draw();
                                self.search($('#gd_buscar_destino_materia').val()).draw();
                            })
                    $('#botones_grilla_despachados').html('');
                    $('#botones_grilla_despachados').append($clearButton,$searchButton);
                    $('#grilla_despachados_filter').html('');
                }

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