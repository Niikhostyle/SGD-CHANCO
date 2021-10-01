@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')


    <div class="row">
        <div class="col-8">
            <h1>Buzones</h1>
        </div>
        <div class="col">
            <button type="button" class="btn btn-success nuevo_buzon">Nuevo Buzón</button>

        </div>
      </div>
    <div class="linea_content_header"></div>

@stop

@section('content')
<div class="container">
    <div class="card" id="card_buzones_grilla">
        <div class="card-body">
            <table id="tabla_buzones_grilla" class="table dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre Buzón</th>
                        <th>Nombre Corto</th>
                        <th>Usuarios Asignados</th>
                        <th>Usuarios con FEA habilitada</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listado_buzones as $list)
                    <tr>
                        <td>{{$list['id_buzon']}}</td>
                        <td>{{$list['nombre']}}</td>
                        <td>{{$list['nombre_corto']}}</td>
                        <td>{{$list['total_us_asignados']}}</td>
                        <td>{{$list['total_us_fea']}}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                     <i class="fas fa-bars"></i>
                                 </button>
                                 <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                     <a class="dropdown-item btn_ver" onclick="ver_buzon({{$list['id_buzon']}},1)" href="#"><i class="fas fa-eye text-blue"></i> Ver</a>
                                     <a class="dropdown-item btn_editar" onclick="ver_buzon({{$list['id_buzon']}},2)" href="#"><i class="fas fa-edit text-blue"></i> Editar</a>
                                     <a class="dropdown-item" onclick="eliminar_buzon({{$list['id_buzon']}})" href="#"><i class="fas fa-trash-alt text-red"></i> Eliminar</a>
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
    <div class="card" id="card_buzon_crear_editar" style="display:none">
        <h5 id="titulo_buzon_crear_editar"class="card-header bg-success" >Nuevo Buzón</h5>
        <div class="card-body">
            <form class="needs-validation" id="form_buzon_crear_editar" method="POST" action="{{route('buzones.store')}}"  >
            @csrf

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="input_nombre">Nombre:</label>
                        <input type="text" class="form-control " id="form_nombre" aria-describedby="nombre_error" placeholder="" value="" name="nombre" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_nombre_corto">Nombre corto:</label>
                        <input type="text" class="form-control " id="form_nombre_corto" name="nombre_corto" value="" aria-describedby="nombre_corto_error" placeholder="" required>
                    </div>
                </div>
                
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">

                        <select multiple="multiple" size="10" name="duallistbox" id="duallistbox" class="duallist" title="">
                            
                            @foreach($listado_usuarios as $disponibles)
                                <option value="{{$disponibles['id']}}">{{$disponibles['nombres']}} {{$disponibles['primer_apellido']}}</option>
                            @endforeach

                        </select>                        
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
                    <input type="hidden" name="hiddBuzon" id="hiddBuzon" value="">
                    
                </div>
            </div>

            </form>

        </div>
    </div>


</div>

@stop

@section('css')

    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-duallistbox.css">

    <style type="text/css">
    
        .bootstrap-duallistbox-container .btn.moveall,
        .bootstrap-duallistbox-container .btn.removeall {
            display: none;
        }
      
        .bootstrap-duallistbox-container .btn.move,
        .bootstrap-duallistbox-container .btn.remove {
            width: 40%;
            height: 30%;
            margin: 20px;
        }

        .customButtonBox {
            margin-top:80px;
        }
        
        .form-control.is-valid, .was-validated .form-control:valid {
            border-color: none !important;
            background-image: none;
        }

        .clear1, clear2
        {
            display:none;
        }
        

     </style>
@stop

@section('js')

<script src="js/jquery.bootstrap-duallistbox.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>

//$(document).ready(function () {

    
    var duallist = $('.duallist').bootstrapDualListbox({
        nonSelectedListLabel: 'Usuarios disponibles:',
        selectedListLabel: 'Usuarios asignados:',
        preserveSelectionOnMove: 'moved',
        moveOnSelect: false,   
        filterPlaceHolder: '',
        filterTextClear: '',
        selectorMinimalHeight:'200',
        infoTextEmpty:'',
        infoText:'',
        moveSelectedLabel:'Mover Asignados',
        removeSelectedLabel:'Mover Disponibles',
        infoTextFiltered: '' 

    });         

    CustomizeDuallistbox('duallistbox');
    changeIcons();

    function changeIcons()
    {
        var dualListContainer = $('select[name="duallistbox"]').bootstrapDualListbox('getContainer');
        dualListContainer.find('.move i').removeClass().addClass('fa fa-arrow-right');//fas fa-arrow-alt-to-right <i class="fas fa-arrow-alt-circle-right"></i>
        dualListContainer.find('.remove i').removeClass().addClass('fa fa-arrow-left');
    }

    function CustomizeDuallistbox(listboxID) {
        var customSettings = $('#' + listboxID).bootstrapDualListbox('getContainer');
        var buttons = customSettings.find('.btn.moveall, .btn.move, .btn.remove, .btn.removeall');

        customSettings.find('.box1, .box2').removeClass('col-md-6').addClass('col-md-5');
        customSettings.find('.box1').after('<div class="customButtonBox col-md-2 text-center"></div>');
        customSettings.find('.customButtonBox').append(buttons);

        customSettings.find('.btn-group.buttons').remove();
    }

    //seleccion de usuarios asignados en select
    function dlb_repopulate (asignados, modificados) 
    {
        $('[name=duallistbox] option').prop('selected', false);
        $('[name=duallistbox] option').prop('disabled', false);

        asignados.forEach(function(option, index) {
            $('[name=duallistbox] option[value="'+option+'"]').prop('selected', true);

            if(modificados[index] == 0)
                $('[name=duallistbox] option[value="'+option+'"]').prop('disabled', true);
         });

        $('[name=duallistbox]').bootstrapDualListbox('refresh', true);
    }

    function ver_buzon(id,op)
    { 
        $(".print-error-msg").hide(); 
        $('#form_buzon_crear_editar').trigger("reset"); 
        $('#card_buzon_crear_editar').show(); 
        $('select#duallistbox').bootstrapDualListbox('refresh', true);

        if (op == 2)
        {
            $('#titulo_buzon_crear_editar').html('Editar Usuario'); 
            $('.btn-submit').show();
        }            
        else
        {
            $('#titulo_buzon_crear_editar').html('Ver Usuario'); 
            $('.btn-submit').prop("disabled", false); 
            $('.btn-submit').hide(); 
        }

        $.ajax({ 
                url: "buzones/"+id, 
                type:'GET', 
                dataType: 'json',
                success: function(data) { 
                    if(data.status=='400'){ 
                        Swal.fire({ 
                        icon: 'error', 
                        title: 'Oops...', 
                        text: data.data.comentario, 
                        confirmButtonText: 'Cerrar', 
                    }); 
                    }else{ 
                        if(data.status=='200' || data.status=='201'){ 

                            $("input[name='nombre']").val(data.data.nombre);
                            $("input[name='nombre_corto']").val(data.data.nombre_corto);
                            $("input[name='hiddBuzon']").val(data.data.id_buzon);
                                                        
                            var aUsuarios = [];
                            var aUsuariosModificar = [];

                            $.each(data.data.usuarios_asignados, function(key, item) 
                            {
                                aUsuarios.push(item.id_usuario);
                                aUsuariosModificar.push(item.permite_modificar);
                            });
                            

                            //seleccionar los usuarios asignados
                            //dlb_repopulate(['3', '1']);
                            dlb_repopulate(aUsuarios, aUsuariosModificar);
                        } 
                    } 
                    $('.btn-submit').prop("disabled", false); 
                    //$('.btn-submit').html( 'Actualizar' ); 
                }, 
                error: function (e) { 
                    data = e.responseJSON; 
                    if (typeof data.errors !== 'undefined') { 
                        printErrorMsg(data.errors); 
                    } 
                    $('.btn-submit').prop("disabled", false); 
                    //$('.btn-submit').html( 'Guardar' ); 
                } 
            }); 

            
    }

    function eliminar_buzon(id)
    { 
        $(".print-error-msg").hide(); 
        var token = $("input[name='_token']").val();

        $.ajax({ 
                url: "buzones/"+id, 
                type:'DELETE', 
                dataType: 'json',
                data: {_token :token},
                success: function(data) { 
                    if(data.status == '200')
                    { 
                        toastr.success("Buzón eliminado","Aviso!");                     
                        autoRefresh();
                    } 
                    else
                    {                         
                        toastr.error(data.data.comentario,"Aviso!");
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

    $('#tabla_buzones_grilla').DataTable({
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        responsive: true,
        language: lenguaje_datatable
    });


    $(".nuevo_buzon").click(function(e){
        $('#form_buzon_crear_editar').trigger("reset");
        $('#titulo_buzon_crear_editar').html('Nuevo Buzón');
        $('#card_buzon_crear_editar').show();
        $('.btn-submit').show();
        
        $('[name=duallistbox] option').prop('selected', false);
        $('[name=duallistbox] option').prop('disabled', false);
        $('select#duallistbox').bootstrapDualListbox('refresh', true);
        
    });

    $(".btn_cerrar_guardar").click(function(e){
        $('#card_buzon_crear_editar').hide();
        $('#form_buzon_crear_editar').trigger("reset");
    });


    $(".btn-submit").click(function(e){
        e.preventDefault();
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            if (form.checkValidity() === false) {
                form.classList.add('was-validated');
            }
        });

        $(".print-error-msg").hide();
        var _token = $("input[name='_token']").val();
        var nombre = $("input[name='nombre']").val();
        var nombre_corto = $("input[name='nombre_corto']").val();
        var hiddBuzon = $("input[name='hiddBuzon']").val();
        var usuarios_asignados = $('[name="duallistbox"]').val();

        if (hiddBuzon == '') //crear
        {
            var urlAccion = "{{route('buzones.store')}}";
            var typeAccion = 'POST';
        }
        else //editar
        {
            var urlAccion = "{{route('buzones.update')}}";
            var typeAccion = 'PUT';
        }    
        
        $.ajax({
            url: urlAccion,
            type: typeAccion,
            data: { 
                    _token:_token, nombre:nombre, nombre_corto:nombre_corto, usuarios_asignados:usuarios_asignados, hiddBuzon:hiddBuzon                       
                  },
            success: function(data) 
            {
                if(data.status == '200')
                {
                    toastr.success("Buzón actualizado","Aviso!");
                    autoRefresh();
                }
                else if(data.status == '201')
                {
                    toastr.success("Buzón creado","Aviso!");                  
                    autoRefresh();
                }
                else
                {
                    toastr.error(data.data.comentario,"Aviso!");                    
                }
            },
            error: function (e) {
                data = e.responseJSON;
                if (typeof data.errors !== 'undefined') {
                    printErrorMsg(data.errors);
                }
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


//});


</script>
@stop
