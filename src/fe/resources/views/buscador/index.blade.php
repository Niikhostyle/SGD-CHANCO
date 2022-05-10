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
            <div class=" form-row">   
                <div class="col-md-8 md-4">  
                <input class="form-control"  type="text" id="busqueda_simple" name="busqueda_simple" >
                </div>
                <div class="col-md-1 md-4">  
                    <i id="botones_busqueda_simple"></i>
                </div>
                <div class="col-md-3 md-4">  
                    <a href="#" class="btn btn-link desplegar_opciones_avanzadas">
                    <i class="fa fa-angle-double-down "></i> Búsqueda avanzada</a>
                    <a href="#" style="display:none" class="btn btn-link cerrar_opciones_avanzadas">
                    <i class="fa fa-angle-double-up "></i> Búsqueda simple</a>
                </div>
            </div> 
        </div>
    </div>
    <div class="card" id="card_opciones_avanzadas" style="display:none">
        <div class="card-body">
            <div class="form-row">
                <div class="col-md-2 md-2">
                    <div class="form-group">
                    <label for="id_documento">ID Documento: </label>
                        <input type="text" class="form-control" id="buscar_id_documento" name="buscar_id_documento">
                    </div>
                </div>
                <div class="col-md-5 md-5">
                    <div class="form-group">
                        <label for="select_tipo_documento" >Tipo Documento</label>
                        <br>
                        <select class="form-control" id="buscar_tipo_documento" name="buscar_tipo_documento" >
                            <option value="">Seleccionar</option>
                                @foreach($listado_tiposdoc as $list)
                                <option value="{{$list['nombre']}}">{{$list['nombre']}}</option>
                                @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-5 md-5 buscar_fila">
                    <div class="form-group">
                        <input type="checkbox" name="buscar_efectos_sobre_terceros" id="buscar_efectos_sobre_terceros" class="valign middle">
                        <label for="check_efectos_sobre_terceros">Efectos Sobre Terceros</label>
                    </div>
                    
                </div>
            </div>
            <div class="form-row">

                 <div class="col-md-2 md-2">
                    <div class="form-group">
                    <label for="id_folio">Folio: </label>
                        <input type="text" class="form-control" id="buscar_folio" name="buscar_folio">
                    </div>
                </div>
                <div class="col-md-1 md-1">
                    <div class="form-group">
                        <label for="">Rango de Fechas </label>                  
                        <input type="date" id="buscar_fecha_ini" name="buscar_fecha_ini" class="form-control">
                    </div>
                </div>
                
                <div class="col-md-1 md-1">
                    <div class="form-group">
                        <label for="">&nbsp;</label>                  
                        <input type="date" id="buscar_fecha_fin" name="buscar_fecha_fin" class="form-control">
                    </div>
                </div>     
                
                <div class="col-md-3 md-3">
                    <div class="form-group">
                        <label for="select_buzon_origen">Buzón Orígen</label>
                        <select  class="form-control" id="buscar_buzon_origen" name="buscar_buzon_origen" >
                            <option value="">Seleccionar</option>
                            @foreach($listBuzones as $list)
                            <option value="{{$list['nombre']}}">{{$list['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>                    
                </div>

                <div class="col-md-5 md-5 buscar_fila">
                    <div class="form-group">
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
                            <span id="boton_carpetas_texto"> Resultado de búsqueda  <i><b> </b></i> </span>
                            <i class="fa fa-chevron-circle-up" style="float:right;margin-top: 8px;"></i>
                            <i class="fa fa-chevron-circle-down" style="float:right;margin-top: 8px;"></i>
                        </button>
                        </h5>
                       
                    </div>

                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#carpetas">
                        
                            <div class="" id="card_buscador_grilla">
                                <div class="card-body">
                                    <table id="grilla_recibidos"  class="table dt-responsive nowrap no-footer dtr-inline dataTable collapsed" style="width:100%">
                                        <thead>
                                            <tr class="grilla_header">
                                                <th>ID</th>
                                                <th>Fecha</th>
                                                <th>TD</th>
                                                <th>Folio</th>
                                                <th>Buzón origen</th>
                                                <th>Buzón Actual</th>
                                                <th>Materia</th>
                                                <th>Efectos Terceros</th>
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
                        <tr class="grilla_header">
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Buzón Origen</th>
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
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
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

        .btn_busqueda {
            margin-right: 20px;
        }
    </style>

@stop

@section('js')


<script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script src="/js/fglobales.js"></script>
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
    
   
    $(document).ready(function () {

        $(function() {            

            fn_grilla_recibidos();
       
        });
        
       
    });

    owl = $('.owl-carousel').owlCarousel(); 
      
    $("#buscar_efectos_sobre_terceros").click(function(e){
        console.log($('#buscar_efectos_sobre_terceros').val());
        console.log($('#buscar_efectos_sobre_terceros').is(':checked'));
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

    async function fn_grilla_recibidos(){

        $('#grilla_recibidos tbody').empty();        

        grilla_recibidos = $('#grilla_recibidos').DataTable({
                dom: 'Brtip', 
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
                                    
                                    if (column > 7) {
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
                serverSide: false,
                ajax: '/buscadorListar',
                type:'json',
                order: [[ 0, 'desc' ]], 
                responsive: true,                
                language: lenguaje_datatable,
                columns: [
                    { data: 'identificador', name: 'identificador' },
                    { data: 'fecha_documento', render: function(data, type, row)
                            {
                                if(data == null)
                                    return '';
                                else
                                { 
                                    return data;//moment(data).format('DD-MM-YYYY');
                                }

                                return '';
                            }
                
                    },
                    { data: 'tipo_documento', name: 'tipo_documento' },
                    { data: 'folio', name: 'folio' },
                    { data: 'buzon_origen', name: 'buzon_origen' },
                    { data: 'buzon_actual', name: 'buzon_actual' },
                    { data: 'materia', name: 'materia' },
                    { data: 'efectos_terceros', searchable: true, visible: false,},
                    { data: 'id_documento',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            
                            if(data==null){
                                return '';
                            }else{
                                
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
                        }
                        return '';
                    }
                    }
                    
                ],
                initComplete : function() {
                    
                    var input = $('#busqueda_simple input').unbind(),
                    self = this.api(),
                    $clearButton = $('<button class="btn btn-secondary btn_cerrar_guardar btn_busqueda">')
                            .text('Limpiar')
                            .click(function() {
                                $('#buscar_id_documento').val('');
                                $('#buscar_folio').val('');
                                $('#buscar_tipo_documento').find('option:eq(0)').prop('selected', true);
                                $('#buscar_buzon_origen').find('option:eq(0)').prop('selected', true);
                                $('#buscar_fecha_ini').val('');
                                $('#buscar_fecha_fin').val('');
                                $('#buscar_efectos_sobre_terceros').prop('checked', false);

                                $searchButton.click();
                            }),
                    $searchButton = $('<button class="btn btn-success buscar_btn_buscar btn_busqueda">')
                            .text('Buscar')
                            .click(function() {
                                
                                    grilla_recibidos.draw();
                                
                                    grilla_recibidos.columns(0).search($('#buscar_id_documento').val()).draw();
                                    grilla_recibidos.columns(2).search($('#buscar_tipo_documento').val()).draw();
                                    grilla_recibidos.columns(3).search($('#buscar_folio').val()).draw();                               
                                    
                                    grilla_recibidos.columns(4).search($('#buscar_buzon_origen').val()).draw();  
                                    if ($('#buscar_efectos_sobre_terceros').is(':checked'))
                                        grilla_recibidos.columns(7).search($('#buscar_efectos_sobre_terceros').is(':checked')).draw(); 
                                    else
                                        grilla_recibidos.columns(7).search("true|false", true, false).draw(); 
                                

                            })
                    $simpleSearchButton = $('<button class="btn btn-success" id_btn_filtrar">')
                    .text('Buscar')
                    .click(function() {
                        self.search($('#busqueda_simple').val()).draw();
                        $(".btn_cerrar_guardar").trigger("click");
                    })
                    $('#botones_grilla_despachados').html('');
                    $('#botones_grilla_despachados').append($clearButton,$searchButton);
                    $('#botones_busqueda_simple').append($simpleSearchButton);
                    $('#grilla_despachados_filter').html('');
                    
                    
                }
                
               

        }); 
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

 
</script>
@stop