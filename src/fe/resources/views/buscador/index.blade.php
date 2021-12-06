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
                        <input type="text" class="form-control" id="buscar_id_documento" name="buscar_id_documento">
                    </div>
                </div>
                <div class="col-md-4 md-4">
                    <div class="form-group">
                        <label for="select_tipo_documento" >Tipo Documento</label>
                        <br>
                        <select class="form-control" id="buscar_tipo_documento" name="buscar_tipo_documento" >
                            <option value="">Seleccionar</option>
                                <option value="">----------</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 md-4">
                    <div class="form-group">
                        <label for="select_buzon_origen">Buzón Orígen</label>
                        <br>
                        <select  class="form-control" id="buscar_buzon_origen" name="buscar_buzon_origen" >
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
                        <button type="button" class="btn btn-success nuevo_usuario">Buscar</button>
                        <i id="botones_grilla_recibidos"></i>
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
                        <h2 class="mb-0">
                        <!--<div class="col-md-3 md-12">  
                            <i type="button" class="fa fa-chevron-circle-down desplegar_grilla_documento"></i> </a>
                            <i type="button" class="fa fa-chevron-circle-up  cerrar_grilla_documento" style="display:none"></i> </a>
                        </div>-->
                        <button class="btn btn-block text-left buzones_carpetas_btn_colapsable_text" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <span id="boton_carpetas_texto"> Documentos  <i><b> </b></i> </span>
                            <i class="fa fa-chevron-circle-up" style="float:right;margin-top: 8px;"></i>
                            <i class="fa fa-chevron-circle-down" style="float:right;margin-top: 8px;"></i>
                        </button>
                        </h2>
                    </div>
                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#carpetas">
                    <div class="container">
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


    
                            </div>
    
        <!--DOCUMENTO VER-->

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
    var grilla_recibidos;

    

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

    async function fn_grilla_recibidos(){

        //$('#documento').hide();
        //if ( $.fn.DataTable.isDataTable('#grilla_recibidos') ) {
          //      $('#grilla_recibidos').DataTable().destroy();
       // }
        $('#grilla_recibidos tbody').empty();

        grilla_recibidos = $('#grilla_recibidos').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/buscadorListar?11',
                type:'json',
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true,
                language: lenguaje_datatable,
                columns: [
                    ,
                    { data: 'id_documento', name: 'documento.id_documento ' },
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

                                        if (row.id_tipo_destino == 1) //principal
                                        {
                                            //agrega listado de acciones

                                            //var jsona = JSON.parse(row.json_acciones);
                                            
                                            //console.log(jsona);
                                    
                                            //for (let i in accionesSolicitadas) {
                                        //     botonera +=' <a class="dropdown-item btn-menu-ver" href="#"><i class="fas fa-eye text-blue"></i>'+accionesSolicitadas[i]['id_accion']+'</a>';
                                        // }
                                            if(row.id_estado_documento != 7)
                                            {
                                                botonera +=' <a class="dropdown-item btn-menu-ver" onclick="accion_editar('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-edit text-blue"></i> Editar</a>';
                                                botonera +=' <a class="dropdown-item btn-menu-ver" onclick="accion_visar('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-check-circle text-blue"></i> Visar</a>';
                                                botonera +=' <a class="dropdown-item btn-menu-editar" onclick="responder_recibidos('+data+')"  href="#"><i class="fas fa-reply text-orange"></i> Responder</a>';
                                                botonera +=' <a class="dropdown-item btn-menu-editar" onclick="derivar_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-share text-green"></i> Derivar</a>';

                                            }                                          


                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                            
                                            
                                            if(row.id_estado_documento != 6 && row.id_estado_documento != 7)
                                                botonera +=' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>';
                                        
                                            botonera +=' <a class="dropdown-item btn-menu-editar" onclick="bitacora_recibidos('+data+')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';
                                            botonera +='<a class="dropdown-item btn-menu-deshabilitar" onclick="favorito_recibidos('+row.id_documento_buzon+')" href="#"><i class="far fa-star text-green"></i> ( + ) Favoritos</a>';
                                        }
                                        else{
                                            botonera +=' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                            if(row.id_estado_documento != 6)
                                                botonera +=' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos('+data+','+row.id_documento_buzon+','+row.id_documento_buzon_padre+')"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>';
                                            botonera +=' <a class="dropdown-item btn-menu-editar" onclick="bitacora_recibidos('+data+')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';
                                            botonera +='<a class="dropdown-item btn-menu-deshabilitar" onclick="favorito_recibidos('+row.id_documento_buzon+')" href="#"><i class="far fa-star text-green"></i> ( + ) Favoritos</a>';
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
               

        });
        }

    $(document).ready(function () {
    

        //var div_por_recibir_width = document.getElementById('nav-por-recibir').getBoundingClientRect().width;
        //$('#nav-despachados').attr("style","width:"+div_por_recibir_width+'px');
        //$('#nav-recibidos').attr("style","width:"+div_por_recibir_width+'px');

        $(function() {

            

            fn_grilla_recibidos();

        
        });
    });


        
</script>
@stop