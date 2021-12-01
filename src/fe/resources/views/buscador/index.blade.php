@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')

    
        <div class="row">
            <div class="col-8">
                <h1>Buscar Documentos</h1>
            </div>
        </div>
        
        <div class="linea_content_header"></div>
        <br>
    <div class="card">
        <div class="card-body">
            <div class=" form-row">   
                <div class="col-md-8 md-4">  
                    <input id="name" name="name" class="form-control" type="text" >

                </div>
                <div class="col-md-1 md-4">  
                    <button class="btn btn-light" type="button" id="id_btn_filtrar"><i class="fas fa-search text-blue"></i> </button> 
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
                        <input type="text" class="form-control" id="id_documento" >
                    </div>
                </div>
                <div class="col-md-4 md-4">
                    <div class="form-group">
                        <label for="select_tipo_documento" >Tipo Documento</label>
                        <br>
                        <select class="form-control" id="tipo_documento" name="tipo_documento" >
                            <option value="">Seleccionar</option>
                                <option value="">----------</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 md-4">
                    <div class="form-group">
                        <label for="select_buzon_origen">Buzón Orígen</label>
                        <br>
                        <select  class="form-control" id="buzon_origen" name="buzon_origen" >
                            <option value="">Seleccionar</option>
                                <option value="">Oficio</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-md-4 md-4">
                    <div class="form-group">
                        <label for="">Rango de Fechas </label>
                    
                        <br>
                        <input type="date" id="birthday" name="birthday">
                        <input type="date" id="birthday" name="birthday">
                    </div>
                </div>
                
                
                <div class="col-md-4 md-4">
                    <div class="form-group">
                        <input type="checkbox" name="Efectos_sobre_terceros" id="id_efectos_sobre_terceros" class="valign middle">
                        <label for="check_efectos_sobre_terceros">Efectos Sobre Terceros</label>
                    </div>
                </div>

                <div class="col-md-4 md-4">
                    <div class="form-group">
                        <br>
                        <button type="button" class="btn btn-success nuevo_usuario">Buscar</button>
                    </div>    
                </div>
            </div>    
        </div>
    </div>
@stop


@section('content')
<div class="container"> 
    <div class="card" id="card_favorito_grilla">
        <div class="card-body">
            <table id="tabla_favorito_grilla" class="table dt-responsive nowrap" style="width:100%">
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
                <tbody>
                     @foreach($lista_documento['data'] as $list)
                        <tr >
                            <td>{{$list['id_documento']}}</td>
                            <td>{{$list['fecha_documento']}}</td>
                            <td>{{$list['tipo_documento']}}</td>
                            <td>{{$list['folio']}}</td>
                            <td>{{$list['origen']}}</td>
                            <td>Alcaldia</td>
                            <td>{{$list['materia']}}</td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item btn-menu-ver"  onclick="visualizar_usuario({{$list['id_documento']}})" href="#"><i class="fas fa-eye text-blue"></i> Ver</a>
                                        <a class="dropdown-item btn-menu-deshabilitar"  href="#" ><i class="fas fa-download text-blue"></i> Descargar</a>
                                        
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <ul></ul>

    <div class="card" id="card_ver_documento" style="display:none">

         <h4 id="titulo_ver_documento"class="card-header " >Ver Documento</h5>
        <div class="card-body">
        <form class="needs-validation" id="form_documento_ver"   >
            @csrf
            
            <ul class="pagination">
                <button type="button" class="btn btn-outline-secondary boton_ocultar_versiones_anteriores" style="padding: 48px 15px; border-right: 0px;">
                    <i class="fas fa-angle-double-left"></i>
                </button>
                <div class="flex-container">
                    <div>1458
                        20/10/215
                    </div>
                    <div>1458
                        20/10/215</div>  
                    <div>1458
                        20/10/215</div>  
                    <button type="button" class="btn btn-outline-secondary boton_ocultar_versiones_anteriores" style="padding: 48px 10px; border: 0px;">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div>aqui
                        20/10/215</div>
                    <button type="button" class="btn btn-outline-secondary boton_ocultar_versiones_anteriores" style="padding: 48px 10px; border: 0px;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                    <div>1458
                        20/10/215</div>  
                        
                    <div>1458
                        20/10/215</div>
                    <div>1458
                        20/10/215</div>  
                </div>
                <button type="button" class="btn btn-outline-secondary boton_ocultar_versiones_anteriores" style="padding: 48px 15px; border-left: 0px;">
                    <i class="fas fa-angle-double-right"></i>
                </button>
            </ul>
            <div class="container">
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4">
                    <!--<div class="form-control">Buzón Origen: <i>Alejandro Nuñez</i></div>
                    <input type="text" class="form-control" id="form_identificador" name="identificador" required>
                    <input type="text" class="form-control" id="form_folio" name="folio" required>
                    <input type="text" class="form-control" id="form_fecha" name="fecha" required>-->
                    <div class="form-control">Buzón Origen: <i>Alejandro Nuñez</i></div>
                    <div class="form-control">ID: <i>1</i></div>
                    <div class="form-control">Folio: <i>840683374</i></div>
                    <div class="form-control">Fecha: <i>2021-11-05 </i></div>
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="col-md-3 mb-3">
                    <label for="inputState">Tipo Documento:</label>
                    <select id="form_documento" name="id_tipo_documento" class="form-control"  required>
                        <option selected>Seleccionar</option>
                        <option>...</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="inputState">Nivel Acceso</label>
                    <select id="form_nivel_acceso"  name="id_nivel_acceso" class="form-control" required>
                        <option selected>Seleccionar</option>
                        <option>...</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="inputState">Efectos sobre terceros</label>
                    <select id="form_efectos_terceros"  name="id_efectos_terceros" class="form-control" required>
                        <option selected>Seleccionar</option>
                        <option>...</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="inputState">Contestar/Hasta</label>
                    <input type="date" class="form-control" id="form_contestar_hasta"  name="contestar_hasta" required>
                </div>        
            </div>
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <label for="inputState">Respuesta a:</label>
                    <select id="form_respuesta"  name="id_respuesta" class="form-control" required>
                        <option selected>Seleccionar</option>
                        <option>...</option>
                    </select>
                </div>
                <div class="col-md-8 mb-3">
                    <label for="inputState">Materia:</label>
                    <input type="text" class="form-control" id="form_materia"  name="materia" required>
                </div>      
            </div>
    
            <div class="form-row">
                <div class="col-md-12 mb-3">
                    <label for="inputState">Anterior:</label>
                    <input type="text" class="form-control" id="form_anterior"  name="anterior" required>
                </div>      
            </div>
            
            <div class="form-floating">
                <label for="floatingTextarea">Descripción o Extracto</label>
                <textarea class="form-control" placeholder="comment" id="form_descripcion_extracto"  name="descripcion_extracto" required></textarea>   
            </div>
            
            <div class="form-floating">
                <label for="floatingTextarea">Archivo Principal</label>
                <textarea class="form-control"  raw="4" id=""  name="" required></textarea>   
            </div>
            <!--
            <div class="card" id="card_desplegar_versiones" style="display:true" >
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-md-11 mb-3" >
                            <label for="exampleFormControlTextarea1">Archivo Principal</label>
                            <div class="dropzone" id="" >
                                <div class="flex-container" style="display: flex;">
                               
    
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-1 mb-3" >
                            <label for="exampleFormControlTextarea1">Versiones</label>
                            <button type="button" class="btn btn-outline-secondary boton_desplegar_versiones_anteriores" style="padding: 49px 32px;">
                                <i class="fas fa-angle-double-left"></i>
                            </button>	
                        </div>
                    </div>
                </div>
            </div>
            -->
        
            <div class="card" id="card_ocultar_versiones" style="display:none" >
                <div class="card-body">
                    <div class="form-row">
                        <div class="col-md-1 mb-3" >
                            <label for="exampleFormControlTextarea1">Archivo</label>
                            <button type="button" class="btn btn-outline-secondary boton_ocultar_versiones_anteriores" style="padding: 48px 32px;">
                                <i class="fas fa-angle-double-right"></i>
                            </button>	
                        </div>
                        <div class="col-md-11 mb-3" >
                            <label for="exampleFormControlTextarea1">Versiones anteriores</label>
                            <div class="dropzone" id="" >
                                <div class="flex-container" style="display: flex;">
                               
    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>		
            </div>
            <br>
    
            <div class="form-row">
                
                <div class="col-md-8 mb-3">
                    <label for="inputState">Destinatario Principal:</label>
                    <input type="text" class="form-control" id="form_destinatario_principal"  name="destinatario_principal" required>
                </div>  
                
                <div class="col-md-4 mb-3">
                    <label for="inputState">Acciones Solicitadas:</label>
                    <select id="form_acciones_solicitadas"  name="id_acciones_solicitadas" class="form-control" required>
                        <option selected>Seleccionar acciones</option>
                        <option>...</option>
                    </select>   
                    </div>
            </div>
            <div class="form-floating">
                <label for="floatingTextarea">Comentario a Destinatario Principal:</label>
                <textarea class="form-control"  id="form_comentario"  name="comentario" required></textarea>   
            </div>
            <div class="form-row">
                <div class="col-md-12 mb-3">
                    <label for="inputState">Otro(s) Destinatario(s):</label>
                    <input type="text" class="form-control" id="form_otros_destinatarios"  name="otros_destinatarios" required>
                </div>      
            </div>
            <div class="form-floating">
                <label for="floatingTextarea">Comentario(s) Otro(s) Destinatario(s)</label>
                <textarea class="form-control"  id="form_comentario_otro"  name="comentario_otro" required></textarea>   
            </div>
            <br>
            <!--
            <div class="row">
                <div class="col-md-10"> </div>
                <div class="col-md-2">
                    <button type="button"  class="btn btn-secondary w-100 btn_cerrar_ver_documento">Cerrar</button>
                </div>
               
            </div> -->    
        </div>
        </form>

        <h4 id="titulo_ver_documento"class="card-header " >Bitácora</h5>
        <br>
        <div class="card-body">
            <div class="col">ID: </div>
            <div class="col">Materia: </div>
            <br>
            
                <!--<table border="0" cellspacing="5" cellpadding="5">
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
                                    
                                </select>
                            </td>
                            <td>
                                <select class="form-control"  id="gr_buscar_estado" name="gr_buscar_estado" >
                                    <option value=''> Todos </option>
                                    
                                </select>
                            </td>
                            <td><input type="search" aria-controls="grilla_recibidos" class="form-control"  id="gr_buscar_origen_materia" name="gr_buscar_origen_materia"></td>
                            <td id="botones_grilla_recibidos">
                            </td>
                        </tr>
                    </tbody>
                </table>-->
           
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
                    <table id="tabla_bitacora_grilla" class="table dt-responsive nowrap no-footer dtr-inline dataTable collapsed" style="width:100%">
                        <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Buzón Origen</th>
                                <th>Acción </th>
                                <th>Mensaje</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($lista_bitacora['data'] as $list)
                                <tr>
                                    
                                    @if($list['accion']==2 && $list['tipo_destino']==1) 
                                   
                                    <td style=" background-color: #b0f785;">DDP</td>
                                    <td>{{$list['fecha_documento']}}</td>
                                    <td>{{$list['buzon_origen']}}</td>
                                    <td>{{$list['nombre_accion']}}</td>
                                    <td>{{$list['mensaje_respuesta']}}</td>
                                    
                                    @endif
                                    
                                    
                                    @if($list['accion']==2 && $list['tipo_destino']==2) 
                                    <td style="background-color: #b3eccb;">DOO</td>
                                    <td>{{$list['fecha_documento']}}</td>
                                    <td>{{$list['buzon_origen']}}</td>
                                    <td>{{$list['nombre_accion']}}</td>
                                    <td>{{$list['mensaje_respuesta']}}</td>
                                    @endif
                                    @if($list['accion']==4 ) 
                                    <td style="background-color: #edf495;">CAP</td> 
                                    <td>{{$list['fecha_documento']}}</td>
                                    <td>{{$list['buzon_origen']}}</td>
                                    <td>{{$list['nombre_accion']}}</td>
                                    <td>{{$list['mensaje_respuesta']}}</td>
                                    @endif
                                    
                                    
                                </tr>
                            @endforeach
                            
                        </tbody>
                    </table>
                </div>
                
            <div class="row">
                <div class="col-md-10"> </div>
                <div class="col-md-2">
                    <button type="button"  class="btn btn-secondary w-100 btn_cerrar_ver_documento">Cerrar</button>
                </div>
            </div>  
        </div>
    </div>



       
   

    
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">

    <style type="text/css">
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
    </style>

@stop

@section('js')
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

    $(document).ready(function(){

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

        $(".btn-menu-ver").click(function(e){
            $('#card_ver_documento').show();
            
            
        });
        $(".btn_cerrar_ver_documento").click(function(e){
            $('#card_ver_documento').hide();
        });
        
        //fn_grilla_bitacora();
    });

    function visualizar_usuario(identificador){
        //$('#cargando').show();
        $(".print-error-msg").hide();
        if(identificador>0){
            $('#form_documento_ver').trigger("reset");
            $('#card_documento_ver').hide();
            $.ajax({
                        url: "favoritos/"+identificador,
                        type:'GET',
                        success: function(data) {
                            if(data.status==400){
                                toastr.error(data.data.comentario,"Oops...");

                            }else{
                                console.log(data);
                                if(data.status==200 || data.status==201){
                                    
                                    $('#form_documento_ver').trigger("reset");
                                    $("input[name='identificador']").val(data.data.identificador);
                                    $("input[name='folio']").val(data.data.folio);
                                    $("input[name='fecha']").val(data.data.fecha);
                                    $("select[name='id_tipo_documento']").val(data.data.id_tipo_documento);
                                    $("select[name='id_nivel_acceso']").val(data.data.id_nivel_acceso);
                                    $("select[name='id_efectos_terceros']").val(data.data.id_efectos_terceros);
                                    $("input[name='contestar_hasta']").val(data.data.contestar_hasta);
                                    $("select[name='id_respuesta']").val(data.data.id_respuesta);
                                    $("input[name='materia']").val(data.data.materia);
                                    $("input[name='anterior']").val(data.data.anterior);

                                    $("textarea[name='descripcion_extracto']").val(data.data.descripcion);
                                    
                                    $("input[name='destinatario_principal']").val(data.data.destinatario_principal);
                                    $("select[name='id_acciones_solicitadas']").val(data.data.id_acciones_solicitadas);
                                    $("input[name='comentario']").val(data.data.comentario);
                                    $("input[name='otros_destinatarios']").val(data.data.otros_destinatarios);
                                    $("input[name='comentario_otro']").val(data.data.comentario_otro);
                                }
                            }
                            $('#cargando').hide();
                            //$('.btn-acciones-guardar-editar').hide();
                            //$('#titulo_usuario_crear_editar').html('Visualizar Usuario');
                            //$('#form_run').focus();
                            $('.form-control').prop("disabled", true);
                            $('#card_documento_ver').show();
                        },
                        error: function (e) {
                            data = e.responseJSON;
                            if (typeof data.errors !== 'undefined') {
                                printErrorMsg(data.errors);
                            }
                            $('.btn-submit').prop("disabled", false);
                            $('.btn-submit').html( 'Guardar' );
                            $('#cargando').hide();
                        }
                    });

        }
    }




        
</script>
@stop