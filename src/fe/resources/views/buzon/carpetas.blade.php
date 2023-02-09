@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')
    <div class="row">
        <div class="col-10">
            <h1>Buzón: {{$nombre_buzon}}</h1>
        </div>
        <div class="col-2">
            <button id="add_documento" type="button" class="btn text-nowrap btn-min-w  btn-success nuevo_documento">Nuevo Documento</button>
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
                                <div class="pb-4 pt-2">
                                    <button onclick="recepcion_masiva()" class="btn text-nowrap btn-min-w  btn-sm btn-primary btn-recepcion-masiva">Recibir Masivo</button>
                                </div>
                                <table id="grilla_por_recibir"  class="table dt-responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr class="grilla_header">
                                            <th data-priority="1">Sel</th>
                                            <th data-priority="1">ID Doc.</th>
                                            <th data-priority="1">F. Entrada</th>
                                            <th data-priority="1">Materia</th>
                                            
                                            <th data-priority="2">TD</th>
                                            <th data-priority="2">TE</th>
                                            <th data-priority="2">Origen</th>
                                            <th data-priority="2">Contestar Hasta</th>
                                        </tr>
                                    </thead>
                                </table>
                                
                            </div>
                            <div class="tab-pane fade" id="nav-recibidos" role="tabpanel" aria-labelledby="nav-recibidos-tab">
                                <!-- table border="0" cellspacing="5" cellpadding="5" style="margin-bottom:30px;">
                                    <tbody>
                                        <tr>
                                            <td>ID Doc:</td>
                                            <td>Tipo Documento:</td>
                                            <td>Estado:</td>
                                            <td>Materia:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><input class="form-control"  type="text" id="gr_buscar_id_doc" name="gr_buscar_id_doc"></td>
                                            <td>
                                                <select id="gr_buscar_tipo_doc" name="gr_buscar_tipo_doc" class="form-control " multiple="multiple">
                                                    @foreach($listado_tiposdoc as $list)
                                                    <option value="{{$list['nombre']}}">{{$list['nombre']}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control"  id="gr_buscar_estado" name="gr_buscar_estado"  multiple="multiple">
                                                    
                                                    @foreach($listado_parametros['estado_documento'] as $estado_documento)
                                                        @if($estado_documento['id_estado_documento'] > 3)    
                                                            <option value='{{$estado_documento['id_estado_documento']}}'> {{$estado_documento['nombre']}} </option>
                                                        @endif    
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="search" aria-controls="grilla_recibidos" class="form-control"  id="gr_buscar_origen_materia" name="gr_buscar_origen_materia"></td>
                                            <td id="botones_grilla_recibidos">
                                            </td>
                                        </tr>
                                </tbody></table -->
                                <div class="row">
                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                        ID Doc:<br/><input class="form-control"  type="text" id="gr_buscar_id_doc" name="gr_buscar_id_doc">
                                    </div>
                                    <div class="col-lg-3 col-md-12 col-sm-12">
                                       Tipo Documento:<br/><select id="gr_buscar_tipo_doc" style="display:grid;" name="gr_buscar_tipo_doc" class="form-control " multiple="multiple">
                                                    @foreach($listado_tiposdoc as $list)
                                                    <option value="{{$list['nombre']}}">{{$list['nombre']}}</option>
                                                    @endforeach
                                                </select>
                                    </div>
                                    <div class="col-lg-2 col-md-12 col-sm-12">
                                       Estado:<br/><select class="form-control"  id="gr_buscar_estado" name="gr_buscar_estado"  multiple="multiple">
                                                    
                                                    @foreach($listado_parametros['estado_documento'] as $estado_documento)
                                                        @if($estado_documento['id_estado_documento'] > 3)    
                                                            <option value='{{$estado_documento['id_estado_documento']}}'> {{$estado_documento['nombre']}} </option>
                                                        @endif    
                                                    @endforeach
                                                </select>
                                    </div>
                                    <div class="col-lg-3 col-md-12 col-sm-12">
                                        Materia:<br/><input type="search" aria-controls="grilla_recibidos" class="form-control"  id="gr_buscar_origen_materia" name="gr_buscar_origen_materia">
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-sm-12 pt-4" id="botones_grilla_recibidos">
                                        
                                    </div>
                                </div>
                                <p>&nbsp;</p>
                                <div class="row">
                                    <div class="text-left" style="width: 48%;">
                                        <label class="text-bold">Acciones Masivas</label>
                                        <select class="form-control-sm" onchange="seleccionarAccionMasiva(this.value);" id="selAccion">
                                            <option value="">Seleccione</option>
                                            <option value="1">Derivar</option>
                                            <option value="0">Archivar</option>
                                            <option value="2">Firmar</option>
                                        </select>
                                        &nbsp;&nbsp;<button class="btn text-nowrap btn-min-w  btn-primary btn-aplicar" id="btnAplicar" style="display:none" >Aplicar</button>
                                    </div>
                                    <div class="text-right" style="width: 50%;">
                                        <select id='filtro-td' multiple><option>Principal</option><option>Secundario</option></select>
                                    </div>
                                </div>
                                <br/>
                                <table id="grilla_recibidos"  class="table dt-responsive nowrap no-footer dtr-inline dataTable collapsed" style="width:100%;">
                                    <thead>
                                        <tr class="grilla_header">
                                            <th data-priority="1">Sel</th>
                                            <th data-priority="1"></th>
                                            <th data-priority="1"></th>
                                            <th data-priority="2"></th>
                                            <th data-priority="1">E</th>
                                            <th data-priority="0">ID Doc</th>
                                            
                                            <th data-priority="3">Fecha Recepción</th>
                                            <th data-priority="0">Materia</th>
                                            <th data-priority="2">TD</th>
                                            <th data-priority="2">TE</th>
                                            <th data-priority="2">Origen</th>
                                            <th data-priority="2">Contestar Hasta</th>
                                            <th data-priority="0">Folio</th>
                                            <th data-priority="1">Acciones</th>
                                            
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="nav-despachados" role="tabpanel" aria-labelledby="nav-despachados-tab"  style="width: 100%;">
                                <table border="0" cellspacing="5" cellpadding="5" style="margin-bottom:30px;">
                                    <tbody>
                                        <tr>
                                            <td>ID Doc:</td>
                                            <td>Tipo Documento:</td>
                                            <td>Estado:</td>
                                            <td>Materia:</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td><input class="form-control"  type="text" id="gd_buscar_id_doc" name="gd_buscar_id_doc"></td>
                                            <td>
                                                <select id="gd_buscar_tipo_doc" name="gd_buscar_tipo_doc" class="form-control" multiple>
              
                                                    @foreach($listado_tiposdoc as $list)
                                                    <option value="{{$list['nombre']}}">{{$list['nombre']}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <select class="form-control"  id="gd_buscar_estado" name="gd_buscar_estado" multiple >
                                                 
                                                    @foreach($listado_parametros['estado_documento'] as $estado_documento)
                                                        @if($estado_documento['id_estado_documento'] < 3)    
                                                            <option value='{{$estado_documento['id_estado_documento']}}'> {{$estado_documento['nombre']}} </option>
                                                        @endif 
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="text" class="form-control"  id="gd_buscar_destino_materia" name="gd_buscar_destino_materia"></td>
                                            <td id="botones_grilla_despachados">
                                            </td>
                                        </tr>
                                    </tbody></table>
                                    <table id="grilla_despachados"  class="table dt-responsive nowrap no-footer dtr-inline dataTable collapsed " style="width:100%">
                                        <thead>
                                            <tr class="grilla_header">
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
                                                <th class="all">Acciones</th>
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
                                <ul class="list-group list-group-horizontal">
                                    <li class="list-group-item col-md-2"><b>ID:</b> <i><span id="idAsignado">No Asignado</span></i></li>
                                    <li class="list-group-item col-md-2"><b>Folio:</b> <i><span id="idFolio">No Asignado</span></i></li>
                                    <li class="list-group-item col-md-2"><b>Fecha:</b> <i><span id="idFecha">No Asignado</span></i></li>
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
                                <div id="dropzone-principal" class="dropzone dropzone-files"></div>    
                                
                                <div id="card_desplegar_versiones" class="bl1 header1" > 
                                    <label class="">Versiones</label>                       
                                    <button type="button" class="btn text-nowrap btn-min-w  boton_desplegar_versiones_anteriores" style="padding: 49px 15px;">
                                        <i class="fas fa-angle-double-right fa-3x"></i>
                                    </button>
                                </div> 
                                <div class="bl2"  id="card_ocultar_versiones" style="display:none" >
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
                            <label for="exampleFormControlTextarea1">Anexos:</label>
                            
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
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label for="floatingTextarea">Comentario a Destinatario Principal:<i onclick="vernotas(1)" title="ver mensajes anteriores" class="fa fa-sticky-note btn btn-sm btn-light"></i></label>
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
                            <div class="col-md-12 mb-3">
                                <label for="floatingTextarea">Comentario(s) Otro(s) Destinatario(s): <i onclick="vernotas(2)"  title="ver mensajes anteriores"  class="fa fa-sticky-note btn btn-sm btn-light"></i></label>
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
                                    <button type="button"  class="btn text-nowrap btn-min-w  btn-secondary  btn_cerrar_guardar">Cerrar</button>
                                    <button type="button" id="submit-edit" class="btn text-nowrap btn-min-w  btn-light btn-guardar-submit-edit ">Guardar</button>
                                    <button type="button" id="submit-all" class="btn text-nowrap btn-min-w  btn-success btn-guardar-submit">Guardar y Cerrar</button>
                                    <button type="button" id="submit-enviar" class="btn text-nowrap btn-min-w  btn-primary btn-enviar-submit " style="display:none">Enviar</button>
                                    <span class="" id="addButton"></span>
                                    <input type="hidden" name="hiddIdDocumento" id="hiddIdDocumento" value="">
                                    <input type="hidden" name="hiddIdDocumentoBuzon" id="hiddIdDocumentoBuzon" value="">
                                    <input type="hidden" name="hiddIdBuzon" id="hiddIdBuzon" value="{{$id_buzon}}">
                                    <input type="hidden" name="hiddIdOrigen" id="hiddIdOrigen" value="">
                                    <input type="hidden" name="hiddIdFileDelete" id="hiddIdFileDelete" value="">
                                    <input type="hidden" name="hiddIdResponder" id="hiddIdResponder" value="">
                                    <input type="hidden" name="hiddIdTipoDestino" id="hiddIdTipoDestino" value="">
                                    
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
            <input class="form-check-input" type="checkbox" value="DOD" name="buscar_accion" id="accion_dop">
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
                <table id="tabla_bitacora_grilla" class="table dt-responsive nowrap" style="width:100%">
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


@stop

@section('css')

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" integrity="sha512-jU/7UFiaW5UBGODEopEqnbIAHOI8fO6T99m7Tsmqs2gkdujByJfkCbbfPSN4Wlqlb9TGnsuC0YgUgWkRBK7B9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
    <link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css"/>
    
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
            display:none;
        }

        .flex-container {
            display: flex;
            flex-wrap: nowrap;
            background-color: #e9f1fe;
            border: 1px solid #005c9e;
            margin-bottom: 30px;
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
        
        .addFrm {
            float:right;
        }

        .btnFirma {
            float: right;
            margin-left:10px;
            margin-bottom:10px;
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
        
        .btn-min-w{
            min-width: 10%!important;
        }
        
        .multiselect-native-select{
            display: grid;
        }
        
     </style>

    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js" integrity="sha512-oQq8uth41D+gIH/NJvSJvVB85MFk1eWpMK6glnkg6I7EdMqC1XVkW7RxLheXwmFdG03qScCM7gKS/Cx3FYt7Tg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ url('js/ckfinder/ckfinder.js') }}"></script>
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script src="/js/fglobales.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>


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
    var bloqueo_accion=false;
    isDelete = true; 
    var objDoc =null;

    var allBuzonesT2 = @json($allBuzonesT2);
    var allBuzones = @json($allBuzones);
    var allBuzones2 = @json($allBuzones2);
    var listadoDocPendientes = @json($listDocPendientesBuzon);
    var idTipoFlujo = "";  

    const txtArchivado = [];
    txtArchivado[0] = ['Archivar','archivará','Archivado'];
    txtArchivado[1] = ['Desarchivar','desarchivará','Desarchivado'];

    aplicaFrm = @json($aplicaFrm);

    owl = $('.owl-carousel').owlCarousel(); 

    $("a[data-toggle=\"tab\"]").on("shown.bs.tab", function (e) {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });
    $.fn.dataTable.ext.errMode =  'none';

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
        maxFilesize: 50, //MB
        maxFiles: 1,
        dictDefaultMessage: "Arrastre y suelte archivos pdf aquí <br> <i class='fa fa-upload fa-lg'></i>",
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
        maxFilesize: 50, //MB
        //maxFiles: 2,
        dictDefaultMessage: "Arrastre y suelte archivos pdf aquí <br> <i class='fa fa-upload fa-lg'></i>",
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
                dropzoneAnexo.removeAllFiles(true);
                }
            );
            this.on("queuecomplete", function (file) {

            });     
        },        
        sending: function(file, xhr, formData){
            var idb = $("input[name='hiddIdDocumentoBuzon']").val();
            var idoc = $("input[name='hiddIdDocumento']").val();
            formData.append('id_documento_buzon', idb);
            formData.append('id_documento', idoc);

        },
    };

    Dropzone.options.dropzoneOtros = {
        headers:{
            'X-CSRF-TOKEN' : "{{csrf_token()}}"
        },
        url: "{{route('files.store')}}",
        autoProcessQueue: false,
        uploadMultiple: true,
        maxFilesize: 50, //MB
        //maxFiles: 2,
        dictDefaultMessage: "Arrastre y suelte archivos pdf aquí <br> <i class='fa fa-upload fa-lg'></i>",
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


        }        
    };

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


    /* **DOCUMENTOS** SCRIPT */

    const editor_cuerpo = CKEDITOR.replace('form_cuerpo', {  
             
        filebrowserBrowseUrl     : "{{ route('ckfinder_browser') }}",
        filebrowserImageBrowseUrl: "{{ route('ckfinder_browser') }}?type=Images&token=123",
        filebrowserImageUploadUrl: "{{ route('ckfinder_connector') }}?command=QuickUpload&type=Images",
    }); 

    CKFinder.config( { connectorPath: '/ckfinder/connector' } );   

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
        habilita_boton('btn-guardar-submit');
        if($("#idAsignado").html() != "No Asignado"){
            $('.btn-guardar-submit-edit').show();   
        }
        else{
            $('.btn-guardar-submit').html("Guardar");
        }
        habilita_boton('btn-guardar-submit-edit');

        /* responder a */

        if (e.isTrigger && $("input[name='hiddIdResponder']").val() != '')
        {
            $('#form_respuesta_a').multiselect({numberDisplayed: 6});
            $('#form_respuesta_a').multiselect('deselectAll', true);
            $('#form_respuesta_a').multiselect('select', $("input[name='hiddIdResponder']").val());
            $('#form_respuesta_a').multiselect('refresh');
        }
        else
            $("input[name='hiddIdResponder']").val('');
       
    });

    function addBtnFirma()
    {
        if($('#chkFrm').prop("checked")){
            $('#btnFirma').show();
            var buttonFrmMasiva = '<button onClick="envioFrm()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit">Firma Masiva</button> ';
            $('#btnFirma').html(buttonFrmMasiva);
        }
        else{
            $('#btnFirma').hide();
        }

            var column = grilla_recibidos.column(1); 
            column.visible( !column.visible() );
    }

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
        isDelete = false;

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
        $('.btn-guardar-submit-edit').hide();   
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

        //inicializa formulario

        $('#form_crear_editar').trigger("reset");
        $("input[name='encabezado']").val('');
        editor_cuerpo.setData('');
        $("textarea[id='form_comentario_el']").val('');
        $("textarea[id='form_comentario_otro_el']").val('');

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
                        toastr.error(data.data.comentario,"¡Aviso!");
                    }
                    else {
                        if(data.status=='200' || data.status=='201')
                        {
                            $("input[name='encabezado']").val(data.data.plantilla_encabezado);
                            $("input[name='hiddIdOrigen']").val(data.data.id_tipo_origen);

                            idTipoFlujo = data.data.id_tipo_flujo;

                            editor_cuerpo.setData(data.data.plantilla_cuerpo);

                            //habilita respuesta a: solo a flujo libre

                            if (idTipoFlujo != 1)
                            {
                                $('#form_respuesta_a').multiselect('disable');
                                $('#form_respuesta_a').multiselect('deselectAll', true);
                            }
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
                    habilita_boton('btn-guardar-submit');
                    habilita_boton('btn-guardar-submit-edit');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    toastr.error("Falla al obtener datos","¡Aviso!");
                    habilita_boton('btn-guardar-submit');
                    habilita_boton('btn-guardar-submit-edit');

                }
            });
    }
    // guardar y mantener
    $(".btn-guardar-submit-edit").click(function(e)
    {
        e.preventDefault();
        $('.btn-guardar-submit-edit').html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando'
        );
        $(".print-error-msg").hide();
        deshabilita_boton('btn-guardar-submit-edit');
        deshabilita_boton('btn-guardar-submit');
        deshabilita_boton('btn-enviar-submit');
        deshabilita_boton('btn_cerrar_guardar');
        deshabilita_boton('btn-visar');
        deshabilita_boton('btn-firmar');
        deshabilita_boton('btn-archivar');
        deshabilita_boton('btn-visar-derivar');
        deshabilita_boton('btn-firmar-derivar');
        
        accion_editar_guardar(3);
    });


    //SUBMIT
    $(".btn-guardar-submit").click(function(e)
    {
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

        guarda_documento(1);
    });

    $(".btn-enviar-submit").click(function(e)
    {
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

    });

    function deshabilita_boton(tClase){
        $('.'+tClase+'').prop("disabled", true);
    }
    function habilita_boton(tClase){
        $('.'+tClase+'').prop("disabled", false);
    }

    function testAsync(){
        return new Promise((resolve,reject)=>{
            //here our function should be implemented 
            dropzoneAnexo.processQueue(); 
        });
    }

    async function callerFun(){
        console.log("Caller");
        await testAsync();
        console.log("After waiting");
    }


    //async function guarda_documento(accion, callback)
    function guarda_documento(accion, callback)
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


                        setTimeout(function() {
                            toastr.success("Documento actualizado","¡Aviso!");
                            
                            $('#card_crear_documento').hide();
                            $("#collapseOne").collapse('show');     
                            fn_grilla_recibidos();
                            fn_grilla_despachados();
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
                            $('.btn-guardar-submit').html( 'Guardar y Cerrar' );
                            // $('.btn-vp-sg').hide();       
                            // $('.btn-vp').show();       

                        }, 5000);                        
                        
                    }

                    if (accion == 2)
                    {

                        dropzonePrincipal.processQueue();   
                        dropzoneOtros.processQueue();   
                        dropzoneAnexo.processQueue();  
                        
                        setTimeout(function() {
                            callback(data);
                            respuesta_guarda = data;  
                            // $('.btn-vp-sg').hide();       
                            // $('.btn-vp').show();       
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
                            $('.btn-guardar-submit').html( 'Guardar y Cerrar' );               
                        }, 5000);
                        

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
                    habilita_campos();
                    cargar_datos_grilla(data.data.id_documento,data.data.rel_documento_buzon[0]['id_documento_buzon'],data.data.rel_documento_buzon[0]['id_documento_buzon_padre'],3,1);

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
                    $('.btn-guardar-submit').html( 'Guardar y Cerrar' );
                    habilita_boton('btn-vp');  

                    if(data.data.id_documento != '')
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
                        $('.btn-guardar-submit').html( 'Guardar y Cerrar' );
     
                    //actualiza grilla despachados
                    fn_grilla_despachados();   

                }
                else
                {
                    toastr.error(data.data.comentario,"¡Aviso!");
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
                    if($("#idAsignado").html() != "No Asignado"){
                        $('.btn-guardar-submit').html( 'Guardar y Cerrar' );
                    }
                    else{
                        $('.btn-guardar-submit').html( 'Guardar' );
                    }
                    
                }
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en el documento","¡Aviso!");

                if($("#idAsignado").html() != "No Asignado"){
                    $('.btn-guardar-submit').html( 'Guardar y Cerrar' );
                }
                else{
                    $('.btn-guardar-submit').html( 'Guardar' );
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
                    
                    if(accion == 8)//visar y derivar
                        visar_derivar_documento();
                }
                else
                {
                    toastr.error("Falla al guardar destinatarios","¡Aviso!");
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en la actualización del documento","¡Aviso!");
            }

        });
    }

    function accion_editar_guardar(idCarpeta) //**** revisar si se puede usar funcion que guarda documento ****//
    {
        console.log(idCarpeta)
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

        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        var hiddIdFileDelete = $("input[name='hiddIdFileDelete']").val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        var comentarioPrincipal = $('#form_comentario_el').val();
        var comentarioOtros = $('#form_comentario_otro_el').val();
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();
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
                carpeta:idCarpeta,
                estado:((idCarpeta==2)?4:1)
            },
            success: function(data)
            {
                if(data.status == '200')
                {
                    dropzoneAnexo.processQueue(); 
                    dropzoneOtros.processQueue(); 
                    dropzonePrincipal.processQueue(); 
                    
                    setTimeout(function() {
                        toastr.success("Documento actualizado","¡Aviso!");
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

                    }, 5000);
                }
                else
                {
                    toastr.error(data.data.comentario,"¡Aviso!");
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
                }

                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en el documento","¡Aviso!");

                $('.btn-guardar-submit-edit').html( 'Guardar' );
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

    function enviar_documento()
    {
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        var responder = $('#form_respuesta_a').val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        var tipoDestino = $("input[name='hiddIdTipoDestino']").val();
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
                $('.btn-enviar-submit').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Enviando'
                );
                guarda_documento(2, function(data){                    
                    //continue your function here, inside of the callback
                    if (data.status == 200)
                    {
                        deshabilita_boton('btn-guardar-submit');
                        deshabilita_boton('btn-guardar-submit-edit'); 
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
                            acciones_solicitadas:acciones_solicitadas,
                            destinatarioOtros:otrosDestinatarios,
                            id_tipo_destino:tipoDestino,
                            responder:responder,
                            carpeta:3                
                        },
                        success: function(data)
                        {
                            deshabilita_boton('btn-guardar-submit');
                            deshabilita_boton('btn-guardar-submit-edit'); 
                            if(data.status == '200')
                            {
                                toastr.success("Documento enviado","¡Aviso!");

                                $('#card_crear_documento').hide();        
                                $("#collapseOne").collapse('show');
                                clear_form();
                                fn_grilla_despachados();  
                                habilita_boton('btn-enviar-submit');
                                habilita_boton('btn-guardar-submit');
                                habilita_boton('btn-guardar-submit-edit');      
                                habilita_boton('btn_cerrar_guardar');
                                habilita_boton('btn-visar');
                                habilita_boton('btn-firmar');
                                habilita_boton('btn-archivar');
                                habilita_boton('btn-editar');
                                habilita_boton('btn-recibir-submit');
                                fn_grilla_recibidos();
                            }
                            else
                            {
                                toastr.error(data.data.comentario,"¡Aviso!");
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

                            $('.btn-enviar-submit').html( 'Enviar' );
                            
 
                        },
                        error: function (jqXHR, textStatus, errorThrown) {

                            toastr.error("Falla en el envío del documento","¡Aviso!");

                            $('.btn-enviar-submit').html( 'Enviar' );
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

                
            }
            else{
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

    function derivar_documento()
    {
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        var tipoDestino = $("input[name='hiddIdTipoDestino']").val();

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
                            acciones_solicitadas:acciones_solicitadas,
                            id_tipo_destino:tipoDestino,
                            carpeta:2                                         
                        },
                        success: function(data)
                        {
                            if(data.status == '200')
                            {
                                toastr.success("Documento Derivado","¡Aviso!");

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
                            }
                            else
                            {
                                toastr.error(data.data.comentario,"¡Aviso!");
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

                            $('.btn-enviar-submit').html( 'Enviar' );
                        },
                        error: function (jqXHR, textStatus, errorThrown) {

                            toastr.error("Falla en la derivación del documento","¡Aviso!");
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

                            $('.btn-enviar-submit').html( 'Enviar' );
                            $('.btn-derivar').html('Enviar');
                            $('.btn-derivar-2').html('Guardar y Enviar');
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
                        url:"/actualizar_estado_documento/"+hiddIdDocumentoBuzon,
                        type:'PUT',
                        dataType:'json',
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
                                toastr.success("Documento Visado","¡Aviso!");
                                
                                fn_grilla_recibidos();
                                $('#card_crear_documento').hide();        
                                $("#collapseOne").collapse('show');                   
                            }
                            else
                            {
                                toastr.error(data.data.comentario,"¡Aviso!");
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            toastr.error("Falla en el documento","¡Aviso!");
                        }
                    }); 
                }
            })                 
    }

    function visar_derivar_documento()
    {
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();

        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        if(destinatarioPrincipal !== undefined && acciones_solicitadas != ""){
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
                    console.log(result);
                if (result.value==true) {
                    $('.btn-visar-derivar').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Visando y derivando')
                    $.ajax({
                        url:"/actualizar_estado_documento/"+hiddIdDocumentoBuzon,
                        type:'PUT',
                        dataType:'json',
                        data: {
                            _token:_token,
                            hiddIdDocumento:hiddIdDocumento,
                            buzon:hiddIdBuzon,
                            accion:6                
                        },
                        success: function(data)
                        {
                            if(data.status == '200')//documento visado
                            {//derivar                                
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
                                        acciones_solicitadas:acciones_solicitadas,
                                        carpeta:2                                         
                                    },
                                    success: function(data)
                                    {
                                        if(data.status == '200')
                                        {
                                            toastr.success("Documento Visado y Derivado","¡Aviso!");

                                            $('#card_crear_documento').hide();        
                                            clear_form();
                                            fn_grilla_despachados();
                                            fn_grilla_recibidos();
                                            $('.btn-visar-derivar').html('Visar y Enviar');
                                            location.reload();
                                        }
                                        else
                                        {
                                            toastr.error(data.data.comentario,"¡Aviso!");
                                            $('.btn-visar-derivar').html('Visar y Enviar');
                                        }

                                        
                                    },
                                    error: function (jqXHR, textStatus, errorThrown) {

                                        toastr.error("Falla en la derivación del documento","¡Aviso!");

                                        $('.btn-visar-derivar').html('Visar y Enviar');
                                    }
                                });

                            }//fin derivar
                            else
                            {
                                toastr.error(data.data.comentario,"¡Aviso!");
                                $('.btn-visar-derivar').html('Visar y Enviar');
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            toastr.error("Falla en el documento","¡Aviso!");
                            $('.btn-visar-derivar').html('Visar y Enviar');
                        }
                    }); 
                }
            }) 
        }
        else{
            toastr.error("Falla en el documento: Debe seleccionar un destinatario principal y acciones","¡Aviso!");
        }   
    }

    function envioFrm()
    {
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();

        var matches = [];
        var checkedcollection = grilla_recibidos.$("input[name='checkFrm']:checked", { "page": "all" });
        checkedcollection.each(function (index, elem) {
            matches.push($(elem).val());

        });
        if(matches.length > 0)
        {
            $('.btn-aplicar').html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Firmando'
            );
            deshabilita_boton('btn-aplicar');
            Swal.fire({
                    title: 'Firma electrónica masiva',
                    html: "¿ Está seguro(a) que desea aplicar su firma electrónica al conjunto de documentos seleccionados ?",
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Aceptar'
                    }).then((result) => {
                    if (result.value==true) 
                    {  
                        $.ajax({
                            url: "/firma_masiva/",
                            type: 'PUT',
                            dataType: 'json',
                            data: {
                                _token:_token,
                                buzon:hiddIdBuzon,
                                //docBuzon:matchesBuzon,
                                firmas:matches,
                                accion:7                
                            },
                            success: function(data)
                            {
                                if(data.status == '200')
                                {
                                    toastr.success(data.data,"¡Aviso!");

                                    $('#card_crear_documento').hide();        
                                    fn_grilla_recibidos();
                                    $("#collapseOne").collapse('show');
                                }
                                else
                                {
                                    toastr.error(data.data.comentario,"¡Aviso!");
                                }
                                $('.btn-aplicar').html('Aplicar');
                                habilita_boton('btn-aplicar');
                                location.reload();

                            },
                            error: function (e) {
                                data = e.responseJSON;
                                console.log(data);
                                if (data.data.comentario != "" && data.data.comentario != null)
                                    toastr.error(data.data.comentario,"¡Aviso!");
                                else
                                    toastr.error("Falla en el documento","¡Aviso!");

                                $('.btn-aplicar').html('Aplicar');
                                habilita_boton('btn-aplicar');
                            
                            }
                        });

                    
                    }
                    else{
                        $('.btn-aplicar').html('Aplicar');
                        habilita_boton('btn-aplicar');
                    }
            }) 
        }
        else
            toastr.error("No hay documentos seleccionados para firmar.","¡Aviso!");
    }


    function firmar_documento()
    {
        if(bloqueo_accion){
            return false;
        }
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        deshabilita_boton('btn-recibir-submit');
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
                    $('.btn-recibir-submit').html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Firmar'
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
                    bloqueo_accion =true;
                    $.ajax({
                        url: "/firmar_documento/"+hiddIdDocumentoBuzon,
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
                                toastr.success(data.data,"¡Aviso!");
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
                                fn_grilla_recibidos();
                                $("#collapseOne").collapse('show');
                                habilita_boton('btn-recibir-submit');
                            }
                            else
                            {
                                toastr.error(data.data.comentario,"¡Aviso!");
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
                           
                            $('.btn-recibir-submit').html( 'Firmar' );
                            bloqueo_accion=false;

                        },
                        error: function (e) {
                            data = e.responseJSON;
                            console.log(data);
                            if (data.data.comentario != "" && data.data.comentario != null){
                                toastr.error(data.data.comentario,"¡Aviso!");
                                habilita_boton('btn-recibir-submit');
                            }
                            else{
                                toastr.error("Falla en el documento","¡Aviso!");
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
                            $('.btn-recibir-submit').html( 'Firmar' );
                            bloqueo_accion=false;
                        }
                    });

                    
                }
                else{
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

    function firmar_derivar_documento()
    {
        if(bloqueo_accion){
            return false;
        }
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        
        var destinatarioPrincipal = $('#form_destinatario_principal').val()[0];
        var acciones_solicitadas = $('#form_acciones_solicitadas_el').val();
        var otrosDestinatarios = $('#form_otros_destinatarios_el').val();
        if(destinatarioPrincipal !== undefined && acciones_solicitadas!= ""){    
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
                        console.log(result);
                    if (result.value==true) 
                    {  
                        $('.btn-firmar-derivar').html(
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Firmando'
                        );
                        bloqueo_accion =true;
                        $.ajax({
                            url: "/firmar_documento/"+hiddIdDocumentoBuzon,
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
                                if(data.status == '200'){//// derivar
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
                                            acciones_solicitadas:acciones_solicitadas,
                                            carpeta:2                                         
                                        },
                                        success: function(data)
                                        {
                                            if(data.status == '200')
                                            {
                                                toastr.success("Documento Firmado y Derivado","¡Aviso!");

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
                                            }
                                            else
                                            {
                                                toastr.error(data.data.comentario,"¡Aviso!");
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

                                            $('.btn-enviar-submit').html( 'Enviar' );
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {

                                            toastr.error("Falla en la derivación del documento","¡Aviso!");
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
                                }//// fin derivar
                                else
                                {
                                    toastr.error(data.data.comentario,"¡Aviso!");
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
                            
                                $('.btn-recibir-submit').html( 'Firmar' );
                                $('.btn-firmar-derivar').html('Firmar y Enviar');
                                bloqueo_accion=false;

                            },
                            error: function (e) {
                                data = e.responseJSON;
                                console.log(data);
                                if (data.data.comentario != "" && data.data.comentario != null){
                                    toastr.error(data.data.comentario,"¡Aviso!");
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
                                else{
                                    toastr.error("Falla en el documento","¡Aviso!");
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
                                $('.btn-recibir-submit').html( 'Firmar' );
                                $('.btn-firmar-derivar').html('Firmar y Enviar');
                                bloqueo_accion=false;
                            }
                        });

                        
                    }
                    else{
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
        }
        else{
            toastr.error("Falla en el documento: Debe seleccionr un destinatario y acciones","¡Aviso!");
        }
    }

    function finalizar_documento()
    {
        var _token = $("input[name='_token']").val();
        
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        deshabilita_boton('btn-recibir-submit');

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
                                toastr.success("Documento Finalizado","¡Aviso!");
                                habilita_boton('btn-recibir-submit');
                                $('#card_crear_documento').hide();        
                                fn_grilla_recibidos();
                                $("#collapseOne").collapse('show');
                            }
                            else
                            {
                                toastr.error(data.data.comentario,"¡Aviso!");
                                habilita_boton('btn-recibir-submit');
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            toastr.error("Falla en el documento","¡Aviso!");
                            habilita_boton('btn-recibir-submit');
                        }
                    });
                }
                else{
                    habilita_boton('btn-recibir-submit');
                }
            })
    }

    function recibir_documento(destino)
    {
        var _token = $("input[name='_token']").val();
        $('.btn-recibir-submit').html( '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Recibiendo');
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
                            toastr.success("Documento Recepcionado","¡Aviso!");
                            $('.btn-recibir-submit').html( 'Recibir');
                            $('#card_crear_documento').hide();        
                            clear_form();
                            fn_grilla_recibidos();
                            location.reload();
                        }
                        else
                        {
                            toastr.error(data.data.comentario,"¡Aviso!");
                            $('.btn-recibir-submit').html( 'Recibir');
                        }

                        $('.btn-enviar-submit').html( 'Enviar' );
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        toastr.error("Falla en el documento","¡Aviso!");

                        $('.btn-enviar-submit').html( 'Enviar' );
                    }
                });
            }
            else{
                $('.btn-recibir-submit').html( 'Recibir');
            }
        })        
       
    }

    function archivar_documento(accion)
    {
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
                        comentario:comentario,
                        accion:accion                
                    },
                    success: function(data)
                    {
                        if(data.status == '200')
                        {
                            toastr.success("Documento " + txtArchivado[accion][2],"¡Aviso!");

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
                        }
                        else
                        {
                            toastr.error(data.data.comentario,"¡Aviso!");
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

                        $('.btn-enviar-submit').html( 'Enviar' );
                    },
                    error: function (jqXHR, textStatus, errorThrown) {

                        toastr.error("Falla en el documento","¡Aviso!");
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
                        $('.btn-enviar-submit').html( 'Enviar' );
                    }
                });
            }
            else{
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
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,2,11);//se incorpora accion 11 para identificar la acción de ver el documento 

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        

        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

    }

    function responder_recibidos(id_documento)
    {
        $("input[name='hiddIdResponder']").val(''); 
        $("input[name='hiddIdResponder']").val(id_documento); 
        fn_grilla_despachados();
        cambio_texto_boton_carpetas('Despachados');
        $('#nav-despachados-tab').tab('show');
        $("#add_documento").trigger("click");
    }

    function accion_visar(id_documento,id_documento_buzon,id_documento_buzon_padre){
        $('#titulo_accion').html('Editar Documento');         

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,2,22);//se incorpora accion 22   
        
        //listado de visaciones y firmas
        $('.row_txt_firmar').show();

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html(''); 

        var buttonVisar = '<button onClick="guarda_destinatarios_documento(6)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Visar</button> ';
        $('#addButton').append(buttonVisar);
        
    }

    function accion_pdf(id_documento,id_documento_buzon){

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
                if (result.value==true) 
                {
                    $.ajax({
                        url: "/generar_archivo",
                        type: 'PUT',
                        dataType: 'json',
                        data: {
                            _token:_token,
                            idDocumento:id_documento,
                            idDocumentoBuzon:id_documento_buzon,
                            idBuzon:hiddIdBuzon             
                        },
                        success: function(data)
                        {
                            if(data.status == '200')
                            {
                                toastr.success(data.data,"¡Aviso!");

                                $('#card_crear_documento').hide();
                                $("#collapseOne").collapse('show');     
                                fn_grilla_despachados();

                            }
                            else
                            {
                                toastr.error(data.data.comentario,"¡Aviso!");
                            }
                        },
                        error: function (data, jqXHR, textStatus, errorThrown) {
                            toastr.error("Falla en la generación del archivo","¡Aviso!");
                        }
                    });
            }
        })      
    }

   
    function vista_previa(){

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
                if (result.value==true) {
                    $.ajax({
                        url: "/vista_previa",
                        type: 'GET',
                        dataType: 'binary',
                        data: {
                            _token:_token,
                            idDocumento:hiddIdDocumento,
                            idDocumentoBuzon:hiddIdDocumentoBuzon,
                            idBuzon:hiddIdBuzon             
                        },
                        xhrFields: {
                            responseType: 'blob'
                        },
                        success: function(response){
                            let blob = new Blob([response], {type: 'application/pdf'});                            
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(blob);
                            link.target = "_blank";
                            link.click();
                        },
                        error: function (data, jqXHR, textStatus, errorThrown) {
                            toastr.error("No es posible generar la vista previa.","¡Aviso!");
                        }
                    });
                }
        })      
    }

    function vista_previa_sg(){
        var _token = $("input[name='_token']").val();
        var tipo_documento = $("select[name='tipo_documento']").val();
        var materia = $("input[name='materia']").val();
        var encabezado = $("input[name='encabezado']").val();
        var cuerpo = editor_cuerpo.getData();
        urlAccion = "{{route('documentos.vista_previa_sg')}}";
        $.ajax({
            url: urlAccion,
            type: 'POST',
            dataType: 'json',
            
            data: {
                _token:_token,
                materia:materia,
                encabezado:encabezado,
                cuerpo:cuerpo,
            },
            success: function(data)
            {   
                Swal.close();            
                window.open('/vista_previa_sg/'+data.id_documento);
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en el documento","¡Aviso!");
                Swal.close();

                if($("#idAsignado").html() != "No Asignado"){
                    $('.btn-guardar-submit').html( 'Guardar y Cerrar' );
                }
                else{
                    $('.btn-guardar-submit').html( 'Guardar' );
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

    function accion_firmar(id_documento,id_documento_buzon,id_documento_buzon_padre){
        $('#titulo_accion').html('Ver Documento');         

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,2,33);         

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html(''); 
        habilita_boton('btn-vp');

        //listado de visaciones y firmas
        $('.row_txt_firmar').show();

        var buttonFirmar = '<button onClick="firmar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Firmar</button> ';
        $('#addButton').append(buttonFirmar);
        
    }

    function accion_finalizar(id_documento,id_documento_buzon,id_documento_buzon_padre){
        $('#titulo_accion').html('Ver Documento');         

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,2);

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html(''); 

        var buttonFinaliza = '<button onClick="finalizar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Finalizar</button> ';
        $('#addButton').append(buttonFinaliza);
        
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
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html(''); 

        var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Enviar</button> ';
        $('#addButton').append(buttonDerivar);
        
    }

    function archivar_recibidos(id_documento,id_documento_buzon,id_documento_buzon_padre,accion){

        $('#titulo_accion').html('Ver Documento');         

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre); 

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

        var buttonArchivar = '<button onClick="archivar_documento('+accion+')" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">'+txtArchivado[accion][0]+'</button> ';
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
                    toastr.error(data.data.comentario,"¡Aviso!");
                }
                else
                {
                    if(data.status=='200')
                    {
                        objDoc=data.data;
                        var json_tipo_doc = $.parseJSON(data.data.json_tipo_documento);
                        if (data.data.rel_documento_buzon[0]['contestar_hasta'] != null)
                        {
                            var fechaContestarHasta = data.data.rel_documento_buzon[0]['contestar_hasta'].split(' ');
                            $("input[name='contestar_hasta']").val(fechaContestarHasta[0]);
                        }
                        var idBuzon = $("input[name='hiddIdBuzon']").val();
                        var nFlujo = json_tipo_doc['id_tipo_flujo'];
                        var jsonAcciones = json_tipo_doc['buzones_flujo'];    
                        var jsonTipoAvance = json_tipo_doc['id_tipo_avance'];    
                        var jsonRespuesta = $.parseJSON(data.data.json_respuesta_a); 
                        var jsonDocResponder = data.data.rel_responder;
                        
                        datoTipoJson = json_tipo_doc;

                        $("select[name='tipo_documento']").val(data.data.id_tipo_documento);
                        $("select[name='nivel_acceso']").val(data.data.id_nivel_acceso);
                        $("select[name='efectos_terceros']").val(""+data.data.efectos_terceros+"");
   
                        $("input[name='materia']").val(data.data.materia);
                        $("input[name='anterior']").val(data.data.anterior);
                        $("textarea[name='descripcion']").val(data.data.descripcion);

                        $("input[name='encabezado']").val(json_tipo_doc['plantilla_encabezado']);
                        $("input[name='hiddIdOrigen']").val(json_tipo_doc['id_tipo_origen']);                        

                        editor_cuerpo.setData(data.data.cuerpo);

                        $("input[name='hiddIdDocumento']").val(data.data.id_documento);
                        $("input[name='hiddIdDocumentoBuzon']").val(id_documento_buzon);
                        $("input[name='hiddIdTipoDestino']").val(data.data.rel_documento_buzon_actual[0].id_tipo_destino);
                        $("#idAsignado").html("<b>"+data.data.identificador+"</b>");

                        if (data.data.folio != null)
                            $("#idFolio").html("<b>"+data.data.folio+"</b>");

                        if (data.data.fecha != null)
                            $("#idFecha").html("<b>"+data.data.fecha+"</b>");



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

                        $.each(relDatosBitacora, function(i, item)
                        {                    
                            if (item.id_accion == 6)
                                htmlDatosbitacora += "<div><b>Visado por: </b>" + item.nombres + ' ' + item.primer_apellido + ' - ' + item.nombre + ' - ' + moment(item.fecha).format('DD-MM-YYYY HH:mm') + '</div>';
                            
                            if (item.id_accion == 7)
                                htmlDatosbitacora += "<div><b>Firmado por: </b>" + item.nombres + ' ' + item.primer_apellido + ' - ' + item.nombre + ' - ' + moment(item.fecha).format('DD-MM-YYYY HH:mm') + '</div>';
                            
                            if (item.id_accion == 7 || item.id_accion == 8)
                                isDelete = false;
                            
                        });

                        $('#datos_bitacora_simple').html(htmlDatosbitacora);

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
                                    $('#form_acciones_solicitadas_el').append("<option value='"+accionesFlujo[i][0]+"' >"+accionesFlujo[i][1]+"</option>");
                            }

                            $('#form_acciones_solicitadas_el').multiselect('rebuild');
                            $('#form_acciones_solicitadas_el').multiselect('disable');

                            var bFlujo = false;

                            $.each(relDocumentoBuzon, function(i, item)
                            {                       
                                if (item.id_documento_buzon == id_documento_buzon_padre)
                                {                                   
                                    $('#bzOrigen').text(listadoBuzones[item.id_buzon]);
                                }
                                
                                if (item.id_tipo_destino == 1 && item.id_documento_buzon_padre == buzon_padre)
                                {
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
                               
                            }
                            //habilitar si tipo avance = 1
                            


                        }
                        else if(nFlujo == 1)  //flujo libre
                        {                           
                            if (accion == 1)
                            {
                                $('#form_destinatario_principal').prop("disabled", false);
                                $('#form_acciones_solicitadas_el').multiselect('enable');
                                $('#form_respuesta_a').multiselect('enable'); 

                                //quita accion 9 del listado
                                $("#form_acciones_solicitadas_el option[value='9']").remove();
                                $('#form_acciones_solicitadas_el').multiselect('rebuild');
                            } 

                            /* responder a */

                            //selecciona documentos en respuesta a
                            $('#form_respuesta_a').multiselect({numberDisplayed: 6});
                            $('#form_respuesta_a').multiselect('deselectAll', true);
                            
                            if (carpeta != 3 || (carpeta == 3 && accion == 0))
                                $('#form_respuesta_a').empty();

                            var sDivActualPrev = "";
                            var sDivActualNext = "";
                            var sDivIzq = "";
                            for (let j in jsonRespuesta) 
                            {                           
                                if (carpeta != 3 || (carpeta == 3 && accion == 0))
                                    $('#form_respuesta_a').append("<option selected value='"+jsonRespuesta[j]['id_documento']+"' >"+jsonRespuesta[j]['identificador'] +"-"+jsonRespuesta[j]['materia']+"</option>");
                                else
                                    $('#form_respuesta_a').multiselect('select', jsonRespuesta[j]['id_documento']);
                            
                                //completa carrusel lado izq
                                sDivIzq += ' <div class="item"><div class="item_display">'+jsonRespuesta[j]['identificador']+'<p>'+moment(jsonRespuesta[j]['created_at']).format('DD-MM-YYYY')+'</p></div></div>';                               
                            }

                            $('#form_respuesta_a').multiselect('rebuild');
                            $('#form_respuesta_a').multiselect('refresh');

                            //completar carrusel lado der
                            var sDivDer = "";
                            for (let d in jsonDocResponder)
                                sDivDer += ' <div class="item"><div class="item_display">'+jsonDocResponder[d]['identificador']+'<p>'+moment(jsonDocResponder[d]['created_at']).format('DD-MM-YYYY')+'</p></div></div>';
                                 
                            
                            sDivActual = '<div class="item"><div class="item_display item-doc">'+data.data.identificador+'<p>'+moment(data.data.created_at).format('DD-MM-YYYY')+'</p></div></div>';
                            
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


                            $.each(relDocumentoBuzon, function(i, item)
                            {                     
                                if (item.id_documento_buzon == id_documento_buzon_padre)
                                {                                   
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
                                if(item.id_tipo_destino == 1 && item.id_documento_buzon == buzon_padre && carpeta == 2){ 
                                    var accionesSolicitadas = $.parseJSON(item.json_acciones); 
                                    if(accion == 11){ //seleccion boton ver
                                        deshabilita_campos();
                                        if(item.id_estado_documento == 4){ //documento pendiente
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true); 
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');
                                            
                                            for (let i in accionesSolicitadas) { 
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']); 
                                                if (accionesSolicitadas[i]['id_accion'] == 4){ //editar   
                                                    var buttonEditar = '<button onClick="activar_editar(3)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-editar ">Editar</button> '; 
                                                    $('#addButton').append(buttonEditar);  
                                                }
                                                if (accionesSolicitadas[i]['id_accion'] == 6){ //visar                                                    
                                                    var buttonVisar = '<button onClick="guarda_destinatarios_documento(6)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-visar ">Visar</button> '; 
                                                    $('#addButton').append(buttonVisar); 
                                                } 
                                                if (accionesSolicitadas[i]['id_accion'] == 7){ //Firmar                                                    
                                                    var buttonFirmar = '<button onClick="firmar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-firmar ">Firmar</button> '; 
                                                    $('#addButton').append(buttonFirmar); 
                                                } 
                                                if(accionesSolicitadas[i]['id_accion'] == 11){ //derivar
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
                                                    var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-derivar ">Enviar</button> ';
                                                    $('#addButton').append(buttonDerivar);
                                                } 
                                                if (accionesSolicitadas[i]['id_accion'] == 10){ //finalizar                                                    
                                                    var buttonFinaliza = '<button onClick="finalizar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Finalizar</button> '; 
                                                    $('#addButton').append(buttonFinaliza); 
                                                } 
                                            } 
                                            var buttonArchivar = '<button onClick="archivar_documento(0)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Archivar</button> ';
                                                $('#addButton').append(buttonArchivar); 
                                        }//fin estado documento pendiente
                                        if(item.id_estado_documento == 6){ //documento archivado
                                            var buttonDesarchivar = '<button onClick="archivar_documento(1)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Desarchivar</button> ';
                                                $('#addButton').append(buttonDesarchivar); 
                                        }//fin estado archivado
                                        if(item.id_estado_documento == 9){ //documento firmado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true); 
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');
                                            
                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']); 
                                                if(accionesSolicitadas[i]['id_accion'] == 11){ //derivar
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
                                        }//fin estado firmado
                                        if(item.id_estado_documento == 11){ //visado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true); 
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');
                                            
                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']); 
                                                if(accionesSolicitadas[i]['id_accion'] == 11){ //derivar
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
                                        }//fin estaddo visado
                                    }//fin boton accion ver
                                    ///////////////////////////////
                                    if(accion == 1){ //seleccion boton editar
                                        deshabilita_campos();
                                        if(item.id_estado_documento == 4){ //documento pendiente
                                            $('.btn-guardar-submit').show();
                                            //$('.btn-enviar-submit').html('Guardar y Enviar');
                                            $('#submit-enviar').removeClass('');
                                            $('#submit-enviar').addClass('w-15');
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true); 
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');
                                            
                                            
                                            for (let i in accionesSolicitadas) { 
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']); 
                                                if (accionesSolicitadas[i]['id_accion'] == 6){ //visar                                                    
                                                    var buttonVisar = '<button onClick="guarda_destinatarios_documento(6)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-visar ">Visar</button> '; 
                                                    $('#addButton').append(buttonVisar); 
                                                } 
                                                if (accionesSolicitadas[i]['id_accion'] == 7){ //Firmar                                                    
                                                    var buttonFirmar = '<button onClick="firmar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-firmar ">Firmar</button> '; 
                                                    $('#addButton').append(buttonFirmar); 
                                                } 
                                                if(accionesSolicitadas[i]['id_accion'] == 11){ //derivar
                                                    $('#form_destinatario_principal').prop("disabled", false); 
                                                    $('#form_acciones_solicitadas_el').multiselect('enable'); 
                                                    $('#form_comentario_el').prop("disabled", false);         
                                                    $('#form_otros_destinatarios_el').prop("disabled", false); 
                                                    $('#form_comentario_otro_el').prop("disabled", false); 
                                                    $(".bootstrap-tagsinput-max").removeClass("disabled");
                                                    $(".bootstrap-tagsinput").removeClass("disabled"); 
                                                    var buttonDerivar = '<button onClick="guarda_destinatarios_documento(2)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-derivar-2 ">Guardar y Enviar</button> ';
                                                    $('#addButton').append(buttonDerivar);
                                                    $('#submit-enviar').removeClass('btn-primary'); 
                                                    $('#submit-enviar').addClass('btn-success'); 
                                                } 
                                                if (accionesSolicitadas[i]['id_accion'] == 10){ //finalizar                                                    
                                                    var buttonFinaliza = '<button onClick="finalizar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Finalizar</button> '; 
                                                    $('#addButton').append(buttonFinaliza); 
                                                } 
                                            } 
                                            var buttonArchivar = '<button onClick="archivar_documento(0)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Archivar</button> ';
                                                $('#addButton').append(buttonArchivar); 
                                        }//fin estado documento pendiente
                                        if(item.id_estado_documento == 6){ //documento archivado
                                            var buttonDesarchivar = '<button onClick="archivar_documento(1)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Desarchivar</button> ';
                                                $('#addButton').append(buttonDesarchivar); 
                                        }//fin estadi archivado
                                        if(item.id_estado_documento == 9){ //documento firmado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true); 
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            $('.btn-guardar-submit').show();
                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']); 
                                                if(accionesSolicitadas[i]['id_accion'] == 11){ //derivar
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
                                        }//fin estado firmado
                                        if(item.id_estado_documento == 11){ //visado
                                            $('.btn-guardar-submit').show();
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true); 
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');
                                            
                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']); 
                                                if(accionesSolicitadas[i]['id_accion'] == 11){ //derivar
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
                                        }//fin estado visado
                                    }//fin boton accion editar
                                    ///////////////////////
                                    if(accion ==22){ //seleccion boton visar
                                        $('.btn-recibir-submit').hide();
                                        $('.btn-enviar-submit').hide();
                                        deshabilita_campos();
                                        if(item.id_estado_documento == 4){ //documento pendiente  
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true); 
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            var buttonEditar = '<button onClick="activar_editar(3)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-editar ">Editar</button> '; 
                                            $('#addButton').append(buttonEditar);
                                            
                                            for (let i in accionesSolicitadas) { 
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']); 
                                                if(accionesSolicitadas[i]['id_accion'] == 11){ //derivar
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
                                                } 
                                                
                                                if (accionesSolicitadas[i]['id_accion'] == 7){ //Firmar                                                    
                                                    var buttonFirmar = '<button onClick="firmar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-firmar ">Firmar</button> '; 
                                                    $('#addButton').append(buttonFirmar); 
                                                } 
                                                if (accionesSolicitadas[i]['id_accion'] == 10){ //finalizar                                                    
                                                    var buttonFinaliza = '<button onClick="finalizar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Finalizar</button> '; 
                                                    $('#addButton').append(buttonFinaliza); 
                                                } 
                                            } 
                                            var buttonVisar = '<button onClick="guarda_destinatarios_documento(6)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-visar ">Visar</button> '; 
                                            $('#addButton').append(buttonVisar); 
                                            var buttonArchivar = '<button onClick="archivar_documento(0)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Archivar</button> ';
                                            $('#addButton').append(buttonArchivar); 
                                        }//fin estado documento pendiente
                                        if(item.id_estado_documento == 6){ //documento archivado
                                            var buttonDesarchivar = '<button onClick="archivar_documento(1)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Desarchivar</button> ';
                                                $('#addButton').append(buttonDesarchivar); 
                                        }//fin estadi archivado
                                        if(item.id_estado_documento == 9){ //documento firmado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true); 
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            $('.btn-guardar-submit').show();
                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']); 
                                                if(accionesSolicitadas[i]['id_accion'] == 11){ //derivar
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
                                        }//fin estado firmado
                                        if(item.id_estado_documento == 11){ //visado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true); 
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');
                                            
                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']); 
                                                if(accionesSolicitadas[i]['id_accion'] == 11){ //derivar
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
                                        }//fin estado visado
                                    }//fin boton accion visar
                                    ///////////////////////
                                    if(accion ==33){ //seleccion boton firmar
                                        $('.btn-recibir-submit').hide();
                                        $('.btn-enviar-submit').hide();
                                        deshabilita_campos();
                                        if(item.id_estado_documento == 4){ //documento pendiente  
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true); 
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            var buttonEditar = '<button onClick="activar_editar(3)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-editar ">Editar</button> '; 
                                            $('#addButton').append(buttonEditar);
                                            
                                            for (let i in accionesSolicitadas) { 
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']); 
                                                if(accionesSolicitadas[i]['id_accion'] == 11){ //derivar
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
                                                } 
                                                
                                                if (accionesSolicitadas[i]['id_accion'] == 7){ //Firmar                                                    
                                                    var buttonFirmar = '<button onClick="firmar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-firmar ">Firmar</button> '; 
                                                    $('#addButton').append(buttonFirmar); 
                                                } 
                                                if (accionesSolicitadas[i]['id_accion'] == 10){ //finalizar                                                    
                                                    var buttonFinaliza = '<button onClick="finalizar_documento()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Finalizar</button> '; 
                                                    $('#addButton').append(buttonFinaliza); 
                                                } 
                                            } 
                                            var buttonVisar = '<button onClick="guarda_destinatarios_documento(6)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-visar ">Visar</button> '; 
                                            $('#addButton').append(buttonVisar); 
                                            var buttonArchivar = '<button onClick="archivar_documento(0)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Archivar</button> ';
                                            $('#addButton').append(buttonArchivar); 
                                        }//fin estado documento pendiente
                                        if(item.id_estado_documento == 6){ //documento archivado
                                            var buttonDesarchivar = '<button onClick="archivar_documento(1)" type="button" class="btn text-nowrap btn-min-w  btn-success btn-archivar ">Desarchivar</button> ';
                                                $('#addButton').append(buttonDesarchivar); 
                                        }//fin estadi archivado
                                        if(item.id_estado_documento == 9){ //documento firmado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true); 
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');

                                            $('.btn-guardar-submit').show();
                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']); 
                                                if(accionesSolicitadas[i]['id_accion'] == 11){ //derivar
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
                                        }//fin estado firmado
                                        if(item.id_estado_documento == 11){ //visado
                                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true); 
                                            //quita accion 9 del listado
                                            $("#form_acciones_solicitadas_el option[value='9']").remove();
                                            $('#form_acciones_solicitadas_el').multiselect('rebuild');
                                            
                                            for (let i in accionesSolicitadas) {
                                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']); 
                                                if(accionesSolicitadas[i]['id_accion'] == 11){ //derivar
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
                                        }//fin estado visado
                                    }//fin boton accion firmar
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
                        let htmlFilePrincipal_va = '<div class="col-md-12 file-container-all">';
                        
                        aFilesPrincipal = [];
                        aFilesDelete = [];                  

                        $.each(relDocumentoBuzonArchivo, function(key,value)
                        { 
                           htmlFile = '<div class="file-container '+value.id_documento_buzon_archivo+'">'+
                                       ' <img src="/img/pdf_file.jpg" width="83" height=94" style="" />'+
                                        '<button onClick="ver_archivo(\''+value.nombre_archivo_codificado+'\')" type="button" class="btn text-nowrap btn-min-w  btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="View Details" style="margin-left: 3px;"><i class="fa fa-download"></i></button>'+
                                        '<p style="width: 90px!important;word-break: break-all;font-size: 12px;line-height: 1;margin-top: 15px;margin-bottom: 5px;">'+value.nombre_archivo_original+'</p>';

                            htmlFile_va = '<div class="file-container '+value.id_documento_buzon_archivo+'">'+
                                '  <img src="/img/pdf_file.jpg" width="83" height=94" style="" />'+
                                    '<button onClick="ver_archivo(\''+value.nombre_archivo_codificado+'\')" type="button" class="btn text-nowrap btn-min-w  btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="View Details" style="margin-left: 3px;"><i class="fa fa-download"></i></button>';
                            //if (carpeta == 2 && value.id_documento_buzon == id_documento_buzon && accion == 1)               
                            if (carpeta == 2 &&  accion == 1 && isDelete == true)               
                                htmlFile += '<button onClick="deleteFile(\''+value.id_documento_buzon_archivo+'\')" type="button" class="btn text-nowrap btn-min-w  btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="Eliminar pdf" style="margin-left: -27px;"><i class="fas fa-trash"></i></button>';
                            
                            if (carpeta == 3 && accion == 1)               
                                htmlFile += '<button onClick="deleteFile(\''+value.id_documento_buzon_archivo+'\')" type="button" class="btn text-nowrap btn-min-w  btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="Eliminar Pdf" style="margin-left: -27px;"><i class="fas fa-trash"></i></button>';
                            
                            if (carpeta == 3 && value.id_documento_buzon != id_documento_buzon)
                                htmlFile = "";                                 
                             
                            if (value.id_tipo_archivo == 2) //anexo
                                htmlFileAnexo += htmlFile + '</div>';       

                            if (value.id_tipo_archivo == 3) //otros
                                htmlFileOtros += htmlFile + '</div>'; 
                            
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

                        if (carpeta == 3)
                            $('#card_desplegar_versiones').hide();                        

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
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

    
    }

    function editar_despachados(id_documento,id_documento_buzon,id_documento_buzon_padre)
    {       
        $('#titulo_accion').html('Editar Documento'); 
        
        habilita_campos();
        isDelete = true;
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,3,1); 
        
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
    }

    function accion_editar(id_documento, id_documento_buzon,id_documento_buzon_padre)
    {
        $('#titulo_accion').html('Editar Documento'); 
        
        habilita_campos();
        isDelete = true;
        cargar_datos_grilla(id_documento, id_documento_buzon,id_documento_buzon_padre,2,1);

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

    function visualizar_documento_por_recibir(id_documento,id_documento_buzon,id_documento_buzon_padre, destino)
    {           
        $('#titulo_accion').html('Ver Documento'); 

        deshabilita_campos();
        cargar_datos_grilla(id_documento,id_documento_buzon,id_documento_buzon_padre,1,0);       
        
        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

        var buttonRecibir = '<button onClick="recibir_documento('+destino+')" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit ">Recibir</button>';
        $('#addButton').append(buttonRecibir);
    }

    function eliminar_despachados(id_documento,id_documento_buzon)
    {
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
                if (result.value==true) 
                {
                    $.ajax({
                        url: "/documento/",
                        type: 'delete',
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
                                toastr.success(data.data,"¡Aviso!");                                
                                fn_grilla_despachados();
                                $('#card_crear_documento').hide();
                                $("#collapseOne").collapse('show');  
                            }
                            else
                            {
                                toastr.error(data.data.comentario,"¡Aviso!");
                            }
                        },
                        error: function (data, jqXHR, textStatus, errorThrown) {
                            toastr.error("Falla en la eliminación del documento","¡Aviso!");
                        }
                    });
            }
        })             
        
     
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
                'columnDefs': [
                    {
                        'targets': 0,
                        'checkboxes': {
                            'selectRow': true
                        }
                    }
                ],
                'select': {'style': 'multi'},
                'order': [[1, 'asc']],
                columns: [
                    { data: 'id_documento', name: 'documento.id_documento',
                    },                    
                    { data: 'id_documento', name: 'documento.id_documento',
                    
                        render:function(data,type,row){
                            return "<a href='javascript:visualizar_documento_por_recibir("+row.id_documento+","+row.id_documento_buzon+","+row.id_documento_buzon_padre+","+row.id_tipo_destino+")'>"+data+"</a>";
                        }
                    
                    },                    
                    { data: 'fecha_envio', 
                            render: function(data)
                            {
                                if(data == null)
                                    return '';
                                else                                 
                                    return moment(data).format('DD-MM-YYYY HH:mm');
                            }
                    },
                    { data: 'materia', name: 'documento.materia','width':200,
                        render:function(data){
                            if(data==null){ return ''; }
                            return data.length > 60 ? data.substr( 0, 60 ) +'…' : data;
                        },
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
                    { data: 'contestas_hasta', 
                            render: function(data)
                            {
                                if(data == null)
                                    return '';
                                else                                 
                                    return moment(data).format('DD-MM-YYYY');
                            }
                    }
                    
                ],
                rowCallback: function (row, data, index ) {
                }
            });
            $('#grilla_por_recibir').on('error.dt', function(e, settings, techNote, message) {
                console.log( 'Error DataTables: ', message);
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
                order: [[ 5, 'desc' ]],        
                responsive: true  ,
                language: lenguaje_datatable,  
                'columnDefs': [
                    {
                        'targets': 0,
                        'checkboxes': {
                            'selectRow': true
                        }
                    },
                    {
                        'targets': 1,
                        'checkboxes': {
                            'selectRow': true
                        }
                    },
                    {
                        'targets': 2,
                        'checkboxes': {
                            'selectRow': true
                        }
                    }
                ],
                'select': {'style': 'multi'},
                buttons: [ 'copy', 'excel', 'pdf' ],          
                columns: [
                    { data: 'id_documento', name: 'documento.id_documento',
                        render: function(data, type, row)
                            {
                                if(row.id_estado_documento == 4 ){
                                    return '<input type="checkbox" class="dt-checkboxes chkArchivar" name="chkArchivar" id="chkArchivar" value="'+row.id_documento +'" />';
                                }
                                else{
                                    return '';
                                }                                   
                            }
                    }, 
                    { data: 'id_documento', name: 'documento.id_documento',
                        render: function(data, type, row)
                            {
                                if(row.id_estado_documento == 4 || row.id_estado_documento == 9 || row.id_estado_documento == 11){
                                    return '<input type="checkbox" class="dt-checkboxes chkDerivar" name="chkDerivar" id="chkDerivar" value="'+row.id_documento +'" />';
                                }
                                else{
                                    return '';
                                }                                   
                            }
                    }, 
                    {
                        data: 'estado_documento', name: 'estado_documento.nombre_corto', 
                        targets: 2,
                        searchable:false,
                        orderable:false,
                        className: 'dt-body-center',
                        render: function(data, type, row, full, meta){
                            if (type === 'display') 
                            {
                                if(data==null){
                                    return '';
                                }
                                else
                                {
                                    if (row.id_tipo_destino == 1) //principal
                                    {
                                        //agrega listado de acciones

                                        if(row.id_estado_documento != 6 && row.id_estado_documento != 5 && row.id_estado_documento != 7 && row.id_estado_documento != 8 && row.id_estado_documento != 10 && row.id_estado_documento != 12 && row.id_estado_documento != 13)
                                        {
                                            if (row.json_acciones != null)
                                            {
                                                var accionesSolicitadas = row.json_acciones
                                                
                                                accionesSolicitadas = $.parseJSON(accionesSolicitadas.replace(/(&quot\;)/g,"\""));                                                
                                                jsonTipoDoc = $.parseJSON(row.json_tipo_documento.replace(/(&quot\;)/g,"\""));                                                

                                                for (let i in accionesSolicitadas) 
                                                {                                                    
                                                    if (accionesSolicitadas[i]['id_accion'] == 7) //Firmar                                                   
                                                        return '<input class="dt-checkboxes" type="checkbox" name="checkFrm" value="'+row.id_documento+'-'+row.id_documento_buzon+'">';
                                                } 
                                            }                                                
                                            
                                        }  
                                    }
                                    return '';
                                }
                        }
                        return '';
                        }
                    },
                    { data: 'recibido',
                    render: function(data, type) {
                        if (type === 'display') {
                            if(data==null){
                                return '<div id="addChkFrm"></div>';
                            }else{
                                if(data==true){
                                    return '<span class="fas fa-check text-green"></span><div id="addChkFrm"></div>';
                                }
                            }
                        }
                        return '<div id="addChkFrm"></div>';
                        }
                    },
                    
                    { data: 'estado_documento', name: 'documento_buzon.id_estado_documento', 
                            render: function(data, type, row)
                            {
                                if (type === 'display') 
                                {
                                    let htmlColor = '<div class="fondo_estado" style=" background-color: '+ row.codigo_estado +';">'+data+'</div>';

                                    return htmlColor;
                                }
                                return data;                                   
                            }
                    },
                    { data: 'id_documento', name: 'documento.id_documento',
                        render:function(data,type,row){
                            return "<a href='javascript:ver_recibidos("+row.id_documento+","+row.id_documento_buzon+","+row.id_documento_buzon_padre+")'>"+data+"</a>";
                        }
                    },
                    
                    { data: 'fecha_envio_recepcion',
                            render: function(data)
                            {
                                return moment(data).format('DD-MM-YYYY HH:mm');
                            }
                    },
                    { data: 'materia', name:'documento.materia','width':200,
                        render:function(data){
                            if(data==null){ return ''; }
                            return data.length > 60 ? data.substr( 0, 60 ) +'…' : data;
                        }
                    },
                    { data: 'tipo_documento'},
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
                    { data: 'contestas_hasta', name: 'documento_buzon.contestar_hasta',
                            render: function(data)
                            {
                                if(data == null)
                                    return '';
                                else                                 
                                    return moment(data).format('DD-MM-YYYY');
                            }
                    },
                    {data:'folio',name: 'documento.folio'},
                    { data: 'id_documento', name:'documento.id_documento',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if(data==null){
                                return '';
                            }else{
                                let botonera = '<div class="dropdown">';
                                    botonera += '<button class="btn text-nowrap btn-min-w  btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                        botonera +=' <i class="fas fa-bars"></i>';
                                        botonera +=' </button>';
                                        botonera +='<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

                                        if (row.id_tipo_destino == 1) //principal
                                        {
                                            //agrega listado de acciones                                            
                                            if(row.id_estado_documento != 5 && row.id_estado_documento != 6 && row.id_estado_documento != 7 && row.id_estado_documento != 8 && row.id_estado_documento != 10 && row.id_estado_documento != 12 && row.id_estado_documento != 13)
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
                                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="accion_pdf('+data+','+row.id_documento_buzon+')"  href="#"><i class="fas fa-file text-blue"></i> '+accionesFlujo1[accionesSolicitadas[i]['id_accion']]+'</a>';
                                                        if (accionesSolicitadas[i]['id_accion'] == 10) //finalizar                                                   
                                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="accion_finalizar('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-file text-blue"></i> '+accionesFlujo1[accionesSolicitadas[i]['id_accion']]+'</a>';
                                                    } 
                                                }                                                
                                                
                                                if (jsonTipoDoc['id_tipo_flujo'] == 1) 
                                                    botonera +=' <a class="dropdown-item btn-menu-editar" onclick="responder_recibidos('+data+')" href="#"><i class="fas fa-reply text-orange"></i> Responder</a>';
                                                
                                                botonera +=' <a class="dropdown-item btn-menu-editar" onclick="derivar_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-share text-green"></i> Derivar</a>';
                                            }  

                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                                                                        
                                            if(row.id_estado_documento != 6 && row.id_estado_documento != 7 && row.id_estado_documento != 8 && row.id_estado_documento != 13)
                                                botonera +=' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+',0)"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>';

                                            if(row.id_estado_documento == 6)
                                                botonera +=' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+',1)"  href="#"><i class="fas fa-save text-blue"></i> Desarchivar</a>';
                                                    
                                            botonera +=' <a class="dropdown-item btn-menu-editar" onclick="bitacora('+data+')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';
                                        }
                                        else
                                        {
                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                            
                                            if(row.id_estado_documento != 6)
                                                botonera +=' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+',0)"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>';
                                            
                                            if(row.id_estado_documento == 6)
                                                botonera +=' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+',1)"  href="#"><i class="fas fa-save text-blue"></i> Desarchivar</a>';
                                            
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
                    $clearButton = $('<button class="btn text-nowrap btn-min-w  btn-secondary btn_cerrar_guardar btn_busqueda">')
                            .text('Limpiar')
                            .click(function() {
                                $('#gr_buscar_origen_materia').val('');
                                $('#gr_buscar_estado').multiselect('selectAll', true);
                                $('#gr_buscar_estado').multiselect('deselect', ["6"]);
                               
                                
                                $('#gr_buscar_id_doc').val('');
                                $searchButton.click();
                            }),
                    $searchButton = $('<button class="btn text-nowrap btn-min-w  btn-success buscar_btn_buscar">')
                            .text('Buscar')
                            .click(function() {
                                let estados=$('#gr_buscar_estado').val().join("|");
                                grilla_recibidos.columns(4).search(""+estados+"",true,false).draw();
                                grilla_recibidos.columns(5).search($('#gr_buscar_id_doc').val()).draw();
                                grilla_recibidos.columns(7).search($('#gr_buscar_origen_materia').val()).draw();    
                                grilla_recibidos.columns(8).search($('#gr_buscar_tipo_doc').val().join("|"),true,false).draw();
                            })
                            
                    $('#botones_grilla_recibidos').html('');
                    $('#botones_grilla_recibidos').append($clearButton,$searchButton);
                    $('#grilla_recibidos_filter').html('');      

                    if(aplicaFrm == 1)
                        $("div.addFrm").append("<input type='checkbox' name='chkFrm' id='chkFrm' onClick='addBtnFirma()'> Solo mostrar documentos por firmar <div class='btnFirma' id='btnFirma'></div>");
                    
                        //filtro por TD
                    $("div.addFrm").append("<select id='filtro-td' multiple><option>Principal</option><option>Secundario</option></select>");
                    $('#filtro-td').multiselect('select','Principal');
                    $('#filtro-td').on("change",function(){
                        grilla_recibidos.columns(9).search($('#filtro-td').val().join("|"),true,false).draw();
                    });
                    $('#filtro-td').trigger("change");
                    grilla_recibidos.column(0).visible(false);
                    grilla_recibidos.column(1).visible(false);
                    grilla_recibidos.column(2).visible(false);
                }
                
        });
        
        var column = grilla_recibidos.column(2); 
        column.visible( ! column.visible() ); 
        $('#grilla_recibidos').on('error.dt', function(e, settings, techNote, message) {
            console.log( 'Error DataTables: ', message);
        });   

        $('#grilla_recibidos .addFrm').append('<b>Custom tool bar! Text/images etc.</b>');                
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
            order: [[ 2, 'desc' ]],  
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
                { data: 'estado_documento', name: 'documento_buzon.id_estado_documento',
                            render: function(data, type, row)
                                        {
                                            if (type === 'display') 
                                            {
                                                let htmlColor = '<div class="fondo_estado" style=" background-color: '+ row.codigo_estado +';">'+data+'</div>';

                                                return htmlColor;
                                            }
                                            return data;                                   
                                        }
                },
                { data: 'identificador', name: 'documento.identificador' },
                { data: 'fecha_envio', 
                            render: function(data, type, row)
                            {
                                if(data == null)
                                    return '';
                                else
                                { 
                                    if (row.id_estado_documento == 2)
                                        return moment(data).format('DD-MM-YYYY HH:mm');
                                }

                                return '';
                            }
                },
                { data: 'fecha_recepcion',
                            render: function(data)
                            {
                                if(data == null)
                                    return '';
                                else    
                                    return moment(data).format('DD-MM-YYYY HH:mm');                           
                            }
                }, 
                { data: 'tipo_documento'},
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
                                botonera += '<button class="btn text-nowrap btn-min-w  btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
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
                    $clearButton = $('<button class="btn text-nowrap btn-min-w  btn-secondary btn_cerrar_guardar btn_busqueda">')
                            .text('Limpiar')
                            .click(function() {
                                $('#gd_buscar_destino_materia').val('');
                                $('#gd_buscar_estado').multiselect('selectAll', true);
                                $('#gd_buscar_estado').multiselect('deselect', ["A"]);
                                $('#gd_buscar_tipo_doc').multiselect('selectAll', true);
                                $('#gd_buscar_tipo_doc').multiselect('deselect', ["A"]);
                                $('#gd_buscar_id_doc').val('');
                                $searchButton.click();
                            }),
                    $searchButton = $('<button class="btn text-nowrap btn-min-w  btn-success buscar_btn_buscar">')
                            .text('Buscar')
                            .click(function() {
                                let estados=$('#gd_buscar_estado').val().join("|");
                                grilla_despachados.columns(1).search(""+estados+"",true,false).draw();
                                grilla_despachados.columns(2).search($('#gd_buscar_id_doc').val()).draw();
                                grilla_despachados.columns(5).search($('#gd_buscar_tipo_doc').val().join("|"),true,false).draw();
                                grilla_despachados.columns(7).search($('#gd_buscar_destino_materia').val()).draw();

                            })
                    $('#botones_grilla_despachados').html('');
                    $('#botones_grilla_despachados').append($clearButton,$searchButton);
                    $('#grilla_despachados_filter').html('');
                }
                

        });
        $('#despachados').on('error.dt', function(e, settings, techNote, message) {
            console.log( 'Error DataTables: ', message);
        }); 
    }

    function recepcion_masiva(){
        
        var rows_selected = grilla_por_recibir.column(0).checkboxes.selected();
        if(rows_selected.length > 0){
            $('.btn-recepcion-masiva').html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Recibiendo'
            );
            deshabilita_boton('btn-recepcion-masiva');
            Swal.fire({
                title: 'Recibir',
                html: "Se recepcionará(n) <b>"+rows_selected.length+"</b> Documento(s) <br>¿Desea Continuar?",
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
                }).then((result) => {
                    console.log(result);
                if (result.value==true) 
                {
                    var promiseArray = [];
                    $.each(rows_selected, function(index,obj){
                        $.each(grilla_por_recibir.rows().data(),function(idx,data){
                            //¡OJO! posible inconsistencia de datos
                            if(data.id_documento==obj){
                                var p = new Promise(function(resolve, reject){
                                    $.ajax({
                                            url: "/actualizar_estado_documento/"+data.id_documento_buzon,
                                            type: 'PUT',
                                            dataType: 'json',
                                            data: {
                                                _token:"{{csrf_token()}}",
                                                hiddIdDocumento:data.id_documento,
                                                buzon:data.id_buzon,
                                                destino:data.id_tipo_destino,
                                                accion:3
                                            },
                                            success: function(data)
                                            {
                                                console.log("success",data)
                                                if(data.status == '200'){ 
                                                    return resolve();
                                                    
                                                }else{
                                                    return reject();
                                                }
                                            },
                                            error: function (jqXHR, textStatus, errorThrown) {
                                                console.log(textStatus);
                                                reject(new Error('Error : ' + textStatus));
                                                $('.btn-recepcion-masiva').html('Recibir Masivo');
                                                habilita_boton('btn-recepcion-masiva');
                                            }
                                        });
                                });
                                promiseArray.push(p);
                                
                            }
                        });
                    });
                    Swal.fire({
                        title: 'Recepcionando documentos',
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        onOpen: () => {
                            swal.showLoading();
                        }
                    })
                    Promise.all(promiseArray).then(function(obj) {
                        Swal.close();
                        toastr.success("Documentos Recepcionados","¡Aviso!");
                        fn_grilla_por_recibir();    
                        window.location.reload();
                    });
                }
                else{
                    habilita_boton('btn-recepcion-masiva');
                    $('.btn-recepcion-masiva').html('Recibir Masivo');
                }
            }) 
        }
        else{
            toastr.error("No hay documentos seleccionados para recibir.","¡Aviso!");
        } 
    }

    function archivar_masiva(){
        let arr_chequeados = new Array();
        $(".chkArchivar").each(function() {
            if($(this).is(":checked")){
                arr_chequeados.push($(this).val())
            }
        });
        if(arr_chequeados.length > 0){    
            var rows_selected = grilla_recibidos.column(0).checkboxes.selected();
            $('.btn-aplicar').html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Archivando'
            );
            deshabilita_boton('btn-aplicar');

            Swal.fire({
                title: 'Archivar',
                input: 'textarea',
                inputPlaceholder: 'Ingrese fundamentación para archivar',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debe ingresar un fundamento'
                    }
                },
                html: "Se archivará(n) <b>"+arr_chequeados.length+"</b> Documento(s) <br>¿Desea Continuar?",
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
                }).then((result) => {
                    console.log(result);
                    if (result.value){//==true || result.value.length > 0) {
                        var promiseArray = [];
                        let comentario_archivo = $('.swal2-textarea').val();

                        $.each(rows_selected, function(index,obj){
                            $.each(grilla_recibidos.rows().data(),function(idx,data){
                                if(data.id_documento==obj){
                                    if(arr_chequeados.includes(''+data.id_documento+'')){
                                        var p = new Promise(function(resolve, reject){
                                            
                                            $.ajax({
                                                url: "/archivar_documento/"+data.id_documento_buzon,
                                                type: 'PUT',
                                                dataType: 'json',
                                                data: {
                                                    _token:"{{csrf_token()}}",
                                                    hiddIdDocumento:data.id_documento,
                                                    buzon:data.id_buzon,
                                                    comentario:comentario_archivo,
                                                    accion:"0"                
                                                },
                                                success: function(data)
                                                {
                                                    if(data.status == '200'){ 
                                                        return resolve();
                                                        
                                                    }else{
                                                        return reject();
                                                    }

                                                },
                                                error: function (jqXHR, textStatus, errorThrown) {

                                                    toastr.error("Falla en el documento","¡Aviso!");

                                                    habilita_boton('btn-aplicar');
                                                    $('.btn-aplicar').html('Aplicar');
                                                }
                                            });
                                        });
                                    }
                                    promiseArray.push(p);
                                }
                            });
                        });
                        Swal.fire({
                            title: 'Archivando documentos',
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                            onOpen: () => {
                                swal.showLoading();
                            }
                        })
                        Promise.all(promiseArray).then(function(obj) {
                            Swal.close();
                            toastr.success("Documentos Archivados","¡Aviso!");
                            fn_grilla_por_recibir();
                            window.location.reload();
                        });
                    }
                    else{
                        habilita_boton('btn-aplicar');
                        $('.btn-aplicar').html('Aplicar');
                    }
            })  
        }
        else{
            toastr.error("No hay documentos seleccionados para archivar.","¡Aviso!");
        }
    }

    function derivar_masiva(){
        let arr_chequeados_der = new Array();
        let continuar = 1;
        $(".chkDerivar").each(function() {
            if($(this).is(":checked")){
                arr_chequeados_der.push($(this).val())
            }
        });
        if(arr_chequeados_der.length > 0){
            let nSecundarios = 0;
            let nPrincipal = 0;
            var rows_selected = grilla_recibidos.column(1).checkboxes.selected();
            $.each(rows_selected, function(index,obj){
                $.each(grilla_recibidos.rows().data(),function(idx,data){
                    if(data.id_documento==obj){
                        if(arr_chequeados_der.includes(''+data.id_documento+'')){
                            if(data.id_tipo_destino == 1){
                                nPrincipal++;
                            }
                            if(data.id_tipo_destino == 2){
                                nSecundarios++;
                            }
                        }
                    }
                });
            });
            if(nSecundarios > 0 && nPrincipal > 0){//verificar que se haya seleccionado solo un tipo de destino
                continuar = 0;
                Swal.fire({
                    title: '<span style="font-size:30px"><i class="fa fa-exclamation-triangle fa-2x" aria-hidden="true" style="color:orange"></i><br/><strong>Aviso</strong></span>',
                    html:
                        '<p>Ud. ha seleccionado documentos principales y secundarios.</p><p>La funcionalidad permite derivar masivamente solo documentos principales o solo documentos secundarios.</p><p> Favor seleccione nuevamente.</p>',
                    icon: 'warning',
                    showCloseButton: true,
                    focusConfirm: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText:
                        'Aceptar'
                });
            }
            else{
                ///////
                if(arr_chequeados_der.length > 0 && continuar > 0){    
                    //var rows_selected = grilla_recibidos.column(1).checkboxes.selected();
                    $('.btn-aplicar').html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Derivando'
                    );
                    deshabilita_boton('btn-aplicar');
                    setTimeout(function() {
                        $('#fDerivarMasivaDestPpal').select2();
                        $("#fDerivarMasivaDestPpal").trigger('change');  
                        $('#fDerivarMasivaAcciones').multiselect('enable');
                        $('#fDerivarMasivaAcciones').multiselect({
                            nonSelectedText: 'Seleccione Acciones',
                            numberDisplayed: 6,
                            buttonWidth: '100%'
                        });
                        $("#fDerivarMasivaAcciones option[value='9']").remove();
                        $('#fDerivarMasivaAcciones').multiselect('rebuild');
                        if(nSecundarios > 0){
                            $('#fDerivarMasivaDestPpal').prop("disabled", true);
                            $('#fDerivarMasivaComPpal').prop("disabled", true);
                            $('#fDerivarMasivaAcciones').multiselect('disable');
                        }
                        $('#fDerivarMasivaDestOtros').tagsinput({
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

                    }, 300);
                    Swal.fire({
                        title: 'Derivar',
                        html: "<p>Se Derivará(n) <b>"+arr_chequeados_der.length+"</b> Documento(s) <br>¿Desea Continuar?</p>"+
                        "<br/>"+
                        "<form class='needs-validation text-left' id='fDerivarMariva' method='POST' action=''>"+
        "                       <div class='form-row'>"+
        "                            <div class='col-md-8 mb-3'>"+
        "                                <label for='inputState'>Destinatario Principal:</label><br>"+
        "                                <select class='form-control' style='width: 100%' id='fDerivarMasivaDestPpal' name='fDerivarMasivaDestPpal'>"+
        "                                   <option value=''>Seleccione</option>"+
        "                                    @foreach($allBuzones2 as $b)"+
        "                                        <option value='{{$b['id']}}'>{{$b['text']}}</option>"+
        "                                    @endforeach    "+
        "                                </select>"+
        "                            </div>"+
        "                            <div class='col-md-4 mb-3'>"+
        "                                <label for='inputState'>Acciones Solicitadas:</label><br>"+
        "                                <select id='fDerivarMasivaAcciones' class='form-control' multiple='multiple' style='text-align:left !important'>                                    "+
        "                                    @foreach($listadoAcciones as $accion)"+
        "                                        @if($accion['id_tipo_accion'] == 1)"+
        "                                            <option value='{{$accion['id_accion']}}'>{{$accion['nombre']}}</option>"+
        "                                        @endif    "+
        "                                    @endforeach    "+
        "                                </select>"+
        "                                </div>"+
        "                        </div>"+
        "                        <div class='form-row'>"+
        "                            <div class='col-md-12 mb-3'>"+
        "                                <label for='floatingTextarea'>Comentario a Destinatario Principal:</label>"+
        "                                <textarea class='form-control'  id='fDerivarMasivaComPpal' ></textarea>"+
        "                            </div>"+
        "                        </div>"+
        "                        <div class='form-row'>"+
        "                            <div class='col-md-12 mb-3'>"+
        "                                <label for='inputState'>Otro(s) Destinatario(s):</label>"+
        "                                <input type='text' class='form-control' id='fDerivarMasivaDestOtros' data-role='tagsinput' >"+
        "                            </div>"+
        "                        </div>"+
        "                        <div class='form-row'>"+
        "                            <div class='col-md-12 mb-3'>"+
        "                                <label for='floatingTextarea'>Comentario(s) Otro(s) Destinatario(s): </label>"+
        "                                <textarea class='form-control' id='fDerivarMasivaComOtro'></textarea>"+
        "                            </div>"+
        "                        </div>"+
        "                    </form>",
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Aceptar',
                        width:'950px'
                        }).then((result) => {
                            //console.log(result);
                            var destinatarioPrincipal = $('#fDerivarMasivaDestPpal').val();
                            var acciones_solicitadas = $('#fDerivarMasivaAcciones').val();
                            var otrosDestinatarios = $('#fDerivarMasivaDestOtros').val();
                            var comentarioPrincipal = $('#fDerivarMasivaComPpal').val();
                            var comentarioOtros  = $('#fDerivarMasivaComOtro').val();
                            let msgValidacion = "";
                            if(nPrincipal >0){
                                if(destinatarioPrincipal == ''){
                                    msgValidacion = msgValidacion + 'Debe seleccionar un destinatario principal.<br>';
                                }
                                if(acciones_solicitadas == ''){
                                    msgValidacion = msgValidacion + 'Debe seleccionar al menos una acción.<br>';
                                }
                            }
                            if(nSecundarios >0){
                                if(otrosDestinatarios == ''){
                                    msgValidacion = msgValidacion + 'Debe seleccionar un destinatario.<br>';
                                }
                            }
                            if (result.value){//==true || result.value.length > 0) {
                                if(msgValidacion == ''){
                                    var promiseArray = [];
                                    $.each(rows_selected, function(index,obj){
                                        $.each(grilla_recibidos.rows().data(),function(idx,data){
                                            if(data.id_documento==obj){
                                                if(arr_chequeados_der.includes(''+data.id_documento+'')){
                                                    console.log(data);
                                                    var hiddIdBuzon = data.id_buzon;
                                                    var hiddIdDocumento = data.id_documento;
                                                    var hiddIdDocumentoBuzon = data.id_documento_buzon;
                                                    var tipo_destino = data.id_tipo_destino;
                                                    console.log(tipo_destino);
                                                        var p = new Promise(function(resolve, reject){
                                                            $.ajax({
                                                                url: "{{route('buzones.update_documento')}}",
                                                                type: 'PUT',
                                                                dataType: 'json',
                                                                data: {
                                                                    _token:"{{csrf_token()}}",               
                                                                    buzon:data.id_buzon,
                                                                    destinatarioPrincipal:destinatarioPrincipal,
                                                                    destinatarioOtros:otrosDestinatarios,
                                                                    comentarioPrincipal:comentarioPrincipal,
                                                                    comentarioOtros:comentarioOtros,
                                                                    acciones_solicitadas:acciones_solicitadas,
                                                                    hiddIdDocumento:data.id_documento,
                                                                    hiddIdDocumentoBuzon:data.id_documento_buzon,
                                                                    carpeta:2,
                                                                    opcionGuardar:1,
                                                                    id_tipo_destino:tipo_destino 
                                                                },
                                                                success: function(data)
                                                                {
                                                                    if(data.status == '200')
                                                                    {
                                                                        console.log('tipo buzon: '+tipo_destino);
                                                                        var p2 = new Promise(function(resolve, reject){
                                                                        $.ajax({
                                                                            url: "../buzonesCarpetas/"+hiddIdDocumento,
                                                                            type: 'PUT',
                                                                            dataType: 'json',
                                                                            data: {
                                                                                _token:"{{csrf_token()}}",
                                                                                hiddIdDocumento:hiddIdDocumento,
                                                                                id_tipo_destino:tipo_destino,
                                                                                hiddIdDocumentoBuzon:hiddIdDocumentoBuzon,
                                                                                buzon:hiddIdBuzon,
                                                                                destinatarioPrincipal:destinatarioPrincipal,                            
                                                                                destinatarioOtros:otrosDestinatarios,
                                                                                acciones_solicitadas:acciones_solicitadas,
                                                                                carpeta:2                                    
                                                                            },
                                                                            success: function(data)
                                                                            {
                                                                                if(data.status == '200')
                                                                                {
                                                                                    toastr.success("Documento Derivado","¡Aviso!");
                                                                                }
                                                                                else
                                                                                {
                                                                                    toastr.error("Falla en la derivación del documento (2)","¡Aviso!");
                                                                                }
                                                                            },
                                                                            error: function (jqXHR, textStatus, errorThrown) {
                                                                                toastr.error("Falla en la derivación del documento","¡Aviso!");
                                                                                Swal.close();
                                                                                habilita_boton('btn-aplicar');
                                                                                $('.btn-aplicar').html('Aplicar');
                                                                            }
                                                                        });
                                                                        });
                                                                        promiseArray.push(p2);
                                                                    }
                                                                    else
                                                                    {
                                                                        toastr.error("Falla al guardar destinatarios","¡Aviso!");
                                                                    }
                                                                },
                                                                error: function (jqXHR, textStatus, errorThrown) {
                                                                    toastr.error("Falla en la actualización del documento","¡Aviso!");
                                                                }

                                                            });
                                                            Promise.all(promiseArray).then(function(obj) {
                                                                Swal.close();
                                                                toastr.success("Documentos Derivados","¡Aviso!");
                                                                fn_grilla_por_recibir();
                                                                //location.reload();
                                                            });
                                                        });
                                                }
                                                promiseArray.push(p);
                                            }
                                        })
                                    });
                                    Swal.fire({
                                        title: 'Derivando documentos',
                                        allowEscapeKey: false,
                                        allowOutsideClick: false,
                                        onOpen: () => {
                                            swal.showLoading();
                                        }
                                    })
                                    Promise.all(promiseArray).then(function(obj) {
                                        Swal.close();
                                        toastr.success("Documentos Derivados...","¡Aviso!");
                                        fn_grilla_por_recibir();
                                        window.location.reload();
                                    });
                                }
                                else{
                                    toastr.error(msgValidacion,"¡Aviso!");
                                    habilita_boton('btn-aplicar');
                                    $('.btn-aplicar').html('Aplicar');
                                }
                            }
                            else{
                                habilita_boton('btn-aplicar');
                                $('.btn-aplicar').html('Aplicar');
                            }
                    })  
                }
                else{
                    toastr.error("No hay documentos seleccionados para derivar.","¡Aviso!");
                }
                ///////
            }
        }
        else{
            toastr.error("No hay documentos seleccionados para derivar.","¡Aviso!");
        }
    }


    function seleccionarAccionMasiva(nOpcion){
        let opTotal = 3;
        let esVisible = "";
        if(nOpcion != ""){
            $('.btn-aplicar').show();
            for(let n=0;n<opTotal;n++){
                let  column = grilla_recibidos.column(n); 
                if(n==nOpcion){
                    esVisible = true;
                }
                else{
                    esVisible = false;
                }
                column.visible(esVisible);
            }
        }        
        else{
            $('.btn-aplicar').hide();
            for(let n=0;n<opTotal;n++){
                let  column = grilla_recibidos.column(n); 
                column.visible(false);
            }
        }
        
    }
    
    $('.btn-aplicar').click(function(){
        let accion = $('#selAccion').val();
        switch (accion){
            case "0":
                archivar_masiva();
                break;
            case "1":
                derivar_masiva();
                break;                
            case "2":
                envioFrm();
                break;        
        }
    });

    function activar_editar(nBotones){
        $('#form_tipo_documento').prop("disabled", true);
        $("#form_crear_editar :input").prop("disabled", false); 
        editor_cuerpo.setReadOnly(false); 
        $('#dropzone-principal').prop("disabled", false); 
        $('#dropzone-anexo').prop("disabled", false); 
        $('#dropzone-otros').prop("disabled", false); 
        $(".dz-hidden-input").prop("disabled", false);  
        
        if(nBotones == 1){
            $('.btn-guardar-submit').show();
        }
        if(nBotones == 2){
            $('.btn-guardar-submit-edit').show(); 
        }
        if(nBotones == 3){
            $('.btn-guardar-submit').show();
            $('.btn-guardar-submit-edit').show(); 
        }
        
        $('.btn-editar').hide();
        $('#form_tipo_documento').prop("disabled", true);
    }

    function vernotas(tipo){
        objDoc.rel_documento_buzon.sort(function(a, b){
                var nameA=a.fecha.toLowerCase(), nameB=b.fecha.toLowerCase()
                if (nameA < nameB)
                    return -1 
                if (nameA > nameB)
                    return 1
                return 0
        });
        let body = "<div>";
        $.each(objDoc.rel_documento_buzon,function(i,o){
            console.log(o);
            comentario = (tipo==1)?o.comentario_principal:o.comentario_secundario;
            if(comentario!=null){
                body =body+"<div class='card card-body'><p>Para <strong>"+listadoBuzones[o.id_buzon]+"</strong> ("+o.fecha+")<p/><p>Mensaje : "+o.comentario_principal+"</p></div>";
            }
        });
        body+="</div>";
        Swal.fire({
            title:"Mensajes Anteriores",
            html:body,
            width:"90%"

        });
    }

    function cargar_filtros_guardados(){


    }

    $(document).ready(function () {
        $(".nuevo_documento").prop("disabled", true);
        $('#fDerivarMasivaDestPpal').select2();

        $(function() {

            fn_grilla_por_recibir();
            fn_grilla_recibidos();
            fn_grilla_despachados();
            $('#gr_buscar_tipo_doc').multiselect({includeSelectAllOption: true,maxHeight: 400});
            $('#gr_buscar_tipo_doc').multiselect('selectAll', true);

            $('#gr_buscar_estado').multiselect({includeSelectAllOption: true,maxHeight: 400});
            $('#gr_buscar_estado').multiselect('selectAll', true);
            $('#gr_buscar_estado').multiselect('deselect', ["6"]);


            $('#gd_buscar_tipo_doc').multiselect({includeSelectAllOption: true,maxHeight: 300});
            $('#gd_buscar_tipo_doc').multiselect('selectAll', true);
            $('#gd_buscar_estado').multiselect({includeSelectAllOption: true,maxHeight: 300});
            $('#gd_buscar_estado').multiselect('selectAll', true);

        });
    });


</script>
@include('ckfinder::setup')
@stop