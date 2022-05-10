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
<div class="col-12">    
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
                        <td>{{$aOrigen[$list['id_tipo_origen']]}}</td>
                        <td>{{$aFlujo[$list['id_tipo_flujo']]}}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                     <i class="fas fa-bars"></i>
                                 </button>
                                 <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                     <a class="dropdown-item btn_ver" onclick="ver_tipodoc({{$list['id_tipo_documento']}})" href="#"><i class="fas fa-eye text-blue"></i> Ver</a>
                                     <a class="dropdown-item btn_editar" onclick="ver_tipodoc({{$list['id_tipo_documento']}},2)" href="#"><i class="fas fa-edit text-blue"></i> Editar</a>
                                     <a class="dropdown-item" onclick="eliminar_tipodoc({{$list['id_tipo_documento']}})" href="#"><i class="fas fa-trash-alt text-red"></i> Eliminar</a>
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
        
        <div class="card-header" >
            <h4 id="titulo_crear_editar">Nuevo Tipo de Documento</h4>
            <div class="linea_content_header"></div>
        </div>          
        
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
                        <input type="text" class="form-control " id="form_descripcion" name="descripcion" value="" aria-describedby="descripcion_error" placeholder="" >
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
                        <textarea class="form-control " id="form_plantilla_encabezado" name="plantilla_encabezado"></textarea>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label for="input_cuerpo">Plantilla cuerpo:</label>
                        <textarea class="form-control" id="form_plantilla_cuerpo" name="plantilla_cuerpo"></textarea>
                    </div>
                </div>
            </div>    

            <div class="row bloque_buzones_flujo">
                <div class="col-md-12">                    
                    <label for="input_buzon_flujo">Buzones flujo:</label>                                             
                </div> 
                <div class="col-md-6">
                    <select  class="form-control" id="listado_buzones" name="listado_buzones" >
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
    <style type="text/css">
    
               
        .form-control.is-valid, .was-validated .form-control:valid {
            border-color: none !important;
            background-image: none;
        }


     </style>
    
@stop

@section('js')

<script src="js/jquery.bootstrap-duallistbox.js"></script>

<script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ url('js/ckfinder/ckfinder.js') }}"></script>


<script>

    const editor_encabezado = CKEDITOR.replace('form_plantilla_encabezado',{  
             
             filebrowserBrowseUrl     : "{{ route('ckfinder_browser') }}",
             filebrowserImageBrowseUrl: "{{ route('ckfinder_browser') }}?type=Images&token=123",
             filebrowserImageUploadUrl: "{{ route('ckfinder_connector') }}?command=QuickUpload&type=Images",
         });
    const editor_cuerpo = CKEDITOR.replace('form_plantilla_cuerpo',{  
             
             filebrowserBrowseUrl     : "{{ route('ckfinder_browser') }}",
             filebrowserImageBrowseUrl: "{{ route('ckfinder_browser') }}?type=Images&token=123",
             filebrowserImageUploadUrl: "{{ route('ckfinder_connector') }}?command=QuickUpload&type=Images",
         });

      
         CKFinder.config( { connectorPath: '/ckfinder/connector' } );   
        
   
    $('#tabla_grilla').DataTable({
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true,
        language: lenguaje_datatable
    });

    const accionesFlujo2 = @json($acciones_tipoflujo2); //acciones tipo 2
    const accionesFlujo3 = @json($acciones_tipoflujo3);
    const listBuzones = @json($listado_buzones);
    
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

        if (op == '2')
            var columns = dataObject.columns1;
        else if (op == '3')
            var columns = dataObject.columns2;     
        else
            var columns = dataObject.columns1;    

        dataTable = $("#tabla_grilla_buzones").dataTable({
            //rowReorder: true,
            bDestroy : true,
            bProcessing : false,
            paging: false,
            searching: false,
            bPaginate: false,
            bFilter: false,
            bSort: false,
            bInfo: false,
            aaData : dataObject.data,
            aoColumns : columns
        }); 
       
        domTable = document.getElementById('tabla_grilla_buzones');        
    }
/*

table.on( 'row-reorder', function ( e, diff, edit ) {
  var index = edit.triggerRow[0][0];
  table.rows(index).select();
});
*/
$("#tabla_grilla_buzones").on( 'row-reorder', function ( e, diff, edit ) {
        //var result = 'Reorder started on row: '+edit.triggerRow.data()[1]+'<br>';
 
        for ( var i=0, ien=diff.length ; i<ien ; i++ ) {
            var rowData = dataTable.row( diff[i].node ).data();
 
           // result += rowData[1]+' updated to be in position '+
           //     diff[i].newData+' (was '+diff[i].oldData+')<br>';
        }
 
        //$('#result').html( 'Event result:<br>'+result );
    } );

    function ver_tipodoc(id,op)
    { 
        $(".print-error-msg").hide(); 
        $('#form_crear_editar').trigger("reset"); 
        $('#card_crear_editar').show(); 
        $('#form_crear_editar').removeClass("was-validated");
        $('.bloque_buzones_flujo').hide();
        $('.bloque_flujo_interno').hide();     

        
        if (op == 2)
        {
            editor_encabezado.setReadOnly(false);
            editor_cuerpo.setReadOnly(false);
            $('#titulo_crear_editar').html('Editar Tipo de Documento'); 
            $('.form-control').prop("disabled", false);
            $('.btn-submit').show();
        }            
        else
        {
            editor_encabezado.setReadOnly(true);
            editor_cuerpo.setReadOnly(true);
            $('#titulo_crear_editar').html('Ver Tipo de Documento'); 
            $('.btn-submit').prop("disabled", false); 
            $('.form-control').prop("disabled", true);
            $('.btn-submit').hide(); 
        }

        $.ajax({
                url: "tipos_documentos/"+id, 
                type:'GET', 
                dataType: 'json',
                success: function(data) { 
                    if(data.status=='400') { 
                        toastr.error(data.data.comentario,"Aviso!");                        
                    }
                    else { 
                        if(data.status=='200' || data.status=='201'){ 

                            $("input[name='nombre']").val(data.data.nombre);
                            $("input[name='nombre_corto']").val(data.data.nombre_corto);
                            $("input[name='descripcion']").val(data.data.descripcion);
                            $("select[name='tipo_origen']").val(data.data.id_tipo_origen);
                            $("select[name='tipo_flujo']").val(data.data.id_tipo_flujo);
                            $("select[name='tipo_folio']").val(data.data.id_tipo_folio);
                            $("select[name='tipo_avance']").val(data.data.id_tipo_avance);
                            $("select[name='tipo_asignacion_folio']").val(data.data.id_tipo_asignacion_folio);
                            $("select[name='fe']").val(""+data.data.requiere_fe+"");
                            
                            editor_encabezado.setData(data.data.plantilla_encabezado);   
                            editor_cuerpo.setData(data.data.plantilla_cuerpo);   

                            $("input[name='hiddTipoDocumento']").val(data.data.id_tipo_documento);

                            //validaciones

                            changeTipoFolio(data.data.id_tipo_folio);
                            changeTipoOrigen(data.data.id_tipo_origen);
                            changeTipoFlujo(data.data.id_tipo_flujo, data.data.buzones_flujo.length);

                            cargarDatosFlujoAcciones(data.data.buzones_flujo);
                           
                        } 
                    } 
                    $('.btn-submit').prop("disabled", false); 
                }, 
                error: function (jqXHR, textStatus, errorThrown) {                                                      
                    toastr.error("Falla al obtener el tipo de documento","Aviso!");
                    $('.btn-submit').prop("disabled", false); 

                }  
            }); 
    }


    $(".boton_nuevo").click(function(e){
        
        $(".print-error-msg").hide();        
        $('#titulo_crear_editar').html('Nuevo Tipo de Documento');
        $('#card_crear_editar').show();
        $('.btn-submit').show();

        $('#form_crear_editar').trigger("reset");
        $("#form_crear_editar :input").prop("disabled", false);
        editor_encabezado.setData();   
        editor_cuerpo.setData();   
        editor_encabezado.setReadOnly(false);
        editor_cuerpo.setReadOnly(false);

        $('#form_crear_editar').removeClass("was-validated");

        inicialización_formulario();
        
    });

    function inicialización_formulario(){
        $('.bloque_flujo_interno').hide();
        $('.bloque_buzones_flujo').hide();

    }

    function changeTipoOrigen(op)
    {
        if (op == 1) //interno
            $('.bloque_flujo_interno').show();
        else
            $('.bloque_flujo_interno').hide();
    } 

    function changeTipoFolio(op)
    {
        if (op == 3) //sin folio
        {
            $('#form_tipo_asignacion_folio').prop("disabled", true);
            $('#form_tipo_asignacion_folio').val('3');
        }            
        else
        {
            $('#form_tipo_asignacion_folio').prop("disabled", false);
        }
    }

    function changeTipoFlujo(op, n)
    {
        if (op == 1) //libre
        {
            $('.bloque_buzones_flujo').hide();
            $('#form_tipo_avance').prop('selectedIndex',0);
            $('#form_tipo_avance').prop("disabled", true);
        }
        else
        {
            $('.bloque_buzones_flujo').show();
            $('#form_tipo_avance').prop("disabled", false);

            muestraDataTable(op);

            orden = n;
        }
        
    }

    $("#form_tipo_origen").change(function(){
        changeTipoOrigen($(this).val());
    });

    $("#form_tipo_folio").change(function(){
        changeTipoFolio($(this).val());        
	});

    $("#form_tipo_flujo").change(function(){        
        changeTipoFlujo($(this).val(), 0);
	});    

    $(".btn_cerrar_guardar").click(function(e){
        $('#card_crear_editar').hide();
        $('#form_crear_editar').trigger("reset");
        $(".print-error-msg").hide();
        $('#form_crear_editar').removeClass("was-validated");

        editor_encabezado.setReadOnly(false);
        editor_cuerpo.setReadOnly(false);
    });

    function cargarDatosFlujoAcciones(datosTabla)
    {
        var id_flujo_edit = $("select[name='tipo_flujo']").val(); 

        if (id_flujo_edit == 2)
            var nAcciones = accionesFlujo2.length;

        if (id_flujo_edit == 3)
            var nAcciones = accionesFlujo3.length;  

        $.each(datosTabla, function(i, item) 
        {
            var tblRow = [];       

            var nombre_buzonEdit = $("#listado_buzones option[value='"+item.id_buzon+"']").text();
            var hiddIdBuzonEdit = '<input type="hidden" name="hiddIdBuzon_'+item.orden+'" id="hiddIdBuzon_'+item.orden+'" value="'+item.id_buzon+'">'; 
            var hiddIdOrdenEdit = '<input type="hidden" name="hiddIdOrden_'+item.orden+'" id="hiddIdOrden_'+item.orden+'" value="'+item.orden+'">'; 
            var hiddIdTipoDocBuzon = '<input type="hidden" name="hiddId_'+item.orden+'" id="hiddId_'+item.orden+'" value="'+item.id_tipo_documento_buzon+'">'; 

            tblRow.push(item.orden);        
            tblRow.push(nombre_buzonEdit+hiddIdBuzonEdit+hiddIdOrdenEdit+hiddIdTipoDocBuzon);   

            var aAccionesEdit = item.acciones.map(function(objEdit){
                var aAccEd = [];
                aAccEd = objEdit.id_accion;
                return aAccEd;
            });

            for (let j = 2; j < nAcciones-1; j++)
            {           
                if (id_flujo_edit == 2)
                    codAccion = arrAcciones2[j];           

                if (id_flujo_edit == 3)
                    codAccion = arrAcciones3[j];           

                salida =  aAccionesEdit.indexOf(codAccion);
               
                if (salida >= 0)
                    checked = "checked";
                else
                    checked = "";

                var row = "<input "+checked+" data-type='col" + codAccion + "' data-id='" + item.orden + "' type='checkbox' name='chk_" + item.orden + "[]' id='chk_" + item.orden + "' value='" + codAccion + "'>";
                tblRow.push(row);
            }
        
            sHtmlAcciones = '<div class="dropdown">'+
                            '    <button class="btn" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
                            '         <i class="fas fa-bars"></i>'+
                            '     </button>'+
                            '    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">'+
                            //'         <a class="dropdown-item btn_ver" onclick="habilitaOrden()" href="#"><i class="fas fa-grip text-blue"></i> Mover</a>'+
                            '         <a class="dropdown-item" onClick="eliminarBuzonFlujo('+item.orden+')" href="#"><i class="fas fa-trash-alt text-red"></i> Eliminar</a>'+
                            '    </div>'+
                            '</div>';
            tblRow.push(sHtmlAcciones);   

            dataTable.fnAddData(tblRow, true);
            dataTable.fnDraw();

        });                    

    }
    
    function habilitaOrden()
    {
        $('#tabla_grilla_buzones').dataTable().rowReordering();
        //dataTable.rowReorder.enable();
    }

    function eliminarBuzonFlujo(fila)
    {
        var anSelected = fnGetSelected(dataTable,fila);
		var iRow = dataTable.fnGetPosition(anSelected[0]);
		
        dataTable.fnDeleteRow( iRow );

        //reordenaDatosFlujoAcciones(fila);
        $.each($('#tabla_grilla_buzones tr td:first-child'),function(index,val){
            $(this).html(index+1)
        });

    }

    function fnGetSelected( oTableLocal,nFila )
    {
        var aReturn = new Array();
        var aTrs = oTableLocal.fnGetNodes(); //cantidad de filas
        aReturn.push(aTrs[nFila-1]);

        return aReturn;
    }
    
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

        //busca ultimo valor de orden de fila
        var ordenFila = parseInt($('#tabla_grilla_buzones tr:last-child').find('td:first-child').text()) + 1;    

        if (isNaN(ordenFila))
            ordenFila = 1;

        tblRow.push(ordenFila);        
        tblRow.push(nombre_buzon+hiddIdOrden+hiddIdBuzon);        

        for (let i = 2; i < nAcciones-1; i++)
        {           
            if (id_flujo == 2)
                codAccion = arrAcciones2[i];           

            if (id_flujo == 3)
                codAccion = arrAcciones3[i];           

            var row = "<input data-type='col" + codAccion + "' data-id='" + orden + "' type='checkbox' name='chk_" + orden + "[]' id='chk_" + orden + "' value='" + codAccion + "'>";
            tblRow.push(row);
        }

        sHtmlAcciones = '<div class="dropdown">'+
                            '    <button class="btn" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'+
                            '         <i class="fas fa-bars"></i>'+
                            '     </button>'+
                            '    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">'+
                            '         <a class="dropdown-item btn_ver" onclick="" href="#"><i class="fa-solid fa-grip text-blue"></i> Mover</a>'+
                            '         <a class="dropdown-item" onClick="eliminarBuzonFlujo('+orden+')" href="#"><i class="fas fa-trash-alt text-red"></i> Eliminar</a>'+
                            '    </div>'+
                            '</div>';
        tblRow.push(sHtmlAcciones);    
        
        if (id_buzon != '')
        {
            dataTable.fnAddData(tblRow, true);
            dataTable.fnDraw();
        }        
    });

    function eliminar_tipodoc(id)
    { 
        $(".print-error-msg").hide(); 
        var token = $("input[name='_token']").val();

        Swal.fire({
            title: 'Tipo de Documento',
            text: "¿Quiere eliminar el Tipo de Documento?",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
            }).then((result) => {
                
            if (result.value==true) 
            {
                $.ajax({ 
                        url: "tipos_documentos/"+id, 
                        type:'DELETE', 
                        dataType: 'json',
                        data: {_token :token},
                        success: function(data) { 
                            if(data.status == '200')
                            { 
                                toastr.success("Tipo de Documento eliminado","Aviso!");                     
                                autoRefresh();
                            } 
                            else
                            {                         
                                toastr.error(data.data.comentario,"Aviso!");
                            }                    
                        }, 
                        error: function (jqXHR, textStatus, errorThrown) {                                                      
                           toastr.error("Falla al eliminar tipo de documento","Aviso!");
                        } 
                }); 
            }
        })        
    }
    
    //SUBMIT
    $(".btn-submit").click(function(e){
        e.preventDefault();

        $('.btn-submit').html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardar'
        );
           
        var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                if (form.checkValidity() === false) {
                        //e.stopPropagation();
                        form.classList.add('was-validated');
                }
            });
         
        $(".print-error-msg").hide();
        var _token = $("input[name='_token']").val();
        var nombre = $("input[name='nombre']").val();
        var nombre_corto = $("input[name='nombre_corto']").val();
        var descripcion = $("input[name='descripcion']").val();
        var tipo_origen = $("select[name='tipo_origen']").val();
        var tipo_flujo = $("select[name='tipo_flujo']").val();
        var tipo_folio = $("select[name='tipo_folio']").val();
        var tipo_avance = $("select[name='tipo_avance']").val();
        var tipo_asignacion_folio = $("select[name='tipo_asignacion_folio']").val();
        var requiere_fe = $("select[name='fe']").val();
        var plantilla_encabezado = editor_encabezado.getData();
        var plantilla_cuerpo = editor_cuerpo.getData();
        var hiddTipoDocumento = $("input[name='hiddTipoDocumento']").val();
 
        var datosBuzonFlujo = [];

        if (tipo_flujo == 2 || tipo_flujo == 3)
        {
            var nOrden = 1;

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
                
                var valorId = null;

                if ($("input[name='hiddId_"+i+"']").val() != null)
                    valorId = $("input[name='hiddId_"+i+"']").val();

                if ($("input[name='hiddIdBuzon_"+i+"']").val() != null)
                {
                    var filaBuzones = {
                        "id_tipo_documento_buzon": valorId,
                        "id_buzon":$("input[name='hiddIdBuzon_"+i+"']").val(),
                        //"orden":$("input[name='hiddIdOrden_"+i+"']").val(),
                        "orden":nOrden,
                        "acciones": datosAcciones
                    }

                    nOrden++;

                    datosBuzonFlujo.push(filaBuzones);
                }
               
            }  
                 
        } 
        
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
                plantilla_encabezado:plantilla_encabezado,
                plantilla_cuerpo:plantilla_cuerpo,
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
