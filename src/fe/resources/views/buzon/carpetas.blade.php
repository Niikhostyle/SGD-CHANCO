@extends('adminlte::page')
@section('title', 'Panel')

@section('content_header')
<div class="row d-sm-flex justify-content-sm-between">
    <div class="">
        <div class="">
            <div class="">
                <div class="form-group">
                    <h1>Buzón: {{$nombre_buzon}}</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex">
        <div class=" d-flex justify-content-between">
            <div class="px-1 ">
                <input class="form-control" type="search" name="buscador_general" id="buscador_general" placeholder="Buscar por materia o ID" onkeypress="javascript: if (event.key=='Enter') $('#btnBuscadorGeneral').trigger('click');"/>
                <span id="resultadoBusquedaGral"></span>
            </div>
            <div class="">       
                    <button name="btnBuscadorGeneral" id="btnBuscadorGeneral" class="btn btn-success"><i class="fa fa-search d-block d-sm-none py-1"></i><span class="d-none d-sm-block">Buscar</span></button>
                    <button id="add_documento" type="button" class="btn text-nowrap btn-min-w  btn-success nuevo_documento"><i class="fa fa-plus d-block d-sm-none py-1"></i><span class="d-none d-sm-block">Nuevo Documento</span></button>
            </div>
        </div>

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
                        <button class="btn text-nowrap btn-min-w  btn-block text-left buzones_carpetas_btn_colapsable_text" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <span id="boton_carpetas_texto"> Carpetas - <i><b>Por Recibir</b></i> </span>
                            <i class="fa fa-chevron-circle-up" style="float:right;margin-top: 8px;"></i>
                            <i class="fa fa-chevron-circle-down" style="float:right;margin-top: 8px;"></i>
                        </button>
                    </h2>

                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#carpetas">
                    <div class="card-body">
                        <nav class="nav-header">
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                <a style="width: 33%" class="nav-item nav-link active" id="nav-por-recibir-tab" data-toggle="tab" href="#nav-por-recibir" role="tab" aria-controls="nav-home" aria-selected="true" onclick="cambio_texto_boton_carpetas('Por Recibir');">
                                    Por Recibir
                                    @if($n_docs_por_recibir>0)
                                    <span id="gp_contador_pendientes" class="badge badge-success right">
                                        {{$n_docs_por_recibir}}
                                    </span>
                                    @endif
                                </a>
                                <a style="width: 33%" class="nav-item nav-link" id="nav-recibidos-tab" data-toggle="tab" href="#nav-recibidos" role="tab" aria-controls="nav-profile" aria-selected="false" onclick="cambio_texto_boton_carpetas('Recibidos');">
                                    Recibidos
                                    @if($n_docs_recibidos_pendientes>0)
                                    <span  id="gr_contador_pendientes"  class="badge badge-success right">
                                        {{$n_docs_recibidos_pendientes}}
                                    </span>
                                    @endif
                                </a>
                                <a style="width: 33%" class="nav-item nav-link" id="nav-despachados-tab" data-toggle="tab" href="#nav-despachados" role="tab" aria-controls="nav-contact" aria-selected="false" onclick="cambio_texto_boton_carpetas('Despachados');">
                                    Despachados</a>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">

                            @include('buzon.carpetas_porrecibir')
                            @include('buzon.carpetas_recibidos')
                            @include('buzon.carpetas_despachados')
                            
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
            <div class="card-header">
                <h4 id="titulo_accion">Nuevo Documento</h4>
                <div class="linea_content_header"></div>
            </div>
            <div class="card-body">

                <form class="needs-validation" id="form_crear_editar" method="POST" action="">
                    @csrf

                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="form-row section-carousel">
                                <div class="form-row carousel-wrapper">
                                    <div class="owl-carousel owl-theme owl-loaded"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="form-row">
                        <div class="col-md-12">
                            <ul class="list-group list-group-horizontal-md">
                                <li class="list-group-item col-md-2 col-12"><b>Estado:</b> <br><i><span id="idAsignado">No Asignado</span></i></li>
                                <li class="list-group-item col-md-2 col-12"><b>Folio:</b> <br><i><span id="idFolio">No Asignado</span></i></li>
                                <li class="list-group-item col-md-2 col-12"><b>Fecha:</b> <br><i><span id="idFecha">No Asignado</span></i></li>
                                <li class="list-group-item col-md-2 col-12 item-bitacora"><b>Bitácora:</b><br><p class=""><a class="btn btn-info btn-sm" id="boton-bitacora" href="javascript:void(0)" data-toggle="modal" data-target="#modalBitacoraSGD" data-id="" ><i class="fa fa-history"></i></a></p></li>
                            </ul>
                        </div>
                    </div>
                    <br>
                    <div class="form-row">
                        <div class="col-md-3 mb-3">
                            <label for="inputState">Tipo Documento:</label>
                            <select id="form_tipo_documento" name="tipo_documento" class="form-control">
                                <option value="">Seleccionar</option>
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
                                <option value="">Seleccionar</option>
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
                            <label for="inputState">Antecedentes:</label>
                            <input type="text" class="form-control" id="form_anterior" name="anterior">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="floatingTextarea">Descripción o Extracto</label>
                            <textarea class="form-control" id="form_descripcion" name="descripcion"></textarea>
                        </div>
                    </div>

                    <!--los campos cuerpo y anexo son los unicos que varian segun el documento por eso estan desactivado-->
                    <div class="form-row row_cuerpo" style="display:none">
                        <div class="col-md-12 mb-3">
                            <label class="view-txt-row" for="exampleFormControlTextarea1">Cuerpo:</label>
                            <label class="view-pdf">
                                <button onClick="vista_previa_sg()" type="button" class="btn text-nowrap btn-min-w  btn-default btn-vp">
                                    <i class="fa fa-file-pdf fa-solid"></i>&nbsp;&nbsp;Generar vista previa
                                </button>
                            </label>
                            <br><br>
                            <textarea class="form-control tiny" id="form_cuerpo" name="cuerpo"></textarea>
                            <input type="hidden" id="form_encabezado" name="encabezado">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="view-txt-row" for="distribucion">Distribución:</label>
                            <textarea class="form-control tiny" id="form_distribucion" name="distribucion"></textarea>
                        </div>

                    </div>

                    <div style="display:none">
                        <div class="col-md-12">
                            <form> </form>
                        </div>
                    </div>

                    <div class="form-group row_arch_ppal">
                        <label for="exampleFormControlTextarea1">Archivo Principal</label>

                        <div class="card-body card-archivos" id="cargar_principal">
                            <div id="dropzone-principal-view" class="dropzone-view"></div>
                            <div id="dropzone-principal" class="dropzone dropzone-files"></div>

                            <div id="card_desplegar_versiones" class="bl1 header1">
                                <label class="">Versiones</label>
                                <button type="button" class="btn text-nowrap btn-min-w  boton_desplegar_versiones_anteriores" style="padding: 49px 15px;">
                                    <i class="fas fa-angle-double-right fa-3x"></i>
                                </button>
                            </div>
                            <div class="bl2" id="card_ocultar_versiones" style="display:none">
                                <div class="header1">
                                    <label class="">Versiones</label>
                                    <button type="button" class="btn text-nowrap btn-min-w  boton_ocultar_versiones_anteriores" style="padding: 48px 15px;">
                                        <i class="fas fa-angle-double-left fa-3x"></i>
                                    </button>
                                </div>
                                <div class="display_va">
                                    <div id="versiones_anteriores"></div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="form-group row_anexo">
                        <label for="exampleFormControlTextarea1" class="d-flex justify-content-between">
                            <span>Anexos: <span class="nota_destacada">(NOTA: MARQUE LOS ANEXOS QUE REQUIEREN FIRMA)</span></span>
                            
                            <button class="btn btn-primary d-none" data-toggle="modal"  data-target="#modalReferenciaSGD">[+] Documento de SGD</button>
                        </label>

                        <div class="card-body card-archivos" id="cargar_anexo">
                            <div id="dropzone-anexo-view" class="dropzone-view"></div>
                            <div id="dropzone-anexo" class="dropzone dropzone-files"></div>
                        </div>

                    </div>


                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Otros Archivos</label>

                        <div class="card-body card-archivos" id="cargar_otros">
                            <div id="dropzone-otros-view" class="dropzone-view"></div>
                            <div id="dropzone-otros" class="dropzone dropzone-files"></div>
                        </div>

                    </div>

                    <div class="form-row">
                        <div class="col-md-8 mb-3">
                            <label for="inputState">Destinatario Principal:</label><br>
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
                        <div class="col-md-2 mb-3 d-none">
                             <label for="inputState">Fecha Respuesta:</label><br>
                             <input type="date" class="form-control" id="form_fecha_respuesta" name="fecha_respuesta_envio">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="floatingTextarea">Comentario a Destinatario Principal:<i onclick="vernotas(1)" title="ver mensajes anteriores" class="fa fa-sticky-note btn btn-sm btn-light"></i></label>
                            <textarea class="form-control" id="form_comentario_el" disabled="false"></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="inputState">Otro(s) Destinatario(s):</label>
                            <input type="text" class="form-control" id="form_otros_destinatarios_el" data-role="tagsinput" disabled="false">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12 mb-3">
                            <label for="floatingTextarea">Comentario(s) Otro(s) Destinatario(s): <i onclick="vernotas(2)" title="ver mensajes anteriores" class="fa fa-sticky-note btn btn-sm btn-light"></i></label>
                            <textarea class="form-control" id="form_comentario_otro_el" disabled="false"></textarea>
                        </div>
                    </div>
                    <div class="form-row row_archivar">
                        <div class="col-md-12 mb-3">
                            <label for="floatingTextarea">Ingrese fundamentación para archivar/desarchivar</label>
                            <textarea class="form-control" id="form_comentario_archivar"></textarea>
                        </div>
                    </div>

                    <div class="form-row row_txt_firmar" style="display:none">
                        <div class="col-md-12 mb-3">
                            <label for="floatingTextarea">Visaciones y Firmantes</label>
                            <div id="datos_bitacora_simple"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="col-md-12 group-button-align">
                            <button type="button" class="btn text-nowrap btn-min-w  btn-secondary  btn_cerrar_guardar">Cerrar</button>
                            <button type="button" id="submit-edit" class="btn text-nowrap btn-min-w  btn-light btn-guardar-submit-edit ">Guardar</button>
                            <button type="button" id="submit-all" class="btn text-nowrap btn-min-w  btn-success btn-guardar-submit">Guardar y Cerrar</button>
                            <button type="button" id="submit-enviar" class="btn text-nowrap btn-min-w  btn-primary btn-enviar-submit " style="display:none">Enviar</button>
                            <span class="" id="addButton"></span>
                            <input type="hidden" name="hiddIdDocumento" id="hiddIdDocumento" value="">
                            <input type="hidden" name="hiddIdDocumentoBuzon" id="hiddIdDocumentoBuzon" value="">
                            <input type="hidden" name="hiddIdBuzon" id="hiddIdBuzon" value="{{$id_buzon}}">
                            <input type="hidden" name="hiddIdOrigen" id="hiddIdOrigen" value="">
                            <input type="hidden" name="hiddIdFileDelete" id="hiddIdFileDelete" value="">
                            <input type="hidden" name="hiddFirmaAnexo" id="hiddFirmaAnexo" value="">
                            <input type="hidden" name="hiddIdResponder" id="hiddIdResponder" value="">
                            <input type="hidden" name="hiddIdTipoDestino" id="hiddIdTipoDestino" value="">
                            <input type="hidden" name="hiddPrimeraFirma" id="hiddPrimeraFirma" value="0">
                            <input type="hidden" name="hiddUltimaFirma" id="hiddUltimaFirma" value="0">
                            <input type="hidden" name="hiddBuzonPrimera" id="hiddBuzonPrimera" value="0">
                            <input type="hidden" name="hiddBuzonUltima" id="hiddBuzonUltima" value="0">
                            <input type="hidden" name="hiddNroFirmas" id="hiddNroFirmas" value="0">


                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>
<!-- **DOCUMENTOS** GRILLA CREAR DOCUMENTOS -->

<!-- Bitacora-->
<div class="card" id="card_bitacora" style="display:none">
    <div class="card-header">
        <h4 id="titulo_accion">Bitácora</h4>
        <div class="linea_content_header"></div>
    </div>
    <div class="card-body">
        <div class="col"><b>ID: <span id="idAsignado2"></span></b></div>
        <div class="col"><b>Materia: <span id="textMateria"></span></b></div>
        <br>

        <div class="form-check" style="padding-right: 5px;">
            <input class="form-check-input" type="checkbox" value="DDP" name="buscar_accion" id="accion_ddp">
            <label class="form-check-label" for="defaultCheck1">
                Derivaciones destinatarios principales (DDP)
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="DOD" name="buscar_accion" id="accion_dop">
            <label class="form-check-label" for="defaultCheck1">
                Derivaciones otros destinatarios (DOD)
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="CAP" name="buscar_accion" id="accion_cap">
            <label class="form-check-label" for="defaultCheck1">
                Cambios Archivos Principal (CAP)
            </label>
        </div>

        <div class="card-body">
            <table id="tabla_bitacora_grilla" class="table dt-responsive " style="width:100%">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Buzón Origen</th>
                        <th>Usuario </th>
                        <th>Acción </th>
                        <th>Mensaje</th>
                    </tr>

                </thead>
            </table>
        </div>

        <div class="form-row">
            <div class="col-md-12 group-button-align">
                <button type="button" class="btn text-nowrap btn-min-w  btn-secondary  btn_cerrar_bitacora">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Bitacora fin-->

<!-- modal agregar referencia -->
<div class="modal fade" id="modalReferenciaSGD" tabindex="-1" aria-labelledby="modalReferenciaSGDLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalReferenciaSGDLabel">Agregar Referencia de SGD</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div>
        <table class="w-100" id="referencia-resultados">
        </table>
        </div>
        <h6 class="modal-title">Archivos Seleccionados</h6>
        <div id="referencia-seleccionados">
        
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary">Aceptar</button>
      </div>
    </div>
  </div>
</div>

@include('componentes.BitacoraModal')
@include('componentes.PDFModal')

@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" integrity="sha512-jU/7UFiaW5UBGODEopEqnbIAHOI8fO6T99m7Tsmqs2gkdujByJfkCbbfPSN4Wlqlb9TGnsuC0YgUgWkRBK7B9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
<link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<style type="text/css">
    .nav-header {
        text-align: center;
        --padding-bottom: 20px;
    }

    .nav-tabs {
        padding-left: 15px;
        margin-bottom: 0;
        border: none;
    }

    .tab-content {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 15px;
    }


    .disabled {
        background-color: #e9ecef !important;
    }

    .row_archivar {
        display: none;
    }

    .flex-container {
        display: flex;
        flex-wrap: nowrap;
        background-color: #e9f1fe;
        border: 1px solid #005c9e;
        margin-bottom: 30px;
    }


    .item a,
    a:hover {
        text-decoration: underline !important;
    }

    .tox-statusbar__branding {
        display: none;
    }


    .label-info {
        background-color: #5bc0de
    }

    .label-info[href]:focus,
    .label-info[href]:hover {
        background-color: #31b0d5
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
        transition: color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;

    }

    .label-danger {
        background-color: #d9534f;
    }

    .label-warning {
        background-color: #f0ad4e;
    }

    .addFrm {
        float: right;
    }

    .btnFirma {
        float: right;
        margin-left: 10px;
        margin-bottom: 10px;
        line-height: 40px;
    }

    .view-pdf {
        float: right;
    }

    .cke {
        margin-top: 15px !important;
    }

    .swal2-container {
        z-index: 1050 !important;
    }

    .btn-min-w {
        min-width: 10% !important;
    }

    .multiselect-native-select {
        display: grid;
    }

    .item a,
    a:hover {
        text-decoration: underline !important;
    }

    .tox-statusbar__branding {
        display: none;
    }
    .file-container-all {flex-wrap: wrap !important;}

    tbody.text-hide .dropdown .btn-secondary, tbody.text-hide .fondo_estado {
        background-color:white!important;
        border:1px solid white;
    }
    table > thead{
        background:rgba(0, 0, 0, .1);
    }
    

</style>

<link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js" integrity="sha512-oQq8uth41D+gIH/NJvSJvVB85MFk1eWpMK6glnkg6I7EdMqC1XVkW7RxLheXwmFdG03qScCM7gKS/Cx3FYt7Tg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ url('js/ckfinder/ckfinder.js') }}"></script>

<!-- script src="https://cdn.tiny.cloud/1/vrmhk77mujotoyysy5q37jmn5r0kodurg8u7vcs6b5hmzco8/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script> 



<script src="/js/bootstrap-multiselect.js"></script>
<script src="/js/fglobales.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
<script type="text/javascript" src="//cdn.datatables.net/plug-ins/2.2.2/sorting/datetime-moment.js"></script>

<!-- <script src="//cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
<script src="//cdn.ckeditor.com/ckeditor5/41.4.2/classic/translations/es.js"></script> -->

<script>
    $.fn.dataTable.moment( 'HH:mm MMM D, YY' );
    var grilla_despachados;
    const accionesFlujo1 = @json($acciones_tipoflujo1);
    const accionesFlujo2 = @json($acciones_tipoflujo2);
    const accionesFlujo3 = @json($acciones_tipoflujo3);
    const listadoBuzones = @json($listadoBuzones);
    
    const estadosTramitacion = @json($listado_parametros['estado_tramitacion']);
    const pathFiles = "";
    var bloqueo_accion = false;
    isDelete = true;
    var objDoc = null;

    var allBuzonesT2 = @json($allBuzonesT2);
    var allBuzones = @json($allBuzones);
    var allBuzones2 = @json($allBuzones2);
    var listadoDocPendientes = @json($listDocPendientesBuzon);
    var idTipoFlujo = "";
    

    var listadoDocPendientes = @json($listDocPendientesBuzon);
    var idTipoFlujo = "";

    const txtArchivado = [];
    txtArchivado[0] = ['Archivar', 'archivará', 'Archivado'];
    txtArchivado[1] = ['Desarchivar', 'desarchivará', 'Desarchivado'];

    aplicaFrm = @json($aplicaFrm);



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
    //owl = $('.owl-carousel').owlCarousel(); 

    $("a[data-toggle=\"tab\"]").on("shown.bs.tab", function(e) {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });
    $.fn.dataTable.ext.errMode = 'none';


    var referenciaAnexos =[];
    var referenciaAnexosOtros =[];
 

    var totalArchivosCargados = 0;
    var numArchivosPorCargar = 0;
    var numArchivosFinalizados = 0;

    function iniQueueComplete() {
        totalArchivosCargados = 0;
        numArchivosPorCargar = 0;
        numArchivosFinalizados = 0;
    }

    function archivosCargados() {

        if (totalArchivosCargados == numArchivosPorCargar) {
            numArchivosFinalizados = 1;

            return numArchivosFinalizados;
        } else if (numArchivosPorCargar == 0)
            return 1;
        else
            return numArchivosFinalizados;


    }
    
    idDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
    $('#btnBuscadorGeneral').click(function(e) {
        $('#resultadoBusquedaGral').html("Buscando...").removeClass('text-success').removeClass('text-danger');
        var _token = $("input[name='_token']").val();
        var texto = $("#buscador_general").val();
        var buzon = $("input[name='hiddIdBuzon']").val();
        urlAccion = route('buzones.buscar_global',{'id':buzon});
        $.ajax({
            url: urlAccion,
            type: 'POST',
            dataType: 'json',
            data: {
                _token: _token,
                texto: texto,
                buzon: buzon,
            }
            })
            .done(function(data, textStatus, jqXHR) {
              

                console.log(data.data.mensaje);
                if(jqXHR.status ==200){

                    $('#resultadoBusquedaGral').html(data.data.mensaje).addClass('text-success');
                    $('#resultadoBusquedaGral').append('</br>');
                    data.data.carpetas.forEach((e) => {
                        let link = $("<span style='cursor:pointer' data-busqueda='"+texto+"' data-buzon='"+e+"' class='pointer-event btn-link pr-1'>")
                            .html(e)
                            .click(function(ev){

                                let q = $(ev.currentTarget).data('busqueda');
                                let opt = "";
                                if (+q===parseInt(q)){ opt="_buscar_id_doc"; }else{ opt="_buscar_origen_materia"; }
                                switch($(ev.currentTarget).data('buzon')){
                                    case 'Por Recibir':
                                        $("#gp_buscar_id_doc").val('');
                                        $("#gp_buscar_origen_materia").val('');
                                        $("#gp"+opt).val(q);
                                        $("#gp_buscar_tipo_doc").multiselect('selectAll',true);
                                        $("#gp_buscar_estado").multiselect('selectAll',true);
                                        cambio_texto_boton_carpetas($(ev.currentTarget).data('buzon'));
                                    break;
                                    case 'Recibidos':
                                        $("#gr_buscar_id_doc").val('');
                                        $("#gr_buscar_origen_materia").val('');
                                        $("#gr"+opt).val(q);
                                        $("#gr_buscar_tipo_doc").multiselect('selectAll',true);
                                        $("#gr_buscar_estado").multiselect('selectAll',true);
                                        cambio_texto_boton_carpetas($(ev.currentTarget).data('buzon'));
                                        grilla_recibidos
                                            .columns(5).search($('#gr_buscar_id_doc').val())
                                            .columns(7).search($('#gr_buscar_origen_materia').val())
                                            //.columns(14).search(tipos.map(valor => '^' + valor + '$').join('|'), true, true)
                                            .draw();
                                    break;
                                    case 'Despachados':
                                        $("#gd_buscar_id_doc").val('');
                                        $("#gd_buscar_origen_materia").val('');
                                        $("#gd"+opt).val(q);
                                        $("#gd_buscar_tipo_doc").multiselect('selectAll',true);
                                        $("#gd_buscar_estado").multiselect('selectAll',true);
                                        cambio_texto_boton_carpetas($(ev.currentTarget).data('buzon'));
                                    break;
                                    }
                                console.log("-"+$(ev.currentTarget).data('buzon')+"-");
                            })
                        $('#resultadoBusquedaGral').append(link)
                    })
                }else if(jqXHR.status ==201){
                    $('#resultadoBusquedaGral').html(data.data.mensaje).addClass('text-danger');
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                $('#resultadoBusquedaGral').html("ERROR en la búsqueda").addClass('text-danger');
                toastr.error("Falla en la búsqueda global.", "¡Aviso!");
            });

    });

   //dropzone
    Dropzone.options.dropzonePrincipal = {
        headers: {
            'X-CSRF-TOKEN': "{{csrf_token()}}"
        },
        url: route('files.store'),
        autoProcessQueue: false,
        uploadMultiple: true,
        maxFilesize: 50, //MB
        maxFiles: 1,
        dictDefaultMessage: "Arrastre y suelte archivos pdf aquí <br> <i class='fa fa-upload fa-lg'></i>",
        acceptedFiles: "application/pdf",
        addRemoveLinks: true,
        params: {
            'id_tipo_archivo': 1
        },
        createImageThumbnails: true,
        timeout: 100000,
        init: function() {
            dropzonePrincipal = this; // closure              

            if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
                var _this = this;
                // Remove all files
                _this.removeAllFiles();
            }

            $(".btn-delete").click(function() {
                dropzoneAnexo.removeAllFiles(true);
            });

            this.on("addedfile", function(file) {
                numArchivosPorCargar++;
            });

            this.on("success", function(file) {
                totalArchivosCargados++;
            });
            this.on("queuecomplete", function(file) {

            });
        },
        sending: function(file, xhr, formData) {
            var idb = $("input[name='hiddIdDocumentoBuzon']").val();
            var idoc = $("input[name='hiddIdDocumento']").val();
            formData.append('id_documento_buzon', idb);
            formData.append('id_documento', idoc);
        }
    };

    Dropzone.options.dropzoneAnexo = {
        headers: {
            'X-CSRF-TOKEN': "{{csrf_token()}}"
        },
        url: route('files.store'),
        autoProcessQueue: false,
        uploadMultiple: true,
        maxFilesize: 20, //MB
        //maxFiles: 2,
        dictDefaultMessage: "Arrastre y suelte archivos pdf aquí <br> <i class='fa fa-upload fa-lg'></i>",
        acceptedFiles: "application/pdf",
        addRemoveLinks: true,
        params: {
            'id_tipo_archivo': 2
        },
        createImageThumbnails: true,
        timeout: 100000,
        parallelUploads: 20,
        init: function() {
            dropzoneAnexo = this; // closure 

            if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
                var _this = this;
                // Remove all files
                _this.removeAllFiles();
            }

            $(".btn-delete").click(function() {
                dropzoneAnexo.removeAllFiles(true);
            });
            this.on("queuecomplete", function(file) {

            });

            this.on("addedfile", function(file) {
                if(!file.tipo=='referencia')
                    numArchivosPorCargar++;
            });

            this.on("success", function(file) {
                console.log(file);
                totalArchivosCargados++;
            });
            this.on("error", function(file) {
                console.log("error en cargar el archivo ",file)
            });

        },
        sending: function(file, xhr, formData) {
            console.log(file);
            var idb = $("input[name='hiddIdDocumentoBuzon']").val();
            var idoc = $("input[name='hiddIdDocumento']").val();
            formData.append('id_documento_buzon', idb);
            formData.append('id_documento', idoc);

        },
    };

    Dropzone.options.dropzoneOtros = {
        headers: {
            'X-CSRF-TOKEN': "{{csrf_token()}}"
        },
        url: route('files.store'),
        autoProcessQueue: false,
        uploadMultiple: true,
        maxFilesize: 50, //MB
        //maxFiles: 2,
        dictDefaultMessage: "Arrastre y suelte archivos pdf, word, excel, zip o rar aquí <br> <i class='fa fa-upload fa-lg'></i>",
        acceptedFiles: "application/pdf,application/msword,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.rar, application/x-rar-compressed, application/octet-stream,application/zip, application/octet-stream, application/x-zip-compressed, multipart/x-zip",
        addRemoveLinks: true,
        params: {
            'id_tipo_archivo': 3
        },
        createImageThumbnails: true,
        timeout: 100000,
        parallelUploads: 20,
        init: function() {
            dropzoneOtros = this; // closure              

            if (this.getQueuedFiles().length == 0 && this.getUploadingFiles().length == 0) {
                var _this = this;
                // Remove all files
                _this.removeAllFiles();

            }
            this.on("addedfile", function(file) {
                numArchivosPorCargar++;
            });

            this.on("success", function(file) {
                totalArchivosCargados++;
            });
            this.on("queuecomplete", function(file) {

            });

        },
           
        sending: function(file, xhr, formData) {
            
            var idb = $("input[name='hiddIdDocumentoBuzon']").val();
            var idoc = $("input[name='hiddIdDocumento']").val();
            formData.append('id_documento_buzon', idb);
            formData.append('id_documento', idoc);


        }
    };

    /* VERSIONES PDF */

    $(".boton_desplegar_versiones_anteriores").click(function(e) {
        $('#card_ocultar_versiones').show();
        $('#card_desplegar_versiones').hide();
        $("#dropzone-principal").addClass("displayDropzone");

    });
    $(".boton_ocultar_versiones_anteriores").click(function(e) {
        $('#card_ocultar_versiones').hide();
        $('#card_desplegar_versiones').show();
        $("#dropzone-principal").removeClass("displayDropzone");

    });

    /* **DOCUMENTOS** SCRIPT */
    const editor_cuerpo = CKEDITOR.replace('form_cuerpo', {
        "removePlugins": "exportpdf",
        filebrowserBrowseUrl: route('ckfinder_browser'),
        filebrowserImageBrowseUrl: route('ckfinder_browser')+"?type=Images&token=123",
        filebrowserImageUploadUrl: route('ckfinder_connector')+"?command=QuickUpload&type=Images",
    });

    const editor_distribucion = CKEDITOR.replace('form_distribucion', {
        filebrowserBrowseUrl: "{{ route('ckfinder_browser') }}",
        filebrowserImageBrowseUrl: "{{ route('ckfinder_browser') }}?type=Images&token=123",
        filebrowserImageUploadUrl: "{{ route('ckfinder_connector') }}?command=QuickUpload&type=Images",
        height: 100,
        "removePlugins": "exportpdf",
    });

    CKFinder.config({
        connectorPath: '/ckfinder/connector'
    });

    $(".nuevo_documento").click(function(e) {

        //cambiar carpeta a despachados
        cambio_texto_boton_carpetas("Despachados");

        $("#collapseOne").collapse('hide');
        $("#titulo_accion").html("Nuevo Documento");
        $('#card_crear_documento').show();
        $('#card_bitacora').hide();
        clear_form();
        deshabilita_campos();
        $('#form_tipo_documento').prop("disabled", false);
        $("#form_crear_editar :input").prop("disabled", false);
        editor_cuerpo.setReadOnly(false);
        editor_distribucion.setReadOnly(false);
        // tinymce.get("form_cuerpo").mode.set("design");
        // tinymce.get("form_distribucion").mode.set("design");

        $('.btn-guardar-submit').show();
        habilita_boton('btn-guardar-submit');
        if ($("#idAsignado").html() != "No Asignado") {
            $('.btn-guardar-submit-edit').show();
        } else {
            $('.btn-guardar-submit').html("Guardar");
        }
        habilita_boton('btn-guardar-submit-edit');

        /* responder a */

        if (e.isTrigger && $("input[name='hiddIdResponder']").val() != '') {
            $('#form_respuesta_a').multiselect({
                numberDisplayed: 6
            });
            $('#form_respuesta_a').multiselect('deselectAll', true);
            $('#form_respuesta_a').multiselect('select', $("input[name='hiddIdResponder']").val());
            $('#form_respuesta_a').multiselect('refresh');
        } else
            $("input[name='hiddIdResponder']").val('');

    });

    function deshabilita_campos() {
        $('#form_tipo_documento').prop("disabled", true);
        $("#form_crear_editar :input").prop("disabled", true);
        editor_cuerpo.setReadOnly(true);
        editor_distribucion.setReadOnly(true);
        // tinymce.get("form_cuerpo").mode.set("readonly");
        // tinymce.get("form_distribucion").mode.set("readonly");
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

        $(".dz-hidden-input").prop("disabled", true);
        isDelete = false;

        form_acciones_solicitadas_el.disabled = true;
        form_comentario_el.disabled = true;
        form_otros_destinatarios_el.disabled = true;
        form_comentario_otro_el.disabled = true;
    }

    function habilita_campos() {
        $('#form_tipo_documento').prop("disabled", false);
        $("#form_crear_editar :input").prop("disabled", false);
        editor_cuerpo.setReadOnly(false);
        editor_distribucion.setReadOnly(false);
        // tinymce.get("form_cuerpo").mode.set("design");
        // tinymce.get("form_distribucion").mode.set("design");
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

    function clear_form() {
        ///botones
        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');
        $(".item-bitacora").hide();
        //inicializa formulario

        $('#form_crear_editar').trigger("reset");
        $("input[name='encabezado']").val('');
        editor_cuerpo.setData('');
        editor_distribucion.setData('');
        //tinymce.get("form_cuerpo").setContent('');
        //tinymce.get("form_distribucion").setContent('');
        $("textarea[id='form_comentario_el']").val('');
        $("textarea[id='form_comentario_otro_el']").val('');

        owl = $('.owl-carousel').owlCarousel();
        owl.trigger('destroy.owl.carousel');
        owl.find('.owl-stage-outer').children().unwrap();
        owl.removeClass("owl-center owl-loaded owl-text-select-on");

        //versiones

        $('#card_ocultar_versiones').hide();
        $('#card_desplegar_versiones').show();
        $("#dropzone-principal").removeClass("displayDropzone");

        $('#row_cuerpo').hide();
        $('#row_anexo').hide();
        $(".row_archivar").hide();

        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
        $('#form_respuesta_a').multiselect('deselectAll', true);

        $("#form_destinatario_principal").val(null);
        $("#form_destinatario_principal").trigger('change');

        $('#form_otros_destinatarios_el').tagsinput('removeAll');

        $("input[name='hiddIdDocumentoBuzon']").val('');
        $("input[name='hiddIdDocumento']").val('');
        $("input[name='hiddIdOrigen']").val('');
        $("input[name='hiddIdFileDelete']").val('');

        $("#idAsignado").text('No Asignado');
        $("#idFolio").text('No Asignado');
        $("#idFecha").text('No Asignado');

        //listado de visaciones y firmas
        $('.row_txt_firmar').hide();
        $('#datos_bitacora_simple').html('');

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
        $("#dropzone-principal").removeClass("displayDropzone");

    }

    $(".btn_cerrar_guardar").click(function(e) {
        clear_form();
        clearTimeout(timeoutId);
        $('#card_crear_documento').hide();
        $('#form_crear_editar').trigger("reset");
        $("#collapseOne").collapse('show');
        recarga_grilla_recibidos();
        recarga_grilla_despachados();
    });

    $(".btn_cerrar_bitacora").click(function(e) {
        $('#card_bitacora').hide();
        $("#collapseOne").collapse('show');
    });

    $("#form_tipo_documento").change(function() {
        datosTipoDoc($(this).val());
    });

    function datosTipoDoc(id) {
        $.ajax({
            //url: "../tipos_documentos/" + id,
            url: route('tipos_documentos.show',{'id':id}), 
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.status == '400') {
                    toastr.error(data.data.comentario, "¡Aviso!");
                } else {
                    if (data.status == '200' || data.status == '201') {
                        $("input[name='encabezado']").val(data.data.plantilla_encabezado);
                        $("input[name='hiddIdOrigen']").val(data.data.id_tipo_origen);

                        idTipoFlujo = data.data.id_tipo_flujo;


                        if (data.data.plantilla_cuerpo !== null) {
                            editor_cuerpo.setData(data.data.plantilla_cuerpo);
                        } else {
                            editor_cuerpo.setData('');
                        }

                        //editor_distribucion.setData(data.data.plantilla_distribucion);
                        if (data.data.plantilla_distribucion !== null) {
                            editor_distribucion.setData(data.data.plantilla_distribucion);
                        } else {
                            editor_distribucion.setData('');
                        }

                        //habilita respuesta a: solo a flujo libre

                        if (idTipoFlujo != 1) {
                            $('#form_respuesta_a').multiselect('disable');
                            $('#form_respuesta_a').multiselect('deselectAll', true);
                        } else
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
                habilita_boton('btn-guardar-submit');
                habilita_boton('btn-guardar-submit-edit');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error("Falla al obtener datos", "¡Aviso!");
                habilita_boton('btn-guardar-submit');
                habilita_boton('btn-guardar-submit-edit');

            }
        });
    }

    //SUBMIT
    $(".btn-guardar-submit").click(function(e) {
        var materia = $("input[name='materia']").val();
        var tipo_documento = $("select[name='tipo_documento']").val();
        var nivel_acceso = $("select[name='nivel_acceso']").val();
        var efectos_terceros = $("select[name='efectos_terceros']").val();

        var errores = "";
        if (materia.length == 0 || materia.length > 500) {
            errores = errores + "La materia debe tener entre 1 y 500 caracteres.<br>";
        }
        if (tipo_documento == "") {
            errores = errores + "Seleccione un tipo de documento.<br>";
        }
        if (nivel_acceso == "") {
            errores = errores + "Seleccione un nivel de acceso.<br>";
        }

        if (efectos_terceros == "") {
            errores = errores + "Seleccione un efecto sobre terceros.<br>";
        }

        if (errores != "") {
            toastr.error(errores, "¡Aviso!");
            habilita_boton('btn-guardar-submit');
            habilita_boton('btn-guardar-submit-edit');
            habilita_boton('btn-enviar-submit');
            habilita_boton('btn_cerrar_guardar');
            habilita_boton('btn-visar');
            habilita_boton('btn-firmar');
            habilita_boton('btn-archivar');
            habilita_boton('btn-recibir-submit');
            habilita_boton('btn-derivar');
            habilita_boton('btn-derivar-2');
        } else {
            e.preventDefault();
            $('.btn-guardar-submit').html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando'
            );
            $(".print-error-msg").hide();
            deshabilita_boton('btn-guardar-submit');
            deshabilita_boton('btn-guardar-submit-edit');
            deshabilita_boton('btn-enviar-submit');
            deshabilita_boton('btn_cerrar_guardar');
            deshabilita_boton('btn-visar');
            deshabilita_boton('btn-firmar');
            deshabilita_boton('btn-archivar');
            deshabilita_boton('btn-recibir-submit');
            deshabilita_boton('btn-visar-derivar');
            deshabilita_boton('btn-firmar-derivar');
            deshabilita_boton('btn-derivar');
            deshabilita_boton('btn-derivar-2');
            clearTimeout(timeoutId);
            guarda_documento(1);
        }
    });

    $(".btn-enviar-submit").click(function(e) {
        var materia = $("input[name='materia']").val();
        var tipo_documento = $("select[name='tipo_documento']").val();
        var nivel_acceso = $("select[name='nivel_acceso']").val();
        var efectos_terceros = $("select[name='efectos_terceros']").val();

        var errores = "";
        if (materia.length == 0 || materia.length > 500) {
            errores = errores + "La materia debe tener entre 1 y 500 caracteres.<br>";
        }
        if (tipo_documento == "") {
            errores = errores + "Seleccione un tipo de documento.<br>";
        }
        if (nivel_acceso == "") {
            errores = errores + "Seleccione un nivel de acceso.<br>";
        }
        if (efectos_terceros == "") {
            errores = errores + "Seleccione un efecto sobre terceros.<br>";
        }

        if (errores != "") {
            toastr.error(errores, "¡Aviso!");
            habilita_boton('btn-guardar-submit');
            habilita_boton('btn-guardar-submit-edit');
            habilita_boton('btn-enviar-submit');
            habilita_boton('btn_cerrar_guardar');
            habilita_boton('btn-visar');
            habilita_boton('btn-firmar');
            habilita_boton('btn-archivar');
            habilita_boton('btn-recibir-submit');
            habilita_boton('btn-derivar');
            habilita_boton('btn-derivar-2');
        } else {
            e.preventDefault();
            $(".print-error-msg").hide();
            deshabilita_boton('btn-enviar-submit');
            deshabilita_boton('btn-guardar-submit');
            deshabilita_boton('btn-guardar-submit-edit');
            deshabilita_boton('btn_cerrar_guardar');
            deshabilita_boton('btn-visar');
            deshabilita_boton('btn-firmar');
            deshabilita_boton('btn-archivar');
            deshabilita_boton('btn-editar');
            deshabilita_boton('btn-recibir-submit');
            //guarda_documento();
            enviar_documento();
        }

    });

    function deshabilita_boton(tClase) {
        $('.' + tClase + '').prop("disabled", true);
    }

    function habilita_boton(tClase) {
        $('.' + tClase + '').prop("disabled", false);
    }

    //async function guarda_documento(accion, callback)
    function guarda_documento(accion, callback) {

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
        var distribucion = editor_distribucion.getData();
        // var cuerpo = tinymce.get("form_cuerpo").getContent();
        // var distribucion = tinymce.get("form_distribucion").getContent();
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

        //check para firma anexos
        var aParaFirma = [];
        $('input:checkbox[name="chkFirmaAnexo"]:checked').each(function() {
            if (this.checked) {
                aParaFirma.push($(this).val());
            }
        });

        setea_sesiones_recibidos();
        setea_sesiones_despachados();

        if (hiddIdDocumento == '') //crear
        {
            var urlAccion = route('buzones.store_documento',{'id':hiddIdBuzon});
            var typeAccion = 'POST';
        } else //editar
        {
            var urlAccion = route('documentos.update',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento});
            var typeAccion = 'PUT';
        }

        

        $.ajax({
            url: urlAccion,
            type: typeAccion,
            dataType: 'json',

            data: {
                _token: _token,
                tipo_documento: tipo_documento,
                nivel_acceso: nivel_acceso,
                descripcion: descripcion,
                efectos_terceros: efectos_terceros,
                contestar_hasta: contestar_hasta,
                materia: materia,
                anterior: anterior,
                encabezado: encabezado,
                cuerpo: cuerpo,
                distribucion: distribucion,
                responder: responder,
                buzon: hiddIdBuzon,
                destinatarioPrincipal: destinatarioPrincipal,
                destinatarioOtros: otrosDestinatarios,
                comentarioPrincipal: comentarioPrincipal,
                comentarioOtros: comentarioOtros,
                acciones_solicitadas: acciones_solicitadas,
                hiddIdDocumento: hiddIdDocumento,
                hiddIdDocumentoBuzon: hiddIdDocumentoBuzon,
                hiddIdFileDelete: hiddIdFileDelete,
                carpeta: 3,
                aParaFirma: aParaFirma
            },
            success: function(data) {
                if (data.status == '200') {
                    if (accion == 1) //guarda
                    {
                        dropzonePrincipal.processQueue();
                        dropzoneOtros.processQueue();
                        dropzoneAnexo.processQueue();

                        intervalCarga = setInterval(function() {
                            varTermina = valida_carga();
                            if (varTermina == 1) {
                                // console.log('completado');
                                clearInterval(intervalCarga);
                                iniQueueComplete();

                                //codigo a ejecutar
                                toastr.success("Documento actualizado", "¡Aviso!");

                                $('#card_crear_documento').hide();
                                $("#collapseOne").collapse('show');

                                //fn_grilla_recibidos();
                                recarga_grilla_recibidos();

                                //fn_grilla_despachados();
                                recarga_grilla_despachados();

                                habilita_boton('btn-guardar-submit');
                                habilita_boton('btn-guardar-submit-edit');
                                habilita_boton('btn-enviar-submit');
                                habilita_boton('btn_cerrar_guardar');
                                habilita_boton('btn-visar');
                                habilita_boton('btn-firmar');
                                habilita_boton('btn-archivar');
                                habilita_boton('btn-recibir-submit');
                                habilita_boton('btn-derivar');
                                habilita_boton('btn-derivar-2');
                                $('.btn-guardar-submit').html('Guardar y Cerrar');
                            } else
                                valida_carga();
                        }, 3000);

                    }

                    if (accion == 2) {

                        dropzonePrincipal.processQueue();
                        dropzoneOtros.processQueue();
                        dropzoneAnexo.processQueue();

                        intervalCarga = setInterval(function() {
                            varTermina = valida_carga();
                            if (varTermina == 1) {
                                //console.log('completado');
                                clearInterval(intervalCarga);
                                iniQueueComplete();

                                //codigo a ejecutar
                                callback(data);
                                respuesta_guarda = data;
                                habilita_boton('btn-guardar-submit');
                                habilita_boton('btn-guardar-submit-edit');
                                habilita_boton('btn-enviar-submit');
                                habilita_boton('btn_cerrar_guardar');
                                habilita_boton('btn-visar');
                                habilita_boton('btn-firmar');
                                habilita_boton('btn-archivar');
                                habilita_boton('btn-recibir-submit');
                                habilita_boton('btn-derivar');
                                habilita_boton('btn-derivar-2');
                                $('.btn-guardar-submit').html('Guardar y Cerrar');
                                recarga_grilla_recibidos();
                                recarga_grilla_despachados();
                            } else
                                valida_carga();
                        }, 3000);

                    }
                } else if (data.status == '201') {
                    Swal.fire({
                        //icon: 'info',
                        title: 'Borrador guardado',
                        html: "Se ha guardado exitosamente el borrador del documento: <br>" +
                            "<b>ID: " + data.data.identificador + "</b><br>" +
                            "<b>Materia: " + data.data.materia + "</b>",
                    });
                    habilita_campos();
                    cargar_datos_grilla(data.data.id_documento, data.data.rel_documento_buzon[0]['id_documento_buzon'], data.data.rel_documento_buzon[0]['id_documento_buzon_padre'], 3, 1);

                    $('#form_tipo_documento').prop("disabled", true);

                    //habilita botón enviar y guardar
                    $('.btn-guardar-submit').show();
                    habilita_boton('btn-guardar-submit');
                    $('.btn-guardar-submit-edit').show();
                    habilita_boton('btn-guardar-submit-edit');
                    habilita_boton('btn-enviar-submit');
                    habilita_boton('btn_cerrar_guardar');
                    habilita_boton('btn-visar');
                    habilita_boton('btn-firmar');
                    habilita_boton('btn-archivar');
                    habilita_boton('btn-recibir-submit');
                    habilita_boton('btn-derivar');
                    habilita_boton('btn-derivar-2');
                    $('.btn-guardar-submit').html('Guardar y Cerrar');
                    habilita_boton('btn-vp');

                    if (data.data.id_documento != '')
                        $('.btn-enviar-submit').show();
                    habilita_boton('btn-enviar-submit');
                    habilita_boton('btn-guardar-submit');
                    habilita_boton('btn-guardar-submit-edit');
                    habilita_boton('btn_cerrar_guardar');
                    habilita_boton('btn-firmar');
                    habilita_boton('btn-archivar');
                    habilita_boton('btn-recibir-submit');
                    habilita_boton('btn-derivar');
                    habilita_boton('btn-derivar-2');
                    $('.btn-guardar-submit').html('Guardar y Cerrar');

                    //actualiza grilla despachados
                    fn_grilla_despachados();
                    recarga_grilla_despachados();
                    setTimeout(function() {
                        auto_guardado();
                        //console.log('despues de creado');
                    }, 2000);

                } else {
                    toastr.error(data.data.comentario, "¡Aviso!");
                    habilita_boton('btn-guardar-submit');
                    habilita_boton('btn-guardar-submit-edit');
                    habilita_boton('btn-enviar-submit');
                    habilita_boton('btn_cerrar_guardar');
                    habilita_boton('btn-visar');
                    habilita_boton('btn-firmar');
                    habilita_boton('btn-archivar');
                    habilita_boton('btn-recibir-submit');
                    habilita_boton('btn-derivar');
                    habilita_boton('btn-derivar-2');
                    if ($("#idAsignado").html() != "No Asignado") {
                        $('.btn-guardar-submit').html('Guardar y Cerrar');
                    } else {
                        $('.btn-guardar-submit').html('Guardar');
                    }
                }

            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en el documento 2", "¡Aviso!");

                if ($("#idAsignado").html() != "No Asignado") {
                    $('.btn-guardar-submit').html('Guardar y Cerrar');
                } else {
                    $('.btn-guardar-submit').html('Guardar');
                }
                habilita_boton('btn-guardar-submit');
                habilita_boton('btn-guardar-submit-edit');
                habilita_boton('btn-enviar-submit');
                habilita_boton('btn_cerrar_guardar');
                habilita_boton('btn-visar');
                habilita_boton('btn-firmar');
                habilita_boton('btn-archivar');
                habilita_boton('btn-recibir-submit');
                habilita_boton('btn-derivar');
                habilita_boton('btn-derivar-2');
            }

        });
    }

    function guarda_destinatarios_documento(accion) {

        clearTimeout(timeoutId);
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
        var cuerpo = editor_cuerpo.getData();
        var distribucion = editor_distribucion.getData();
        // var cuerpo = tinymce.get("form_cuerpo").getContent();
        // var distribucion = tinymce.get("form_distribucion").getContent();

        var tipo_documento = $("select[name='tipo_documento']").val();
        var nivel_acceso = $("select[name='nivel_acceso']").val();
        var efectos_terceros = $("select[name='efectos_terceros']").val();
        var contestar_hasta = $("input[name='contestar_hasta']").val();
        var materia = $("input[name='materia']").val();
        var anterior = $("input[name='anterior']").val();
        var descripcion = $("textarea[name='descripcion']").val();
        var encabezado = $("input[name='encabezado']").val();
        var hiddIdFileDelete = $("input[name='hiddIdFileDelete']").val();
        var opcionGuardarDestinatarios = 1;
        //check para firma anexos
        var aParaFirma = [];
        $('input:checkbox[name="chkFirmaAnexo"]:checked').each(function() {
            if (this.checked) {
                aParaFirma.push($(this).val());
            }
        });


        $.ajax({
            url: route('documentos.update',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
            type: 'PUT',
            dataType: 'json',
            data: {
                _token: _token,
                contestar_hasta: contestar_hasta,
                buzon: hiddIdBuzon,
                destinatarioPrincipal: destinatarioPrincipal,
                destinatarioOtros: otrosDestinatarios,
                comentarioPrincipal: comentarioPrincipal,
                comentarioOtros: comentarioOtros,
                acciones_solicitadas: acciones_solicitadas,
                hiddIdDocumento: hiddIdDocumento,
                cuerpo: cuerpo,
                distribucion: distribucion,
                hiddIdDocumentoBuzon: hiddIdDocumentoBuzon,
                carpeta: 2,
                opcionGuardar: opcionGuardarDestinatarios,
                tipo_documento: tipo_documento,
                nivel_acceso: nivel_acceso,
                descripcion: descripcion,
                efectos_terceros: efectos_terceros,
                materia: materia,
                anterior: anterior,
                encabezado: encabezado,
                hiddIdFileDelete: hiddIdFileDelete,
                estado: 4,
                aParaFirma: aParaFirma
            },
            success: function(data) {
                if (data.status == '200') {
                    if (accion == 2) //derivar
                        derivar_documento();

                    if (accion == 6) //visar
                        visar_documento();

                    if (accion == 8) //visar y derivar
                        visar_derivar_documento();
                } else {
                    toastr.error("Falla al guardar destinatarios", "¡Aviso!");
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en la actualización del documento", "¡Aviso!");
            }

        });
    }

    function valida_carga() {
        nroCarga = archivosCargados();
        console.log("valida_carga",nroCarga);
        if (nroCarga == 0) {
            setTimeout(function() {
                valida_carga();
            }, 2000);
        }

        return nroCarga;
    }

    function accion_editar_guardar(idCarpeta) //**** revisar si se puede usar funcion que guarda documento ****//
    {
        //if(idCarpeta != 2){
        $('.btn-recibir-submit').html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando'
        );
        //}
        $('.btn-guardar-submit-edit').html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando'
        );
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
        var distribucion = editor_distribucion.getData();
        // var cuerpo = tinymce.get("form_cuerpo").getContent();
        // var distribucion = tinymce.get("form_distribucion").getContent();
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        var hiddIdFileDelete = $("input[name='hiddIdFileDelete']").val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        var comentarioPrincipal = $('#form_comentario_el').val();
        var comentarioOtros = $('#form_comentario_otro_el').val();
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();

        //check para firma anexos
        var aParaFirma = [];
        $('input:checkbox[name="chkFirmaAnexo"]:checked').each(function() {
            if (this.checked) {
                aParaFirma.push($(this).val());
            }
        });

        deshabilita_boton('btn-recibir-submit');
        deshabilita_boton('btn_cerrar_guardar');
        deshabilita_boton('btn-guardar-submit');
        deshabilita_boton('btn-enviar-submit');
        deshabilita_boton('btn-visar');
        deshabilita_boton('btn-firmar');
        deshabilita_boton('btn-archivar');
        deshabilita_boton('btn-visar-derivar');
        deshabilita_boton('btn-derivar-2');
        deshabilita_boton('btn-derivar');
        deshabilita_boton('btn-firmar-derivar');

        setea_sesiones_recibidos();
        setea_sesiones_despachados();

        $.ajax({
            url: route('documentos.update',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
            type: 'PUT',
            dataType: 'json',
            data: {
                _token: _token,
                tipo_documento: tipo_documento,
                nivel_acceso: nivel_acceso,
                descripcion: descripcion,
                efectos_terceros: efectos_terceros,
                contestar_hasta: contestar_hasta,
                materia: materia,
                anterior: anterior,
                encabezado: encabezado,
                cuerpo: cuerpo,
                distribucion: distribucion,
                buzon: hiddIdBuzon,
                destinatarioPrincipal: destinatarioPrincipal,
                destinatarioOtros: otrosDestinatarios,
                comentarioPrincipal: comentarioPrincipal,
                comentarioOtros: comentarioOtros,
                acciones_solicitadas: acciones_solicitadas,
                hiddIdDocumento: hiddIdDocumento,
                hiddIdDocumentoBuzon: hiddIdDocumentoBuzon,
                hiddIdFileDelete: hiddIdFileDelete,
                carpeta: idCarpeta,
                estado: ((idCarpeta == 2) ? 4 : 1),
                aParaFirma: aParaFirma
            },
            success: function(data) {
                if (data.status == '200') {
                    console.log("procesando lista de archivos",numArchivosPorCargar);
                    console.log("dropzoneAnexo lista de files ",dropzoneAnexo.getQueuedFiles())
                    console.log("dropzoneAnexo lista de referencias ",referenciaAnexos)
                    dropzoneAnexo.processQueue();
                    dropzoneOtros.processQueue();
                    dropzonePrincipal.processQueue();





                    referenciaAnexos.forEach(function(o,i){
                        //console.log(o);
                        let item ={};
                        //agregar algunos parametros
                        item.hiddIdDocumento = hiddIdDocumento;
                        item.hiddIdDocumentoBuzon = hiddIdDocumentoBuzon;
                        item._token = _token;
                        item.id_tipo_archivo = 2;
                        item.name = o.name; 
                        item.url =o.url;

                        //ajax post para agregar referenciaSGD
                        //console.log(item);
                        new Promise(function(resolve, reject) {
                            $.ajax({
                                url: "{{route('archivo.uploadreferenciasgd')}}",
                                type: 'POST',
                                dataType: 'json',
                                data:item,
                                success:function(r){
                                    resolve(r);
                                },
                                error:function(e){
                                    reject(e)
                                }
                            });
                        }).then(function(data) {
                            referenciaAnexos.splice(i,1);
                            //dropzoneAnexo.
                            console.log('common data', data);
                        }).catch(function(reason) {
                            console.log('reason for rejection', reason)
                            toastr.error("Error al cargar el archivo " + item.name + " " + reason.statusText);
                        });
                    });



                    intervalCarga = setInterval(function() {
                        varTermina = valida_carga();
                        if (varTermina == 1) {
                            //console.log('completado');
                            clearInterval(intervalCarga);
                            iniQueueComplete();

                            toastr.success("Documento actualizado", "¡Aviso!");
                            $('.btn-guardar-submit-edit').html("Guardar");
                            //if(idCarpeta != 2){
                            $('.btn-recibir-submit').html('Guardar');
                            //}

                            habilita_boton('btn-recibir-submit');
                            habilita_boton('btn_cerrar_guardar');
                            habilita_boton('btn-guardar-submit-edit');
                            habilita_boton('btn-guardar-submit');
                            habilita_boton('btn-enviar-submit');
                            habilita_boton('btn-visar');
                            habilita_boton('btn-firmar');
                            habilita_boton('btn-archivar');
                            habilita_boton('btn-visar-derivar');
                            habilita_boton('btn-firmar-derivar');
                            habilita_boton('btn-derivar-2');
                            habilita_boton('btn-derivar');

                            //recarga

                            editar_despachados(hiddIdDocumento, hiddIdDocumentoBuzon, null);
                            if (idCarpeta == 2) {
                                accion_editar(hiddIdDocumento, hiddIdDocumentoBuzon, null);
                            }
                        } else
                            console.log("validando carga",varTermina);
                            valida_carga();
                    }, 3000);
                } else {
                    toastr.error(data.data.comentario, "¡Aviso!");
                    habilita_boton('btn-recibir-submit');
                    habilita_boton('btn_cerrar_guardar');
                    habilita_boton('btn-guardar-submit-edit');
                    habilita_boton('btn-guardar-submit');
                    habilita_boton('btn-enviar-submit');
                    habilita_boton('btn-visar');
                    habilita_boton('btn-firmar');
                    habilita_boton('btn-archivar');
                    habilita_boton('btn-visar-derivar');
                    habilita_boton('btn-firmar-derivar');
                    habilita_boton('btn-derivar-2');
                    habilita_boton('btn-derivar');
                    $('.btn-guardar-submit-edit').html("Guardar");
                    //if(idCarpeta != 2){
                    $('.btn-recibir-submit').html('Guardar');
                    //}
                    if (idCarpeta == 2) {
                        accion_editar(hiddIdDocumento, hiddIdDocumentoBuzon, null);
                    }
                }


            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en el documento", "¡Aviso!");

                $('.btn-guardar-submit-edit').html('Guardar');
                habilita_boton('btn-guardar-submit-edit');
                habilita_boton('btn-guardar-submit');
                habilita_boton('btn-enviar-submit');
                habilita_boton('btn_cerrar_guardar');
                habilita_boton('btn-recibir-submit');
                habilita_boton('btn-visar');
                habilita_boton('btn-firmar');
                habilita_boton('btn-archivar');
                habilita_boton('btn-visar-derivar');
                habilita_boton('btn-firmar-derivar');
                habilita_boton('btn-derivar-2');
                habilita_boton('btn-derivar');
                if (idCarpeta == 2) {
                    accion_editar(hiddIdDocumento, hiddIdDocumentoBuzon, null);
                }
            }

        });
    }

    function accion_auto_guardar(idCarpeta) //**** revisar si se puede usar funcion que guarda documento ****//
    {
        $('.btn-recibir-submit').html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando'
        );
        $('.btn-guardar-submit-edit').html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando'
        );
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
        var distribucion = editor_distribucion.getData();
        // var cuerpo = tinymce.get("form_cuerpo").getContent();
        // var distribucion = tinymce.get("form_distribucion").getContent();
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        var hiddIdFileDelete = $("input[name='hiddIdFileDelete']").val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        var comentarioPrincipal = $('#form_comentario_el').val();
        var comentarioOtros = $('#form_comentario_otro_el').val();
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();

        //check para firma anexos
        var aParaFirma = [];
        $('input:checkbox[name="chkFirmaAnexo"]:checked').each(function() {
            if (this.checked) {
                aParaFirma.push($(this).val());
            }
        });

        deshabilita_boton('btn-recibir-submit');
        deshabilita_boton('btn_cerrar_guardar');
        deshabilita_boton('btn-guardar-submit');
        deshabilita_boton('btn-enviar-submit');
        deshabilita_boton('btn-visar');
        deshabilita_boton('btn-firmar');
        deshabilita_boton('btn-archivar');
        deshabilita_boton('btn-visar-derivar');
        deshabilita_boton('btn-derivar-2');
        deshabilita_boton('btn-derivar');
        deshabilita_boton('btn-firmar-derivar');

        setea_sesiones_recibidos();
        setea_sesiones_despachados();

        $.ajax({
            url: route('documentos.update',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
            type: 'PUT',
            dataType: 'json',
            data: {
                _token: _token,
                tipo_documento: tipo_documento,
                nivel_acceso: nivel_acceso,
                descripcion: descripcion,
                efectos_terceros: efectos_terceros,
                contestar_hasta: contestar_hasta,
                materia: materia,
                anterior: anterior,
                encabezado: encabezado,
                cuerpo: cuerpo,
                distribucion: distribucion,
                buzon: hiddIdBuzon,
                destinatarioPrincipal: destinatarioPrincipal,
                destinatarioOtros: otrosDestinatarios,
                comentarioPrincipal: comentarioPrincipal,
                comentarioOtros: comentarioOtros,
                acciones_solicitadas: acciones_solicitadas,
                hiddIdDocumento: hiddIdDocumento,
                hiddIdDocumentoBuzon: hiddIdDocumentoBuzon,
                hiddIdFileDelete: hiddIdFileDelete,
                carpeta: idCarpeta,
                estado: ((idCarpeta == 2) ? 4 : 1),
                aParaFirma: aParaFirma
            },
            success: function(data) {
                if (data.status == '200') {
                    dropzoneAnexo.processQueue();
                    dropzoneOtros.processQueue();
                    dropzonePrincipal.processQueue();

                    setTimeout(function() {
                        toastr.success("Borrador guardado", "¡Aviso!");
                        $('.btn-guardar-submit-edit').html("Guardar");
                        $('.btn-recibir-submit').html('Guardar');

                        habilita_boton('btn-recibir-submit');
                        habilita_boton('btn_cerrar_guardar');
                        habilita_boton('btn-guardar-submit-edit');
                        habilita_boton('btn-guardar-submit');
                        habilita_boton('btn-enviar-submit');
                        habilita_boton('btn-visar');
                        habilita_boton('btn-firmar');
                        habilita_boton('btn-archivar');
                        habilita_boton('btn-visar-derivar');
                        habilita_boton('btn-firmar-derivar');
                        habilita_boton('btn-derivar-2');
                        habilita_boton('btn-derivar');
                        setTimeout(function() {
                            auto_guardado();
                            //console.log('en accion_auto_guardar');
                        }, 2000);
                    }, 5000);
                } else {
                    toastr.error(data.data.comentario, "¡Aviso!");
                    habilita_boton('btn-recibir-submit');
                    habilita_boton('btn_cerrar_guardar');
                    habilita_boton('btn-guardar-submit-edit');
                    habilita_boton('btn-guardar-submit');
                    habilita_boton('btn-enviar-submit');
                    habilita_boton('btn-visar');
                    habilita_boton('btn-firmar');
                    habilita_boton('btn-archivar');
                    habilita_boton('btn-visar-derivar');
                    habilita_boton('btn-firmar-derivar');
                    habilita_boton('btn-derivar-2');
                    habilita_boton('btn-derivar');
                    $('.btn-guardar-submit-edit').html("Guardar");
                    $('.btn-recibir-submit').html('Guardar');
                }


            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en guardado del borrador", "¡Aviso!");

                $('.btn-guardar-submit-edit').html('Guardar');
                habilita_boton('btn-guardar-submit-edit');
                habilita_boton('btn-guardar-submit');
                habilita_boton('btn-enviar-submit');
                habilita_boton('btn_cerrar_guardar');
                habilita_boton('btn-recibir-submit');
                habilita_boton('btn-visar');
                habilita_boton('btn-firmar');
                habilita_boton('btn-archivar');
                habilita_boton('btn-visar-derivar');
                habilita_boton('btn-firmar-derivar');
                habilita_boton('btn-derivar-2');
                habilita_boton('btn-derivar');
            }

        });
    }

    function guardar_enviar() {
        $('.btn-derivar-2').html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando'
        );
        var _token = $("input[name='_token']").val();
        var tipo_documento = $("select[name='tipo_documento']").val();
        var nivel_acceso = $("select[name='nivel_acceso']").val();
        var efectos_terceros = $("select[name='efectos_terceros']").val();
        var contestar_hasta = $("input[name='contestar_hasta']").val();
        var fecha_respuesta = $("input[name='fecha_respuesta_envio']").val();
        var materia = $("input[name='materia']").val();
        var anterior = $("input[name='anterior']").val();
        var descripcion = $("textarea[name='descripcion']").val();
        var encabezado = $("input[name='encabezado']").val();
        var cuerpo = editor_cuerpo.getData();
        var distribucion = editor_distribucion.getData();
        // var cuerpo = tinymce.get("form_cuerpo").getContent();
        // var distribucion = tinymce.get("form_distribucion").getContent();
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        var hiddIdFileDelete = $("input[name='hiddIdFileDelete']").val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        var comentarioPrincipal = $('#form_comentario_el').val();
        var comentarioOtros = $('#form_comentario_otro_el').val();
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();

        //check para firma anexos
        var aParaFirma = [];
        $('input:checkbox[name="chkFirmaAnexo"]:checked').each(function() {
            if (this.checked) {
                aParaFirma.push($(this).val());
            }
        });

        deshabilita_boton('btn-recibir-submit');
        deshabilita_boton('btn_cerrar_guardar');
        deshabilita_boton('btn-guardar-submit');
        deshabilita_boton('btn-enviar-submit');
        deshabilita_boton('btn-visar');
        deshabilita_boton('btn-firmar');
        deshabilita_boton('btn-archivar');
        deshabilita_boton('btn-visar-derivar');
        deshabilita_boton('btn-derivar-2');
        deshabilita_boton('btn-derivar');
        deshabilita_boton('btn-firmar-derivar');

        setea_sesiones_recibidos();
        setea_sesiones_despachados();
        clearTimeout(timeoutId);
        $.ajax({
            url: route('documentos.update',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
            type: 'PUT',
            dataType: 'json',
            data: {
                _token: _token,
                tipo_documento: tipo_documento,
                nivel_acceso: nivel_acceso,
                descripcion: descripcion,
                efectos_terceros: efectos_terceros,
                contestar_hasta: contestar_hasta,
                materia: materia,
                anterior: anterior,
                encabezado: encabezado,
                cuerpo: cuerpo,
                distribucion: distribucion,
                buzon: hiddIdBuzon,
                fecha_respuesta:fecha_respuesta,
                destinatarioPrincipal: destinatarioPrincipal,
                destinatarioOtros: otrosDestinatarios,
                comentarioPrincipal: comentarioPrincipal,
                comentarioOtros: comentarioOtros,
                acciones_solicitadas: acciones_solicitadas,
                hiddIdDocumento: hiddIdDocumento,
                hiddIdDocumentoBuzon: hiddIdDocumentoBuzon,
                hiddIdFileDelete: hiddIdFileDelete,
                carpeta: 2,
                estado: 4,
                aParaFirma: aParaFirma
            },
            success: function(data) {
                if (data.status == '200') {
                    dropzoneAnexo.processQueue();
                    dropzoneOtros.processQueue();
                    dropzonePrincipal.processQueue();

                    intervalCarga = setInterval(function() {
                        varTermina = valida_carga();
                        if (varTermina == 1) {
                            //console.log('completado');
                            clearInterval(intervalCarga);
                            iniQueueComplete();

                            //codigo a ejecutar
                            toastr.success("Documento guardado", "¡Aviso!");
                            derivar_documento();
                            habilita_boton('btn-recibir-submit');
                            habilita_boton('btn_cerrar_guardar');
                            habilita_boton('btn-guardar-submit-edit');
                            habilita_boton('btn-guardar-submit');
                            habilita_boton('btn-enviar-submit');
                            habilita_boton('btn-visar');
                            habilita_boton('btn-firmar');
                            habilita_boton('btn-archivar');
                            habilita_boton('btn-visar-derivar');
                            habilita_boton('btn-firmar-derivar');
                            habilita_boton('btn-derivar-2');
                            habilita_boton('btn-derivar');
                            $('.btn-derivar-2').html('Guardar y Enviar');
                        } else
                            valida_carga();
                    }, 3000);


                } else {
                    toastr.error(data.data.comentario, "¡Aviso!");
                    habilita_boton('btn-recibir-submit');
                    habilita_boton('btn_cerrar_guardar');
                    habilita_boton('btn-guardar-submit-edit');
                    habilita_boton('btn-guardar-submit');
                    habilita_boton('btn-enviar-submit');
                    habilita_boton('btn-visar');
                    habilita_boton('btn-firmar');
                    habilita_boton('btn-archivar');
                    habilita_boton('btn-visar-derivar');
                    habilita_boton('btn-firmar-derivar');
                    habilita_boton('btn-derivar-2');
                    habilita_boton('btn-derivar');
                    $('.btn-derivar-2').html('Guardar y Enviar');
                    $('.btn-guardar-submit-edit').html("Guardar");
                    //if(idCarpeta != 2){
                    $('.btn-recibir-submit').html('Guardar');
                    //}
                }


            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en el documento", "¡Aviso!");

                $('.btn-guardar-submit-edit').html('Guardar');
                habilita_boton('btn-guardar-submit-edit');
                habilita_boton('btn-guardar-submit');
                habilita_boton('btn-enviar-submit');
                habilita_boton('btn_cerrar_guardar');
                habilita_boton('btn-recibir-submit');
                habilita_boton('btn-visar');
                habilita_boton('btn-firmar');
                habilita_boton('btn-archivar');
                habilita_boton('btn-visar-derivar');
                habilita_boton('btn-firmar-derivar');
                habilita_boton('btn-derivar-2');
                habilita_boton('btn-derivar');
                $('.btn-derivar-2').html('Guardar y Enviar');
            }

        });
    }

    function enviar_documento() {
        var _token = $("input[name='_token']").val();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        var responder = $('#form_respuesta_a').val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        var tipoDestino = $("input[name='hiddIdTipoDestino']").val();
        setea_sesiones_recibidos();
        setea_sesiones_despachados();


        Swal.fire({
            title: 'Enviar Documento',
            text: "¿Está seguro(a) que desea enviar este documento?",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.value == true) {
                $('.btn-enviar-submit').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando'
                );
                guarda_documento(2, function(data) {
                    if (data.status == 200) {
                        deshabilita_boton('btn-guardar-submit');
                        deshabilita_boton('btn-guardar-submit-edit');
                        $.ajax({
                            url: route('documentos.enviar_documento',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                            //url: "../buzonesCarpetas/" + hiddIdDocumento,
                            type: 'PUT',
                            dataType: 'json',
                            data: {
                                _token: _token,
                                hiddIdDocumento: hiddIdDocumento,
                                hiddIdDocumentoBuzon: hiddIdDocumentoBuzon,
                                buzon: hiddIdBuzon,
                                destinatarioPrincipal: destinatarioPrincipal,
                                acciones_solicitadas: acciones_solicitadas,
                                destinatarioOtros: otrosDestinatarios,
                                id_tipo_destino: tipoDestino,
                                responder: responder,
                                carpeta: 3
                            },
                            success: function(data) {
                                deshabilita_boton('btn-guardar-submit');
                                deshabilita_boton('btn-guardar-submit-edit');
                                if (data.status == '200') {
                                    toastr.success("Documento enviado", "¡Aviso!");

                                    $('#card_crear_documento').hide();
                                    $("#collapseOne").collapse('show');
                                    clear_form();
                                    //fn_grilla_despachados();  
                                    recarga_grilla_despachados();
                                    //fn_grilla_recibidos();
                                    recarga_grilla_recibidos();
                                    habilita_boton('btn-enviar-submit');
                                    habilita_boton('btn-guardar-submit');
                                    habilita_boton('btn-guardar-submit-edit');
                                    habilita_boton('btn_cerrar_guardar');
                                    habilita_boton('btn-visar');
                                    habilita_boton('btn-firmar');
                                    habilita_boton('btn-archivar');
                                    habilita_boton('btn-editar');
                                    habilita_boton('btn-recibir-submit');
                                } else {
                                    toastr.error(data.data.comentario, "¡Aviso!");
                                    habilita_boton('btn-enviar-submit');
                                    habilita_boton('btn-guardar-submit');
                                    habilita_boton('btn-guardar-submit-edit');
                                    habilita_boton('btn_cerrar_guardar');
                                    habilita_boton('btn-visar');
                                    habilita_boton('btn-firmar');
                                    habilita_boton('btn-archivar');
                                    habilita_boton('btn-editar');
                                    habilita_boton('btn-recibir-submit');
                                }

                                $('.btn-enviar-submit').html('Enviar');


                            },
                            error: function(jqXHR, textStatus, errorThrown) {

                                toastr.error("Falla en el envío del documento", "¡Aviso!");

                                $('.btn-enviar-submit').html('Enviar');
                                habilita_boton('btn-enviar-submit');
                                habilita_boton('btn-guardar-submit');
                                habilita_boton('btn-guardar-submit-edit');
                                habilita_boton('btn_cerrar_guardar');
                                habilita_boton('btn-visar');
                                habilita_boton('btn-firmar');
                                habilita_boton('btn-archivar');
                                habilita_boton('btn-editar');
                                habilita_boton('btn-recibir-submit');

                            }
                        });

                    }
                });


            } else {
                habilita_boton('btn-enviar-submit');
                habilita_boton('btn-guardar-submit');
                habilita_boton('btn-guardar-submit-edit');
                habilita_boton('btn_cerrar_guardar');
                habilita_boton('btn-visar');
                habilita_boton('btn-firmar');
                habilita_boton('btn-archivar');
                habilita_boton('btn-editar');
                habilita_boton('btn-recibir-submit');
            }
        })
    }

    function derivar_documento() {
        var _token = $("input[name='_token']").val();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        var tipoDestino = $("input[name='hiddIdTipoDestino']").val();



        setea_sesiones_recibidos();
        setea_sesiones_despachados();

        var continuar = true;
        var msg = "";

        if (tipoDestino == 2 && otrosDestinatarios == "") {
            continuar = false;
            msg = msg + "Debe seleccionar un destinatario<br>";
        }
        if (tipoDestino == 1) {
            if (destinatarioPrincipal == "" || destinatarioPrincipal === undefined) {
                msg = msg + "Debe seleccionar un destinatario<br>";
                continuar = false;
            }
            if (acciones_solicitadas == "" || acciones_solicitadas === undefined) {
                msg = msg + "Debe seleccionar acciones<br>";
                continuar = false;
            }
        }
        if (continuar) {
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
                if (result.value == true) {
                    $('.btn-derivar').html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando'
                    );

                    $('.btn-derivar-2').html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando'
                    );


                    deshabilita_boton('btn-derivar');
                    deshabilita_boton('btn-derivar-2');
                    deshabilita_boton('btn_cerrar_guardar');
                    deshabilita_boton('btn-guardar-submit');
                    deshabilita_boton('btn-enviar-submit');
                    deshabilita_boton('btn-visar');
                    deshabilita_boton('btn-editar');
                    deshabilita_boton('btn-firmar');
                    deshabilita_boton('btn-archivar');
                    deshabilita_boton('btn-visar-derivar');
                    deshabilita_boton('btn-firmar-derivar');
                    $.ajax({
                        //url: "../buzonesCarpetas/" + hiddIdDocumento,
                        url: route('documentos.enviar_documento',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                        type: 'PUT',
                        dataType: 'json',
                        data: {
                            _token: _token,
                            hiddIdDocumento: hiddIdDocumento,
                            hiddIdDocumentoBuzon: hiddIdDocumentoBuzon,
                            buzon: hiddIdBuzon,
                            destinatarioPrincipal: destinatarioPrincipal,
                            destinatarioOtros: otrosDestinatarios,
                            acciones_solicitadas: acciones_solicitadas,
                            id_tipo_destino: tipoDestino,
                            carpeta: 2
                        },
                        success: function(data) {
                            if (data.status == '200') {
                                toastr.success("Documento Derivado", "¡Aviso!");

                                $('#card_crear_documento').hide();
                                clear_form();
                                fn_grilla_despachados();
                                fn_grilla_recibidos();
                                habilita_boton('btn-derivar');
                                habilita_boton('btn-derivar-2');
                                habilita_boton('btn_cerrar_guardar');
                                habilita_boton('btn-guardar-submit');
                                habilita_boton('btn-enviar-submit');
                                habilita_boton('btn-visar');
                                habilita_boton('btn-editar');
                                habilita_boton('btn-firmar');
                                habilita_boton('btn-archivar');
                                habilita_boton('btn-visar-derivar');
                                habilita_boton('btn-firmar-derivar');
                                $('.btn-derivar').html('Enviar');
                                $('.btn-derivar-2').html('Guardar y Enviar')
                                location.reload();
                            } else {
                                toastr.error(data.data.comentario, "¡Aviso!");
                                habilita_boton('btn-derivar');
                                habilita_boton('btn-derivar-2');
                                habilita_boton('btn_cerrar_guardar');
                                habilita_boton('btn-guardar-submit');
                                habilita_boton('btn-enviar-submit');
                                habilita_boton('btn-visar');
                                habilita_boton('btn-editar');
                                habilita_boton('btn-firmar');
                                habilita_boton('btn-archivar');
                                habilita_boton('btn-visar-derivar');
                                habilita_boton('btn-firmar-derivar');
                                $('.btn-derivar').html('Enviar');
                                $('.btn-derivar-2').html('Guardar y Enviar')
                            }

                            $('.btn-enviar-submit').html('Enviar');
                        },
                        error: function(jqXHR, textStatus, errorThrown) {

                            toastr.error("Falla en la derivación del documento", "¡Aviso!");
                            habilita_boton('btn-derivar');
                            habilita_boton('btn-derivar-2');
                            habilita_boton('btn_cerrar_guardar');
                            habilita_boton('btn-guardar-submit');
                            habilita_boton('btn-enviar-submit');
                            habilita_boton('btn-visar');
                            habilita_boton('btn-editar');
                            habilita_boton('btn-firmar');
                            habilita_boton('btn-archivar');
                            habilita_boton('btn-visar-derivar');
                            habilita_boton('btn-firmar-derivar');

                            $('.btn-enviar-submit').html('Enviar');
                            $('.btn-derivar').html('Enviar');
                            $('.btn-derivar-2').html('Guardar y Enviar');
                        }
                    });
                }
            })
        } else {
            toastr.error("Falla en la derivación del documento: " + msg, "¡Aviso!");
        }
    }

    function visar_documento() {
        var _token = $("input[name='_token']").val();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        setea_sesiones_recibidos();
        setea_sesiones_despachados();

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
            //console.log(result);
            if (result.value == true) {
                $.ajax({
                    url: route('documentos.actualizar_estado',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                    //url: "/actualizar_estado_documento/" + hiddIdDocumentoBuzon,
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        _token: _token,
                        hiddIdDocumento: hiddIdDocumento,
                        buzon: hiddIdBuzon,
                        accion: 6
                    },
                    success: function(data) {
                        if (data.status == '200') {
                            toastr.success("Documento Visado", "¡Aviso!");

                            //fn_grilla_recibidos();
                            recarga_grilla_recibidos();
                            recarga_grilla_despachados();
                            $('#card_crear_documento').hide();
                            $("#collapseOne").collapse('show');
                        } else {
                            toastr.error(data.data.comentario, "¡Aviso!");
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        toastr.error("Falla en el documento", "¡Aviso!");
                    }
                });
            }
        })
    }

    function visar_derivar_documento() {
        var _token = $("input[name='_token']").val();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        setea_sesiones_recibidos();
        setea_sesiones_despachados();
        if (destinatarioPrincipal !== undefined && acciones_solicitadas != "") {
            Swal.fire({
                title: 'Visar y Enviar',
                html: "Se realizará la visación y envío del documento: <br>" +
                    "<b>" + $("input[name='materia']").val() + "</b><br>",
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                //console.log(result);
                if (result.value == true) {
                    $('.btn-visar-derivar').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Visando y derivando')
                    $.ajax({
                        url: route('documentos.actualizar_estado',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                        //url: "/actualizar_estado_documento/" + hiddIdDocumentoBuzon,
                        type: 'PUT',
                        dataType: 'json',
                        data: {
                            _token: _token,
                            hiddIdDocumento: hiddIdDocumento,
                            buzon: hiddIdBuzon,
                            accion: 6
                        },
                        success: function(data) {
                            if (data.status == '200') //documento visado
                            { //derivar                                
                                $.ajax({
                                    //url: "../buzonesCarpetas/" + hiddIdDocumento,
                                    url: route('documentos.enviar_documento',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                                    type: 'PUT',
                                    dataType: 'json',
                                    data: {
                                        _token: _token,
                                        hiddIdDocumento: hiddIdDocumento,
                                        hiddIdDocumentoBuzon: hiddIdDocumentoBuzon,
                                        buzon: hiddIdBuzon,
                                        destinatarioPrincipal: destinatarioPrincipal,
                                        destinatarioOtros: otrosDestinatarios,
                                        acciones_solicitadas: acciones_solicitadas,
                                        carpeta: 2
                                    },
                                    success: function(data) {
                                        if (data.status == '200') {
                                            toastr.success("Documento Visado y Derivado", "¡Aviso!");

                                            $('#card_crear_documento').hide();
                                            clear_form();
                                            //fn_grilla_despachados();
                                            //fn_grilla_recibidos();
                                            recarga_grilla_recibidos();
                                            //recarga_grilla_despachados();
                                            $('.btn-visar-derivar').html('Visar y Enviar');
                                            //location.reload();
                                        } else {
                                            toastr.error(data.data.comentario, "¡Aviso!");
                                            $('.btn-visar-derivar').html('Visar y Enviar');
                                        }


                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {

                                        toastr.error("Falla en la derivación del documento", "¡Aviso!");

                                        $('.btn-visar-derivar').html('Visar y Enviar');
                                    }
                                });

                            } //fin derivar
                            else {
                                toastr.error(data.data.comentario, "¡Aviso!");
                                $('.btn-visar-derivar').html('Visar y Enviar');
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            toastr.error("Falla en el documento", "¡Aviso!");
                            $('.btn-visar-derivar').html('Visar y Enviar');
                        }
                    });
                }
            })
        } else {
            toastr.error("Falla en el documento: Debe seleccionar un destinatario principal y acciones", "¡Aviso!");
        }
    }

    function firmar_documento() {
        clearTimeout(timeoutId);
        if (bloqueo_accion) {
            return false;
        }
        var _token = $("input[name='_token']").val();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        //anexos
        var aParaFirma = [];
        var hayAnexos = "";
        var firmaAnexo = $("input[name='hiddFirmaAnexo']").val();


        $('input:checkbox[name="chkFirmaAnexo"]:checked').each(function() {
            if (this.checked) {
                aParaFirma.push($(this).val());
                hayAnexos = " y <b>anexos</b> seleccionados";
            }
        });

        if (firmaAnexo > 0) {
            hayAnexos = " y <b>anexos</b> seleccionados";
        }

        //y anexos seleccionados

        setea_sesiones_recibidos();
        setea_sesiones_despachados();
        deshabilita_boton('btn-recibir-submit');
        Swal.fire({
            title: 'Firmar',
            html: "Se realizará la firma del documento: <br>" +
                "<b>" + $("input[name='materia']").val() + "</b>" + hayAnexos + "<br>",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            //console.log(result);
            if (result.value == true) {
                $('.btn-firmar').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Firmando'
                );

                deshabilita_boton('btn-recibir-submit');
                deshabilita_boton('btn_cerrar_guardar');
                deshabilita_boton('btn-guardar-submit');
                deshabilita_boton('btn-enviar-submit');
                deshabilita_boton('btn-visar');
                deshabilita_boton('btn-editar');
                deshabilita_boton('btn-firmar');
                deshabilita_boton('btn-archivar');
                deshabilita_boton('btn-visar-derivar');
                deshabilita_boton('btn-firmar-derivar');
                deshabilita_boton('btn-derivar');
                deshabilita_boton('btn-derivar-2');
                bloqueo_accion = true;
                $.ajax({
                    //url: "/firmar_documento/" + hiddIdDocumentoBuzon,
                    url: route('documentos.firmar',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        _token: _token,
                        hiddIdDocumento: hiddIdDocumento,
                        buzon: hiddIdBuzon,
                        accion: 7
                    },
                    success: function(data) {
                        if (data.status == '200') {
                            toastr.success(data.data, "¡Aviso!");
                            habilita_boton('btn-recibir-submit');
                            habilita_boton('btn_cerrar_guardar');
                            habilita_boton('btn-guardar-submit');
                            habilita_boton('btn-enviar-submit');
                            habilita_boton('btn-visar');
                            habilita_boton('btn-editar');
                            habilita_boton('btn-firmar');
                            habilita_boton('btn-archivar');
                            habilita_boton('btn-visar-derivar');
                            habilita_boton('btn-firmar-derivar');
                            habilita_boton('btn-derivar');
                            habilita_boton('btn-derivar-2');
                            $('#card_crear_documento').hide();
                            //fn_grilla_recibidos();
                            $("#collapseOne").collapse('show');
                            habilita_boton('btn-recibir-submit');
                            recarga_grilla_recibidos();
                            recarga_grilla_despachados();
                        } else {
                            toastr.error(data.data.comentario, "¡Aviso!");
                            habilita_boton('btn-recibir-submit');
                            habilita_boton('btn-recibir-submit');
                            habilita_boton('btn_cerrar_guardar');
                            habilita_boton('btn-guardar-submit');
                            habilita_boton('btn-enviar-submit');
                            habilita_boton('btn-visar');
                            habilita_boton('btn-editar');
                            habilita_boton('btn-firmar');
                            habilita_boton('btn-archivar');
                            habilita_boton('btn-visar-derivar');
                            habilita_boton('btn-firmar-derivar');
                            habilita_boton('btn-derivar');
                            habilita_boton('btn-derivar-2');
                        }

                        $('.btn-recibir-submit').html('Firmar');
                        $('.btn-firmar').html('Firmar');
                        bloqueo_accion = false;

                    },
                    //error: function (e) {
                    error: function(data, jqXHR, textStatus, errorThrown) {
                        //data = e.responseJSON;

                        //console.log(data);
                        //if (data.data.comentario != "" && data.data.comentario != null){
                        if (data.comentario != "" && data.comentario != null) {

                            toastr.error(data.comentario, "¡Aviso!");
                            habilita_boton('btn-recibir-submit');
                        } else {
                            toastr.error("Falla en el documento", "¡Aviso!");
                            habilita_boton('btn-recibir-submit');
                            habilita_boton('btn-recibir-submit');
                            habilita_boton('btn_cerrar_guardar');
                            habilita_boton('btn-guardar-submit');
                            habilita_boton('btn-enviar-submit');
                            habilita_boton('btn-visar');
                            habilita_boton('btn-editar');
                            habilita_boton('btn-firmar');
                            habilita_boton('btn-archivar');
                            habilita_boton('btn-visar-derivar');
                            habilita_boton('btn-firmar-derivar');
                            habilita_boton('btn-derivar');
                            habilita_boton('btn-derivar-2');

                        }
                        $('.btn-recibir-submit').html('Firmar');
                        $('.btn-firmar').html('Firmar');
                        bloqueo_accion = false;
                    }
                });


            } else {
                habilita_boton('btn-recibir-submit');
                habilita_boton('btn-recibir-submit');
                habilita_boton('btn_cerrar_guardar');
                habilita_boton('btn-guardar-submit');
                habilita_boton('btn-enviar-submit');
                habilita_boton('btn-visar');
                habilita_boton('btn-editar');
                habilita_boton('btn-firmar');
                habilita_boton('btn-archivar');
                habilita_boton('btn-visar-derivar');
                habilita_boton('btn-firmar-derivar');
                habilita_boton('btn-derivar');
                habilita_boton('btn-derivar-2');

            }
        })
    }

    function firmar_derivar_documento() {
        if (bloqueo_accion) {
            return false;
        }
        var _token = $("input[name='_token']").val();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        var comentarioPrincipal = $('#fDerivarMasivaComPpal').val();
        var comentarioOtros = $('#fDerivarMasivaComOtro').val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        var tipoDestino = $("input[name='hiddIdTipoDestino']").val();
        var responder = $('#form_respuesta_a').val();

        if (destinatarioPrincipal !== undefined && acciones_solicitadas != "") {
            deshabilita_boton('btn-recibir-submit');
            deshabilita_boton('btn_cerrar_guardar');
            deshabilita_boton('btn-guardar-submit');
            deshabilita_boton('btn-enviar-submit');
            deshabilita_boton('btn-visar');
            deshabilita_boton('btn-editar');
            deshabilita_boton('btn-firmar');
            deshabilita_boton('btn-archivar');
            deshabilita_boton('btn-visar-derivar');
            deshabilita_boton('btn-firmar-derivar');
            deshabilita_boton('btn-derivar');
            deshabilita_boton('btn-derivar-2');
            setea_sesiones_recibidos();
            setea_sesiones_despachados();
            Swal.fire({
                title: 'Firmar y Enviar',
                html: "Se realizará la firma y envío del documento: <br>" +
                    "<b>" + $("input[name='materia']").val() + "</b><br>",
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                //console.log(result);
                if (result.value == true) {
                    $('.btn-firmar-derivar').html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Firmando'
                    );
                    bloqueo_accion = true;
                    $.ajax({
                        url: route('documentos.firmar',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                        //url: "/firmar_documento/" + hiddIdDocumentoBuzon,
                        type: 'PUT',
                        dataType: 'json',
                        data: {
                            _token: _token,
                            hiddIdDocumento: hiddIdDocumento,
                            buzon: hiddIdBuzon,
                            accion: 7
                        },
                        success: function(data) {
                            if (data.status == '200') { //// derivar
                                $.ajax({
                                    url: route('documentos.update',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                                    type: 'PUT',
                                    dataType: 'json',
                                    data: {
                                        _token: _token,
                                        buzon: hiddIdBuzon,
                                        destinatarioPrincipal: destinatarioPrincipal,
                                        destinatarioOtros: otrosDestinatarios,
                                        comentarioPrincipal: comentarioPrincipal,
                                        comentarioOtros: comentarioOtros,
                                        acciones_solicitadas: acciones_solicitadas,
                                        hiddIdDocumento: hiddIdDocumento,
                                        hiddIdDocumentoBuzon: hiddIdDocumentoBuzon,
                                        carpeta: 2,
                                        opcionGuardar: 1,
                                        id_tipo_destino: tipoDestino
                                    },
                                    success: function(data) {
                                        if (data.status == '200') {
                                            $.ajax({
                                                url: route('documentos.enviar_documento',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                                                //url: "../buzonesCarpetas/" + hiddIdDocumento,
                                                type: 'PUT',
                                                dataType: 'json',
                                                data: {
                                                    _token: _token,
                                                    hiddIdDocumento: hiddIdDocumento,
                                                    id_tipo_destino: tipoDestino,
                                                    hiddIdDocumentoBuzon: hiddIdDocumentoBuzon,
                                                    buzon: hiddIdBuzon,
                                                    destinatarioPrincipal: destinatarioPrincipal,
                                                    destinatarioOtros: otrosDestinatarios,
                                                    acciones_solicitadas: acciones_solicitadas,
                                                    responder: responder,
                                                    carpeta: 2
                                                },
                                                success: function(data) {
                                                    if (data.status == '200') {
                                                        toastr.success("Documento Firmado y Derivado", "¡Aviso!");

                                                        $('#card_crear_documento').hide();
                                                        clear_form();
                                                        fn_grilla_despachados();
                                                        fn_grilla_recibidos();
                                                        habilita_boton('btn-recibir-submit');
                                                        habilita_boton('btn_cerrar_guardar');
                                                        habilita_boton('btn-guardar-submit');
                                                        habilita_boton('btn-enviar-submit');
                                                        habilita_boton('btn-visar');
                                                        habilita_boton('btn-editar');
                                                        habilita_boton('btn-firmar');
                                                        habilita_boton('btn-archivar');
                                                        habilita_boton('btn-visar-derivar');
                                                        habilita_boton('btn-firmar-derivar');
                                                        habilita_boton('btn-derivar');
                                                        habilita_boton('btn-derivar-2');
                                                        location.reload();
                                                    } else {
                                                        toastr.error(data.data.comentario, "¡Aviso!");
                                                        habilita_boton('btn-recibir-submit');
                                                        habilita_boton('btn_cerrar_guardar');
                                                        habilita_boton('btn-guardar-submit');
                                                        habilita_boton('btn-enviar-submit');
                                                        habilita_boton('btn-visar');
                                                        habilita_boton('btn-editar');
                                                        habilita_boton('btn-firmar');
                                                        habilita_boton('btn-archivar');
                                                        habilita_boton('btn-visar-derivar');
                                                        habilita_boton('btn-firmar-derivar');
                                                        habilita_boton('btn-derivar');
                                                        habilita_boton('btn-derivar-2');
                                                    }

                                                    $('.btn-enviar-submit').html('Enviar');
                                                },
                                                error: function(data, jqXHR, textStatus, errorThrown) {
                                                    // console.log(data);
                                                    // console.log('despues data');
                                                    // console.log(jqXHR);
                                                    // console.log(textStatus+"-"+errorThrown);
                                                    toastr.error("Falla en la derivación del documento", "¡Aviso!");
                                                    habilita_boton('btn-recibir-submit');
                                                    habilita_boton('btn_cerrar_guardar');
                                                    habilita_boton('btn-guardar-submit');
                                                    habilita_boton('btn-enviar-submit');
                                                    habilita_boton('btn-visar');
                                                    habilita_boton('btn-editar');
                                                    habilita_boton('btn-firmar');
                                                    habilita_boton('btn-archivar');
                                                    habilita_boton('btn-visar-derivar');
                                                    habilita_boton('btn-firmar-derivar');
                                                    habilita_boton('btn-derivar');
                                                    habilita_boton('btn-derivar-2');
                                                    $('.btn-firmar-derivar').html('Firmar y Enviar');
                                                }
                                            });
                                        } else {
                                            toastr.error("Falla al guardar destinatarios", "¡Aviso!");
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {
                                        toastr.error("Falla en la actualización del documento", "¡Aviso!");
                                    }
                                });
                            } //// fin derivar
                            else {
                                toastr.error(data.data.comentario, "¡Aviso!");
                                $('.btn-firmar-derivar').html('Firmar y Enviar');
                                habilita_boton('btn-recibir-submit');
                                habilita_boton('btn_cerrar_guardar');
                                habilita_boton('btn-guardar-submit');
                                habilita_boton('btn-enviar-submit');
                                habilita_boton('btn-visar');
                                habilita_boton('btn-editar');
                                habilita_boton('btn-firmar');
                                habilita_boton('btn-archivar');
                                habilita_boton('btn-visar-derivar');
                                habilita_boton('btn-firmar-derivar');
                                habilita_boton('btn-derivar');
                                habilita_boton('btn-derivar-2');
                            }

                            $('.btn-recibir-submit').html('Firmar');
                            $('.btn-firmar-derivar').html('Firmar y Enviar');
                            bloqueo_accion = false;

                        },
                        error: function(e) {
                            data = e.responseJSON;
                            //console.log(data);
                            if (data.data.comentario != "" && data.data.comentario != null) {
                                toastr.error(data.data.comentario, "¡Aviso!");
                                habilita_boton('btn-recibir-submit');
                                habilita_boton('btn_cerrar_guardar');
                                habilita_boton('btn-guardar-submit');
                                habilita_boton('btn-enviar-submit');
                                habilita_boton('btn-visar');
                                habilita_boton('btn-editar');
                                habilita_boton('btn-firmar');
                                habilita_boton('btn-archivar');
                                habilita_boton('btn-visar-derivar');
                                habilita_boton('btn-firmar-derivar');
                                habilita_boton('btn-derivar');
                                habilita_boton('btn-derivar-2');
                                $('.btn-firmar-derivar').html('Firmar y Enviar');
                            } else {
                                toastr.error("Falla en el documento", "¡Aviso!");
                                habilita_boton('btn-recibir-submit');
                                habilita_boton('btn_cerrar_guardar');
                                habilita_boton('btn-guardar-submit');
                                habilita_boton('btn-enviar-submit');
                                habilita_boton('btn-visar');
                                habilita_boton('btn-editar');
                                habilita_boton('btn-firmar');
                                habilita_boton('btn-archivar');
                                habilita_boton('btn-visar-derivar');
                                habilita_boton('btn-firmar-derivar');
                                habilita_boton('btn-derivar');
                                habilita_boton('btn-derivar-2');
                            }
                            $('.btn-recibir-submit').html('Firmar');
                            $('.btn-firmar-derivar').html('Firmar y Enviar');
                            bloqueo_accion = false;
                        }
                    });


                } else {
                    habilita_boton('btn-recibir-submit');
                    habilita_boton('btn_cerrar_guardar');
                    habilita_boton('btn-guardar-submit');
                    habilita_boton('btn-enviar-submit');
                    habilita_boton('btn-visar');
                    habilita_boton('btn-editar');
                    habilita_boton('btn-firmar');
                    habilita_boton('btn-archivar');
                    habilita_boton('btn-visar-derivar');
                    habilita_boton('btn-firmar-derivar');
                    habilita_boton('btn-derivar');
                    habilita_boton('btn-derivar-2');
                    $('.btn-firmar-derivar').html('Firmar y Enviar');
                }
            })
        } else {
            toastr.error("Falla en el documento: Debe seleccionar un destinatario y acciones", "¡Aviso!");
        }
    }

    function finalizar_documento() {
        clearTimeout(timeoutId);
        var _token = $("input[name='_token']").val();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        deshabilita_boton('btn-recibir-submit');
        setea_sesiones_recibidos();
        setea_sesiones_despachados();
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
            //console.log(result);
            if (result.value == true) {

                $.ajax({
                    url: route('documentos.actualizar_estado',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                    //url: "/actualizar_estado_documento/" + hiddIdDocumentoBuzon,
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        _token: _token,
                        hiddIdDocumento: hiddIdDocumento,
                        buzon: hiddIdBuzon,
                        accion: 10
                    },
                    success: function(data) {
                        if (data.status == '200') {
                            toastr.success("Documento Finalizado", "¡Aviso!");
                            habilita_boton('btn-recibir-submit');
                            $('#card_crear_documento').hide();
                            //fn_grilla_recibidos();
                            recarga_grilla_recibidos();
                            $("#collapseOne").collapse('show');
                        } else {
                            toastr.error(data.data.comentario, "¡Aviso!");
                            habilita_boton('btn-recibir-submit');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        toastr.error("Falla en el documento", "¡Aviso!");
                        habilita_boton('btn-recibir-submit');
                    }
                });
            } else {
                habilita_boton('btn-recibir-submit');
            }
        })
    }

    function archivar_documento(accion) {
        var _token = $("input[name='_token']").val();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        var comentario = $("textarea[id='form_comentario_archivar']").val();
        deshabilita_boton('btn-archivar');
        deshabilita_boton('btn_cerrar_guardar');
        deshabilita_boton('btn-editar');
        deshabilita_boton('btn-firmar');
        deshabilita_boton('btn-visar');
        deshabilita_boton('btn-guardar-submit-edit');
        deshabilita_boton('btn-guardar-submit');
        deshabilita_boton('btn-recibir-submit');
        deshabilita_boton('btn-firmar-derivar');
        deshabilita_boton('btn-visar-derivar');
        deshabilita_boton('btn-derivar');
        deshabilita_boton('btn-derivar-2');
        setea_sesiones_recibidos();
        setea_sesiones_despachados();
        Swal.fire({
            title: txtArchivado[accion][0],
            html: "Se " + txtArchivado[accion][1] + " el documento: <br><br>" +
                "<b>" + $("input[name='materia']").val() + "</b><br>",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            //console.log(result);
            if (result.value == true) {
                $.ajax({
                    url: route('documentos.archivar',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                    //url: "/archivar_documento/" + hiddIdDocumentoBuzon,
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        _token: _token,
                        hiddIdDocumento: hiddIdDocumento,
                        buzon: hiddIdBuzon,
                        comentario: comentario,
                        accion: accion
                    },
                    success: function(data) {
                        if (data.status == '200') {
                            toastr.success("Documento " + txtArchivado[accion][2], "¡Aviso!");

                            $('#card_crear_documento').hide();
                            clear_form();
                            fn_grilla_recibidos();
                            habilita_boton('btn-archivar');
                            habilita_boton('btn_cerrar_guardar');
                            habilita_boton('btn-editar');
                            habilita_boton('btn-firmar');
                            habilita_boton('btn-visar');
                            habilita_boton('btn-guardar-submit-edit');
                            habilita_boton('btn-guardar-submit');
                            habilita_boton('btn-recibir-submit');
                            habilita_boton('btn-firmar-derivar');
                            habilita_boton('btn-visar-derivar');
                            habilita_boton('btn-derivar');
                            habilita_boton('btn-derivar-2');
                            location.reload();
                        } else {
                            toastr.error(data.data.comentario, "¡Aviso!");
                            habilita_boton('btn-archivar');
                            habilita_boton('btn_cerrar_guardar');
                            habilita_boton('btn-editar');
                            habilita_boton('btn-firmar');
                            habilita_boton('btn-visar');
                            habilita_boton('btn-guardar-submit-edit');
                            habilita_boton('btn-guardar-submit');
                            habilita_boton('btn-recibir-submit');
                            habilita_boton('btn-firmar-derivar');
                            habilita_boton('btn-visar-derivar');
                            habilita_boton('btn-derivar');
                            habilita_boton('btn-derivar-2');
                        }

                        $('.btn-enviar-submit').html('Enviar');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        toastr.error("Falla en el documento", "¡Aviso!");
                        habilita_boton('btn-archivar');
                        habilita_boton('btn_cerrar_guardar');
                        habilita_boton('btn-editar');
                        habilita_boton('btn-firmar');
                        habilita_boton('btn-visar');
                        habilita_boton('btn-guardar-submit-edit');
                        habilita_boton('btn-guardar-submit');
                        habilita_boton('btn-recibir-submit');
                        habilita_boton('btn-firmar-derivar');
                        habilita_boton('btn-visar-derivar');
                        habilita_boton('btn-derivar');
                        habilita_boton('btn-derivar-2');
                        $('.btn-enviar-submit').html('Enviar');
                    }
                });
            } else {
                habilita_boton('btn-archivar');
                habilita_boton('btn_cerrar_guardar');
                habilita_boton('btn-editar');
                habilita_boton('btn-firmar');
                habilita_boton('btn-visar');
                habilita_boton('btn-guardar-submit-edit');
                habilita_boton('btn-guardar-submit');
                habilita_boton('btn-recibir-submit');
                habilita_boton('btn-firmar-derivar');
                habilita_boton('btn-visar-derivar');
                habilita_boton('btn-derivar');
                habilita_boton('btn-derivar-2');
            }
        })

    }

    function archivar_documento_botonera(accion) {
        var _token = $("input[name='_token']").val();

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        deshabilita_boton('btn-archivar');
        deshabilita_boton('btn_cerrar_guardar');
        deshabilita_boton('btn-editar');
        deshabilita_boton('btn-firmar');
        deshabilita_boton('btn-visar');
        deshabilita_boton('btn-guardar-submit-edit');
        deshabilita_boton('btn-guardar-submit');
        deshabilita_boton('btn-recibir-submit');
        deshabilita_boton('btn-firmar-derivar');
        deshabilita_boton('btn-visar-derivar');
        deshabilita_boton('btn-derivar');
        deshabilita_boton('btn-derivar-2');
        setea_sesiones_recibidos();
        setea_sesiones_despachados();
        Swal.fire({
            title: "Archivar",
            input: 'textarea',
            inputPlaceholder: 'Ingrese fundamentación para archivar',
            inputValidator: (value) => {
                if (!value) {
                    return 'Debe ingresar un fundamento'
                }
            },
            html: "Se archivará el documento: <br><br>" +
                "<b>" + $("input[name='materia']").val() + "</b><br>",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            //console.log(result);
            if (result.value) {
                let comentario_archivo = $('.swal2-textarea').val();
                $.ajax({
                    url: route('documentos.archivar',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                    //url: "/archivar_documento/" + hiddIdDocumentoBuzon,
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        _token: _token,
                        hiddIdDocumento: hiddIdDocumento,
                        buzon: hiddIdBuzon,
                        comentario: comentario_archivo,
                        accion: accion
                    },
                    success: function(data) {
                        if (data.status == '200') {
                            toastr.success("Documento Archivado", "¡Aviso!");

                            $('#card_crear_documento').hide();
                            clear_form();
                            fn_grilla_recibidos();
                            habilita_boton('btn-archivar');
                            habilita_boton('btn_cerrar_guardar');
                            habilita_boton('btn-editar');
                            habilita_boton('btn-firmar');
                            habilita_boton('btn-visar');
                            habilita_boton('btn-guardar-submit-edit');
                            habilita_boton('btn-guardar-submit');
                            habilita_boton('btn-recibir-submit');
                            habilita_boton('btn-firmar-derivar');
                            habilita_boton('btn-visar-derivar');
                            habilita_boton('btn-derivar');
                            habilita_boton('btn-derivar-2');
                            location.reload();
                        } else {
                            toastr.error(data.data.comentario, "¡Aviso!");
                            habilita_boton('btn-archivar');
                            habilita_boton('btn_cerrar_guardar');
                            habilita_boton('btn-editar');
                            habilita_boton('btn-firmar');
                            habilita_boton('btn-visar');
                            habilita_boton('btn-guardar-submit-edit');
                            habilita_boton('btn-guardar-submit');
                            habilita_boton('btn-recibir-submit');
                            habilita_boton('btn-firmar-derivar');
                            habilita_boton('btn-visar-derivar');
                            habilita_boton('btn-derivar');
                            habilita_boton('btn-derivar-2');
                        }

                        $('.btn-enviar-submit').html('Enviar');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        toastr.error("Falla en el documento", "¡Aviso!");
                        habilita_boton('btn-archivar');
                        habilita_boton('btn_cerrar_guardar');
                        habilita_boton('btn-editar');
                        habilita_boton('btn-firmar');
                        habilita_boton('btn-visar');
                        habilita_boton('btn-guardar-submit-edit');
                        habilita_boton('btn-guardar-submit');
                        habilita_boton('btn-recibir-submit');
                        habilita_boton('btn-firmar-derivar');
                        habilita_boton('btn-visar-derivar');
                        habilita_boton('btn-derivar');
                        habilita_boton('btn-derivar-2');
                        $('.btn-enviar-submit').html('Enviar');
                    }
                });
            } else {
                habilita_boton('btn-archivar');
                habilita_boton('btn_cerrar_guardar');
                habilita_boton('btn-editar');
                habilita_boton('btn-firmar');
                habilita_boton('btn-visar');
                habilita_boton('btn-guardar-submit-edit');
                habilita_boton('btn-guardar-submit');
                habilita_boton('btn-recibir-submit');
                habilita_boton('btn-firmar-derivar');
                habilita_boton('btn-visar-derivar');
                habilita_boton('btn-derivar');
                habilita_boton('btn-derivar-2');
            }
        })

    }

    /* **DOCUMENTOS** SCRIPT */

    function cambio_texto_boton_carpetas(texto,busqueda) {
        $('#documento').hide();
        $('#card_crear_documento').hide();
        $('#card_bitacora').hide();
        if (texto.length > 20 || texto.length == 0) {
            texto = '';
        }
        $('#boton_carpetas_texto').html('Carpetas - <i><b>' + texto + '</b></i>');
         if (texto == 'Por Recibir') {
            fn_grilla_por_recibir(busqueda);
            $('#nav-por-recibir-tab').tab('show');
        }
        if (texto == 'Recibidos') {
            fn_grilla_recibidos(busqueda);
            $('#nav-recibidos-tab').tab('show');
        }
        if (texto == 'Despachados') {
            fn_grilla_despachados(busqueda);
            $('#nav-despachados-tab').tab('show');
        }
        sessionStorage.setItem('carpeta_seleccionada', texto);

    }

    function mostrar_documento(texto) {
        $('#documento .card-title').html('Documento: ' + texto);
        $('#documento').show();
    }

    function accion_visar(id_documento, id_documento_buzon, id_documento_buzon_padre) {
        $('#titulo_accion').html('Editar Documento ID '+id_documento);

        deshabilita_campos();
        cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre, 2, 22); //se incorpora accion 22   

        //listado de visaciones y firmas
        $('.row_txt_firmar').show();

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

        var buttonVisar = '<button onClick="guarda_destinatarios_documento(6)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Visar</button> ';
        $('#addButton').append(buttonVisar);

    }

    function accion_pdf(id_documento, id_documento_buzon) {

        var _token = $("input[name='_token']").val();
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();

        Swal.fire({
            title: 'Generar Pdf',
            html: "El botón presionado asignará folio, fecha y generará un PDF que no podrá ser editado posteriormente.",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.value == true) {
                $.ajax({
                    url: route('documentos.generar',{'buzon':id_documento_buzon,'id':id_documento}),
                    //url: "/generar_archivo",
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        _token: _token,
                        idDocumento: id_documento,
                        idDocumentoBuzon: id_documento_buzon,
                        idBuzon: hiddIdBuzon
                    },
                    success: function(data) {
                        if (data.status == '200') {
                            toastr.success(data.data, "¡Aviso!");

                            $('#card_crear_documento').hide();
                            $("#collapseOne").collapse('show');
                            fn_grilla_despachados();

                        } else {
                            toastr.error(data.data.comentario, "¡Aviso!");
                        }
                    },
                    error: function(data, jqXHR, textStatus, errorThrown) {
                        toastr.error("Falla en la generación del archivo", "¡Aviso!");
                    }
                });
            }
        })
    }

    function vista_previa() {

        var _token = $("input[name='_token']").val();
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        Swal.fire({
            title: 'Vista previa',
            html: "Se generará una vista previa del documento, recuerde guardar antes de generar.",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.value == true) {
                $.ajax({
                    url: route('documentos.vista_previa',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                    //url: "/vista_previa",
                    type: 'GET',
                    dataType: 'binary',
                    data: {
                        _token: _token,
                        idDocumento: hiddIdDocumento,
                        idDocumentoBuzon: hiddIdDocumentoBuzon,
                        idBuzon: hiddIdBuzon
                    },
                    xhrFields: {
                        responseType: 'blob'
                    },
                    success: function(response) {
                        let blob = new Blob([response], {
                            type: 'application/pdf'
                        });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.target = "_blank";
                        link.click();
                    },
                    error: function(data, jqXHR, textStatus, errorThrown) {
                        toastr.error("No es posible generar la vista previa.", "¡Aviso!");
                    }
                });
            }
        })
    }

    function vista_previa_sg() {
        var _token = $("input[name='_token']").val();
        var tipo_documento = $("select[name='tipo_documento']").val();
        var materia = $("input[name='materia']").val();
        var encabezado = $("input[name='encabezado']").val();
        var cuerpo = editor_cuerpo.getData();
        var distribucion = editor_distribucion.getData();
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        // var cuerpo = tinymce.get("form_cuerpo").getContent();
        // var distribucion = tinymce.get("form_distribucion").getContent();
        urlAccion = route('documentos.vista_previa_sg');
        $.ajax({
            url: urlAccion,
            type: 'POST',
            dataType: 'json',

            data: {
                _token: _token,
                materia: materia,
                encabezado: encabezado,
                cuerpo: cuerpo,
                distribucion: distribucion,
                tipo_documento: tipo_documento
            },
            success: function(data) {
                Swal.close();
                //window.open('/vista_previa_sg/' + data.id_documento);
                window.open(route('documentos.vp_sg',{'id':data.id_documento}));

            },
            error: function(jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en el documento", "¡Aviso!");
                Swal.close();

                if ($("#idAsignado").html() != "No Asignado") {
                    $('.btn-guardar-submit').html('Guardar y Cerrar');
                } else {
                    $('.btn-guardar-submit').html('Guardar');
                }
                habilita_boton('btn-guardar-submit');
                habilita_boton('btn-guardar-submit-edit');
                habilita_boton('btn-enviar-submit');
                habilita_boton('btn_cerrar_guardar');
            }

        });
        Swal.fire({
            title: 'Generando vista previa',
            allowEscapeKey: false,
            allowOutsideClick: false,
            onOpen: () => {
                swal.showLoading();
            }
        })
    }

    function accion_firmar(id_documento, id_documento_buzon, id_documento_buzon_padre) {
        $('#titulo_accion').html('Ver Documento ID '+id_documento);

        deshabilita_campos();
        cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre, 2, 33);
        //cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,2,44);  

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');
        habilita_boton('btn-vp');

        //listado de visaciones y firmas
        $('.row_txt_firmar').show();

        var buttonFirmar = '<button onClick="firmar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit btn-firmar">Firmar</button> ';
        $('#addButton').append(buttonFirmar);

    }

    function accion_finalizar(id_documento, id_documento_buzon, id_documento_buzon_padre) {
        $('#titulo_accion').html('Ver Documento ID '+id_documento);

        deshabilita_campos();
        cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre, 2);

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

        var buttonFinaliza = '<button onClick="finalizar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Finalizar</button> ';
        $('#addButton').append(buttonFinaliza);

    }

    function derivar_recibidos(id_documento, id_documento_buzon, id_documento_buzon_padre) {
        $('#titulo_accion').html('Ver Documento ID '+id_documento);

        deshabilita_campos();
        cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre, 2, 1);

        $('#form_comentario_el').prop("disabled", false);

        $('#form_otros_destinatarios_el').prop("disabled", false);
        $('#form_comentario_otro_el').prop("disabled", false);
        $(".bootstrap-tagsinput").removeClass("disabled");

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');


        var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Enviar</button> ';
        $('#addButton').append(buttonDerivar);
        
        
    }

    function accion_derivar_recibidos(id_documento, id_documento_buzon, id_documento_buzon_padre) {
        $('#titulo_accion').html('Ver Documento ID '+id_documento);

        deshabilita_campos();
        cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre, 2, 44);

        $('#form_comentario_el').prop("disabled", false);

        $('#form_otros_destinatarios_el').prop("disabled", false);
        $('#form_comentario_otro_el').prop("disabled", false);
        $(".bootstrap-tagsinput").removeClass("disabled");

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

        var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Enviar</button> ';
        $('#addButton').append(buttonDerivar);

    }

    function archivar_recibidos(id_documento, id_documento_buzon, id_documento_buzon_padre, accion) {

        $('#titulo_accion').html('Ver Documento ID '+id_documento);

        deshabilita_campos();
        cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre, 2);

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

        var buttonArchivar = '<button onClick="archivar_documento(' + accion + ')" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">' + txtArchivado[accion][0] + '</button> ';
        $('#addButton').append(buttonArchivar);

        $(".row_archivar").show();

    }

    function bitacora(id_documento) {

        $("#collapseOne").collapse('hide');
        $('#card_crear_documento').hide();
        $('#card_bitacora').show();

        $('input[name="buscar_accion"]').on('change', function() {
            var types = $('input:checkbox[name="buscar_accion"]:checked').map(function() {
                return '^' + this.value + '\$';
            }).get().join('|');

            gridBitacora.fnFilter(types, 0, true, false, false, false);
        });

        cargar_datos_bitacora(id_documento);
    }

    function bitacora_modal(id_documento){
            cargar_datos_bitacora(id_documento);
            let html = $("#card_bitacora").html();
            swal.fire({html:html,width: '90%'});
    }

    function cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre, carpeta, accion) {
        clear_form();
        console.log("cargar_datos_grilla");
        $("#collapseOne").collapse('hide');
        $('#card_crear_documento').show();
        $('#card_bitacora').hide();

        if (carpeta == 2)
            var docBuzon = id_documento_buzon_padre;
        else
            var docBuzon = id_documento_buzon;
        $.ajax({
           // url: "/documentos/" + id_documento,
            url: route('documentos.ver',{'buzon':docBuzon,'id':id_documento}),
            type: 'GET',
            dataType: 'json',
            //async: false,
            data: {
                hiddIdDocumentoBuzon: docBuzon
            }, 
            success: function(data) {
                if (data.status == '400') {
                    toastr.error(data.data.comentario, "¡Aviso!");
                } else {
                    if (data.status == '200') {
                        objDoc = data.data;

                        var json_tipo_doc = $.parseJSON(data.data.json_tipo_documento);
                        if (data.data.rel_documento_buzon[0]['contestar_hasta'] != null) {
                            var fechaContestarHasta = data.data.rel_documento_buzon[0]['contestar_hasta'].split(' ');
                            $("input[name='contestar_hasta']").val(fechaContestarHasta[0]);
                        }
                        var idBuzon = $("input[name='hiddIdBuzon']").val();
                        var nFlujo = json_tipo_doc['id_tipo_flujo'];
                        var jsonAcciones = json_tipo_doc['buzones_flujo'];
                        var jsonTipoAvance = json_tipo_doc['id_tipo_avance'];
                        var jsonRespuesta = $.parseJSON(data.data.json_respuesta_a);
                        var jsonDocResponder = data.data.rel_responder;


                        $("input[name='hiddPrimeraFirma']").val(0);
                        $("input[name='hiddUltimaFirma']").val(0);
                        $("input[name='hiddBuzonPrimera']").val(0);
                        $("input[name='hiddBuzonUltima']").val(0);
                        $("input[name='hiddNroFirmas']").val(0);
                        if (typeof json_tipo_doc['derivar_primera_firma'] !== 'undefined') {
                            $("input[name='hiddPrimeraFirma']").val(json_tipo_doc['derivar_primera_firma']);
                        }
                        if (typeof json_tipo_doc['derivar_ultima_firma'] !== 'undefined') {
                            $("input[name='hiddUltimaFirma']").val(json_tipo_doc['derivar_ultima_firma']);
                        }
                        if (typeof json_tipo_doc['buzon_primera_firma'] !== 'undefined') {
                            $("input[name='hiddBuzonPrimera']").val(json_tipo_doc['buzon_primera_firma']);
                        }
                        if (typeof json_tipo_doc['buzon_ultima_firma'] !== 'undefined') {
                            $("input[name='hiddBuzonUltima']").val(json_tipo_doc['buzon_ultima_firma']);
                        }

                        if (typeof json_tipo_doc['numero_firmas'] !== 'undefined') {
                            $("input[name='hiddNroFirmas']").val(json_tipo_doc['numero_firmas']);
                        }
                        //derivar despues de firmar
                        var hiddPrimeraFirma = json_tipo_doc['derivar_primera_firma'];
                        var hiddUltimaFirma = json_tipo_doc['derivar_ultima_firma'];
                        var hiddBuzonPrimera = json_tipo_doc['buzon_primera_firma'];
                        var hiddBuzonUltima = json_tipo_doc['buzon_ultima_firma'];
                        var hiddNroFirmas = json_tipo_doc['numero_firmas'];

                        datoTipoJson = json_tipo_doc;

                        $("select[name='tipo_documento']").val(data.data.id_tipo_documento);
                        $("select[name='nivel_acceso']").val(data.data.id_nivel_acceso);
                        $("select[name='efectos_terceros']").val("" + data.data.efectos_terceros + "");

                        $("input[name='materia']").val(data.data.materia);
                        $("input[name='anterior']").val(data.data.anterior);
                        $("textarea[name='descripcion']").val(data.data.descripcion);

                        $("input[name='encabezado']").val(json_tipo_doc['plantilla_encabezado']);
                        $("input[name='hiddIdOrigen']").val(json_tipo_doc['id_tipo_origen']);

                        editor_cuerpo.setData(data.data.cuerpo);
                        //tinymce.get("form_cuerpo").setContent(data.data.cuerpo);
                        editor_distribucion.setData(data.data.distribucion);
                        //tinymce.get("form_cuerpo").setContent(data.data.cuerpo);
                        // if(data.data.cuerpo !== null){
                        //     tinymce.get("form_cuerpo").setContent(data.data.cuerpo);
                        // }
                        // else{
                        //     tinymce.get("form_cuerpo").setContent('&nbsp;');
                        // }

                        //editor_distribucion.setData(data.data.plantilla_distribucion);
                        // if(data.data.distribucion !== null){
                        //     tinymce.get("form_distribucion").setContent(data.data.distribucion);
                        // }
                        // else{
                        //     tinymce.get("form_distribucion").setContent('&nbsp;');
                        // }

                        $("input[name='hiddIdDocumento']").val(data.data.id_documento);
                        $("input[name='hiddIdDocumentoBuzon']").val(id_documento_buzon);
                        if (data.data.rel_documento_buzon_actual.length > 0) {
                            jQuery.each(data.data.rel_documento_buzon_actual, function(i, val) {
                                if (val.id_buzon = docBuzon)
                                    $("input[name='hiddIdTipoDestino']").val(val.id_tipo_destino);
                            })

                        } else {
                            $("input[name='hiddIdTipoDestino']").val(1);
                        }
                        //reemplazo IDDOC por EstadoTramitacion
                        $("#idAsignado").html("<b>" + estadosTramitacion[(data.data.estado_tramitacion-1)].nombre + "</b>");

                        if (data.data.folio != null)
                            $("#idFolio").html("<b>" + data.data.folio + "</b>");

                        if (data.data.fecha != null){
                            let fFolio = new Date(data.data.fecha);
                            $("#idFecha").html("<b>" + fFolio.toLocaleDateString() + "</b>");
                        }
                        if(data.data.id_documento!=null){
                            $(".item-bitacora").show();
                            $("#boton-bitacora").data("id",data.data.id_documento);
                        }
                       


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
                            $('.row_anexo').show();
                            //$('.row_anexo').hide();
                            $('#form_archivo_principal_el').hide();
                            $('#cargar_archivo_principal_el').show();
                        }

                        $('#form_otros_archivos_el').hide();
                        $('#cargar_otros_archivos').show();

                        var relDocumentoBuzon = data.data.rel_documento_buzon;

                        //acciones bitacora
                        var relDatosBitacora = data.data.rel_bitacora;
                        var htmlDatosbitacora = "";
                        var firmasRealizadas = 0;
                        var htmlDatosVisadores = "";
                        var htmlDatosVisadoresPrev = "";
                        var htmlDatosFirmantes = "";
                        var txtDatosPrev = "";

                        $.each(relDatosBitacora, function(i, item) {
                            if (item.id_accion == 6) {
                                let patron = /\[.+?\]/g;

                                txtDatos = item.id_buzon + item.id_usuario;
                                if (txtDatos != txtDatosPrev) {
                                    htmlDatosVisadores = htmlDatosVisadores.replace(/[\[\]]/g, '');
                                    htmlDatosVisadores += "[<div><b>Visado por: </b>" + item.nombres + ' ' + item.primer_apellido + ' - ' + item.nombre + ' - ' + moment(item.fecha).format('DD-MM-YYYY HH:mm') + '</div>]';
                                } else {
                                    htmlDatosVisadoresNew = "<div><b>Visado por: </b>" + item.nombres + ' ' + item.primer_apellido + ' - ' + item.nombre + ' - ' + moment(item.fecha).format('DD-MM-YYYY HH:mm') + '</div>';
                                    htmlDatosVisadores = htmlDatosVisadores.replace(patron, htmlDatosVisadoresNew);
                                }

                                txtDatosPrev = item.id_buzon + item.id_usuario;

                            }

                            if (item.id_accion == 7) {
                                htmlDatosFirmantes += "<div><b>Firmado por: </b>" + item.nombres + ' ' + item.primer_apellido + ' - ' + item.nombre + ' - ' + moment(item.fecha).format('DD-MM-YYYY HH:mm') + '</div>';
                                firmasRealizadas++;
                            }

                            if (item.id_accion == 4) {
                                txtDatosPrev = "";
                                htmlDatosVisadores = "";
                                htmlDatosVisadoresPrev = "";
                            }

                            if (item.id_accion == 7 || item.id_accion == 8)
                                isDelete = false;

                        });

                        htmlDatosVisadores = htmlDatosVisadores.replace(/[\[\]]/g, '');

                        htmlDatosbitacora = htmlDatosVisadores + htmlDatosFirmantes;

                        $('#datos_bitacora_simple').html(htmlDatosbitacora);

                        if (carpeta == 3 || carpeta == 2) {
                            var buzon_padre = id_documento_buzon;
                            var flujoSgte = json_tipo_doc['flujo_actual'] + 1; //solo aplica cuando está en carpeta = 2
                        } else {
                            var buzon_padre = id_documento_buzon_padre;
                            var flujoSgte = json_tipo_doc['flujo_actual'];
                        }

                        //agrega las acciones correspondientes al tipo de flujo
                        if (nFlujo == 3)
                            var accionesFlujo = accionesFlujo3;

                        //flujo controlado
                        if (nFlujo == 2 || nFlujo == 3) //SE AGREGÓ MIXTO PERO SIN BUZONES PERSONALES - PENDIENTE
                        {
                            //agrega las acciones correspondientes al tipo de flujo
                            var accionesFlujo = accionesFlujo2;

                            //habilitar en carpeta = 3 agregar item extra

                            if (carpeta == 3 && accion == 1) {
                                $('#form_destinatario_principal').prop("disabled", false);
                                $(".bootstrap-tagsinput").removeClass("disabled");
                            }

                            var aBuzonesDerivaciones = [];

                            //obtener accion, buzon en orden siguiente dentro del flujo definido
                            for (let i in jsonAcciones) {
                                //revisar valores de flujo cuando está en carpeta 3
                                if (carpeta == 3) {
                                    if (jsonAcciones[i].orden < 2) {
                                        var aAcciones = jsonAcciones[i].acciones;
                                        var idBuzonAccion = jsonAcciones[i].id_buzon;

                                        if (jsonAcciones[i].orden == 0)
                                            break;
                                    }
                                } else {
                                    if (jsonAcciones[i].orden == flujoSgte) {
                                        var aAcciones = jsonAcciones[i].acciones;
                                        var idBuzonAccion = jsonAcciones[i].id_buzon;

                                        aBuzonesDerivaciones.push({
                                            "id": idBuzonAccion,
                                            "text": listadoBuzones[idBuzonAccion],
                                            "accion": aAcciones
                                        });
                                    }
                                }

                                //guarda buzon y acciones flujo anterior
                                if ((jsonAcciones[i].orden == json_tipo_doc['flujo_actual'] - 1) && jsonAcciones[i].orden != 0 && ((jsonTipoAvance == 2 || jsonTipoAvance == 4) && jsonAcciones[i].orden != 1)) //validar que no se repita 
                                {
                                    aBuzonesDerivaciones.push({
                                        "id": jsonAcciones[i].id_buzon,
                                        "text": listadoBuzones[jsonAcciones[i].id_buzon],
                                        "accion": jsonAcciones[i].acciones
                                    });
                                }

                                //guardar buzon y acciones de flujo 1 para reinicio
                                if (jsonAcciones[i].orden == 1 && jsonAcciones[i].orden != json_tipo_doc['flujo_actual'] && (jsonTipoAvance == 2 || jsonTipoAvance == 4)) {
                                    aBuzonesDerivaciones.push({
                                        "id": jsonAcciones[i].id_buzon,
                                        "text": listadoBuzones[jsonAcciones[i].id_buzon],
                                        "accion": jsonAcciones[i].acciones
                                    });
                                }
                            }

                            $('#form_acciones_solicitadas_el').empty();
                            //console.log(accionesFlujo);
                            for (let i in accionesFlujo) {
                                $('#form_acciones_solicitadas_el').append("<option value='" + accionesFlujo[i][0] + "' >" + accionesFlujo[i][1] + "</option>");
                            }

                            $('#form_acciones_solicitadas_el').multiselect('rebuild');
                            $('#form_acciones_solicitadas_el').multiselect('disable');

                            var bFlujo = false;

                            $.each(relDocumentoBuzon, function(i, item) {
                                if (item.id_documento_buzon == id_documento_buzon_padre) {
                                    $('#bzOrigen').text(listadoBuzones[item.id_buzon]);
                                }

                                if (item.id_tipo_destino == 1 && item.id_documento_buzon_padre == buzon_padre) {
                                    bFlujo = true;

                                    //agrega buzon q corresponde al flujo actual segun carpeta
                                    $("#form_destinatario_principal").val(item.id_buzon);
                                    $("#form_destinatario_principal").trigger('change');

                                    var accionesSolicitadas = $.parseJSON(item.json_acciones);
                                    $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                    for (let i in accionesSolicitadas) {
                                        $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                    }

                                    $("textarea[id='form_comentario_el']").val(item.comentario_principal);

                                }

                                if (item.id_tipo_destino == 2 && item.id_documento_buzon_padre == buzon_padre) {
                                    $('#form_otros_destinatarios_el').tagsinput('add', {
                                        "value": item.id_buzon,
                                        "text": listadoBuzones[item.id_buzon]
                                    });
                                    $("textarea[id='form_comentario_otro_el']").val(item.comentario_secundario);
                                }
                            });

                            if (bFlujo == false) {
                                if (idBuzonAccion != null && idBuzonAccion != '') {

                                    //agrega buzon q corresponde al flujo actual segun carpeta
                                    $("#form_destinatario_principal").val(idBuzonAccion);
                                    $("#form_destinatario_principal").trigger('change');


                                    $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                    for (let i in aAcciones)
                                        $('#form_acciones_solicitadas_el').multiselect('select', aAcciones[i]['id_accion']);
                                } else
                                    deshabilita_campos();
                            }

                            if (carpeta == 2) {
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
                                            maximumSelected: function(args) {
                                                var message = 'Sólo puede seleccionar ' + args.maximum + ' elemento';
                                                if (args.maximum != 1) {
                                                    message += 's';
                                                }
                                                return message;
                                            },
                                        }
                                    }).on('select2:unselect', function(e) {
                                        var data = e.params.data;

                                        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);

                                    }).on('select2:select', function(e) {
                                        var aAcciones = e.params.data.accion;

                                        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                        for (let i in aAcciones) {
                                            $('#form_acciones_solicitadas_el').multiselect('select', aAcciones[i]['id_accion']);
                                        }
                                    });

                                    $('#form_destinatario_principal').val(idBuzonAccion).trigger('change');

                                }

                            }
                            //habilitar si tipo avance = 1



                        } else if (nFlujo == 1) //flujo libre
                        {
                            if (accion == 1) {
                                $('#form_destinatario_principal').prop("disabled", false);
                                $('#form_acciones_solicitadas_el').multiselect('enable');
                                $('#form_respuesta_a').multiselect('enable');

                                //quita accion 9 del listado
                                $("#form_acciones_solicitadas_el option[value='9']").remove();
                                $('#form_acciones_solicitadas_el').multiselect('rebuild');
                            }

                            /* responder a */

                            //selecciona documentos en respuesta a
                            $('#form_respuesta_a').multiselect({
                                numberDisplayed: 6
                            });
                            $('#form_respuesta_a').multiselect('deselectAll', true);

                            if (carpeta != 3 || (carpeta == 3 && accion == 0))
                                $('#form_respuesta_a').empty();

                            var sDivActualPrev = "";
                            var sDivActualNext = "";
                            var sDivIzq = "";
                            for (let j in jsonRespuesta) {
                                if (carpeta != 3 || (carpeta == 3 && accion == 0))
                                    $('#form_respuesta_a').append("<option selected value='" + jsonRespuesta[j]['id_documento'] + "' >" + jsonRespuesta[j]['identificador'] + "-" + jsonRespuesta[j]['materia'] + "</option>");
                                else
                                    $('#form_respuesta_a').multiselect('select', jsonRespuesta[j]['id_documento']);

                                //completa carrusel lado izq
                                sDivIzq += ' <div class="item"><div class="item_display"><a href="#" onclick="ver_recibidos_alerta(' + jsonRespuesta[j]['identificador'] + ',' + id_documento_buzon + ',' + docBuzon + ',\'' + jsonRespuesta[j]['materia'] + '\')">' + jsonRespuesta[j]['identificador'] + '</a><p>' + moment(jsonRespuesta[j]['created_at']).format('DD-MM-YYYY') + '</p></div></div>';
                            }

                            $('#form_respuesta_a').multiselect('rebuild');
                            $('#form_respuesta_a').multiselect('refresh');

                            //completar carrusel lado der
                            var sDivDer = "";
                            for (let d in jsonDocResponder) {
                                sDivDer += ' <div class="item"><div class="item_display" ><a href="#" onclick="ver_recibidos_alerta(' + jsonDocResponder[d]['identificador'] + ',' + id_documento_buzon + ',' + docBuzon + ',\'' + jsonDocResponder[d]['materia'] + '\')" >' + jsonDocResponder[d]['identificador'] + '</a><p>' + moment(jsonDocResponder[d]['created_at']).format('DD-MM-YYYY') + '</p></div></div>';
                            }


                            sDivActual = '<div class="item"><div class="item_display item-doc" ><a href="#" onclick="ver_recibidos_alerta(' + data.data.identificador + ',' + id_documento_buzon + ',' + docBuzon + ',\'' + data.data.materia + '\')" >' + data.data.identificador + '</a><p>' + moment(data.data.created_at).format('DD-MM-YYYY') + '</p></div></div>';

                            if (sDivDer != '')
                                sDivActualPrev = '<div class="item"><div class="item_prev"><i class="fas fa-reply-all fa-2x"></i></div></div>';

                            if (sDivIzq != '')
                                sDivActualNext = '<div class="item"><div class="item_next"><i class="fas fa-reply-all fa-2x"></i></div></div>';

                            if (sDivIzq != '' || sDivDer != '') {
                                owl = $('.owl-carousel').owlCarousel();
                                owl.trigger('destroy.owl.carousel');
                                owl.find('.owl-stage-outer').children().unwrap();
                                owl.removeClass("owl-center owl-loaded owl-text-select-on");

                                var content = sDivIzq + sDivActualNext + sDivActual + sDivActualPrev + sDivDer;
                                owl.html(content);

                                //reinitialize the carousel (call here your method in which you've set specific carousel properties)
                                owl.owlCarousel({
                                    items: 8,
                                    margin: 10,
                                    dots: true,
                                    nav: true,
                                    navText: ["<div class='nav-button owl-prev'>‹</div>", "<div class='nav-button owl-next'>›</div>"],

                                }).trigger('refresh.owl.carousel');
                            }

                            $.each(relDocumentoBuzon, function(i, item) {
                                if (item.id_documento_buzon == id_documento_buzon_padre) {
                                    $('#bzOrigen').text(listadoBuzones[item.id_buzon]);
                                }

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
                                if (item.id_tipo_destino == 1 && item.id_documento_buzon == buzon_padre && carpeta == 2) {
                                    var accionesSolicitadas = $.parseJSON(item.json_acciones);
                                    if (accion == 11) { //seleccion boton ver
                                        deshabilita_campos();
                                        if (item.id_estado_documento == 4) { //documento pendiente
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                                if (accionesSolicitadas[i]['id_accion'] == 4) { //editar   
                                                    var buttonEditar = '<button onClick="activar_editar(3)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-editar ">Editar</button> ';
                                                    $('#addButton').append(buttonEditar);
                                                }
                                                if (accionesSolicitadas[i]['id_accion'] == 6) { //visar                                                    
                                                    var buttonVisar = '<button onClick="guarda_destinatarios_documento(6)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-visar ">Visar</button> ';
                                                    $('#addButton').append(buttonVisar);
                                                }
                                                if (accionesSolicitadas[i]['id_accion'] == 7) { //Firmar                                                    
                                                    var buttonFirmar = '<button onClick="firmar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-firmar ">Firmar</button> ';
                                                    $('#addButton').append(buttonFirmar);
                                                }
                                                if (accionesSolicitadas[i]['id_accion'] == 10) { //finalizar                                                    
                                                    var buttonFinaliza = '<button onClick="finalizar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Finalizar</button> ';
                                                    $('#addButton').append(buttonFinaliza);
                                                }
                                            }
                                            $('#form_destinatario_principal').prop("disabled", false);
                                            $('#form_acciones_solicitadas_el').multiselect('enable');
                                            $('#form_comentario_el').prop("disabled", false);
                                            $('#form_otros_destinatarios_el').prop("disabled", false);
                                            $('#form_comentario_otro_el').prop("disabled", false);
                                            $(".bootstrap-tagsinput-max").removeClass("disabled");
                                            $(".bootstrap-tagsinput").removeClass("disabled");
                                            //$('.btn-enviar-submit').show(); 
                                            $('#submit-enviar').removeClass('btn-primary');
                                            $('#submit-enviar').addClass('btn-success');
                                            var buttonDerivar = '<button onClick="guardar_enviar()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-derivar-2 ">Guardar y Enviar</button> ';
                                            $('#addButton').append(buttonDerivar);
                                            var buttonArchivar = '<button onClick="archivar_documento_botonera(0)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Archivar</button> ';
                                            $('#addButton').append(buttonArchivar);
                                            //console.log('ver-pendiente');


                                        } //fin estado documento pendiente
                                        if (item.id_estado_documento == 6) { //documento archivado
                                            var buttonDesarchivar = '<button onClick="archivar_documento(1)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Desarchivar</button> ';
                                            $('#addButton').append(buttonDesarchivar);
                                        } //fin estado archivado
                                        if (item.id_estado_documento == 9) { //documento firmado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                                if (accionesSolicitadas[i]['id_accion'] == 11) { //derivar
                                                    $('#form_destinatario_principal').prop("disabled", false);
                                                    $('#form_acciones_solicitadas_el').multiselect('enable');
                                                    $('#form_comentario_el').prop("disabled", false);
                                                    $('#form_otros_destinatarios_el').prop("disabled", false);
                                                    $('#form_comentario_otro_el').prop("disabled", false);
                                                    $(".bootstrap-tagsinput-max").removeClass("disabled");
                                                    $(".bootstrap-tagsinput").removeClass("disabled");
                                                    var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-derivar ">Enviar</button> ';
                                                    $('#addButton').append(buttonDerivar);
                                                    $('#submit-enviar').removeClass('btn-primary');
                                                    $('#submit-enviar').addClass('btn-success');
                                                }
                                            }

                                        } //fin estado firmado
                                        if (item.id_estado_documento == 11) { //visado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                                if (accionesSolicitadas[i]['id_accion'] == 11) { //derivar
                                                    $('#form_destinatario_principal').prop("disabled", false);
                                                    $('#form_acciones_solicitadas_el').multiselect('enable');
                                                    $('#form_comentario_el').prop("disabled", false);
                                                    $('#form_otros_destinatarios_el').prop("disabled", false);
                                                    $('#form_comentario_otro_el').prop("disabled", false);
                                                    $(".bootstrap-tagsinput-max").removeClass("disabled");
                                                    $(".bootstrap-tagsinput").removeClass("disabled");
                                                    var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-derivar ">Enviar</button> ';
                                                    $('#addButton').append(buttonDerivar);
                                                    $('#submit-enviar').removeClass('btn-primary');
                                                    $('#submit-enviar').addClass('btn-success');
                                                }
                                            }
                                        } //fin estaddo visado
                                        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                    } //fin boton accion ver
                                    ///////////////////////////////
                                    if (accion == 1) { //seleccion boton editar
                                        if (item.id_estado_documento == 4) { //documento pendiente
                                            $('.btn-guardar-submit').show();
                                            //$('.btn-enviar-submit').html('Guardar y Enviar');
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            $('#form_destinatario_principal').prop("disabled", false);
                                            $('#form_acciones_solicitadas_el').multiselect('enable');
                                            $('#form_comentario_el').prop("disabled", false);
                                            $('#form_otros_destinatarios_el').prop("disabled", false);
                                            $('#form_comentario_otro_el').prop("disabled", false);
                                            $(".bootstrap-tagsinput-max").removeClass("disabled");
                                            $(".bootstrap-tagsinput").removeClass("disabled");
                                            var buttonDerivar = '<button onClick="guardar_enviar()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-derivar-2 ">Guardar y Enviar</button> ';
                                            $('#addButton').append(buttonDerivar);
                                            $('#submit-enviar').removeClass('btn-primary');
                                            $('#submit-enviar').addClass('btn-success');

                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                                if (accionesSolicitadas[i]['id_accion'] == 6) { //visar                                                    
                                                    var buttonVisar = '<button onClick="guarda_destinatarios_documento(6)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-visar ">Visar</button> ';
                                                    $('#addButton').append(buttonVisar);
                                                }
                                                if (accionesSolicitadas[i]['id_accion'] == 7) { //Firmar                                                    
                                                    var buttonFirmar = '<button onClick="firmar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-firmar ">Firmar</button> ';
                                                    $('#addButton').append(buttonFirmar);
                                                }
                                                if (accionesSolicitadas[i]['id_accion'] == 10) { //finalizar                                                    
                                                    var buttonFinaliza = '<button onClick="finalizar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Finalizar</button> ';
                                                    $('#addButton').append(buttonFinaliza);
                                                }
                                            }

                                            var buttonArchivar = '<button onClick="archivar_documento_botonera(0)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Archivar</button> ';
                                            $('#addButton').append(buttonArchivar);
                                        } //fin estado documento pendiente
                                        if (item.id_estado_documento == 6) { //documento archivado
                                            var buttonDesarchivar = '<button onClick="archivar_documento(1)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Desarchivar</button> ';
                                            $('#addButton').append(buttonDesarchivar);
                                        } //fin estadi archivado
                                        if (item.id_estado_documento == 9) { //documento firmado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            $('.btn-guardar-submit').show();
                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                                if (accionesSolicitadas[i]['id_accion'] == 11) { //derivar
                                                    $('#form_destinatario_principal').prop("disabled", false);
                                                    $('#form_acciones_solicitadas_el').multiselect('enable');
                                                    $('#form_comentario_el').prop("disabled", false);
                                                    $('#form_otros_destinatarios_el').prop("disabled", false);
                                                    $('#form_comentario_otro_el').prop("disabled", false);
                                                    $(".bootstrap-tagsinput-max").removeClass("disabled");
                                                    $(".bootstrap-tagsinput").removeClass("disabled");
                                                    var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-derivar ">Enviar </button> ';
                                                    $('#addButton').append(buttonDerivar);
                                                    $('#submit-enviar').removeClass('btn-primary');
                                                    $('#submit-enviar').addClass('btn-success');
                                                }
                                            }
                                        } //fin estado firmado
                                        if (item.id_estado_documento == 11) { //visado
                                            $('.btn-guardar-submit').show();
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                                if (accionesSolicitadas[i]['id_accion'] == 11) { //derivar
                                                    $('#form_destinatario_principal').prop("disabled", false);
                                                    $('#form_acciones_solicitadas_el').multiselect('enable');
                                                    $('#form_comentario_el').prop("disabled", false);
                                                    $('#form_otros_destinatarios_el').prop("disabled", false);
                                                    $('#form_comentario_otro_el').prop("disabled", false);
                                                    $(".bootstrap-tagsinput-max").removeClass("disabled");
                                                    $(".bootstrap-tagsinput").removeClass("disabled");
                                                    var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-derivar ">Enviar </button> ';
                                                    $('#addButton').append(buttonDerivar);
                                                    $('#submit-enviar').removeClass('btn-primary');
                                                    $('#submit-enviar').addClass('btn-success');
                                                }
                                            }
                                        } //fin estado visado
                                        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                    } //fin boton accion editar
                                    ///////////////////////
                                    if (accion == 22) { //seleccion boton visar
                                        $('.btn-recibir-submit').hide();
                                        $('.btn-enviar-submit').hide();
                                        deshabilita_campos();
                                        if (item.id_estado_documento == 4) { //documento pendiente  
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            var buttonEditar = '<button onClick="activar_editar(3)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-editar ">Editar</button> ';
                                            $('#addButton').append(buttonEditar);

                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                                if (accionesSolicitadas[i]['id_accion'] == 7) { //Firmar                                                    
                                                    var buttonFirmar = '<button onClick="firmar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-firmar ">Firmar</button> ';
                                                    $('#addButton').append(buttonFirmar);
                                                }
                                                if (accionesSolicitadas[i]['id_accion'] == 10) { //finalizar                                                    
                                                    var buttonFinaliza = '<button onClick="finalizar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Finalizar</button> ';
                                                    $('#addButton').append(buttonFinaliza);
                                                }
                                            }
                                            var buttonVisar = '<button onClick="guarda_destinatarios_documento(6)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-visar ">Visar</button> ';
                                            $('#addButton').append(buttonVisar);
                                            $('#form_destinatario_principal').prop("disabled", false);
                                            $('#form_acciones_solicitadas_el').multiselect('enable');
                                            $('#form_comentario_el').prop("disabled", false);
                                            $('#form_otros_destinatarios_el').prop("disabled", false);
                                            $('#form_comentario_otro_el').prop("disabled", false);
                                            $(".bootstrap-tagsinput-max").removeClass("disabled");
                                            $(".bootstrap-tagsinput").removeClass("disabled");
                                            $('#submit-enviar').removeClass('btn-primary');
                                            $('#submit-enviar').addClass('btn-success');
                                            var buttonVisarDerivar = '<button onClick="guarda_destinatarios_documento(8)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-visar-derivar w-15">Visar y Enviar</button> ';
                                            $('#addButton').append(buttonVisarDerivar);
                                            var buttonArchivar = '<button onClick="archivar_documento_botonera(0)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Archivar</button> ';
                                            $('#addButton').append(buttonArchivar);
                                            //console.log('visar-pendiente');
                                        } //fin estado documento pendiente
                                        if (item.id_estado_documento == 6) { //documento archivado
                                            var buttonDesarchivar = '<button onClick="archivar_documento(1)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Desarchivar</button> ';
                                            $('#addButton').append(buttonDesarchivar);
                                        } //fin estadi archivado
                                        if (item.id_estado_documento == 9) { //documento firmado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            $('.btn-guardar-submit').show();
                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                                if (accionesSolicitadas[i]['id_accion'] == 11) { //derivar
                                                    $('#form_destinatario_principal').prop("disabled", false);
                                                    $('#form_acciones_solicitadas_el').multiselect('enable');
                                                    $('#form_comentario_el').prop("disabled", false);
                                                    $('#form_otros_destinatarios_el').prop("disabled", false);
                                                    $('#form_comentario_otro_el').prop("disabled", false);
                                                    $(".bootstrap-tagsinput-max").removeClass("disabled");
                                                    $(".bootstrap-tagsinput").removeClass("disabled");
                                                    var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-derivar ">Enviar </button> ';
                                                    $('#addButton').append(buttonDerivar);
                                                    $('.btn-guardar-submit').hide();
                                                    $('.btn-guardar-submit').hide();
                                                    $('#submit-enviar').removeClass('btn-primary');
                                                    $('#submit-enviar').addClass('btn-success');
                                                }
                                            }
                                            //console.log('visar-firmado');
                                        } //fin estado firmado
                                        if (item.id_estado_documento == 11) { //visado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                            }
                                            $('#form_destinatario_principal').prop("disabled", false);
                                            $('#form_acciones_solicitadas_el').multiselect('enable');
                                            $('#form_comentario_el').prop("disabled", false);
                                            $('#form_otros_destinatarios_el').prop("disabled", false);
                                            $('#form_comentario_otro_el').prop("disabled", false);
                                            $(".bootstrap-tagsinput-max").removeClass("disabled");
                                            $(".bootstrap-tagsinput").removeClass("disabled");
                                            var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-derivar ">Enviar </button> ';
                                            $('#addButton').append(buttonDerivar);
                                            $('#submit-enviar').removeClass('btn-primary');
                                            $('#submit-enviar').addClass('btn-success');
                                            //console.log('visar-visado');
                                        } //fin estado visado
                                        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                    } //fin boton accion visar
                                    ///////////////////////
                                    if (accion == 33) { //seleccion boton firmar
                                        $('.btn-recibir-submit').hide();
                                        $('.btn-enviar-submit').hide();
                                        deshabilita_campos();
                                        if (item.id_estado_documento == 4) { //documento pendiente  
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            var buttonEditar = '<button onClick="activar_editar(3)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-editar ">Editar</button> ';
                                            $('#addButton').append(buttonEditar);

                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                                if (accionesSolicitadas[i]['id_accion'] == 7) { //Firmar                                                    
                                                    var buttonFirmar = '<button onClick="firmar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-firmar ">Firmar</button> ';
                                                    $('#addButton').append(buttonFirmar);
                                                }
                                                if (accionesSolicitadas[i]['id_accion'] == 10) { //finalizar                                                    
                                                    var buttonFinaliza = '<button onClick="finalizar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Finalizar</button> ';
                                                    $('#addButton').append(buttonFinaliza);
                                                }
                                            }
                                            $('#form_destinatario_principal').prop("disabled", false);
                                            $('#form_acciones_solicitadas_el').multiselect('enable');
                                            $('#form_comentario_el').prop("disabled", false);
                                            $('#form_otros_destinatarios_el').prop("disabled", false);
                                            $('#form_comentario_otro_el').prop("disabled", false);
                                            $(".bootstrap-tagsinput-max").removeClass("disabled");
                                            $(".bootstrap-tagsinput").removeClass("disabled");
                                            $('#submit-enviar').removeClass('btn-primary');
                                            $('#submit-enviar').addClass('btn-success');
                                            var buttonFirmarDerivar = '<button onClick="firmar_derivar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-firmar-derivar w-15">Firmar y Enviar</button> ';
                                            $('#addButton').append(buttonFirmarDerivar);

                                            var buttonVisar = '<button onClick="guarda_destinatarios_documento(6)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-visar ">Visar</button> ';
                                            $('#addButton').append(buttonVisar);
                                            var buttonArchivar = '<button onClick="archivar_documento_botonera(0)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Archivar</button> ';
                                            $('#addButton').append(buttonArchivar);

                                            //console.log('firmar-pendiente');
                                            firmar_derivar_automatico(hiddPrimeraFirma, hiddUltimaFirma, hiddBuzonPrimera, hiddBuzonUltima, firmasRealizadas, hiddNroFirmas, 1);

                                        } //fin estado documento pendiente
                                        if (item.id_estado_documento == 6) { //documento archivado
                                            var buttonDesarchivar = '<button onClick="archivar_documento(1)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Desarchivar</button> ';
                                            $('#addButton').append(buttonDesarchivar);
                                        } //fin estadi archivado
                                        if (item.id_estado_documento == 9) { //documento firmado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            $('.btn-guardar-submit').show();
                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                            }
                                            $('#form_destinatario_principal').prop("disabled", false);
                                            $('#form_acciones_solicitadas_el').multiselect('enable');
                                            $('#form_comentario_el').prop("disabled", false);
                                            $('#form_otros_destinatarios_el').prop("disabled", false);
                                            $('#form_comentario_otro_el').prop("disabled", false);
                                            $(".bootstrap-tagsinput-max").removeClass("disabled");
                                            $(".bootstrap-tagsinput").removeClass("disabled");
                                            var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-derivar ">Enviar </button> ';
                                            $('#addButton').append(buttonDerivar);
                                            $('.btn-guardar-submit').hide();
                                            $('.btn-guardar-submit').hide();
                                            $('#submit-enviar').removeClass('btn-primary');
                                            $('#submit-enviar').addClass('btn-success');
                                            //console.log('firmar-firmado');
                                            firmar_derivar_automatico(hiddPrimeraFirma, hiddUltimaFirma, hiddBuzonPrimera, hiddBuzonUltima, firmasRealizadas, hiddNroFirmas, 1);
                                        } //fin estado firmado
                                        if (item.id_estado_documento == 11) { //visado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                                if (accionesSolicitadas[i]['id_accion'] == 11) { //derivar
                                                    $('#form_destinatario_principal').prop("disabled", false);
                                                    $('#form_acciones_solicitadas_el').multiselect('enable');
                                                    $('#form_comentario_el').prop("disabled", false);
                                                    $('#form_otros_destinatarios_el').prop("disabled", false);
                                                    $('#form_comentario_otro_el').prop("disabled", false);
                                                    $(".bootstrap-tagsinput-max").removeClass("disabled");
                                                    $(".bootstrap-tagsinput").removeClass("disabled");
                                                    var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-derivar ">Enviar </button> ';
                                                    $('#addButton').append(buttonDerivar);
                                                    $('#submit-enviar').removeClass('btn-primary');
                                                    $('#submit-enviar').addClass('btn-success');
                                                }
                                                if (accionesSolicitadas[i]['id_accion'] == 7) { //Firmar                                                    
                                                    var buttonFirmar = '<button onClick="firmar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-firmar ">Firmar</button> ';
                                                    $('#addButton').append(buttonFirmar);
                                                }
                                            }
                                            //console.log('firmar-visado');
                                            firmar_derivar_automatico(hiddPrimeraFirma, hiddUltimaFirma, hiddBuzonPrimera, hiddBuzonUltima, firmasRealizadas, hiddNroFirmas, 1);
                                        } //fin estado visado                                        
                                    } //fin boton accion firmar
                                    if (accion == 44) { //accion derivar
                                        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                        //quita accion 9 del listado
                                        $("#form_acciones_solicitadas_el option[value='9']").remove();
                                        $('#form_acciones_solicitadas_el').multiselect('rebuild');
                                        $('#form_destinatario_principal').prop("disabled", false);
                                        $('#form_acciones_solicitadas_el').multiselect('enable');
                                        $('#form_comentario_el').prop("disabled", false);
                                        $('#form_otros_destinatarios_el').prop("disabled", false);
                                        $('#form_comentario_otro_el').prop("disabled", false);
                                        $(".bootstrap-tagsinput-max").removeClass("disabled");
                                        $(".bootstrap-tagsinput").removeClass("disabled");
                                    } //fin boton accion derivar
                                }

                                if (item.id_tipo_destino == 2 && item.id_documento_buzon_padre == buzon_padre) {
                                    $('#form_otros_destinatarios_el').tagsinput('add', {
                                        "value": item.id_buzon,
                                        "text": listadoBuzones[item.id_buzon]
                                    });
                                    $("textarea[id='form_comentario_otro_el']").val(item.comentario_secundario);
                                }
                                if (item.id_tipo_destino == 2 && item.id_documento_buzon == buzon_padre && carpeta == 2) {
                                    if (item.id_estado_documento != 5 && item.id_estado_documento != 6 && item.id_estado_documento != 7 && item.id_estado_documento != 8 && item.id_estado_documento != 10 && item.id_estado_documento != 12 && item.id_estado_documento != 13) {
                                        $('#form_destinatario_principal').prop("disabled", true);
                                        $('#form_comentario_el').prop("disabled", true);
                                        $('#form_acciones_solicitadas_el').multiselect('disable');
                                    }
                                }
                            });
                        }

                        var relDocumentoBuzonArchivo = data.data.rel_archivos;
                        //console.log(relDocumentoBuzonArchivo);
                        let htmlFile = "";
                        let htmlFileAnexo = '<div class="col-md-12 group-button-alig file-container-all">';
                        let htmlFileOtros = '<div class="col-md-12 group-button-align file-container-all">';
                        let htmlFilePrincipal = '<div class="col-md-12 group-button-align file-container-all">';
                        let htmlFilePrincipal_va = '<div class="col-md-12 file-container-all">';

                        aFilesPrincipal = [];
                        aFilesDelete = [];

                        let contAnexo = 1;
                        var firma_anexo = 0;

                        $.each(relDocumentoBuzonArchivo, function(key, value) {
                            if (value.firma_anexo == 1) {
                                if (value.estado_firma_anexo == 1) //firmado
                                {
                                    var chkFirmaAnexo = '<div class="btn-anexo-firmado1"> <i class="fa fa-check-circle"></i> </div>';
                                    chkFirmaAnexo += '<div style="margin-left: -5px;position: absolute;top: 25px;left: 80px;color:#123977">  <i class="fa fa-square"></i> </div>';

                                    var chkFirmaAnexoView = '<div class="btn-anexo-firmado1">  <i class="fa fa-check-circle"></i> </div>';
                                    chkFirmaAnexoView += '<div style="margin-left: -5px;position: absolute;top: 25px;left: 80px;color:#123977">  <i class="fa fa-square"></i> </div>';

                                } else {
                                    var chkFirmaAnexo = '<input type="checkbox" value="' + value.id_documento_buzon_archivo + '-1" data-toggle="tooltip" checked data-placement="right" title="Al seleccionar esta casilla, este anexo se firmará electrónicamente por el primer firmante (Alcalde, Administrador, etc.)" name="chkFirmaAnexo" style="accent-color: #8ed752;position: absolute;top: 10px;left: 80px;">';
                                    chkFirmaAnexo += '<input type="checkbox" value="' + value.id_documento_buzon_archivo + '-2" onClick="selCheckAnexo(this)" data-toggle="tooltip" data-placement="right" title="Al seleccionar esta casilla, este anexo se firmará electrónicamente por el segundo firmante (Secretario Municipal, etc.)" name="chkFirmaAnexo" style="accent-color: #123977;position: absolute;top: 30px;left: 80px;">';

                                    var chkFirmaAnexoView = '<div style="margin-left: -5px;position: absolute;top: 5px;left: 80px; color:#8ed752">  <i class="fa fa-check-square"></i> </div>';
                                    chkFirmaAnexoView += '<div style="margin-left: -5px;position: absolute;top: 25px;left: 80px;color:#123977">  <i class="fa fa-square"></i> </div>';
                                    firma_anexo += 1;
                                }
                            } else if (value.firma_anexo == 2) {
                                if (value.estado_firma_anexo == 1) {
                                    var chkFirmaAnexo = '<div class="btn-anexo-firmado1"> <i class="fa fa-check-circle"></i> </div>';
                                    chkFirmaAnexo += '<input type="checkbox" checked value="' + value.id_documento_buzon_archivo + '-2" data-toggle="tooltip" data-placement="top" title="Al seleccionar esta casilla, este anexo se firmará electrónicamente por el segundo firmante (Secretario Municipal, etc.)" name="chkFirmaAnexo" style="accent-color: #123977;position: absolute;top: 30px;left: 80px;">';
                                    //chkFirmaAnexo += '<div class="btn-anexo-firmado2"> <i class="fa fa-check-circle"></i> </div>';

                                    var chkFirmaAnexoView = '<div class="btn-anexo-firmado1">  <i class="fa fa-check-circle"></i> </div>';
                                    chkFirmaAnexoView += '<div style="margin-left: -5px;position: absolute;top: 25px;left: 80px; color:#123977">  <i class="fa fa-check-square"></i> </div>';
                                    firma_anexo += 1;

                                } else if (value.estado_firma_anexo == 2) {
                                    var chkFirmaAnexo = '<div class="btn-anexo-firmado1"> <i class="fa fa-check-circle"></i> </div>';
                                    chkFirmaAnexo += '<div class="btn-anexo-firmado2"> <i class="fa fa-check-circle"></i> </div>';

                                    var chkFirmaAnexoView = '<div class="btn-anexo-firmado1">  <i class="fa fa-check-circle"></i> </div>';
                                    chkFirmaAnexoView += '<div class="btn-anexo-firmado2">  <i class="fa fa-check-circle"></i> </div>';
                                } else {
                                    var chkFirmaAnexo = '<input type="checkbox" checked value="' + value.id_documento_buzon_archivo + '-1" data-toggle="tooltip" data-placement="top" title="Al seleccionar esta casilla, este anexo se firmará electrónicamente por el primer firmante (Alcalde, Administrador, etc.)" name="chkFirmaAnexo" style="accent-color: #8ed752;position: absolute;top: 10px;left: 80px;">';
                                    chkFirmaAnexo += '<input type="checkbox" onClick="selCheckAnexo(this)" checked value="' + value.id_documento_buzon_archivo + '-2" data-toggle="tooltip" data-placement="top" title="Al seleccionar esta casilla, este anexo se firmará electrónicamente por el segundo firmante (Secretario Municipal, etc.)" name="chkFirmaAnexo" style="accent-color: #123977;position: absolute;top: 30px;left: 80px;">';

                                    var chkFirmaAnexoView = '<div style="margin-left: -5px;position: absolute;top: 5px;left: 80px; color:#8ed752">  <i class="fa fa-check-square"></i> </div>';
                                    chkFirmaAnexoView += '<div style="margin-left: -5px;position: absolute;top: 25px;left: 80px; color:#123977">  <i class="fa fa-check-square"></i> </div>';

                                    firma_anexo += 1;
                                }
                            } else {
                                //if (value.mb <= 4)
                                //{
                                var chkFirmaAnexo = '<input type="checkbox" name="chkFirmaAnexo" value="' + value.id_documento_buzon_archivo + '-1" style="position: absolute;top: 5px;left: 80px;" data-toggle="tooltip" data-placement="top" title="Al seleccionar esta casilla, este anexo se firmará electrónicamente por el primer firmante (Alcalde, Administrador, etc.)" >';
                                chkFirmaAnexo += '<input type="checkbox" name="chkFirmaAnexo" value="' + value.id_documento_buzon_archivo + '-2" onClick="selCheckAnexo(this)" style="position: absolute;top: 23px;left: 80px;" data-toggle="tooltip" data-placement="top" title="Al seleccionar esta casilla, este anexo se firmará electrónicamente por el segundo firmante (Secretario Municipal, etc.)">';
                                //}
                                //else
                                //    var chkFirmaAnexo = '';

                                var chkFirmaAnexoView = '';
                            }
                            var extension = value.nombre_archivo_original.split('.').pop();
                            var imagen = "";

                            switch (extension) {
                                case "xls":
                                case "xlsx":
                                    imagen = "excel.png";
                                    break;
                                case "doc":
                                case "docx":
                                    imagen = "word.png";
                                    break;
                                case "rar":
                                    imagen = "rar.png";
                                    break;
                                case "zip":
                                    imagen = "zip.png";
                                    break;
                                default:
                                    imagen = "pdf.png";
                                    break;
                            }
                            htmlFile = '<div class="file-container ' + value.id_documento_buzon_archivo + '">' +
                                '<span data-toggle="modal" data-target="#PDFModal"  style="cursor:pointer" onClick="initPDFViewer(\''+route('panel.index') + '/files/'+value.nombre_archivo_codificado + '\',\''+value.nombre_archivo_codificado+'\')"><img  src="/img/' + imagen + '" width="83" height=94" style="" /></span> {checkAnexo}' +
                                '<button onClick="ver_archivo(\'' + value.nombre_archivo_codificado + '\')" type="button" class="btn text-nowrap btn-min-w  btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="Descargar" style="margin-left: 3px;"><i class="fa fa-download"></i></button>' +
                                '<p style="width: 90px!important;word-break: break-all;font-size: 12px;line-height: 1;margin-top: 15px;margin-bottom: 5px;">' + value.nombre_archivo_original + '</p>';

                            htmlFile_va = '<div class="file-container ' + value.id_documento_buzon_archivo + '">' +
                                '  <img src="/img/' + imagen + '" width="83" height=94" style="" />' +
                                '<button onClick="ver_archivo(\'' + value.nombre_archivo_codificado + '\')" type="button" class="btn text-nowrap btn-min-w  btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="Ver" style="margin-left: 3px;"><i class="fa fa-download"></i></button>';
                            //if (carpeta == 2 && value.id_documento_buzon == id_documento_buzon && accion == 1)               

                            if (value.id_tipo_archivo == 1 || value.id_tipo_archivo == 3) //ppal externo
                            {
                                htmlFile = htmlFile.replace("{checkAnexo}", "");
                            }

                            if (carpeta == 2) {
                                //revisar                           
                                if (accion != 1 || accion == 'undefined' || accion == 'null') //22 visar 33 firmar 44 derivar 11 ver undefined archivar
                                    htmlFile = htmlFile.replace("{checkAnexo}", chkFirmaAnexoView);

                                if ((accion == 33 || accion == 1) && value.id_tipo_archivo == 2)
                                    htmlFile = htmlFile.replace("{checkAnexo}", chkFirmaAnexo);
                                else
                                    htmlFile = htmlFile.replace("{checkAnexo}", "");

                                if (accion == 1 && isDelete == true)
                                    htmlFile += '<button onClick="deleteFile(\'' + value.id_documento_buzon_archivo + '\')" type="button" class="btn text-nowrap btn-min-w  btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="Eliminar pdf" style="margin-left: -27px;"><i class="fas fa-trash"></i></button>';
                            }

                            if (carpeta == 1)
                                htmlFile = htmlFile.replace("{checkAnexo}", chkFirmaAnexoView);

                            if (carpeta == 3) {
                                if (accion == 1) {
                                    htmlFile = htmlFile.replace("{checkAnexo}", chkFirmaAnexo);
                                    htmlFile += '<button onClick="deleteFile(\'' + value.id_documento_buzon_archivo + '\')" type="button" class="btn text-nowrap btn-min-w  btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="Eliminar Pdf" style="margin-left: -27px;"><i class="fas fa-trash"></i></button>';
                                } else
                                    htmlFile = htmlFile.replace("{checkAnexo}", chkFirmaAnexoView);

                                //if (value.id_documento_buzon != id_documento_buzon)
                                //    htmlFile = "";  
                            }

                            if (value.id_tipo_archivo == 2) //anexo
                                htmlFileAnexo += htmlFile + '</div>';

                            if (value.id_tipo_archivo == 3) //otros
                                htmlFileOtros += htmlFile + '</div>';

                            if (value.id_tipo_archivo == 1 && value.version == 1) //principal
                                htmlFilePrincipal += htmlFile + '</div>';

                            //versiones anteriores 

                            if (value.id_tipo_archivo == 1 && value.version != 1)
                                htmlFilePrincipal_va += htmlFile_va + '</div>';

                            contAnexo += 1;

                        });

                        $('#hiddFirmaAnexo').val(firma_anexo);

                        $('#dropzone-principal-view').html(htmlFilePrincipal + '</div>');
                        $('#dropzone-anexo-view').html(htmlFileAnexo + '</div>');
                        $('#dropzone-otros-view').html(htmlFileOtros + '</div>');
                        $('#versiones_anteriores').html(htmlFilePrincipal_va + '</div>');

                        if (carpeta == 3)
                            $('#card_desplegar_versiones').hide();





                    }
                }
            },
            error: function(e) {
                data = e.responseJSON;
                $("#collapseOne").collapse('show');
                $('#card_crear_documento').hide();
                toastr.error("Problemas al cargar la información del documento");
                console.log(e);
                if (typeof data.errors !== 'undefined') {
                    printErrorMsg(data.errors);
                }
            }
        });
    }

    function selCheckAnexo(x) {
        var checkValSel = x.value;
        var checkComp = checkValSel.substring(0, checkValSel.length - 2) + '-1';

        if (x.checked == true) {
            $('input:checkbox[name="chkFirmaAnexo"]').each(function() {
                let valorChkComp = $(this).val();
                valorChkComp = valorChkComp.substring(0, valorChkComp.length - 2);

                let valorChk = valorChkComp + '-1';

                if (valorChk == checkComp) {
                    $(this).prop("checked", true);
                }
            });
        }
    }

    function firmar_derivar_automatico(hiddPrimeraFirma, hiddUltimaFirma, hiddBuzonPrimera, hiddBuzonUltima, firmasRealizadas, hiddNroFirmas, bloquear) {
        if (hiddPrimeraFirma == 1 && firmasRealizadas == 0) { //derivar en la primera firma
            $("#form_destinatario_principal").val(hiddBuzonPrimera);
            $("#form_destinatario_principal").trigger('change');
            if (bloquear == 1) {
                $("#form_destinatario_principal").prop("disabled", true);
            }
            $('.btn-firmar-derivar').remove()
            $(".btn-firmar").html("Firmar y Enviar");
        }
        if (hiddUltimaFirma == 1 && firmasRealizadas == (hiddNroFirmas - 1)) { //derivar en la primera firma
            $("#form_destinatario_principal").val(hiddBuzonUltima);
            $("#form_destinatario_principal").trigger('change');
            if (bloquear == 1) {
                $("#form_destinatario_principal").prop("disabled", true);
                $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                $('#form_acciones_solicitadas_el').multiselect('select', '11');
                $('#form_acciones_solicitadas_el').multiselect('rebuild');
            }
            $('.btn-firmar-derivar').remove()
            $(".btn-firmar").html("Firmar y Enviar");
        }
    }

    function deleteFile(codFile) {
        //obtener datos y eliminar el seleccionado

        var listDelete = [];
        var valDelete = $('#hiddIdFileDelete').val();

        if (valDelete.length != 0)
            listDelete = valDelete.split(",");

        listDelete.push(codFile);

        $('#hiddIdFileDelete').val(listDelete.join(","));

        $('.' + codFile + '').hide();
    }

    function ver_despachados(id_documento, id_documento_buzon, id_documento_buzon_padre) {
        $('#titulo_accion').html('Ver Documento ID '+id_documento);
        $('.row_cuerpo').show();
        deshabilita_campos();
        cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre, 3, 0);

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');


    }

    function editar_despachados(id_documento, id_documento_buzon, id_documento_buzon_padre, ag = 0) {
        $('#titulo_accion').html('Editar Documento ID '+id_documento);
        $('.row_cuerpo').show();
        habilita_campos();
        isDelete = true;
        cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre, 3, 1);

        $('#form_tipo_documento').prop("disabled", true);

        $('.btn-guardar-submit').show();
        habilita_boton('btn-guardar-submit');
        $('.btn-guardar-submit-edit').show();
        habilita_boton('btn-guardar-submit-edit');
        $('.btn-enviar-submit').show();
        habilita_boton('btn-enviar-submit');
        habilita_boton('btn_cerrar_guardar');
        $('#addButton').html('');
        habilita_boton('btn-vp');
        if (ag > 0) {
            setTimeout(function() {
                auto_guardado();
                //console.log('despues editar_despachados');
            }, 2000);
        }
    }

    function accion_editar(id_documento, id_documento_buzon, id_documento_buzon_padre) {
        $('#titulo_accion').html('Editar Documento ID '+id_documento);

        habilita_campos();
        isDelete = true;
        cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre, 2, 1);

        $('#form_tipo_documento').prop("disabled", true);
        $('#form_respuesta_a').multiselect('disable');

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        habilita_boton('btn-vp');

        var buttonGuardar = '<button onClick="accion_editar_guardar(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Guardar</button> ';
        $('#addButton').html('');
        $('#addButton').append(buttonGuardar);
    }

    function accion_clonar(id_documento, id_documento_buzon, id_documento_buzon_padre, materia) {
        var _token = $("input[name='_token']").val();

        Swal.fire({
            title: 'Copiar documento',
            html: "Se realizará Copia editable del Documento: <br><strong>" + materia + "</strong>",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.value == true) {
                $.ajax({
                    url: route('documentos.clonar',{'buzon':id_documento_buzon,'id':id_documento}),
                    //url: "/clonar",
                    type: 'PUT',
                    dataType: 'json',
                    //dataType: 'binary',
                    data: {
                        _token: _token,
                        idDocumento: id_documento,
                        idDocumentoBuzon: id_documento_buzon,
                        idDocumentoBuzonPadre: id_documento_buzon_padre
                    },
                    success: function(data) {
                        if (data.status == '200') {
                            toastr.success("Documento copiado exitosamente.", "¡Aviso!");
                            setTimeout(function() {
                                cambio_texto_boton_carpetas('Despachados');
                                editar_despachados(data.data.rel_documento_buzon[0].id_documento, data.data.rel_documento_buzon[0].id_documento_buzon, data.data.rel_documento_buzon[0].id_documento_buzon_padre);
                            }, 1000);
                            //location.reload();                              
                        } else {
                            toastr.error(data.data.comentario, "¡Aviso!");
                        }
                    },
                    error: function(data, jqXHR, textStatus, errorThrown) {
                        toastr.error("Falla en la copia del documento", "¡Aviso!");
                    }
                });
            }
        })
    }

    function eliminar_despachados(id_documento, id_documento_buzon) {
        var _token = $("input[name='_token']").val();

        Swal.fire({
            title: 'Eliminar documento',
            html: "Se realizará la eliminación del documento <br>",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.value == true) {
                $.ajax({
                    url: route('documentos.delete',{'buzon':id_documento_buzon,'id':id_documento}),
                    //url: "/documento/",
                    type: 'delete',
                    dataType: 'json',
                    data: {
                        _token: _token,
                        idDocumento: id_documento,
                        idDocumentoBuzon: id_documento_buzon
                    },
                    success: function(data) {
                        if (data.status == '200') {
                            toastr.success(data.data, "¡Aviso!");
                            fn_grilla_despachados();
                            $('#card_crear_documento').hide();
                            $("#collapseOne").collapse('show');
                        } else {
                            toastr.error(data.data.comentario, "¡Aviso!");
                        }
                    },
                    error: function(data, jqXHR, textStatus, errorThrown) {
                        toastr.error("Falla en la eliminación del documento", "¡Aviso!");
                    }
                });
            }
        })


    }

    function eliminar_enviado(id_documento, id_documento_buzon) {
        var _token = $("input[name='_token']").val();

        Swal.fire({
            title: 'Eliminar Envío',
            html: "Se eliminarán los envíos del documento<br>",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.value == true) {
                $.ajax({
                    url: route('documentos.eliminar_documento_enviado',{'buzon':id_documento_buzon,'id':id_documento}),
                    //url: "/eliminar_documento",
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        _token: _token,
                        idDocumento: id_documento,
                        idDocumentoBuzon: id_documento_buzon
                    },
                    success: function(data) {
                        if (data.status == '200') {
                            toastr.success(data.data, "¡Aviso!");
                            fn_grilla_despachados();
                            $('#card_crear_documento').hide();
                            $("#collapseOne").collapse('show');
                        } else {
                            toastr.error(data.data.comentario, "¡Aviso!");
                        }
                    },
                    error: function(data, jqXHR, textStatus, errorThrown) {
                        toastr.error("Falla en la eliminación del documento", "¡Aviso!");
                    }
                });
            }
        })


    }

    function auto_guardado() {
        timeoutId = setTimeout(function() {
            if ($('#hiddIdDocumento').val() != "") {
                accion_auto_guardar(3);
                //console.log('ppal');
            }
        }, 180000);
    }

    function activar_editar(nBotones) {
        $('#form_tipo_documento').prop("disabled", true);
        $("#form_crear_editar :input").prop("disabled", false);
        editor_cuerpo.setReadOnly(false);
        editor_distribucion.setReadOnly(false);
        // tinymce.get("form_cuerpo").mode.set("design");
        // tinymce.get("form_distribucion").mode.set("design");
        $('#dropzone-principal').prop("disabled", false);
        $('#dropzone-anexo').prop("disabled", false);
        $('#dropzone-otros').prop("disabled", false);
        $(".dz-hidden-input").prop("disabled", false);

        if (nBotones == 1) {
            $('.btn-guardar-submit').show();
        }
        if (nBotones == 2) {
            $('.btn-guardar-submit-edit').show();
        }
        if (nBotones == 3) {
            $('.btn-guardar-submit').show();
            $('.btn-guardar-submit-edit').show();
        }

        $('.btn-editar').hide();
        $('#form_tipo_documento').prop("disabled", true);
    }

    function vernotas(tipo) {
        objDoc.rel_documento_buzon.sort(function(a, b) {
            var nameA = a.fecha.toLowerCase(),
                nameB = b.fecha.toLowerCase()
            if (nameA < nameB)
                return -1
            if (nameA > nameB)
                return 1
            return 0
        });
        let body = "<div>";
        $.each(objDoc.rel_documento_buzon, function(i, o) {
            comentario = (tipo == 1) ? o.comentario_principal : o.comentario_secundario;
            if (comentario != null) {
                body = body + "<div class='card card-body'><p>Para <strong>" + listadoBuzones[o.id_buzon] + "</strong> (" + o.fecha + ")<p/><p>Mensaje : " + o.comentario_principal + "</p></div>";
            }
        });
        body += "</div>";
        Swal.fire({
            title: "Mensajes Anteriores",
            html: body,
            width: "90%"

        });
    }

    function eliminar_sesiones() {
        //sessionStorage.clear();
        sessionStorage.removeItem('username');
        sessionStorage.removeItem('id_despachados');
        sessionStorage.removeItem('td_despachados');
        sessionStorage.removeItem('estados_despachados');
        sessionStorage.removeItem('materia_despachados');
        sessionStorage.removeItem('id_recibidos');
        sessionStorage.removeItem('td_recibidos');
        sessionStorage.removeItem('estados_recibidos');
        sessionStorage.removeItem('materia_recibidos');
        sessionStorage.removeItem('carpeta_seleccionada');
    }

    function cargar_filtros_guardados() {
        //inicio sesiones para filtros grilla despachados
        const id_despachados = sessionStorage.getItem('id_despachados');
        if (id_despachados != "") {
            $("#gd_buscar_id_doc").val(id_despachados);
        }

        const td_despachados = sessionStorage.getItem('td_despachados');
        if (td_despachados != "" && td_despachados != null) {
            $("#gd_buscar_tipo_doc").multiselect('deselectAll', true);
            const arreglo_td_despachados = td_despachados.split("|");
            $.each(arreglo_td_despachados, function(index, value) {
                $("#gd_buscar_tipo_doc").multiselect('select', value);
            });
        }

        //CARGAR ESTADOS, SINO, TODOS
        const estados_despachados = sessionStorage.getItem('estados_despachados');
        if (estados_despachados != "" && estados_despachados != null) {
            $("#gd_buscar_estado").multiselect('deselectAll', true);
            const arreglo_estados_despachados = estados_despachados.split("|");
            $.each(arreglo_estados_despachados, function(index, value) {
                $("#gd_buscar_estado").multiselect('select', value);
            });
        }else{
            $("#gd_buscar_estado").multiselect('selectAll', true);
        }

        const materia_despachados = sessionStorage.getItem('materia_despachados');
        if (materia_despachados != "") {
            $("#gd_buscar_destino_materia").val(materia_despachados);
        }
        //recarga_grilla_despachados();
        //fin sesiones para filtros grilla despachados

        //inicio sesiones para filtros grilla recibidos
        const id_recibidos = sessionStorage.getItem('id_recibidos');
        if (id_recibidos != "") {
            $("#gr_buscar_id_doc").val(id_recibidos);
        }

        const td_recibidos = sessionStorage.getItem('td_recibidos');
        if (td_recibidos != "" && td_recibidos != null) {
            $("#gr_buscar_tipo_doc").multiselect('deselectAll', true);
            const arreglo_td_recibidos = td_recibidos.split("|");
            $.each(arreglo_td_recibidos, function(index, value) {
                $("#gr_buscar_tipo_doc").multiselect('select', value);
            });
        }

        //CARGAR ESTADOS, SINO, POR DEFECTO LOS PENDIENTES
        const estados_recibidos = sessionStorage.getItem('estados_recibidos');
        if (estados_recibidos != "" && estados_recibidos != null) {
            console.log("Estados recibidos localstorage",estados_recibidos);
            $("#gr_buscar_estado").multiselect('deselectAll', true);
            const arreglo_estados_recibidos = estados_recibidos.split("|");
            $.each(arreglo_estados_recibidos, function(index, value) {
                $("#gr_buscar_estado").multiselect('select', value);
            });
        }else{
            console.log("Estados recibidos por defecto");
            $("#gr_buscar_estado").multiselect('select', [4]);
        }

        const materia_recibidos = sessionStorage.getItem('materia_recibidos');
        if (materia_recibidos != "") {
            $("#gr_buscar_origen_materia").val(materia_recibidos);
        }
        /* COMENTADO PORQUE CAUSABA LENTITUD*/
        //recarga_grilla_recibidos();
        //fin sesiones para filtro gsrilla recibidos
        setTimeout(function() {
            if (sessionStorage.getItem('carpeta_seleccionada')) {
                const tCarpeta = sessionStorage.getItem('carpeta_seleccionada')
                cambio_texto_boton_carpetas(tCarpeta);
                //eliminar sesiones
                eliminar_sesiones();
            }
        }, 1000);
    }
    
    //refrencia Documento SGD 
    function buscarDocumentoReferenciaSGD(){   
/*
        $.ajax({
            url: "{{ route('buscador.referenciasgd') }}",
            type: 'GET',
            dataType: 'json',
            //async: false,
            data: {
                q: query
            },
            success: function(data) {
                console.log(data)

                $("#referencia-resultados").html(data);
            }
        });

        
        var mockFile = { 
                name: 'PRUEBA-3.pdf', 
                size: 12345, 
                type: 'document/pdf', 
                status: Dropzone.ADDED, 
                url: 'PRUEBA-3-20250122-42612453.pdf',
                dataUrl:'/img/pdf.png',
                accepted: true,
                tipo:'referencia'
            };
            dropzoneAnexo.emit('addedfile', mockFile);
            dropzoneAnexo.emit("thumbnail", mockFile, '/img/pdf.png');
            dropzoneAnexo.emit('complete', mockFile);
            dropzoneAnexo.files.push(mockFile);
            referenciaAnexos.push(mockFile);
        */
    }

    $(document).ready(function() {

        //$(".nuevo_documento").prop("disabled", true);
        $('#fDerivarMasivaDestPpal').select2();
        @if($log_firma != "")
            toastr.error("{{ str_replace(PHP_EOL, null,$log_firma) }}", "¡Aviso!");
        @endif

        $('#form_acciones_solicitadas_el').multiselect({
            nonSelectedText: 'Seleccione Acciones',
            numberDisplayed: 6,
            buttonWidth: '100%'
        });

        $('#form_respuesta_a').multiselect({
            nonSelectedText: 'Seleccione Documentos',
            allSelectedText: 'Seleccionados',
            numberDisplayed: 4,
            buttonWidth: '100%'
        });

        timeoutId = "";
        
        //cargar los filtros previamente seleccionados
        cargar_filtros_guardados();

        //
       
        $('#form_destinatario_principal').select2({
            data: allBuzones2,
            maximumSelectionLength: 1,
            placeholder: '',
            tags: false,
            language: {
                maximumSelected: function(args) {
                    var message = 'Sólo puede seleccionar ' + args.maximum + ' elemento';
                    if (args.maximum != 1) {
                        message += 's';
                    }
                    return message;
                },
                noResults: function() {
                    return 'No se encontraron resultados';
                }
            }
        }).on('select2:unselect', function(e) {
            var data = e.params.data;
            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
        }).on('select2:select', function(e) {
            if (form_acciones_solicitadas_el.disabled == true)
                $('#form_acciones_solicitadas_el').multiselect('select', 6);
            else
                $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
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



        $("#referencia-resultados").DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            type: 'json',
            searchDelay: 1200,
            responsive: true,
            ajax:{
                url:"{{ route('buscador.referenciasgd') }}",
            },
            language: lenguaje_datatable,
            //headers:[],
            columns: [
                {data: 'anio',title:'Año'},
                {data: 'nombre_corto'},
                {data: 'folio'},
                {data: 'id_tipo_archivo'},
                {data: 'nombre_archivo_original'},
            ],
        });

    });

</script>
@include('ckfinder::setup')
@stop