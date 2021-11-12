@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')


<div class="row">
        <div class="col-8">
            <h3>Favorito</h3>
        </div>
        
      </div>
    <div class="linea_content_header"></div>

@stop

    
@section('content')    
<div class="container"> 
    <div class="card" id="card_favorito_grilla">
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
                            <button type="button" class="btn btn-link">{{$list['id_documento']}}</button>
                        </td>
                        <td>{{$list['fecha_documento']}}</td>
                        <td>{{$list['tipo_documento']}}</td>
                        <td>{{$list['origen']}}</td>
                        <td>{{$list['materia']}}</td>
                        

                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                     <i class="fas fa-bars"></i>
                                 </button>
                                 <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                     <a class="dropdown-item btn-menu-ver" onclick="visualizar_usuario({{$list['id_documento']}})"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>
                                     <a class="dropdown-item btn-menu-editar" onclick="editar_usuario()"  href="#"><i class="fas fa-trash-alt text-red"></i> Deshabilitar</a>
                                     
                                 </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="alert alert-warning print-error-msg" style="display:none">
        <ul></ul>
    </div>
    <div id='cargando' style="display:none">
        <span class="spinner-border text-success" role="status" aria-hidden="true"></span>
        Cargando...
    </div>

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
                    <div class="form-control">Buzón Origen: <i>Alejandro Nuñez</i></div>
                    <input type="text" class="form-control" id="form_identificador" name="identificador" required>
                    <input type="text" class="form-control" id="form_folio" name="folio" required>
                    <input type="text" class="form-control" id="form_fecha" name="fecha" required>
                </div>
            </div>
            <br>
            <div class="form-row">
                <div class="col-md-3 mb-3">
                    <label for="inputState">Tipo Documento:</label>
                    <select id="form_documento" name="id_documento" class="form-control"  required>
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
            <div class="row">
                <div class="col-md-10"> </div>
                <div class="col-md-2">
                    <button type="button"  class="btn btn-secondary w-100 btn_cerrar_ver_documento">Cerrar</button>
                </div>
               
            </div>    
        </div>
        </form>
    </div>
</div>


</div>
<!--
<div class="container">
    <h4 id="titulo_ver_documento"class="card-header " >Bitácora</h5>
        <br>
        <div class="col">ID: </div>
        <div class="col">Materia: </div>
        <br>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">
              Derivaciones destinatarios principales ()DDP
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">
              Dereivaciones otros destinatarios (DOO)
            </label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
            <label class="form-check-label" for="defaultCheck1">
              Cambios Archivos Principal (CAP)
            </label>
          </div>
          <div class="card" id="card_favorito_grilla">
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
                    <tbody>
                       
                        <tr >
                            <td style="background-color: #b0f785;">DDP</td>
                            <td>08-07-2021</td>
                            <td>Alejandra Nuñez</td>
                            <td>Derivación a buzón "Juridica"</td>
                            <td>Envio para revisión</td>
                            
    
                            
                        </tr>
                      
                    </tbody>
                </table>
            </div>
          </div>
          

</div>
-->
</div>

@stop

@section('css')

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
                                    $("select[name='id_documento']").val(data.data.id_documento);
                                    $("select[name='id_nivel_acceso']").val(data.data.id_nivel_acceso);
                                    $("select[name='id_efectos_terceros']").val(data.data.id_efectos_terceros);
                                    $("input[name='contestar_hasta']").val(data.data.contestar_hasta);
                                    $("select[name='id_respuesta']").val(data.data.id_respuesta);
                                    $("input[name='materia']").val(data.data.materia);
                                    $("input[name='anterior']").val(data.data.anterior);
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