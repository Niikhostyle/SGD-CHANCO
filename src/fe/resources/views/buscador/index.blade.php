@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')

    
    <div class="row">
        <div class="col-8">
            <h1>Buscar Documentos</h1>
        </div>
    </div>
    
    <div class="linea_content_header"></div>
    <div class="card">
        <div class="card-body">
            <div class=" form-row">   
                <div class="col-md-8 md-4">  
                <input class="form-control"  type="text" id="busqueda_simple" name="busqueda_simple" placeholder="Folio o Materia.">
                </div>
                <div class="col-md-1 md-4">  
                    <i id="botones_busqueda_simple"></i>
                </div>
                <div class="col-md-3 md-4">  
                    <a href="#" class="btn btn-link desplegar_opciones_avanzadas">
                    <i class="fa fa-angle-double-down "></i> Búsqueda avanzadas</a>
                    <a href="#" style="display:none" class="btn btn-link cerrar_opciones_avanzadas">
                    <i class="fa fa-angle-double-up "></i> Búsqueda simple</a>
                </div>
            </div> 
        </div>
    </div>
    <div class="card" id="card_opciones_avanzadas" style="display:none">
        <div class="card-body">
            <div class="form-row">
                <div class="col-md-4 md-4">
                    <div class="form-group">
                    <label for="id_documento">ID Documento: </label>
                        <input type="text" class="form-control" id="buscar_id_documento" name="buscar_id_documento">
                    </div>
                </div>
                <div class="col-md-4 md-4">
                    <div class="form-group">
                        <label for="select_tipo_documento" >Tipo Documento</label>
                        <br>
                        <select class="form-control" id="buscar_tipo_documento" name="buscar_tipo_documento" >
                            <option value="">Seleccionar</option>
                                @foreach($listado_tiposdoc as $list)
                                <option value="{{$list['id_tipo_documento']}}">{{$list['nombre']}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 md-4">
                    <div class="form-group">
                        <label for="select_buzon_origen">Buzón Orígen</label>
                        <br>
                        <select  class="form-control" id="buscar_buzon_origen" name="buscar_buzon_origen" >
                            <option value="">Seleccionar</option>
                            @foreach($listBuzones as $list)
                            <option value="{{$list['id_buzon']}}">{{$list['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 md-4">
                    <div class="form-group">
                        <label for="">Rango de Fechas </label>
                    
                        <br>
                        <input type="date" id="buscar_fecha_ini" name="buscar_fecha_ini">
                        <input type="date" id="buscar_fecha_fin" name="buscar_fecha_fin">
                    </div>
                </div>
                
                
                <div class="col-md-4 md-4">
                    <div class="form-group">
                        <input type="checkbox" name="buscar_fectos_sobre_terceros" id="buscar_efectos_sobre_terceros" class="valign middle">
                        <label for="check_efectos_sobre_terceros">Efectos Sobre Terceros</label>
                    </div>
                </div>

                <div class="col-md-4 md-4">
                    <div class="form-group">
                        <br>
    
                        <i id="botones_grilla_despachados"></i>
                    </div>    
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
                            <span id="boton_carpetas_texto"> Documentos  <i><b> </b></i> </span>
                            <i class="fa fa-chevron-circle-up" style="float:right;margin-top: 8px;"></i>
                            <i class="fa fa-chevron-circle-down" style="float:right;margin-top: 8px;"></i>
                        </button>
                        </h5>
                        <div class="linea_content_header"></div>
                    </div>
                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#carpetas">
                        
                            <div class="card" id="card_buscador_grilla">
                                <div class="card-body">
                                    <table id="grilla_recibidos"  class="table dt-responsive nowrap no-footer dtr-inline dataTable collapsed" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Fecha</th>
                                                <th>TD</th>
                                                <th>Folio</th>
                                                <th>Buzón origen</th>
                                                <th>Buzón Actual</th>
                                                <th>Materia</th>
                                                <th>Acciones</th>
                                                <th></th>
                                    
                                            </tr>
                                        </thead>
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
                        <div class="container">
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
                                <div class="form-control">Buzón Origen: <i><span id="textBuzonorigen"></span></i></div>
                                <div class="form-control">ID: <i><span id="idAsignado">No Asignado</span></i></div>
                                <div class="form-control">Folio: <i>No Asignado</i></div>
                                <div class="form-control">Fecha: <i>No Asignado</i></div>
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
                        <div class="form-group row_anexo">
                            <label for="exampleFormControlTextarea1">Anexos:</label>
                            
                            <div class="card-body card-archivos" id="cargar_anexo">
                                <div id="dropzone-anexo-view" class="dropzone-view"></div>                                                                                        
                            </div>

                        </div>

                        <div class="form-group row_arch_ppal">
                            <label for="exampleFormControlTextarea1">Archivo Principal</label>
                            
                            <div class="card-body card-archivos" id="cargar_principal">
                                <div id="dropzone-principal-view" class="dropzone-view"></div>                                                                                
                            </div>

                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">Otros Archivos</label>
                            
                            <div class="card-body card-archivos" id="cargar_otros">
                                <div id="dropzone-otros-view" class="dropzone-view"></div>                                                                            
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
        <h4 id="titulo_ver_documento"class="card-header " >Bitácora</h5>
            <div class="col">ID: <i><span id="idAsignado"></span></i></div>
            <div class="col">Materia: <i><span id="textMateria"></span></i></div>
            <br>
          
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="id_buscar_ddp" name="buscar_ddp" checked="checked" >
                <label class="form-check-label" for="defaultCheck1">
                    Derivaciones destinatarios principales (DDP)
                </label>
            </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" value="2" id="id_buscar_doo" name="buscar_doo" checked="checked">
                <label class="form-check-label" for="defaultCheck1">
                    Dereivaciones otros destinatarios (DOO)
                </label>
                </div>
                <div class="form-check">
                <input class="form-check-input" type="checkbox" value="3" id="id_buscar_cap" name="buscar_cap" checked="checked">
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
                
            <div class="row">
                <div class="col-md-10"> </div>
                <div class="col-md-2">
                    <button type="button"  class="btn btn-secondary w-100 btn_cerrar_documento">Cerrar</button>
                </div>
            </div>  
        </div>
    

   <!-- Bitacora fin-->

    
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
<link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css"/>


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

        .card-archivos {
            display: flex;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .file-container-all { display: flex; }
        .file-container { position: relative; }
        .file-container img { display: block; }
        .fa-icon1 { position: absolute; bottom:0; left:0; }
        .fa-icon2 { position: absolute; bottom:0; left:30px; }
    </style>

@stop

@section('js')


<script src="{{ asset('/js/funciones.js') }}"></script>
<script src="{{ asset('/vendor/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script>

       /** function myFunction() {
            var checkBox = document.getElementById("id_buscar_ddp");
            var text = document.getElementById("text");
            if (checkBox.checked == true){
                text.style.display = "block";
            } else {
                text.style.display = "none";
            }
        } */
    var grilla_bitacora;
    var grilla_recibidos;
    const editor_cuerpo = CKEDITOR.replace('form_cuerpo');
    const listadoBuzones = @json($listadoBuzones);

    $(document).ready(function () {
    

        //var div_por_recibir_width = document.getElementById('nav-por-recibir').getBoundingClientRect().width;
        //$('#nav-despachados').attr("style","width:"+div_por_recibir_width+'px');
        //$('#nav-recibidos').attr("style","width:"+div_por_recibir_width+'px');

        $(function() {

            

            fn_grilla_recibidos();

        
        });
    });


           /**$('#tabla_favorito_grilla').DataTable({
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            language: lenguaje_datatable
        });**/
        
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

      //  $(".btn-menu-ver").click(function(e){
        //    $('#card_ver_documento').show();
            
            
        //});
        //$(".btn_cerrar_ver_documento").click(function(e){
          //  $('#card_ver_documento').hide();
        //});
        
        //fn_grilla_bitacora();
    

    

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

    $(".btn_cerrar_documento").click(function(e){
        //$('#card_documento').trigger("reset");
        $('#card_documento').hide();
        $('#card_bitacora').hide();
        $("#collapseOne").collapse('show');
       
    });

    async function fn_grilla_recibidos(){

        //$('#documento').hide();
        //if ( $.fn.DataTable.isDataTable('#grilla_recibidos') ) {
          //      $('#grilla_recibidos').DataTable().destroy();
       // }
        $('#grilla_recibidos tbody').empty();

        grilla_recibidos = $('#grilla_recibidos').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/buscadorListar',
                type:'json',
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true,
                language: lenguaje_datatable,
                columns: [
                    
                    { data: 'identificador', name: 'documento.identificador ' },
                    { data: 'fecha_documento', name: 'documento.fecha' },
                    { data: 'tipo_documento', name: 'tipo_documento.nombre' },
                    { data: 'folio', name: 'documento.folio' },
                    { data: 'origen', name: 'tipo_origen.nombre' },
                    { data: 'nombre_buzon', name: 'buzon.nombre' },
                    { data: 'materia', name: 'documento.materia ' },
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

                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="visualizar_documento('+row.id_documento+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';

                                                                                    


                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos('+row.id_documento+')"  href="#"><i class="fas fa-download text-blue"></i> Descargar</a>';
                                            
                                            
                                            
                                        
                                        

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
                    var input = $('#busqueda_simple input').unbind(),
                    self = this.api(),
                    $clearButton = $('<button class="btn btn-light buscar_btn_limpiar">')
                            .text('Limpiar')
                            .click(function() {
                                $('#buscar_id_documento').val('');
                                $('#buscar_tipo_documento').find('option:eq(0)').prop('selected', true);
                                $('#buscar_buzon_origen').find('option:eq(0)').prop('selected', true);
                                $('#buscar_fecha_ini').find('option:eq(0)').prop('date', true);
                                $('#buscar_fecha_fin').find('option:eq(0)').prop('date', true);
                                $('#buscar_fectos_sobre_terceros').find('option:eq(0)').prop('checked', true);
                                $searchButton.click();
                            }),
                    $searchButton = $('<button class="btn btn-success buscar_btn_buscar">')
                            .text('Buscar')
                            .click(function() {
                                grilla_recibidos.columns(0).search($('#buscar_id_documento').val()).draw();
                                grilla_recibidos.columns(2).search($('#buscar_tipo_documento').val()).draw();
                                //grilla_recibidos.columns(2).search($('#buscar_fecha_ini').val()).draw();
                                grilla_recibidos.columns(5).search($('#buscar_buzon_origen').val()).draw();
                                //grilla_recibidos.columns(2).search($('#buscar_fecha_fin').val()).draw();
                                grilla_recibidos.columns().search($('#buscar_fectos_sobre_terceros').val()).draw();
                                //self.search($('#gd_buscar_destino_materia').val()).draw();
                            })
                    $simpleSearchButton = $('<button class="btn btn-light" id_btn_filtrar">')
                    .text('Buscar')
                    .click(function() {
                        //grilla_recibidos.columns(4).search($('#busqueda_simple').val()).draw();
                        //grilla_recibidos.columns(6).search($('#busqueda_simple').val()).draw();
                       // grilla_recibidos.columns(2).search($('#busqueda_simple').val()).draw();
                        //grilla_recibidos.columns(3).search($('#busqueda_simple').val()).draw();
                       // grilla_recibidos.columns(5).search($('#busqueda_simple').val()).draw();
                        self.search($('#busqueda_simple').val()).draw();
                    })
                    $('#botones_grilla_despachados').html('');
                    $('#botones_grilla_despachados').append($clearButton,$searchButton);
                    $('#botones_busqueda_simple').append($simpleSearchButton);
                    $('#grilla_despachados_filter').html('');
                }

        });

       
        }

        // VER DOCUMENTO Y CARGAR BITACORA

    function visualizar_documento(id_documento, id_documento_buzon, id_documento_buzon_padre)
    {
        $(".print-error-msg").hide();

        if(id_documento > 0)
        {
            $("#collapseOne").collapse('hide');
            $('#card_documento').show();
            $('#card_bitacora').show();

            
            //deshabilita campos
            $('.form-disabled').prop("disabled", true);
            editor_cuerpo.setReadOnly(true);
            $('#form_acciones_solicitadas_el').multiselect('disable');
            $('#form_destinatario_principal_el').prop("disabled", true);
            $('#form_otros_destinatarios_el').prop("disabled", true);
            $(".bootstrap-tagsinput-max").addClass("disabled");
            $(".bootstrap-tagsinput").addClass("disabled");  

            cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre);//,id_documento_buzon_padre);
            cargar_datos_bitacora(id_documento, id_documento_buzon, id_documento_buzon_padre);

        }
    }
   
    function cargar_datos_bitacora(id_documento, id_documento_buzon, id_documento_buzon_padre)
    {
    
    //clear_form();
    $.getJSON('buscador/'+id_documento, function(response) {
    $('#tabla_bitacora_grilla').dataTable({
        processing: true,
        data: response.data,
        
        destroy: true,
        
        searching: false,
        columns: [
            { data: 'tipo_destino',
                render: function(data, row) {
                        
                    let textAbrv = data;

                    if (data == 1)
                        return 'P'; 
                    else 
                        return 'S';
                }
            },
            {data: 'fecha_documento'},
            {data: 'buzon_origen'},
            {data: 'nombre_accion'},
            {data: 'mensaje_respuesta'}
        ]
    });

    $("#idAsignado").text(response.data[0].identificador);
    $("#textMateria").text(response.data[0].materia);
    //window.someGlobalOrWhatever = response.balance
    });

    
    /* 
        $('#tabla_bitacora_grilla').dataTable( {
            "ajax": "buscador/"+id_documento,
            "columns":[
                {data: 'id_documento_buzon'},
                {data: 'fecha_documento'},
                {data: 'buzon_origen'},
                {data: 'nombre_accion'},
                {data: 'mensaje_respuesta'}
            ]                                   
        });
        */
        /*
        $.ajax({
            url: "buscador/"+id_documento,
            type:'GET',
            dataType: 'json',
            success: function(data) {
                if(data.status=='400') {
                    toastr.error(data.data.comentario,"Aviso!");
                }
                else
                {       var datos = data.data;
                        console.log(datos);
                        console.log(datos.buzon_origen);
                        $('#tabla_bitacora_grilla').dataTable( {
                            processing: true,
                            data: datos,
                            columns: [
                                {"data" : "DDP"},
                                {"data" : "datos.fecha_documento"},
                                {"data" : "datos.buzon_origen"},
                                {"data" : "datos.nombre_accion"},
                                {"data" : "datos.mensaje_respuesta"}            
                            ],
                        });
                    if(data.status == '200')
                    {
                        

                    }
                }
            },
            error: function (e) {
                console.log("ERROR");
                data = e.responseJSON;
                if (typeof data.errors !== 'undefined') {
                    printErrorMsg(data.errors);
                }
            }
        });
        */

    }

    function cargar_datos_grilla(id_documento, id_documento_buzon)
    {
        $.ajax({
            url: "/documentos/"+id_documento,
            type:'GET',
            dataType: 'json',
            data: {
                    hiddIdDocumentoBuzon:id_documento_buzon
                    },
            success: function(data) {
                if(data.status=='400') {
                    toastr.error(data.data.comentario,"Aviso!");
                }
                else
                {
                    if(data.status == '200')
                    {
                        var json_tipo_doc = $.parseJSON(data.data.json_tipo_documento);
                        var fechaContestarHasta = data.data.rel_documento_buzon[0]['contestar_hasta'].split(' ');
                        var idBuzon = $("input[name='hiddIdBuzon']").val();
                        var carpeta = "";
                        var idBuzonOrigen = "";

                        $.each(data.data.rel_documento_buzon, function(key,value)
                        {
                            if (value.id_documento_buzon == id_documento_buzon)
                            {
                                carpeta = value.id_carpeta;
                                idBuzonOrigen = value.id_buzon;
                            }
                        });

                        $("#textBuzonorigen").text(listadoBuzones[idBuzonOrigen]);
                        
                        $("select[name='tipo_documento']").prepend("<option value='"+json_tipo_doc['id_tipo_documento']+"' selected='selected'>"+json_tipo_doc['nombre']+"</option>");
                        $("select[name='nivel_acceso']").val(data.data.id_nivel_acceso);
                        $("select[name='efectos_terceros']").val(""+data.data.efectos_terceros+"");
                        $("input[name='contestar_hasta']").val(fechaContestarHasta[0]);
                        $("input[name='materia']").val(data.data.materia);
                        $("input[name='anterior']").val(data.data.anterior);
                        $("textarea[name='descripcion']").val(data.data.descripcion);                     

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

                        //archivos    
                        var relDocumentoBuzonArchivo = data.data.rel_archivos;

                        let htmlFile = "";
                        let htmlFileAnexo = '<div class="col-md-12 group-button-alig file-container-all">';
                        let htmlFileOtros = '<div class="col-md-12 group-button-align file-container-all">';
                        let htmlFilePrincipal = '<div class="col-md-12 group-button-align file-container-all">';

                        $.each(relDocumentoBuzonArchivo, function(key,value)
                        {   
                            htmlFile = '<div class="file-container '+value.id_documento_buzon_archivo+'">'+
                                        ' <img src="https://upload.wikimedia.org/wikipedia/commons/8/87/PDF_file_icon.svg" width="75" height=75" style="height:75px;" />'+
                                            '<a href="/imagenes/'+value.nombre_archivo_codificado+'" class="btn-descargar" target="_blank"><i class="fas fa-download fa-icon1"></i></a>';
                            
                            if (carpeta == 3 && value.id_documento_buzon != id_documento_buzon)
                                htmlFile = "";                                 
                                
                            if (value.id_tipo_archivo == 2) //anexo
                                htmlFileAnexo += htmlFile + '</div>';       

                            if (value.id_tipo_archivo == 3) //otros
                                htmlFileOtros += htmlFile + '</div>'; 
                            
                            if (value.id_tipo_archivo == 1) //principal
                                htmlFilePrincipal += htmlFile + '</div>'; 
    
                        });

                        $('#dropzone-principal-view').html(htmlFilePrincipal + '</div>');
                        $('#dropzone-anexo-view').html(htmlFileAnexo + '</div>');
                        $('#dropzone-otros-view').html(htmlFileOtros + '</div>');

                        //destinatarios

                        var relDocumentoBuzon = data.data.rel_documento_buzon;
                        
                        if (carpeta == 3 || carpeta == 2)
                            var buzon_padre = id_documento_buzon;
                        else
                            var buzon_padre = id_documento_buzon_padre; 
                            
                        $.each(relDocumentoBuzon, function(i, item)
                        {                       
                            if (item.id_tipo_destino == 1 && item.id_documento_buzon_padre == buzon_padre)
                            {
                                $('#form_destinatario_principal_el').tagsinput('add', {"value": item.id_buzon, "text": listadoBuzones[item.id_buzon]});
                                $("textarea[id='form_comentario_el']").val(item.comentario_principal);

                                //seleccionar acciones

                                var accionesSolicitadas = $.parseJSON(item.json_acciones);

                                console.log(accionesSolicitadas);
                                    
                                $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                                for (let i in accionesSolicitadas) {
                                    $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                                }
                            }

                            if (item.id_tipo_destino == 2 && item.id_documento_buzon_padre == buzon_padre)
                            {
                                $('#form_otros_destinatarios_el').tagsinput('add', {"value": item.id_buzon, "text": listadoBuzones[item.id_buzon]});
                                $("textarea[id='form_comentario_otro_el']").val(item.comentario_secundario);
                            }  
                        });

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

</script>
@stop