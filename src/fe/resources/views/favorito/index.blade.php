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
                                            <tr class="grilla_header">
                                                
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
                    <h4 id="titulo_accion">Ver Documento <span id="BitIdAsignado"></span></h4>
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
                                    <ul class="list-group list-group-horizontal-md">
                                        <li class="list-group-item col-md-2 col-12"><b>Estado:</b> <br><i><span id="idAsignado">No Asignado</span></i></li>
                                        <li class="list-group-item col-md-2 col-12"><b>Folio:</b> <br><i><span id="idFolio">No Asignado</span></i></li>
                                        <li class="list-group-item col-md-2 col-12"><b>Fecha:</b> <br><i><span id="idFecha">No Asignado</span></i></li>
                                        <li class="list-group-item col-md-2 col-12 item-bitacora"><b>Bitácora:</b><br><p class=""><a class="btn btn-info btn-sm" id="boton-bitacora" href="javascript:void(0)" data-toggle="modal" data-target="#modalBitacoraSGD" data-id="" ><i class="fa fa-history"></i></a></p></li>
                                    </ul>
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

        .odd:hover, .even:hover{
            background: whitesmoke;
        }

    </style>
@stop

@section('js')

<script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script src="/js/fglobales.js?ver=1"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>

<script>

$(document).ready(function(){

    $('#tabla_favorito_grilla').dataTable({
            order: [[ 1, 'desc' ]], 
            responsive: true,
            language: lenguaje_datatable
        });


	$(".btn-menu-ver").click(function(e){
		$('#card_ver_documento').show();	
		
	});

});

const editor_cuerpo = CKEDITOR.replace('form_cuerpo');
const listadoBuzones = @json($listadoBuzones);

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
                    var jsonRespuesta = $.parseJSON(data.data.json_respuesta_a); 
                    var jsonDocResponder = data.data.rel_responder;
                    $('#form_respuesta_a').empty();
                    for (let j in jsonRespuesta) 
                    {                           
                        //completa carrusel lado izq
                        sDivIzq += ' <div class="item"><div class="item_display" ><a href="" onclick="visualizar_documento_alerta('+jsonRespuesta[j]['identificador']+','+id_buzon+','+idBuzonOrigen+',\''+jsonRespuesta[j]['materia']+'\')">'+jsonRespuesta[j]['identificador']+'</a><p>'+moment(jsonRespuesta[j]['created_at']).format('DD-MM-YYYY')+'</p></div></div>';                               
                        $('#form_respuesta_a').append("<option selected>"+jsonRespuesta[j]['identificador']+"-"+jsonRespuesta[j]['materia']+"</option>");

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

function autoRefresh() {
    window.setTimeout(function(){
                        location.reload();
                    },2000);
}

</script>


@stop