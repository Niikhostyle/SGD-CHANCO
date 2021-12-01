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
                                                <th>Buzón</th>
                                                <th>Estado</th>
                                                <th>ID Doc</th>
                                                <th>Fecha Documento</th>
                                                <th>TD</th>
                                                <th>Origen</th>
                                                <th>Materia</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>                                        
                                            <tr >
                                            @foreach($lista_favoritos['data'] as $list)
                                                <td>{{$list['nombre_buzon']}}</td>
                                                <td> {{$list['estado_documento']}} </td>
                                                <td>
                                                    <button type="button" class="btn btn-link" style="margin-top: -6px;">{{$list['identificador']}}</button>
                                                </td>
                                                <td>{{$list['fecha_documento']}}</td>
                                                <td>{{$list['tipo_documento']}}</td>
                                                <td>{{$list['nombre_buzon']}}</td>
                                                <td>{{$list['materia']}}</td>
                                                

                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="fas fa-bars"></i>
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a class="dropdown-item btn-menu-ver" onclick="visualizar_documento({{$list['id_documento']}},{{$list['id_documento_buzon']}})"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>
                                                            <a class="dropdown-item btn-menu-deshabilitar" onclick="estado_favorito({{$list['id_documento_buzon']}})" href="#">
                                                                @if($list['estado_favorito']==true)
                                                                    <i class="fas fa-trash-alt text-red"></i> Quitar
                                                                @endif
                                                            </a>
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
                                    <button type="button"  class="btn btn-secondary w-10 btn_cerrar_guardar">Cerrar</button>
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

$(document).ready(function(){

    $('#tabla_favorito_grilla').DataTable({
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            language: lenguaje_datatable
        });


	$(".btn-menu-ver").click(function(e){
		$('#card_ver_documento').show();
		
		
	});
	$(".btn_cerrar_ver_documento").click(function(e){
		$('#card_ver_documento').hide();
		

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
    $("#collapseOne").collapse('show');
});

function visualizar_documento(id_documento, id_documento_buzon)
{
    $(".print-error-msg").hide();

    if(id_documento > 0)
    {
        $("#collapseOne").collapse('hide');
        $('#card_documento').show();
        
        //deshabilita campos
        $('.form-disabled').prop("disabled", true);
        editor_cuerpo.setReadOnly(true);
        $('#form_acciones_solicitadas_el').multiselect('disable');
        $('#form_destinatario_principal_el').prop("disabled", true);
        $('#form_otros_destinatarios_el').prop("disabled", true);
        $(".bootstrap-tagsinput-max").addClass("disabled");
        $(".bootstrap-tagsinput").addClass("disabled");  

        cargar_datos_grilla(id_documento, id_documento_buzon);//,id_documento_buzon_padre);

    }
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


function estado_favorito(id)
{
    Swal.fire({
        title: 'Quitar de favoritos',
        html: "¿Está seguro (a) que desea quitar este <br>" +
                "     documento de sus favoritos?<br>",   
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
        }).then((result) => {
            console.log(result);
        if (result.value==true) {
            console.log(result.isConfirmed);
            $(".print-error-msg").hide();
            var token = $("input[name='_token']").val();
            $.ajax({
                    url: "favoritos/"+id,
                    type:'PUT',
                    dataType: 'json',
                    data: {
                        _token:token,
                        estado:false                        
                    },
                    success: function(data) {
                        if(data.status == '200')
                        {
                            toastr.success("Documento Actualizado","Aviso!");
                            autoRefresh();
                        }
                        else
                        {
                            toastr.error(data.data.comentario,"Aviso!");
                        }
                    },
                    error: function (e) {
                        data = e.responseJSON;
                        //if (typeof data.errors !== 'undefined') {
                        // printErrorMsg(data.errors);
                        console.log(e);
                            printErrorMsg(data);
                    }
                //}
            });
        }
    })


}

function autoRefresh() {
    window.setTimeout(function(){
                        location.reload();
                    },2000);
}

</script>


@stop