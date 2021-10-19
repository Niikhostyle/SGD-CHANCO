@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')


    <div class="row">
        <div class="col-8">
            <h1>Tipos de Documentos</h1>
        </div>
        <div class="col">
            <button type="button" class="btn btn-success boton_nuevo">Nuevo Tipo de Documento</button>

        </div>
      </div>
    <div class="linea_content_header"></div>

@stop

@section('content')
<div class="container">
    <div class="card" id="card_grilla">
        <div class="card-body">
            <table id="tabla_grilla" class="table dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Origen</th>
                        <th>Tipo Flujo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listado_tiposdoc as $list)
                    <tr>
                        <td>{{$list['nombre']}}</td>
                        <td>{{$list['id_tipo_origen']}}</td>
                        <td>{{$list['id_tipo_flujo']}}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                     <i class="fas fa-bars"></i>
                                 </button>
                                 <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                     <a class="dropdown-item btn_ver" onclick="ver_tipodoc()" href="#"><i class="fas fa-eye text-blue"></i> Ver</a>
                                     <a class="dropdown-item btn_editar" onclick="ver_tipodoc()" href="#"><i class="fas fa-edit text-blue"></i> Editar</a>
                                     <a class="dropdown-item" onclick="eliminar_tipodoc()" href="#"><i class="fas fa-trash-alt text-red"></i> Eliminar</a>
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
    <div class="card" id="card_crear_editar" style="display:none">
        <h5 id="titulo_crear_editar"class="card-header bg-success" >Nuevo Tipo de Documento</h5>
        <div class="card-body">
            <form class="needs-validation" id="form_crear_editar" method="POST" action=""  >
            @csrf

            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="input_nombre">Nombre:</label>
                        <input type="text" class="form-control " id="form_nombre" aria-describedby="nombre_error" placeholder="" value="" name="nombre" required>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="form-group">
                        <label for="input_descripcion">Descripción:</label>
                        <input type="text" class="form-control " id="form_descripcion" name="descripcion" value="" aria-describedby="descripcion_error" placeholder="" required>
                    </div>
                </div>
                
            </div>

            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="input_nombre_corto">Nombre corto:</label>
                        <input type="text" class="form-control " id="form_nombre_corto" aria-describedby="nombre_corto_error" placeholder="" value="" name="nombre_corto" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="input_origen">Origen:</label>
                        <select class="form-control" id="form_tipo_origen" name="tipo_origen" required>
                            <option value="">Seleccionar</option>
                            @foreach($datosOrigen as $dato)
                                <option value="{{$dato['id_tipo_origen']}}">{{$dato['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="input_flujo">Tipo Flujo:</label>
                        <select class="form-control" id="form_tipo_flujo" name="tipo_flujo" required>
                            <option value="">Seleccionar</option>
                            @foreach($datosFlujo as $dato)
                                <option value="{{$dato['id_tipo_flujo']}}">{{$dato['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="input_tipo_avance">Tipo Avance:</label>
                        <select class="form-control" id="form_tipo_avance" name="tipo_avance" required>
                            <option value="">Seleccionar</option>
                            @foreach($datosAvance as $dato)
                                <option value="{{$dato['id_tipo_avance']}}">{{$dato['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
            </div>            

            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="input_tipo_folio">Tipo Folio:</label>
                        <select class="form-control" id="form_tipo_folio" name="tipo_folio" required>
                            <option value="">Seleccionar</option>
                            @foreach($datosFolio as $dato)
                                <option value="{{$dato['id_tipo_folio']}}">{{$dato['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="input_tipo_asignacion_folio">Asignación Folio y Fecha:</label>
                        <select class="form-control" id="form_tipo_asignacion_folio" name="tipo_asignacion_folio" required>
                            <option value="">Seleccionar</option>
                            @foreach($datosAsignacionFolio as $dato)
                                <option value="{{$dato['id_tipo_asignacion_folio']}}">{{$dato['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="input_fe">Requiere FE:</label>
                        <select class="form-control" id="form_fe" name="fe" required>
                            <option value="">Seleccionar</option>
                            <option value="true">Si</option>
                            <option value="false">No</option>
                        </select>
                    </div>
                </div>               
                
            </div>  
            
            <div class="row bloque_flujo_interno">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="input_encabezado">Plantilla encabezado:</label>
                        <textarea class="form-control"></textarea>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="input_cuerpo">Plantilla cuerpo:</label>
                        <textarea class="form-control"></textarea>
                    </div>
                </div>
            </div>    

            <div class="row bloque_buzones_flujo">
                <div class="col-md-12">                    
                    <label for="input_buzon_flujo">Buzones flujo:</label>                                             
                </div> 
                <div class="col-md-6">
                    <select  class="form-control" id="listado_buzones" name="listado_buzones" required>
                        <option value="">Busque y seleccione buzón</option>
                        @foreach($listado_buzones as $buzon)
                            <option value="{{$buzon['id_buzon']}}">{{$buzon['nombre']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success btn-agrega-buzon w-100">Agregar</button>
                </div>
                <div class="col-md-4"></div> 
                <div class="col-md-12">
                    <div class="form-group">                        
                        
                        <table id="tabla_grilla_buzones" class="table dt-responsive nowrap" style="width:100%">
                            <tbody>  
                            </tbody>
                        </table>
                        
                        <span id="carga_tabla_grilla_buzones"></span>

                    </div>  
                </div>
            </div>

            <div class="row">
                <div class="col-md-8"> </div>
                <div class="col-md-2">
                    <button type="button"  class="btn btn-secondary w-100 btn_cerrar_guardar">Cerrar</button>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success btn-submit w-100">Guardar</button>
                    <input type="hidden" name="hiddTipoDocumento" id="hiddTipoDocumento" value="">
                    
                    
                </div>
                
            </div>

            </form>

        </div>
    </div>


</div>

@stop

@section('css')

    <link rel="stylesheet" href="/css/admin_custom.css">
    
@stop

@section('js')

<script src="js/jquery.bootstrap-duallistbox.js"></script>

<script>

    $('#tabla_grilla').DataTable({
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true,
        language: lenguaje_datatable
    });

    const accionesFlujo2 = @json($acciones_tipoflujo2);
    const accionesFlujo3 = @json($acciones_tipoflujo3);
    //const acciones_t2 = @json($accionesT2);  
    //const acciones_t3 = @json($accionesT3);  

    //const cantAccionesF2 = accionesFlujo2.length - 2;
    //const cantAccionesF3 = accionesFlujo3.length - 2;

    var arrAcciones2 = accionesFlujo2.map(function(obj2){
        var aAcc2 = [];
        aAcc2 = obj2.id;
        return aAcc2;
    });

    var arrAcciones3 = accionesFlujo3.map(function(obj3){
        var aAcc3 = [];
        aAcc3 = obj3.id;
        return aAcc3;
    });

    var dataObject = {
        columns1: accionesFlujo2,
        columns2: accionesFlujo3,
        data: [            
        ],
        data1: [            
        ],
        data2: [            
        ]
    };

    var dataTable,
        domTable, 
        htmlTable = '<table id="tabla_grilla_buzones"><tbody></tbody></table>';

    function muestraDataTable(op)
    {
        if ($.fn.DataTable.fnIsDataTable(domTable)) {
            dataTable.fnDestroy(true);
            $('#carga_tabla_grilla_buzones').append(htmlTable);
        } 

        //var data = (op == '2') ? sp : np;

        if (op == '2')
            var data = dataObject.data1;
        else if (op == '3')
            var data = dataObject.data2;     
        else
            var data = dataObject.data;

        //var columns = (op == '2') ? spColumns : npColumns;    

        if (op == '2')
            var columns = dataObject.columns1;
        else if (op == '3')
            var columns = dataObject.columns2;     
        else
            var columns = dataObject.columns1;    

        dataTable = $("#tabla_grilla_buzones").dataTable({
            bDestroy : true,
            bProcessing : false,
            paging: false,
            searching: false,
            aaData : data,
            aoColumns : columns
        }); 
       
        domTable = document.getElementById('tabla_grilla_buzones');        
    }

    $(".boton_nuevo").click(function(e){
        
        $(".print-error-msg").hide();
        $('#form_crear_editar').trigger("reset");
        $('#titulo_crear_editar').html('Nuevo Tipo de Documento');
        $('#card_crear_editar').show();
        $('.btn-submit').show();

        $('#form_crear_editar').removeClass("was-validated");

        inicialización_formulario();
        
    });

    function inicialización_formulario(){
        $('.bloque_flujo_interno').hide();
        $('.bloque_buzones_flujo').hide();
    }

    $("#form_tipo_origen").change(function(){

        if ($(this).val() == 1) //interno
            $('.bloque_flujo_interno').show();
        else
            $('.bloque_flujo_interno').hide();

	});

    $("#form_tipo_folio").change(function(){

        if ($(this).val() == 3) //sin folio
        {
            $('#form_tipo_asignacion_folio').prop("disabled", true);
            $('#form_tipo_asignacion_folio').val('3');
        }            
        else
        {
            $('#form_tipo_asignacion_folio').prop("disabled", false);
        }
	});

    $("#form_tipo_flujo").change(function(){

        if ($(this).val() == 1) //libre
        {
            $('.bloque_buzones_flujo').hide();
            $('#form_tipo_avance').prop('selectedIndex',0);
            $('#form_tipo_avance').prop("disabled", true);
        }
        else
        {
            $('.bloque_buzones_flujo').show();
            $('#form_tipo_avance').prop("disabled", false);

            muestraDataTable($(this).val());

            orden = 0;
        }
	});    

    $(".btn_cerrar_guardar").click(function(e){
        $('#card_crear_editar').hide();
        $('#form_crear_editar').trigger("reset");
        $(".print-error-msg").hide();
        $('#form_crear_editar').removeClass("was-validated");
    });
    
    $(".btn-agrega-buzon").click(function(e) {

        orden = orden + 1; 
        
        var id_buzon = $('#listado_buzones').val();
        var nombre_buzon = $('select[name="listado_buzones"] option:selected').text(); 
        var hiddIdBuzon = '<input type="hidden" name="hiddIdBuzon_'+orden+'" id="hiddIdBuzon_'+orden+'" value="'+id_buzon+'">'; 
        var hiddIdOrden = '<input type="hidden" name="hiddIdOrden_'+orden+'" id="hiddIdOrden_'+orden+'" value="'+orden+'">'; 

        var id_flujo = $("select[name='tipo_flujo']").val(); 

        if (id_flujo == 2)
            var nAcciones = accionesFlujo2.length;

        if (id_flujo == 3)
            var nAcciones = accionesFlujo3.length;
         
        var tblRow = [];
        
        tblRow.push(orden+hiddIdOrden);        
        tblRow.push(nombre_buzon+hiddIdBuzon);        

        for (let i = 2; i < nAcciones; i++)
        {           
            if (id_flujo == 2)
                codAccion = arrAcciones2[i];           

            if (id_flujo == 3)
                codAccion = arrAcciones3[i];           

            var row = "<input data-type='col" + codAccion + "' data-id='" + orden + "' type='checkbox' name='chk_" + orden + "[]' id='chk_" + orden + "' value='" + codAccion + "'>";
            tblRow.push(row);
        }

        if (id_buzon != '')
        {
            dataTable.fnAddData(tblRow, true);
            dataTable.fnDraw();

            //$('input[name="hiddIdBuzon"]').val(id_buzon);
            //$('input[name="hiddIdOrden"]').val(orden);

 
        }        
    });
    

    $(".btn-submit").click(function(e){
        e.preventDefault();

        $('.btn-submit').html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardar'
        );
/*
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            if (form.checkValidity() === false) {
                e.preventDefault();
				e.stopPropagation();

                form.classList.add('was-validated');
            }
            
            if(form.checkValidity() === true){
                form.classList.remove("was-validated");
            }
        });
*/
        $(".print-error-msg").hide();
        var _token = $("input[name='_token']").val();
        var nombre = $("input[name='nombre']").val();
        var nombre_corto = $("input[name='nombre_corto']").val();
        var descripcion = $("input[name='descripcion']").val();
        var tipo_origen = $("select[name='tipo_origen']").val();
        var tipo_flujo = $("select[name='tipo_flujo']").val();
        var tipo_folio = $("select[name='tipo_folio']").val();
        var tipo_avance = $("select[name='tipo_flujo']").val();
        var tipo_asignacion_folio = $("select[name='tipo_flujo']").val();
        var requiere_fe = $("input[name='fe']").val();

        if (tipo_flujo == 2 || tipo_flujo == 3)
        {
            var datosBuzonFlujo = [];
            

            for (let i = 1; i <= orden; i++)
            {
                var arr = $('[name="chk_'+i+'[]"]:checked').map(function(){
                    return this.value;
                }).get();
                
                var str = arr.join(',');                
                
                let datosAcciones = [];

                for (let j = 0; j < arr.length; j++)
                {
                    var filAcciones = { 
                        "id_accion": arr[j] 
                    }
                    
                    datosAcciones.push(filAcciones);
                }
               
                var filaBuzones = {
                    "id_buzon":$("input[name='hiddIdBuzon_"+i+"']").val(),
                    "orden":$("input[name='hiddIdOrden_"+i+"']").val(),
                    "acciones": datosAcciones
                }

                datosBuzonFlujo.push(filaBuzones);

            }  
            //console.log(datosBuzonFlujo);
            //console.log(JSON.stringify(datosBuzonFlujo));          
        }
//"buzones_flujo":[{"id_buzon":"41","orden":"1","acciones":[{"id_accion":"1"},{"id_accion":"3"}]},{"id_buzon":"42","orden":"2","acciones":[{"id_accion":"1"}]}]
        //datosBuzonFlujo = JSON.stringify(datosBuzonFlujo);
        
        var hiddTipoDocumento = $("input[name='hiddTipoDocumento']").val();

        if (hiddTipoDocumento == '') //crear
        {
            var urlAccion = "{{route('tipos_documentos.store')}}";
            var typeAccion = 'POST';
        }
        else //editar
        {
            var urlAccion = "{{route('tipos_documentos.update')}}";
            var typeAccion = 'PUT';
        }    
        
        $.ajax({
            url: urlAccion,
            type: typeAccion,
            dataType: 'json',
            contentType: 'application/x-www-form-urlencoded',
            data: { 
                    _token:_token, 
                    nombre:nombre, 
                    nombre_corto:nombre_corto, 
                    descripcion:descripcion,
                    tipo_origen:tipo_origen,
                    tipo_flujo:tipo_flujo,
                    tipo_folio:tipo_folio,
                    tipo_avance:tipo_avance,
                    tipo_asignacion_folio:tipo_asignacion_folio,  
                    requiere_fe:requiere_fe,                  
                    bzs_flujo:datosBuzonFlujo,
                    hiddTipoDocumento:hiddTipoDocumento
                  },
            success: function(data) 
            {
                if(data.status == '200')
                {
                    toastr.success("Tipo de Documento actualizado","Aviso!");
                    autoRefresh();
                }
                else if(data.status == '201')
                {
                    toastr.success("Tipo de Documento creado","Aviso!");                  
                    autoRefresh();
                }
                else
                {
                    toastr.error(data.data.comentario,"Aviso!");                    
                }

                $('.btn-submit').html( 'Guardar' );
            },
            error: function (e) {
                data = e.responseJSON;
                if (typeof data.errors !== 'undefined') {
                    printErrorMsg(data.errors);
                }

                $('.btn-submit').html( 'Guardar' );
            }
        });             
    });

    function printErrorMsg(msg) {
        $(".print-error-msg").find("ul").html('');
        $(".print-error-msg").show();
        $.each( msg, function( key, value ) {
            $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
        });
    }

    function autoRefresh() {
        window.setTimeout(function(){ 
                            location.reload();
                        },2000);
    }




</script>
@stop
