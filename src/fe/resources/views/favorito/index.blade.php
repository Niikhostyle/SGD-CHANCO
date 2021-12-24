@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')


<div class="row">
    <div class="col-12">
        <h1>Favoritos</h1>
    </div>    
</div>
<div class="linea_content_header"></div>

@stop

    
@section('content')    

<div class="row">
    <div class="col-12">
        <div class="accordion" id="carpetas">
            <div class="card">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                    <button class="btn btn-block text-left buzones_carpetas_btn_colapsable_text" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <span id="boton_carpetas_texto"> Favoritos </span>
                        <i class="fa fa-chevron-circle-up" style="float:right;margin-top: 8px;"></i>
                        <i class="fa fa-chevron-circle-down" style="float:right;margin-top: 8px;"></i>
                    </button>
                    </h2>

                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#carpetas">
                    <div class="card-body">

                        <div class=""> 
                            <div class="" id="card_favorito_grilla">
                                <div class="card-body">
                                    <table id="tabla_favorito_grilla" class="table dt-responsive nowrap" style="width:100%">
                                        <thead>
                                            <tr>
                                                
                                                <th>ID Doc</th>
                                                <th>Fecha Documento</th>
                                                <th>TD</th>
                                                <th>Origen</th>
                                                <th>Materia</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>                                        
                                            
                                            @foreach($lista_favoritos['data'] as $list)
                                            <tr >
                                                <td>{{$list['identificador']}}</td>
                                                <td>{{$list['fecha_documento']}}</td>
                                                <td>{{$list['tipo_documento']}}</td>
                                                <td>{{$list['buzon_origen']}}</td>
                                                <td>{{$list['materia']}}</td>                                                

                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fas fa-bars"></i>
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a class="dropdown-item btn-menu-ver" onclick="visualizar_documento({{$list['id_documento']}})"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>
                                                            <a class="dropdown-item btn-menu-deshabilitar" onclick="del_favorito({{$list['id_documento']}})" href="#"><i class="fas fa-trash-alt text-red"></i> Quitar</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
        
                        </div>
                    </div>
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
                Dereivaciones otros destinatarios (DOO)
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

<script src="{{ asset('/vendor/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script src="/js/fglobales.js"></script>

<script>

$(document).ready(function(){

    $('#tabla_favorito_grilla').dataTable({
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            language: lenguaje_datatable
        });


	$(".btn-menu-ver").click(function(e){
		$('#card_ver_documento').show();	
		
	});

});

const editor_cuerpo = CKEDITOR.replace('form_cuerpo');
const listadoBuzones = @json($listadoBuzones);

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


function visualizar_documento(id_documento)
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

function autoRefresh() {
    window.setTimeout(function(){
                        location.reload();
                    },2000);
}

</script>


@stop