@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')

    
    <div class="row">
        <div class="col-8">
            <h1>Búsqueda de Documentos</h1>
        </div>
    </div>
    
    <div class="linea_content_header"></div>
    <br>
    <div class="card">
        <div class="card-body">
            <div class="d-sm-flex align-items-center">   
                <div class="flex-fill  mr-sm-2">  
                <input class="form-control"  type="text" id="busqueda_simple" name="busqueda_simple" placeholder="Buscar por materia, folio o ID" onkeypress="filtrar_enter()">
                </div>
                <div class="">  
                    <i id="botones_busqueda_simple"></i>
                    <button id="btnBuscarSimple" class="Whoops btn btn-block btn-success d-sm-inline-flex mt-sm-0"><span class="spinner-border spinner-border-sm d-none"></span>&nbsp; Buscar</button>
                </div>
                <div class="">  
                    <a href="#" class="btn btn-link desplegar_opciones_avanzadas">
                    <i class="fa fa-angle-double-down "></i> Búsqueda avanzada</a>
                    <a href="#" style="display:none" class="btn btn-link cerrar_opciones_avanzadas">
                    <i class="fa fa-angle-double-up "></i> Búsqueda simple</a>
                    
                </div>
            </div> 
        </div>
    </div>
    <div class="card" id="card_opciones_avanzadas" style="display:none">
        <div class="card-body row">
                <div class="col-md-2 md-2">
                    <div class="form-group">
                    <label for="id_folio">Folio: </label>
                        <input type="text" class="form-control" id="buscar_folio" name="buscar_folio">
                    </div>
                </div>
                <div class="col-md-2 md-2">
                    <div class="form-group">
                    <label for="id_documento">ID Documento: </label>
                        <input type="text" class="form-control" id="buscar_id_documento" name="buscar_id_documento">
                    </div>
                </div>
                <div class="col-md-4 md-5">
                    <div class="form-group">
                        <label for="select_tipo_documento" >Tipo Documento</label>
                        <br>
                        <select style="width:100%" class="form-control" id="buscar_tipo_documento" name="buscar_tipo_documento" >
                            <option value="">Seleccionar</option>
                                @foreach($listado_tiposdoc as $list)
                                <option value="{{$list['nombre']}}">{{$list['nombre']}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2 md-3">
                    <div class="form-group">
                        <label for="select_anio">Año</label>
                        <br/>
                        <select  style="width:100%" class="form-control" id="buscar_anio" name="buscar_anio" >
                            <option value="">Seleccionar</option>
                            @foreach($listadoAnios as $anios)
                            <option value="{{$anios['descripcion']}}">{{$anios['descripcion']}}</option>
                            @endforeach
                        </select>
                    </div>                    
                </div>
                
                <div class="col-md-2 md-3">
                    <div class="form-group">
                        <label for="select_anio">Estado Tramitación</label>
                        <br/>
                        <select  style="width:100%" class="form-control" id="estado_tramitacion" name="estado_tramitacion" >
                            <option value="">Seleccionar</option>
                            @foreach($listadoEstadoTramitacion as $anios)
                            <option value="{{$anios['id']}}">{{$anios['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>                    
                </div>

                <div class="col-md-4 md-3">
                    <div class="form-group">
                        <label for="select_buzon_origen">Buzón Orígen</label>
                        <br/>
                        <select  style="width:100%" class="form-control" id="buscar_buzon_origen" name="buscar_buzon_origen" >
                            <option value="">Seleccionar</option>
                            @foreach($listBuzones as $list)
                            <option value="{{$list['nombre']}}">{{$list['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>                    
                </div>
                <div class="col-md-4 md-3">
                    <div class="form-group">
                        <label for="select_buzon_actual">Buzón Actual</label>
                        <br/>
                        <select  style="width:100%" class="form-control" id="buscar_buzon_actual" name="buscar_buzon_actual" >
                            <option value="">Seleccionar</option>
                            @foreach($listBuzones as $list)
                            <option value="{{$list['nombre']}}">{{$list['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>                    
                </div>
                <div class="col-md-4 md-3 row">
                    <label class="col-12" for="">Rango de Fechas Creación</label>  
                    <div class="col-12 col-md-6" id="fic">   
                        <div class="form-group">
                                            
                            <input type="date" id="buscar_fecha_ini" name="buscar_fecha_ini" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-6"  id="ftc">
                        <div class="form-group">                 
                            <input type="date" id="buscar_fecha_fin" name="buscar_fecha_fin" class="form-control">
                        </div>
                    </div>
                </div>  

            <!-- <div class="linea_content_header mb-3"></div> -->

                <div class="col-md-4 md-3">
                    <div class="form-group">
                        <label for="select_buzon_actual">Derivado por</label>
                        <br/>
                        <select  style="width:100%" class="form-control" id="buscar_derivado" name="buscar_derivado" onchange="activa_fechas(this.value)" >
                            <option value="">Seleccionar</option>
                            @foreach($listBuzones as $list)
                            <option value="{{$list['nombre']}}">{{$list['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>                    
                </div>

                <div class="col-md-2 md-2" id="fid" style="display:none;">
                    <div class="form-group">
                        <label class="" for="">Rango de Fechas Derivación</label>                  
                        <input type="date" id="buscar_fecha_ini_d" name="buscar_fecha_ini_d" class="form-control">
                    </div>
                </div>
                
                <div class="col-md-2 md-2" id="ftd" style="display:none;">
                    <div class="form-group">
                        <label for="">&nbsp;</label>                  
                        <input type="date" id="buscar_fecha_fin_d" name="buscar_fecha_fin_d" class="form-control">
                    </div>
                </div>  
                <div class="col-md-3 md-5 d-flex">
                    <div class="custom-control custom-checkbox align-content-center">
                    <input type="checkbox" class="custom-control-input" name="buscar_efectos_sobre_terceros" id="buscar_efectos_sobre_terceros">
                    <label class="custom-control-label" for="buscar_efectos_sobre_terceros">Efectos Sobre Terceros</label>
                    </div>
                </div>
                <div class="col-md-3 md-5 d-flex">
                    <div class="custom-control custom-checkbox align-content-center">
                    <input type="checkbox" class="custom-control-input" name="buscar_respondidos" id="buscar_respondidos">
                    <label class="custom-control-label" for="buscar_respondidos">Respondido</label>
                    </div>
                </div>
               
                <div class="col-12 d-flex justify-content-end">
                    <div class="form-group">
                        <!-- <i id="botones_grilla_despachados"></i> 
                        <br/>-->
                        <button class="btn btn-secondary" id="btnLimpiar">Limpiar</button>&nbsp;&nbsp;<button id="btnBuscar" class="btn btn-success"><span class="spinner-border spinner-border-sm d-none"></span>&nbsp; Buscar</button>
                    </div>
                </div>
 
        </div>
    </div>
   
@stop


@section('content')
<!--<div class="container">-->

            <div class="accordion" id="carpetas">
                <div class="card">
                    <div class="card-header" >
                        <h5 class="mb-0">
                        <!--<div class="col-md-3 md-12">  
                            <i type="button" class="fa fa-chevron-circle-down desplegar_grilla_documento"></i> </a>
                            <i type="button" class="fa fa-chevron-circle-up  cerrar_grilla_documento" style="display:none"></i> </a>
                        </div>-->
                        <button class="btn btn-block text-left buzones_carpetas_btn_colapsable_text" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <span id="boton_carpetas_texto"> Resultado de búsqueda  <i><b> </b></i> </span>
                            <i class="fa fa-chevron-circle-up" style="float:right;margin-top: 8px;"></i>
                            <i class="fa fa-chevron-circle-down" style="float:right;margin-top: 8px;"></i>
                        </button>
                        </h5>
                       
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#carpetas">
                        
                            <div class="" id="card_buscador_grilla">
                                <div class="card-body">
                                    <table id="grilla_recibidos"  class="table-sm text-sm table dt-responsive no-footer dtr-inline dataTable collapsed" style="width:100%">
                                    </table>
                                
                                </div>
                            </div>
                        
                    </div>
                </div>
            </div>

        <!-- Form Documentos -->

        <div class="row" id="card_documento" style="display:none">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" >
                        <h4 id="titulo_accion">Ver Documento</h4>
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
                                    <select id="form_tipo_documento" name="tipo_documento" class="form-control form-disabled">                                                                       
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="inputState">Nivel Acceso</label>
                                    <select class="form-control form-disabled" id="form_nivel_acceso" name="nivel_acceso">
                                        @foreach($nivel_acceso as $dato)
                                        <option value="{{$dato['id_nivel_acceso']}}">{{$dato['nombre']}}</option>
                                        @endforeach
                                    </select>
    
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="inputState">Efectos sobre terceros</label>
                                    <select id="form_efectos_terceros" name="efectos_terceros" class="form-control form-disabled">
                                        <option selected>Seleccionar</option>
                                        <option value="true">Si</option>
                                        <option value="false">No</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="inputState">Contestar/Hasta</label>
                                    <input type="date" class="form-control form-disabled" id="form_contestar_hasta" name="contestar_hasta">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label for="inputState">Respuesta a:</label>
                                    <div>    
                                        <a href="#" class="btn bg-lightblue" onclick="ver_documentos_respuesta_a()"><i class="fa fa-search"></i> <span class="d-none d-sm-inline">Ver</span> <span id="contador_respuesta_a" class="badge badge-light"></span></a>&nbsp;
                                    </div>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="inputState">Materia:</label>
                                    <input type="text" class="form-control form-disabled" id="form_materia" name="materia">
                                </div>
                            </div>
    
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="inputState">Anterior:</label>
                                    <input type="text" class="form-control form-disabled" id="form_anterior" name="anterior">
                                </div>
                            </div>
    
                            <div class="form-row">
                                <div class="col-md-12">
                                    <label for="floatingTextarea">Descripción o Extracto</label>
                                    <textarea class="form-control form-disabled" id="form_descripcion" name="descripcion"></textarea>
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
                                
                            <div class="form-group row_arch_ppal">
                                <label for="exampleFormControlTextarea1">Archivo Principal</label>
                                
                                <div class="card-body card-archivos" id="cargar_principal">
                                    <div id="dropzone-principal-view" class="dropzone-view"></div> 
                                    <div id="dropzone-principal" class="dropzone-files dropzone-none"></div>       
                                
                                    <div id="card_desplegar_versiones" class="bl1 header1" > 
                                        <label class="">Versiones</label>                       
                                        <button type="button" class="btn boton_desplegar_versiones_anteriores" style="padding: 49px 15px;">
                                            <i class="fas fa-angle-double-right fa-3x"></i>
                                        </button>
                                    </div> 
                                    <div class="bl2"  id="card_ocultar_versiones" style="display:none" >
                                        <div class="header1">
                                            <label class="">Versiones</label>
                                            <button type="button" class="btn boton_ocultar_versiones_anteriores" style="padding: 48px 15px;">
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
                                <label for="exampleFormControlTextarea1">Anexos:</label>
                                
                                <div class="card-body card-archivos" id="cargar_anexo">
                                    <div id="dropzone-anexo-view" class="dropzone-view"></div>
                                    <div id="dropzone-anexo" class="dropzone-files dropzone-none"></div>                                                          
                                                                                          
                                </div>
    
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Documentos de Referencia</label>
                                <div class="card-body card-archivos" id="cargar_referencias">
                                    <div id="contenedor_documentos_referencia" class="mb-2"></div>
                                </div>
                            </div>
    
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Otros Archivos</label>
                                
                                <div class="card-body card-archivos" id="cargar_otros">
                                    <div id="dropzone-otros-view" class="dropzone-view"></div>
                                    <div id="dropzone-otros" class="dropzone-files dropzone-none"></div>   
                                                                                              
                                </div>
    
                            </div>
            <div class="d-none">
                            <div class="form-row">
                                <div class="col-md-8 mb-3">
                                    <label for="inputState">Destinatario Principal:</label>
                                    <input type="text" class="form-control form-disabled" id="form_destinatario_principal_el" data-role="tagsinput">
                                </div>
    
                                <div class="col-md-4 mb-3">
                                    <label for="inputState">Acciones Solicitadas:</label><br>
                                    <select id="form_acciones_solicitadas_el" class="form-control form-disabled" multiple="multiple">                                    
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
                                    <textarea class="form-control form-disabled"  id="form_comentario_el"></textarea>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12 mb-3">
                                    <label for="inputState">Otro(s) Destinatario(s):</label>
                                    <input type="text" class="form-control form-disabled" id="form_otros_destinatarios_el" data-role="tagsinput">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-12">
                                    <label for="floatingTextarea">Comentario(s) Otro(s) Destinatario(s)</label>
                                    <textarea class="form-control form-disabled" id="form_comentario_otro_el"></textarea>
                                </div>
                            </div>
                               </div>                     
                            <div class="form-row">                                
                                    <div class="col-md-12 group-button-align">
                                        <input type="hidden" name="hiddIdDocumento" id="hiddIdDocumento" value="">
                                        <input type="hidden" name="hiddIdDocumentoBuzon" id="hiddIdDocumentoBuzon" value="">
                                        <input type="hidden" name="hiddIdBuzon" id="hiddIdBuzon" value="">
                                        <input type="hidden" name="hiddIdOrigen" id="hiddIdOrigen" value="">
    
                                    </div>                          
                            </div>
                        </form>
                    </div>
    
                </div>
    
            </div>
        </div>    

    <!-- Form Documentos -->

    <!-- Bitacora-->  
        
    <div class="card" id="card_bitacora"  style="display:none">
        <div class="card-header" >
            <h4 id="titulo_accion">Bitácora</h4>
            <div class="linea_content_header"></div>
        </div>
    <div class="card-body">
        <div class="form-check" style="padding-right: 5px;">
            <input class="form-check-input" type="checkbox" value="DDP" name="buscar_accion" id="accion_ddp">
            <label class="form-check-label" for="defaultCheck1" >
                Derivaciones destinatarios principales (DDP)
            </label>
        </div>
            <div class="form-check" >
            <input class="form-check-input" type="checkbox" value="DOD" name="buscar_accion" id="accion_dod">
            <label class="form-check-label" for="defaultCheck1">
                Derivaciones otros destinatarios (DOD)
            </label>
            </div>
            <div class="form-check" >
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
                            <th>Buzón</th>
                            <th>Usuario </th>
                            <th>Acción </th>
                            <th>Mensaje</th>
                        </tr>

                    </thead>
                </table>
            </div>         
        </div>
</div>
   
    <!-- Bitacora fin-->
    <div class="row ">
    <div class="col-md-10"> </div>
    <div class="col-md-2">
        <p><button style="display:none" type="button" class="btn btn-secondary w-100 btn_cerrar_guardar">Cerrar</button></p>
    </div>
</div>  

@include('componentes.BitacoraModal')
@stop

@section('css')

<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
<link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">


    <style type="text/css">
        .disabled {
            background-color: #e9ecef;
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

        .odd:hover, .even:hover{
            background: whitesmoke;
        }

        .buttons-excel {
            margin-bottom: 10px;
            float:right;
        }

        .buscar_fila {
            padding-top:30px;
            padding-left:50px !important; 
        }

        .item a, a:hover{
            text-decoration: underline !important;
        }
        
    </style>
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')


<script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script src="/js/fglobales.js?ver=1"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<!-- jquery y bootstrap -->
 
 
 <!-- datatables con bootstrap -->


 <!-- Para usar los botones -->
 
 


<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>


<script>

    var grilla_recibidos;
    const editor_cuerpo = CKEDITOR.replace('form_cuerpo');
    const listadoBuzones = @json($listadoBuzones);
    const estadosTramitacion = @json($listadoEstadoTramitacion);
   
    $(document).ready(function () {
        $('#buscar_buzon_origen').select2({ width: 'resolve'});
        $('#buscar_buzon_actual').select2({ width: 'resolve'});
        $('#buscar_tipo_documento').select2({ width: 'resolve'});
        $('#buscar_derivado').select2({ width: 'resolve'});
        $('#buscar_anio').select2({ width: 'resolve'});
        $('#grilla_recibidos').DataTable({
            dom: 'Blrtip', 
                buttons: {
                    dom:{
                        button:{
                            className: 'btn'
                        }
                    },
                    buttons:[
                        {
                            extend:"excel",
                            filename:'SGD-Resultados_de_busqueda',
                            exportOptions: { 
                                // columns: function(column, data, node) {
                                    
                                //     if (column > 10) {
                                //         return false;
                                //     }
                                //     return true;
                                // }
                                //columns: [0,1,2,3,4,5,6,7,8,9,10,11 ]
                             },
                            text:'Descargar busqueda',
                            className: 'btn btn-success',
                            excelStyles:{
                                temlate:'header_blue'
                            }
                        }
                    ]
                },
            processing: true,
            serverSide: true,
            deferLoading: 0,
            lengthMenu: [ [10, 25, 50, 100, -1 ], [10, 25, 50, 100, "Todos"]],
            ajax: route('buscador.listar'), //'/buscadorListar?folio=0',
            order:[[0,'DESC'], [4,'asc'] ],
            language: lenguaje_datatable,
            columns: [
                    { data: 'identificador', name: 'identificador', title:'ID',
                        render: function(data, type, row) {
                            //codigo para evitar mostrar en reservado y confidencial
                            switch(row.id_nivel_acceso){
                                case 1:
                                    return "<a href='javascript:visualizar_documento(" + row.id_documento + "," + row.id_documento_buzon + "," + row.id_documento_buzon_padre + ")'>" + data + "</a>";
                                    break;
                                case 2:
                                    return data;
                                    break;
                                case 3:
                                    var salida = row.list_usuarios.split(',');
                                    var acciones = false;
                                    for(i=0; i<salida.length; i++){
                                        if(salida[i]==row.id)
                                        {
                                            acciones = true;
                                            if (acciones==true){
                                                i=salida.length;
                                            }
                                        }
                                    }
                                    if (acciones==true){
                                        return "<a href='javascript:visualizar_documento(" + row.id_documento + "," + row.id_documento_buzon + "," + row.id_documento_buzon_padre + ")'>" + data + "</a>";
                                    }else{
                                        return data;
                                    }
                                    break;
                            }
                        }
                    },
                    { data: 'tipo_documento', name: 'tipo_documento' , title:'Tipo de Documento'},
                    { data: 'materia', name: 'documento.materia',title:'Materia',width: '30%'},
                    { data: 'folio', name: 'folio', title:'Folio' },
                    { data: 'fecha_documento',data: 'fecha_documento',title:'Fecha', render: function(data, type, row)
                            {
                                if(data == null)
                                    return '';
                                else
                                { 
                                    return moment(row.fecha_documento).format('DD-MM-YYYY');
                                }

                                return '';
                            }
                 
                    },
                    { data: 'buzon_origen', name: 'buzon_origen',title:'Buzón origen'  },
                    { data: 'buzon_actual', name: 'buzon_actual',title:'Buzón Actual' },
                    { data: 'n_estado_tramitacion', name: 'n_estado_tramitacion',title:'E. Tramitación' },
                    { data: 'destinatario', name: 'destinatario',title:'Derivado a' },
                    { data: 'id_respuesta', name:'id_respuesta',title:'ID Respuesta'},
                    { data: 'fecha_respuesta',name:'fecha_respuesta',title:'Fecha DOC Respuesta'},
                    { data: 'efectos_terceros', searchable: true, visible: false,title:'Efectos Terceros'},
                    { data: 'id_documento', name: 'descarga',title:'Descarga',
                        render:function(data, type, row){
                            //console.log(row);
                            if(row.id_nivel_acceso == 1 && parseInt(row.folio) > 0){
                                return "<a href='descargar_docto?idDocumento="+data+"' target='_blank'>Descargar</a>";
                            }
                            else{
                                return '';
                            }
                        }
                    },
                    { data: 'id_documento',title:'Opciones',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            
                            if(data==null){
                                return '';
                            }else{
                                if(row.id_nivel_acceso == 1){
                                    let botonera = '<div class="dropdown">';
                                    let botonera_confidencial = '<div class="dropdown">';
                                    
                                        botonera_confidencial += '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                            botonera_confidencial +=' <i class="fas fa-bars"></i>';
                                            botonera_confidencial +=' </button>';
                                            

                                        botonera += '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                            botonera +=' <i class="fas fa-bars"></i>';
                                            botonera +=' </button>';
                                            botonera +='<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                                            
                                            if(row.id_nivel_acceso == 1 )
                                                {
                                                botonera +=' <a class="dropdown-item btn-menu-ver" onclick="visualizar_documento('+row.id_documento+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                                botonera +=' <a class="dropdown-item btn-menu-ver" onclick="descargar_documento('+row.id_documento+','+row.id_documento_buzon+')" ><i class="fas fa-download text-blue"></i> Descargar</a>';

                                                }   
                                            if(row.id_nivel_acceso == 2 )
                                            {
                                                botonera +=' <a class="dropdown-item btn-menu-ver" onclick="visualizar_documento('+row.id_documento+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                                botonera +=' <a class="dropdown-item btn-menu-ver" onclick="descargar_documento('+row.id_documento+','+row.id_documento_buzon+')"><i class="fas fa-download text-blue"></i> Descargar</a>';
                                            }
                                            if(row.id_nivel_acceso == 3)
                                            {   
                                                var salida = row.list_usuarios.split(',');
                                                var acciones = false;

                                                for(i=0; i<salida.length; i++){

                                                    if(salida[i]==row.id)
                                                    {
                                                        
                                                        acciones = true;
                                                        if (acciones==true){
                                                            i=salida.length;
                                                        }
                                                    }
                                            
                                                }
                                                if (acciones==true){
                                                    botonera +=' <a class="dropdown-item btn-menu-ver" onclick="visualizar_documento('+row.id_documento+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                                    botonera +=' <a class="dropdown-item btn-menu-ver" onclick="descargar_documento('+row.id_documento+','+row.id_documento_buzon+')"  ><i class="fas fa-download text-blue"></i> Descargar</a>';
                                                    
                                                }
                                                //console.log(acciones)
                                                if(acciones==false){
                                                    botonera_confidencial += '</div>';
                                                    botonera_confidencial += '</div>';
                                                    return botonera_confidencial;
                                                }
                                            }                                     
                                        botonera += '</div>';
                                        botonera += '</div>';
                                    return botonera;
                                }
                                else{
                                    return '';
                                }
                            }
                        }
                        return '';
                    }
                    },
                  
                    
                    
                ]
            //,visible:true, searchable: true
        });

         $('#grilla_recibidos').on('processing.dt', function (e, settings, processing) {            
            if(processing){
                $("#grilla_recibidos tbody").addClass('text-hide');
                $("#btnBuscar span").removeClass('d-none');
                $("#btnBuscar").attr('disabled','disabled');
                $("#btnBuscarSimple span").removeClass('d-none');
                $("#btnBuscarSimple").attr('disabled','disabled');
                console.log("procesando grilla");
            }else{
                $("#grilla_recibidos tbody").removeClass('text-hide');
                $("#btnBuscar span").addClass('d-none');
                $("#btnBuscar").removeAttr('disabled');
                $("#btnBuscarSimple span").addClass('d-none');
                $("#btnBuscarSimple").removeAttr('disabled');
                console.log("end procesando grilla");
            }
        })
    });
      
    function activa_fechas(valor){
        if(valor !=""){
            $('#fid').show();
            $('#ftd').show();
            $('#buscar_fecha_ini').attr("disabled","disabled");
            $('#buscar_fecha_fin').attr("disabled","disabled");
            $('#buscar_fecha_ini').val('');
            $('#buscar_fecha_fin').val('');
        }
        else{
            $('#fid').hide();
            $('#ftd').hide();
            $('#buscar_fecha_ini').removeAttr("disabled");;
            $('#buscar_fecha_fin').removeAttr("disabled");;

        }
    }
    
    $('#btnBuscarSimple').click(function() {
        $('#card_documento').hide();
        $('#card_bitacora').hide();	
        $('.btn_cerrar_guardar').hide();
        $("#collapseOne").collapse('show');
        let busqueda_simple = $('#busqueda_simple').val();
        $('#grilla_recibidos').DataTable().ajax.url('/buscadorListar?busqueda_simple='+busqueda_simple).load();
    });

    $('#btnBuscar').click(function() {
        $('#card_documento').hide();
        $('#card_bitacora').hide();	
        $('.btn_cerrar_guardar').hide();
        $("#collapseOne").collapse('show');
        let id_documento = $('#buscar_id_documento').val();
        let tipo_documento = $('#buscar_tipo_documento').val();
        let folio = $('#buscar_folio').val();
        let finicio1 = $('#buscar_fecha_ini').val();
        let finicio = finicio1;
        let ffin1 = $('#buscar_fecha_fin').val();
        let ffin = ffin1;
        let fid = $('#buscar_fecha_ini_d').val();
        let ftd = $('#buscar_fecha_fin_d').val();
        let id_buzon = $('#buscar_buzon_origen').val();
        let id_buzon_actual = $('#buscar_buzon_actual').val();
        let id_buzon_derivado = $('#buscar_derivado').val();
        let anio = $('#buscar_anio').val();
        let estado_tramitacion = $('#estado_tramitacion').val();
        let terceros = "";
        if($('#buscar_efectos_sobre_terceros').is(":checked")){
            terceros = $('#buscar_efectos_sobre_terceros').is(":checked");
        }
        let respondidos = "";
        if($('#buscar_respondidos').is(":checked")){
            respondidos = 1;
        }

        $('#grilla_recibidos').DataTable().ajax.url('/buscadorListar?buscar_id_documento='+id_documento+'&buscar_folio='+folio+'&buscar_tipo_documento='+tipo_documento+'&buscar_fecha_fin='+ffin+'&buscar_fecha_ini='+finicio+'&buscar_buzon_origen='+id_buzon+'&terceros='+terceros+'&buscar_buzon_actual='+id_buzon_actual+'&buscar_anio='+anio+'&buscar_derivado='+id_buzon_derivado+'&buscar_fecha_ini_d='+fid+'&buscar_fecha_fin_d='+ftd+'&respondidos='+respondidos+'&estado_tramitacion='+estado_tramitacion).load();
        var table = $('#grilla_recibidos').DataTable();
        var column = table.column(8);
        column.visible(false);
        if(id_buzon_derivado != ""){
            column.visible(true);
        }
       
    });

    $('#btnLimpiar').click(function(){
        $('#buscar_id_documento').val('');
        $('#buscar_folio').val('');
        $('#buscar_tipo_documento').val(null).trigger('change');
        $('#buscar_buzon_origen').val(null).trigger('change');
        $('#buscar_buzon_actual').val(null).trigger('change');
        $('#buscar_derivado').val(null).trigger('change');
        $('#buscar_anio').val(null).trigger('change');
        $('#buscar_fecha_ini').val('');
        $('#buscar_fecha_fin').val('');
        $('#buscar_fecha_ini_d').val('');
        $('#buscar_fecha_fin_d').val('');
        $('#buscar_efectos_sobre_terceros').prop('checked', false);
        $('#buscar_respondidos').prop('checked', false);
    });

    owl = $('.owl-carousel').owlCarousel(); 

    /* VERSIONES PDF */

   
    $(".boton_desplegar_versiones_anteriores").click(function(e){
        $('#card_ocultar_versiones').show();
        $('#card_desplegar_versiones').hide();
        $("#dropzone-principal").addClass("displayDropzone");
        
    });
    $(".boton_ocultar_versiones_anteriores").click(function(e){
        $('#card_ocultar_versiones').hide();
        $('#card_desplegar_versiones').show();
        $("#dropzone-principal").removeClass("displayDropzone");

    });

    $(".desplegar_opciones_avanzadas").click(function(e){
        $('#card_opciones_avanzadas').show();
        $(".desplegar_opciones_avanzadas").hide();
        $(".cerrar_opciones_avanzadas").show();
    });

    $(".cerrar_opciones_avanzadas").click(function(e){
        $('#card_opciones_avanzadas').hide();
        $(".cerrar_opciones_avanzadas").hide();
        $(".desplegar_opciones_avanzadas").show();
    });     

    $('#buzon_origen').select2();
        $('#tipo_documento').select2();


    $('#form_acciones_solicitadas_el').multiselect({
            nonSelectedText: 'Seleccione Acciones'
    });

    $('#form_destinatario_principal_el').tagsinput({
            maxTags: 1,
            itemValue: 'value',
            itemText: 'text'        
    });

    $('#form_otros_destinatarios_el').tagsinput({
        itemValue: 'value',
        itemText: 'text'
    });

    $(".btn_cerrar_guardar").click(function(e){
        $('#card_documento').hide();
        $('#card_bitacora').hide();	
        $('.btn_cerrar_guardar').hide();
        $("#collapseOne").collapse('show');
    });

   
    function filtrar_enter(){
        if (event.key === "Enter") {
            event.preventDefault();
            $("#botones_busqueda_simple button").trigger("click");
        }
       // $("#botones_busqueda_simple button").trigger("click");
    }

    

    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var dateFrom = $('#buscar_fecha_ini').val();
            var dateTo = $('#buscar_fecha_fin').val();
            var date = data[1];

            if ((dateFrom == '' && dateTo == '') ||
                (dateFrom == '' && Date.parse(date) <= Date.parse(dateTo)) ||
                (Date.parse(dateFrom) <= Date.parse(date) && dateTo == '') ||
                (Date.parse(dateFrom) <= Date.parse(date) && Date.parse(date) <= Date.parse(dateTo))) {
                return true;
            }
            return false;
        }
        
    );


        // VER DOCUMENTO Y CARGAR BITACORA

    function visualizar_documento(id_documento, id_documento_buzon, id_documento_buzon_padre)
    {
        $(".print-error-msg").hide();

        if(id_documento > 0)
        {
            $("#collapseOne").collapse('hide');
            $('#card_documento').show();
            $('#card_bitacora').show();
            $('.btn_cerrar_guardar').show();
            
            //deshabilita campos
            $('.form-disabled').prop("disabled", true);
            editor_cuerpo.setReadOnly(true);
            $('#form_acciones_solicitadas_el').multiselect('disable');
            $('#form_destinatario_principal_el').prop("disabled", true);
            $('#form_otros_destinatarios_el').prop("disabled", true);
            $(".bootstrap-tagsinput-max").addClass("disabled");
            $(".bootstrap-tagsinput").addClass("disabled"); 
            cargar_datos_grilla_interno(id_documento);
            get_tabla_bitacora(id_documento,'#tabla_bitacora_grilla');
            //cargar_datos_bitacora(id_documento);

        }
    }
   
function cargar_datos_grilla_interno(id_documento)
{
    console.log("cargar_datos_grilla_interno");
    owl.trigger('destroy.owl.carousel'); 
    owl.find('.owl-stage-outer').children().unwrap();
    owl.removeClass("owl-center owl-loaded owl-text-select-on");

    $.ajax({
        //url: "/documentos/"+id_documento,
        //buscador.show
        url:route('buscador.show',{'id':id_documento}),
        type:'GET',
        dataType: 'json',
        success: function(data) {
            if(data.status=='400') {
                toastr.error(data.data.comentario,"Aviso!");
            }
            else
            {
                if(data.status == '200')
                {
                    var json_tipo_doc = $.parseJSON(data.data.json_tipo_documento);
                    
                    if (data.data.rel_documento_buzon[0]['contestar_hasta'] != null)
                        {
                            var fechaContestarHasta = data.data.rel_documento_buzon[0]['contestar_hasta'].split(' ');
                            $("input[name='contestar_hasta']").val(fechaContestarHasta[0]);
                        }

                    var idBuzon = $("input[name='hiddIdBuzon']").val();
                    var carpeta = "";
                    var idBuzonOrigen = "";
                    $.each(data.data.rel_documento_buzon, function(key,value)
                    {
                        id_buzon = value.id_buzon;
                        if (value.id_documento_buzon_padre == null) //buzon origen id_documento_buzon_padre = null
                            idBuzonOrigen = value.id_buzon;

                    });

                    $("#textBuzonorigen").text(listadoBuzones[idBuzonOrigen]);
                    
                    $("select[name='tipo_documento']").prepend("<option value='"+json_tipo_doc['id_tipo_documento']+"' selected='selected'>"+json_tipo_doc['nombre']+"</option>");
                    $("select[name='nivel_acceso']").val(data.data.id_nivel_acceso);
                    $("select[name='efectos_terceros']").val(""+data.data.efectos_terceros+"");
                    $("input[name='materia']").val(data.data.materia);
                    $("input[name='anterior']").val(data.data.anterior);
                    $("textarea[name='descripcion']").val(data.data.descripcion);                     

                    $("input[name='hiddIdOrigen']").val(json_tipo_doc['id_tipo_origen']);
                    editor_cuerpo.setData(data.data.cuerpo);
                    
                    $("input[name='hiddIdDocumento']").val(data.data.id_documento);
                    //$("input[name='hiddIdDocumentoBuzon']").val(id_documento_buzon);

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
                        $('#form_archivo_principal_el').hide();
                        $('#cargar_archivo_principal_el').show();
                    }

                    //archivos    
                    var relDocumentoBuzonArchivo = data.data.rel_archivos;

                    let htmlFile = "";
                    let htmlFileAnexo = '<div class="col-md-12 group-button-alig file-container-all">';
                    let htmlFileOtros = '<div class="col-md-12 group-button-align file-container-all">';
                    let htmlFilePrincipal = '<div class="col-md-12 group-button-align file-container-all">';
                    let htmlFilePrincipal_va = '<div class="col-md-12 file-container-all">';
                    
                    aFilesPrincipal = [];
                    aFilesDelete = [];                  

                    $.each(relDocumentoBuzonArchivo, function(key,value)
                    {   
                        var extension = value.nombre_archivo_original.split('.').pop();
                        var imagen = "";
                        imagen = "pdf.png";
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
                                // default:
                                    
                                //     break;
                            }
                        
                        htmlFile = '<div class="file-container '+value.id_documento_buzon_archivo+'">'+
                                       ' <img src="/img/'+imagen+'" width="83" height=94" style="" />'+
                                        //   '<a href="/files/'+value.nombre_archivo_codificado+'" class="btn-descargar" target="_blank"><i class="fas fa-download fa-icon1"></i></a>';
                                        '<button onClick="ver_archivo(\''+value.nombre_archivo_codificado+'\')" type="button" class="btn btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="View Details" style="margin-left: 3px;"><i class="fa fa-download"></i></button>'+
                                        '<p style="width: 90px!important;word-break: break-all;font-size: 12px;line-height: 1;margin-top: 15px;margin-bottom: 5px;">'+value.nombre_archivo_original+'</p>';
                        htmlFile_va = '<div class="file-container '+value.id_documento_buzon_archivo+'">'+
                            '  <img src="/img/'+imagen+'" width="83" height=94" style="" />'+
                                //'<a href="/files/'+value.nombre_archivo_codificado+'" class="btn-descargar" target="_blank"><i class="fas fa-download fa-icon1"></i></a>';
                                '<button onClick="ver_archivo(\''+value.nombre_archivo_codificado+'\')" type="button" class="btn btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="View Details" style="margin-left: 3px;"><i class="fa fa-download"></i></button>';

                        if (value.id_tipo_archivo == 2) //anexo
                            htmlFileAnexo += htmlFile + '</div>';  
                            

                        if (value.id_tipo_archivo == 3){ //otros
                            htmlFileOtros += htmlFile + '</div>'; 
                        }     
                        
                        if (value.id_tipo_archivo == 1 && value.version == 1) //principal
                            htmlFilePrincipal += htmlFile + '</div>'; 

                        //versiones anteriores 
                        
                        if (value.id_tipo_archivo == 1 && value.version != 1) 
                            htmlFilePrincipal_va += htmlFile_va + '</div>'; 
 
                    });

                    $('#dropzone-principal-view').html(htmlFilePrincipal + '</div>');
                        $('#dropzone-anexo-view').html(htmlFileAnexo + '</div>');
                        $('#dropzone-otros-view').html(htmlFileOtros + '</div>');
                        $('#versiones_anteriores').html(htmlFilePrincipal_va + '</div>');
                     


                    //destinatarios

                    var relDocumentoBuzon = data.data.rel_documento_buzon_actual;
                    
                    $.each(relDocumentoBuzon, function(i, item)
                    {                       
                        if (item.id_tipo_destino == 1)
                        {
                            $('#form_destinatario_principal_el').tagsinput('add', {"value": item.id_buzon, "text": listadoBuzones[item.id_buzon]});
                            $("textarea[id='form_comentario_el']").val(item.comentario_principal);

                            //seleccionar acciones

                            var accionesSolicitadas = $.parseJSON(item.json_acciones);
                                
                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                            for (let i in accionesSolicitadas) {
                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                            }
                        }

                        if (item.id_tipo_destino == 2)
                        {
                            $('#form_otros_destinatarios_el').tagsinput('add', {"value": item.id_buzon, "text": listadoBuzones[item.id_buzon]});
                            $("textarea[id='form_comentario_otro_el']").val(item.comentario_secundario);
                        }  
                    });

                    /* responder a */                   

                    var sDivActualPrev = "";
                    var sDivActualNext = "";
                    var sDivIzq = "";
                    console.log(data.data);
                    var jsonRespuesta = data.data.referencias.respuesta_a; 
                    var jsonReferenciasAnexos = data.data.referencias.anexos; 
                    var jsonDocResponder = data.data.rel_responder;
                   

                    $("form#form_crear_editar").find("input[name='documentos_respuesta[]']").remove();
                    $('#contador_respuesta_a').text(jsonRespuesta.length);
                    for (j in jsonRespuesta) 
                    {
                        console.log(j);
                        //completa carrusel lado izq
                        sDivIzq += ' <div class="item"><div class="item_display" ><a href="" onclick="visualizar_documento_alerta('+jsonRespuesta[j]['identificador']+','+id_buzon+','+idBuzonOrigen+',\''+jsonRespuesta[j]['materia']+'\')">'+jsonRespuesta[j]['identificador']+'</a><p>'+moment(jsonRespuesta[j]['created_at']).format('DD-MM-YYYY')+'</p></div></div>';                               
                        $("form#form_crear_editar").append("<input type='hidden' name='documentos_respuesta[]' value='"+jsonRespuesta[j]['id_documento']+"' />");

                    }

                    //REFERENCIAS SGD
                    //carga los documentos que son referencias de SGD
                    $("#contenedor_documentos_referencia").html("");
                    if(jsonReferenciasAnexos!=null && jsonReferenciasAnexos.length>0){
                        jsonReferenciasAnexos.forEach(function(value) {
                            $("form#form_crear_editar").append("<input type='hidden' name='documentos_referencias[]' value='"+value.id_documento+"' />");
                            //crear elementos en vista
                            let el =$("<div></div>");
                            el.addClass("file-container");
                            el.append($("<img>",{src:"/img/pdf.png", width:83, height:94}));
                            el.append($("<button>",{
                                type:"button",
                                class:"btn text-nowrap btn-min-w  btn-sm btn-arch btn-default btn-outline-secondary rounded-circle",
                                title:"Descargar",
                                style:"mdargin-left: 3px;",
                                click:function(e){
                                    e.stopPropagation();
                                    console.log( "descargar referencia",$(this));
                                    window.open(route('buscador.descargar_plc')+"?idDocumento="+value.id_documento, '_blank');
                                }
                            }).html($("<i>",{class:"fa fa-download"})));

                            
                            el.append($("<p>",{
                                style:"width: 90px!important;word-break: break-all;font-size: 12px;line-height: 1;margin-top: 15px;margin-bottom: 5px;"
                            }).text(value.tipodoc+" - "+value.identificador));   

                            $("#contenedor_documentos_referencia").append(el);

                        });
                        
                    }



                    //completar carrusel lado der
                    var sDivDer = "";
                    for (let d in jsonDocResponder){
                        sDivDer += ' <div class="item"><div class="item_display" "><a href="#" onclick="visualizar_documento_alerta('+jsonDocResponder[d]['identificador']+','+id_buzon+','+idBuzonOrigen+',\''+jsonDocResponder[d]['materia']+'\')" >'+jsonDocResponder[d]['identificador']+'</a><p>'+moment(jsonDocResponder[d]['created_at']).format('DD-MM-YYYY')+'</p></div></div>';
                    }
                     
                    sDivActual = '<div class="item"><div class="item_display item-doc" ><a href="#" onclick="visualizar_documento_alerta('+data.data.identificador+','+id_buzon+','+idBuzonOrigen+',\''+data.data.materia+'\')">'+data.data.identificador+'</a><p>'+moment(data.data.created_at).format('DD-MM-YYYY')+'</p></div></div>';
                    
                    if (sDivDer != '')
                        sDivActualPrev = '<div class="item"><div class="item_prev"><i class="fas fa-reply-all fa-2x"></i></div></div>';

                    if (sDivIzq != '')
                        sDivActualNext = '<div class="item"><div class="item_next"><i class="fas fa-reply-all fa-2x"></i></div></div>';


                    if (sDivIzq != '' || sDivDer != '')
                    {
                        owl.trigger('destroy.owl.carousel'); 
                        owl.find('.owl-stage-outer').children().unwrap();
                        owl.removeClass("owl-center owl-loaded owl-text-select-on");

                        var content = sDivIzq + sDivActualNext + sDivActual + sDivActualPrev + sDivDer;
                        owl.html(content);

                        //reinitialize the carousel (call here your method in which you've set specific carousel properties)
                        owl.owlCarousel({
                            items:8,
                            margin: 10,
                            dots: true,
                            nav: true,
                            navText: ["<div class='nav-button owl-prev'>‹</div>", "<div class='nav-button owl-next'>›</div>"],
                            
                        }).trigger('refresh.owl.carousel');
                    }             


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

    function descargar_documento( id_documento, id_documento_buzon)
    {
        
        //var _token = $("input[name='_token']").val();

        $.ajax({
            url: "/descargar_documento2/",
            type: 'GET',
            dataType: 'json',
            data: {
                
                idDocumento:id_documento,
                idDocumentoBuzon:id_documento_buzon             
            },
            success: function(data){
                //console.log(data.data);
                //window.location = '/files/principal_191_.pdf' ;
                if(data.status=='200')
                    {
                        console.log("hola");
                        //window.location = (data.data.data);
                        window.open(data.data.data, 'Download');
                    }
               
                     
                
            }
        });
         
             
    }


function ver_documentos_respuesta_a(){
    
    let seleccionados =  $("form#form_crear_editar").find("input[name='documentos_respuesta[]']").map(function() {
        return $(this).val();
    }).get();
    console.log(seleccionados);
    let params = new URLSearchParams();
    seleccionados.forEach(e => {
        params.append('seleccionados[]', e);
    });
    if(seleccionados.length==0){
        params.append('seleccionados[]', 0);
    }

    let id_buzon = $("input[name='hiddIdBuzon']").val();
    $.getJSON(route('documentos.informacionresumen')+'?'+params.toString(), function (response) {
        console.log(response);
        if(response.length==0){
            toastr.info('No hay documentos seleccionados', 'Información');
            return;
        }
        let html = "<ul class='text-left'>";
        response.forEach(element => {
            if(element.estado_tramitacion.id > 2)
                html+="<li><a target='_blank' class='underline' href='"+route('buscador.descargar_plc')+"?idDocumento="+element.id_documento+"' >"+element.id_documento+" - "+element.materia+"</a></li>";
            else
                html+="<li>"+element.id_documento+" - "+element.materia+"</li>";
        });
        html+="</ul>";

        Swal.fire({
            title: "Documentos de Respuesta",
            html: html,
            icon: "info",
            //showDenyButton: true,
            //showCancelButton: true,
            confirmButtonText: "Cerrar",
            showCancelButton: false,
            //denyButtonText: `Cerrar`
        }).then((result) => {
            if(result.value){
                $('#btn_respuesta_a').trigger('click');
            }     
        });



    }).fail(function (jqXHR, textStatus, errorThrown) {
        toastr.error('Error al obtener los datos de documentos pendientes: '+errorThrown, textStatus, errorThrown);
    });

}
 
</script>
@stop
