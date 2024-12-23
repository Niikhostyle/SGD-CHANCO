@extends('adminlte::page')

@section('title', 'Panel') 

@section('content_header')
@stop

@section('content')
   <div class="row" style="color:#fff">
        <div class="col-md-3">
            <div class="form-row m-3"  style="background-color: #6BB4BD;">
                <div class="row col-12">
                    <div class="col-12">
                            <h4>Documentos Tramitados</h4>
                    </div>
                </div>
                <div class="row col-12">
                    <div class="col-6 text-center p-2 link-inicio">
                        <a href="#" onclick="mostrar_categorias();" ><h2>{{$total_documentos}}</h2></a>
                    </div>
                    <div class="col-6 text-center p-2">
                        <i id="iconoDoctos" class="fa fa-file fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-row m-3"  style="background-color: #F47715;">
                <div class="row col-12">
                    <div class="col-12">
                        <h4>Usuarios</h4>
                    </div>
                </div>
                <div class="row col-12">
                    <div class="col-6 text-center p-2">
                    <h2>{{$total_usuarios}}</h2>
                    </div>
                    <div class="col-6 text-center p-2">
                        <i id="iconoUsuarios" class="fa fa-users fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-row m-3"  style="background-color: #74B677;">
                <div class="row col-12">
                    <div class="col-12">
                        <h4>Buzones</h4>
                    </div>
                </div>
                <div class="row col-12">
                    <div class="col-6 text-center p-2">
                    <h2>{{$total_buzones}}</h2>
                    </div>
                    <div class="col-6 text-center p-2">
                        <i id="iconoBuzones" class="fa fa-envelope fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-row m-3"  style="background-color: #D4DE89;">
                <div class="row col-12">
                    <div class="col-12">
                        <h4>Favoritos</h4>
                    </div>
                </div>
                <div class="row col-12">
                    <div class="col-6 text-center p-2 link-inicio">
                        @if($total_favoritos > 0)
                        <a href="/favoritos"  ><h2>{{$total_favoritos}}</h2></a>
                        @else
                            <h2>{{$total_favoritos}}</h2>
                        @endif
                    </div>
                    <div class="col-6 text-center p-2">
                        <i id="iconoFavoritos" class="fa fa-star fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
   </div>
   <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class=" form-row" id="panelBuscador">   
                        <div class="row col-12">
                            <div class="col-md-5 mb-1">
                                <input class="form-control" type="text" name="buscar_docto" id="buscar_docto" placeholder="Buscar documento por materia, descripción, folio o ID">
                            </div>
                            <div class="col-md-1 mb-1">
                                <button class="btn btn-success" id="btnBuscarDocto">Buscar</button>
                            </div>
                            <div class="col-md-5 mb-1">
                                <input class="form-control" type="text" name="buscar_contacto" id="buscar_contacto" placeholder="Buscar contacto por nombre, cargo o correo">
                            </div>
                            <div class="col-md-1 mb-1">
                                <button class="btn btn-success" id="btnBuscarContacto">Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="accordion" id="carpetas" style="display:none;">
                <div class="card">
                    <div class="card-header" id="headingOne">
                        <h2 class="mb-0">
                        <button class="btn text-nowrap btn-min-w btn-block text-left buzones_carpetas_btn_colapsable_text" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <span id="boton_carpetas_texto">Resultado de búsqueda</b></i></span>
                            <i class="fa fa-chevron-circle-up" style="float:right;margin-top: 8px;"></i>
                            <i class="fa fa-chevron-circle-down" style="float:right;margin-top: 8px;"></i>
                        </button>
                        </h2>
                    </div>
                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#carpetas">
                        <div class="card-body">
                            <div class="col-12" id="contenedor_tabla_doctos" style="display:none;">
                                <table id="grilla_documentos"  class="table dt-responsive nowrap no-footer dtr-inline dataTable collapsed" style="width:100%">
                                    <thead>
                                        <tr class="grilla_header">
                                            <th data-priority="0">ID</th>
                                            <th>TD</th>
                                            <th data-priority="1">Materia</th>
                                            <th data-priority="2">Fecha DOC</th>
                                            <th data-priority="1">Folio</th>
                                            <th data-priority="1">Fecha creación</th>
                                            <th data-priority="2">Buzón origen</th>
                                            <th data-priority="2">Buzón Actual</th>
                                            <th>Efectos Terceros</th>
                                            <th>Acciones</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-12 table-responsive" id="contenedor_tabla_contacto" style="display:none;">
                            <!-- <table id="grilla_contactos" class="table dt-responsive nowrap no-footer dtr-inline dataTable collapsed" style="width:100%;"> -->
                                <table id="grilla_contactos" style="width:100%;">
                                    <thead>
                                        <tr class="">
                                            <th class="grilla_header p-2">Nombre</th>
                                            <td>&nbsp;</td>
                                            <th class="grilla_header p-2 ">Cargo</th>
                                            <td>&nbsp;</td>
                                            <th class="grilla_header p-2">Correo Electrónico</th>
                                            <td>&nbsp;</td>
                                            <th class="grilla_header p-2r">Contacto</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12" id="contenedor_tabla_categorias" style="display:none;">
                                <table class="tabla_categorias" id="grilla_categorias1"  style="float:left;">
                                    <thead>
                                        <tr>
                                            <th class="grilla_header p-2" style="width:80%">Tipo de Documento</th>
                                            <td style="width:5%">&nbsp</td>
                                            <th class="grilla_header p-2" style="width:15%">Cantidad</th>
                                            <td>&nbsp</td>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <table class="tabla_categorias" id="grilla_categorias2"  style="float:left;">
                                    <thead>
                                        <tr>
                                            <th class="grilla_header p-2" style="width:80%">Tipo de Documento</th>
                                            <td style="width:5%">&nbsp</td>
                                            <th class="grilla_header p-2" style="width:15%">Cantidad</th>
                                            <td>&nbsp</td>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                <table class="tabla_categorias" id="grilla_categorias3"  style="float:left;">
                                    <thead>
                                        <tr>
                                            <th class="grilla_header p-2" style="width:80%">Tipo de Documento</th>
                                            <td style="width:5%">&nbsp</td>
                                            <th class="grilla_header p-2" style="width:15%">Cantidad</th>
                                            <td>&nbsp</td>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card" id="card-logo">
                <div class="card-body">
                    <div class="row text-right" id="logo4_panel">
                        <div class="col-12">
                            <img class="logo_panel" src="{{ asset(env('CODIGO_SGD').'/img/logo4.png') }}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                    <ul class="list-group list-group-horizontal">
                                        <li class="list-group-item col-md-6"><b>Buzón Origen:</b> <i><span id="textBuzonorigen"></span></i></li>
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
                                    <select id="form_respuesta_a" name="respuesta_a" class="form-control form-disabled">                                  
                                    </select>
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
                                <label for="exampleFormControlTextarea1">Otros Archivos</label>
                                
                                <div class="card-body card-archivos" id="cargar_otros">
                                    <div id="dropzone-otros-view" class="dropzone-view"></div>
                                    <div id="dropzone-otros" class="dropzone-files dropzone-none"></div>   
                                                                                              
                                </div>
    
                            </div>
    
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
                <table id="tabla_bitacora_grilla" class="table dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr class="grilla_header">
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
        </div>
        <div class="row ">
            <div class="col-md-10"> </div>
            <div class="col-md-2">
                <p><button style="display:none" type="button" class="btn btn-secondary w-100 btn_cerrar_guardar">Cerrar</button></p>
            </div>
        </div>
</div>
@stop

@section('css')
    <style>
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
        .tabla_categorias{
                width:33%;
        }
        @media (max-width: 768px) {
            .tabla_categorias{
                width:100%;
            }
        }

        .link-inicio a{
            color:#fff !important;
            text-decoration: none;
        }
        .link-inicio a:hover{
            text-decoration: underline;
        }

    </style>
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
    <link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">

@stop

@section('js')
    <script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
    <script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="/js/bootstrap-multiselect.js"></script>
    <script src="/js/fglobales.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script>
        var grilla_documentos;
        const editor_cuerpo = CKEDITOR.replace('form_cuerpo');
        owl = $('.owl-carousel').owlCarousel(); 
        const listadoBuzones = @json($listadoBuzones);

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

        $('input[name="buscar_accion"]').on('change', function () 
        {
            var types = $('input:checkbox[name="buscar_accion"]:checked').map(function() {
                return '^' + this.value + '\$';
            }).get().join('|');
            gridBitacora.fnFilter(types, 0, true, false, false, false);
        });
        
        $(document).ready(function () {
            setTimeout(function() { 
                animar_iconos('iconoDoctos',10000,'fa-pulse',1,1);
            }, 2000);
            setTimeout(function() { 
                animar_iconos('iconoUsuarios',15000,'fa-spin',0,1);
            }, 7000);
            setTimeout(function() { 
                animar_iconos('iconoFavoritos',8500,'fa-spin',1,0);
            }, 9000);
            setTimeout(function() { 
                animar_iconos('iconoBuzones',11000,'fa-pulse',0,1);
            }, 6000);

            $('#contenedor_tabla_doctos').hide();
            $('#contenedor_tabla_contacto').hide();
            $('#contenedor_tabla_categorias').hide();
            $('#carpetas').hide();
            $('#grilla_documentos').DataTable({
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
                                exportOptions: { 
                                    columns: function(column, data, node) {
                                        
                                        if (column > 9) {
                                            return false;
                                        }
                                        return true;
                                    }
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
                "lengthMenu": [ [10, 25, 50, 100, -1 ], [10, 25, 50, 100, "Todos"]],
                ajax: '/buscadorListar?busqueda_simple=xyza',
                order:[[0,'DESC'], [4,'asc'] ],
                language: lenguaje_datatable,
                columns: [
                        { data: 'identificador', name: 'identificador' },
                        { data: 'tipo_documento', name: 'tipo_documento' },
                        { data: 'materia', name: 'documento.materia',render: function(data, type, row)
                            {
                               if(data!=null){
                                    if(data.length > 50){
                                        return data.substring(0,50)+"...";
                                    }
                                    else{
                                        return data;
                                    }     
                                }else{
                                    return data;
                                }      
                            },
                        },
                        { data: 'fecha_documento_firma',data: 'fecha_documento_firma', render: function(data, type, row)
                                {
                                    if(data == null)
                                        return '';
                                    else
                                    { 
                                        return moment(data).format('DD-MM-YYYY');
                                    }

                                    return '';
                                }
                    
                        },

                        
                        { data: 'folio', name: 'folio' },
                        { data: 'fecha_documento',data: 'fecha_documento', render: function(data, type, row)
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
                        { data: 'buzon_origen', name: 'buzon_origen' },
                        { data: 'buzon_actual', name: 'buzon_actual' },                    
                        { data: 'efectos_terceros', searchable: true, visible: false,},
                        { data: 'id_documento', name: 'descarga',
                            render:function(data, type, row){

                                if(row.id_nivel_acceso == 1){
                                    return "<a href='descargar_docto?idDocumento="+data+"' target='_blank'>Descargar</a>";
                                }
                                else{
                                    return '';
                                }
                            }
                        },
                        { data: 'id_documento',
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
            var table = $('#grilla_documentos').DataTable();
    });

    $('#btnBuscarDocto').click(function() {
        $('#card_documento').hide();
        $('#card_bitacora').hide();	
        $('#card-logo').hide();
        $('.btn_cerrar_guardar').hide();
        $("#collapseOne").collapse('show');
        $('#carpetas').show();
        $('#logoPLC').hide();
        $('#contenedor_tabla_contacto').hide();
        $('#contenedor_tabla_categorias').hide();
        $('#contenedor_tabla_doctos').show();
        $('#boton_carpetas_texto').html("Resultado de la Búsqueda");
        let busqueda_simple = $('#buscar_docto').val();
        if(busqueda_simple !=""){
            $('#grilla_documentos').DataTable().ajax.url('/buscadorListar?busqueda_simple='+busqueda_simple+'&inicio=1').load();
        }
        else{
            $('#grilla_documentos').DataTable().ajax.url('/buscadorListar?busqueda_simple=123xyza&inicio=1').load();
        }
    });

    $('#btnBuscarContacto').click(function() {
        $('#card_documento').hide();
        $('#card_bitacora').hide();	
        $('#card-logo').hide();
        $('.btn_cerrar_guardar').hide();
        $("#collapseOne").collapse('show');
        $('#carpetas').show();
        $('#logoPLC').hide();
        $('#contenedor_tabla_doctos').hide();
        $('#contenedor_tabla_categorias').hide();
        $('#contenedor_tabla_contacto').show();
        $('#boton_carpetas_texto').html("Resultado de la Búsqueda");
        var _token = $("input[name='_token']").val();
        var texto = $("#buscar_contacto").val();
        $("#grilla_contactos").find("tr:gt(0)").remove();
        let filaInicial = "<tr><td class='p-2 text-center' style='font-size: 30px;' colspan='7'>Procesando...<span class='spinner-border spinner-border-xl' role='  status' aria-hidden='true'></span></td></tr>";
        $("#grilla_contactos tbody").append(filaInicial);
        let fila = "";
        let nroFila = 0;
        urlAccion = "{{route('usuarios.buscar')}}";
        $.ajax({
            url: urlAccion,
            type: 'POST',
            data: {
                _token:_token,
                texto:texto            
            },
            success: function(data){   
                $("#grilla_contactos").find("tr:gt(0)").remove();
                if(data.length > 0){
                    
                    $.each(data, function(i, val) {
                        nroFila++;
                        var color= '#fff';
                        if(isEven(nroFila)){
                            var color= '#f4f6f9';
                        }
                        let cargo = val.cargo
                        let contacto = val.numero_contacto
                        if( cargo === null){
                            cargo = 'Sin información';
                        }
                        if( contacto === null){
                            contacto = 'Sin información';
                        }
                        fila = "<tr><td class='p-2' style='background-color:"+color+";'>"+val.nombres+" "+val.primer_apellido+" "+val.segundo_apellido+"</td><td>&nbsp;</td><td class='p-2' style='background-color:"+color+";'>"+cargo+"</td><td>&nbsp;</td><td class='p-2'  style='background-color:"+color+";'>"+val.email+"</td><td>&nbsp;</td><td class='p-2' style='background-color:"+color+";'>"+contacto+"</td></tr>";
                        $("#grilla_contactos tbody").append(fila);
                    })
                }
                else{
                    $("#grilla_contactos tbody").append("<tr><td class='p-2 text-center' style='border-bottom:1px solid #333;border-top:1px solid #333;' colspan='7'>Ningún dato disponible en esta tabla</td></tr>")
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en la búsqueda","¡Aviso!");
            }
        
        });
        
    });

    function mostrar_categorias(){
        $('#card_documento').hide();
        $('#card_bitacora').hide();	
        $('.btn_cerrar_guardar').hide();
        $('#card-logo').hide();
        $("#collapseOne").collapse('show');
        $('#carpetas').show();
        $('#logoPLC').hide();
        $('#contenedor_tabla_doctos').hide();
        $('#contenedor_tabla_categorias').show();
        $('#contenedor_tabla_contacto').hide();
        $('#boton_carpetas_texto').html("Documentos Tramitados");
        var _token = $("input[name='_token']").val();
        urlAccion = "{{route('buscador.categorias')}}";
        $("#grilla_categorias1").find("tr:gt(0)").remove();
        $("#grilla_categorias2").find("tr:gt(0)").remove();
        $("#grilla_categorias3").find("tr:gt(0)").remove();
        let idTabla = 1;
        let filaInicial = "<tr><td class='p-2 text-center' style='font-size: 30px;' colspan='4'>Procesando...<span class='spinner-border spinner-border-xl' role='  status' aria-hidden='true'></span></td></tr>";
        $("#grilla_categorias2 tbody").append(filaInicial);

        $.ajax({
            url: urlAccion,
            type: 'GET',
            data: {
                _token:_token         
            },
            success: function(data){  
                let nTotalRegistros = data.length;
                let nRegistrosPorTabla = Math.round(nTotalRegistros/3);
                if(nTotalRegistros >= 4){
                    $("#grilla_categorias"+idTabla).find("tr:gt(0)").remove();
                }
                else{
                    $("#grilla_categorias2").find("tr:gt(0)").remove();
                }
                let nroFila = 0;
                if(data.length > 0){
                    $.each(data, function(i, val) {
                        nroFila++;
                        var color= '#fff';
                        if(isEven(nroFila)){
                            var color= '#f4f6f9';
                        }
                        fila = "<tr><td class='p-2' style='background-color:"+color+";'>"+val.tipo+"</td><td>&nbsp</td><td class='p-2 text-right' style='background-color:"+color+";'>"+val.total+"</td><td>&nbsp</td></tr>";
                        $("#grilla_categorias"+idTabla+" tbody").append(fila);
                        if(nroFila % nRegistrosPorTabla == 0){
                            idTabla++;
                            $("#grilla_categorias"+idTabla).find("tr:gt(0)").remove();
                        }
                    })
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en la búsqueda","¡Aviso!");
            }
        
        });
    }


    function isEven(n) {
        return n % 2 == 0;
    }

    function isOdd(n) {
        return Math.abs(n % 2) == 1;
    }

    function animar_iconos(idIcono,tiempo,clase,girar,agrandar){
        if(girar == 1){
            $('#'+idIcono).addClass(clase);
        }
        if(agrandar == 1){
            $('#'+idIcono).removeClass("fa-3x");
            $('#'+idIcono).addClass("fa-2x");
        }
        setTimeout(function() { 
            if(girar == 1){
                $('#'+idIcono).removeClass(clase);
            }
            if(agrandar == 1){
                $('#'+idIcono).removeClass("fa-2x");
                $('#'+idIcono).addClass("fa-3x");
            }
        }, 2000);
        setTimeout(function() { 
            animar_iconos(idIcono,tiempo,clase,girar,agrandar);
        }, tiempo);
    }

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

            cargar_datos_grilla(id_documento);
            cargar_datos_bitacora(id_documento);

        }
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
                //window.location = '/files/principal_191_.pdf' ;
                if(data.status=='200')
                    {

                        //window.location = (data.data.data);
                        window.open(data.data.data, 'Download');
                    }
               
                     
                
            }
        });
         
             
    }

    </script>

@stop