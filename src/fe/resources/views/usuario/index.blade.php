@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')


<div class="row">
    <div class="col-8">
        <h1>Usuarios</h1>
    </div>
    <div class="col">
        <button type="button" class="btn btn-success nuevo_usuario">Nuevo Usuario</button>
    </div>
</div>
<div class="linea_content_header"></div>

@stop

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card" id="card_usuario_grilla">
            <div class="card-body">
                <table id="tabla_usuario_grilla" class="table dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>RUN</th>
                            <th>Email(Cuenta)</th>
                            <th>N° Buzones</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lista_usuarios['data'] as $list)
                        <tr @if($list['id_estado_usuario']==2)style="background-color:#e2e2e2"@endif>
                            <td>{{$list['id']}}</td>
                            <td><a href="javascript:void(0)" onClick="visualizar_usuario( {{$list['id']}} )">{{$list['nombres'].' '.$list['primer_apellido'].' '.$list['segundo_apellido']}}</td>
                            <td>{{$list['run']}}</td>
                            <td>{{$list['email']}}</td>
                            <td>{{count($list['usuarios_buzon'])}}</td>
                            <td>
                                <?php
                                foreach($estados_usuario as $estado)
                                if($estado['id_estado_usuario']==$list['id_estado_usuario']){
                                        echo $estado['nombre'];
                                }

                            ?>
                            </td>

                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item btn-menu-ver" onclick="visualizar_usuario({{$list['id']}})"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>
                                        <a class="dropdown-item btn-menu-editar" onclick="editar_usuario({{$list['id']}})"  href="#"><i class="fas fa-edit text-blue"></i> Editar</a>
                                        <a class="dropdown-item btn-menu-deshabilitar" onclick="estado_usuario({{$list['id']}})" href="#">
                                            @if($list['id_estado_usuario']==1)
                                                <i class="fas fa-trash-alt text-red"></i> Deshabilitar
                                            @endif
                                            @if($list['id_estado_usuario']==2)
                                                <i class="fas fa-plus-circle text-green"></i> Habilitar
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
        <div class="alert alert-warning print-error-msg" style="display:none">
            <ul></ul>
        </div>
        <div id='cargando' style="display:none">
            <span class="spinner-border text-success" role="status" aria-hidden="true"></span>
            Cargando...
        </div>
    </div>
</div>    

<div class="row">
    <div class="col-12">

    <div class="card" id="card_usuario_crear_editar" style="display:none">
       
        
        <div class="card-header" >
        <div class="d-flex align-items-center">
            <button class="btn btn-default btn-sm mr-2 btn-volver-listado " ><i class="fa fa-list"></i></button>
            <h4 class="mb-0 flex-fill" id="titulo_usuario_crear_editar">Nuevo Usuario</h4>
            <button class=" btn btn-default btn-sm ml-2 btn-editar-usuario"  data-identificador=""><i class="fa fa-edit"></i></button>
        </div>
            
            <div class="linea_content_header"></div>
        </div>
        
        <div class="card-body">
            <form class="needs-validation" id="form_usuario_crear_editar" method="POST"  enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="form_run">RUN</label>
                        <input type="text" class="form-control" id="form_run" name="run" aria-describedby="run_error" placeholder="99229922-1" required>
                        <input type="hidden" name="form_id_usuario" value="">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_perfil">Perfil</label>
                        <select  class="form-control" id="form_perfil" name="id_perfil" required>
                            <option value="">Seleccionar</option>
                            @foreach($perfiles as $perfil)
                                <option value="{{$perfil['id_perfil']}}">{{$perfil['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_estado">Estado</label>
                        <select  class="form-control" id="form_estado" name="id_estado_usuario" required>
                            <option value="">Seleccionar</option>

                            @foreach($estados_usuario as $estado)
                                <option value="{{$estado['id_estado_usuario']}}">{{$estado['nombre']}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_nombres">Nombres</label>
                        <input type="text" class="form-control " id="form_nombres" aria-describedby="nombres_error" placeholder="" name="nombres" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_primer_apellido">Primer Apellido</label>
                        <input type="text" class="form-control " id="form_primer_apellido" name="primer_apellido" aria-describedby="primer_apellido_error" placeholder="" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_segundo_apellido">Segundo Apellido</label>
                        <input type="text" class="form-control" id="form_segundo_apellido" name="segundo_apellido" aria-describedby="segundo_apellido_error" placeholder="">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="form_contacto">Número Contacto</label>
                        <input  type="text" class="form-control" id="form_contacto" name="form_contacto" aria-describedby="run_error" placeholder="999999999" value="" >
                        
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="for_cargo">Cargo</label>
                        <input type="text" class="form-control" id="form_cargo" name="form_cargo" placeholder="administrativo" aria-describedby="cargo_error"  value="">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="for_foto">Fotografía Perfil</label>
                        <input type="file" accept="image/*" class="form-control" id="form_foto" name="form_foto" aria-describedby="imagen_error" placeholder="">
                        <input type="hidden" id="hiddFoto" name="hiddFoto" value="" >
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                   <span id="displayImgPerfil"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_contrasena">Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="form_contrasena" name="password" aria-describedby="contrasena_error"  >
                            <div class="input-group-append">
                                <i class="btn border fa fa-eye" id="mostrarC"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_reescribir_contrasena">Reescribir Contraseña</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="form_reescribir_contrasena" name="re_password" aria-describedby="reescribir_contrasena_error" >
                            <div class="input-group-append">
                                <i class="btn border fa fa-eye" id="mostrarCC"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_email">Email (Usuario)</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="form_email" name="email" aria-describedby="email_error" placeholder="" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_firma_electronica">Firma electrónica avanzada</label>
                        <select  class="form-control" id="form_aplica_fea" name="aplica_fea">
                            <option value="">Seleccionar</option>
                            <option value="true">Habilitada</option>
                            <option value="false">Denegada</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_generacion_pdf">Generación PDF</label>
                        <select  class="form-control" id="form_genera_pdf" name="aplica_genera_pdf">
                            <option value="">Seleccionar</option>
                            <option value="true">Habilitada</option>
                            <option value="false">Denegada</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                    <label for="input_imagen_firma">Imagen de firma</label>                        
                        <input type="file" accept="image/*" class="form-control" id="form_imagen_firma" name="form_imagen_firma" aria-describedby="imagen_error" placeholder="">
                        <input type="hidden" id="hiddFirma" name="hiddFirma" >
                        <div class="displayImg"></div>                       
                    </div>
                </div>
            </div>
            <div class="row">
           
                <div class="col-md-12">
                    <select multiple="multiple" size="10" name="buzonusuario[]" id="buzonusuario" class="duallist" title="">
                        @foreach ($buzones as $item)
                            <option value="{{$item["id_buzon"]}}">{{ $item["nombre"]}}</option>
                        @endforeach
                    </select> 
                </div>
                <div class="col-md-4">
                    <div class="form-group">    
                        
                     </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                       
                    </div>
                </div>
            </div>
            <div class="align-items-end d-flex justify-content-end">
                <button type="button"  class="btn btn-secondary w-25 btn_cerrar_guardar">Cerrar</button>
                <button type="button" class="btn btn-success btn-acciones-guardar-editar btn-submit w-25 ml-2">Guardar</button>
                <button type="button" class="btn btn-success btn-acciones-guardar-editar btn-actualizar w-25 ml-2">Actualizar</button>
            </div>
            </div>

            </form>

        </div>
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

        .clear1, .clear2
        {
            display:none;
        }
        

     </style>
@stop

@section('js')
<script src="js/jquery.bootstrap-duallistbox.js"></script>
<script>
 $(document).ready(function () {

        $('#tabla_usuario_grilla').DataTable({
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            language: lenguaje_datatable
        });

        $(".nuevo_usuario").click(function(e){
            $('.btn-acciones-guardar-editar').hide();
            $('#titulo_usuario_crear_editar').html('Nuevo Usuario');
            $(".print-error-msg").hide();
            $('#form_usuario_crear_editar').removeClass("was-validated");
            $('#form_usuario_crear_editar').trigger("reset");
            $('.form-control').prop("disabled", false);
            $('#card_usuario_crear_editar').show();
            $('.btn-submit').show();
            $('#form_run').focus();
            $(".displayImg").html('');
            $("#displayImgPerfil").html('');
        });

        $(".btn_cerrar_guardar").click(function(e){
            $('#card_usuario_grilla').show();
            $('#card_usuario_crear_editar').hide();
            $('#form_usuario_crear_editar').trigger("reset");
            $(".print-error-msg").hide();
            $('#form_usuario_crear_editar').removeClass("was-validated");
            $(".displayImg").html('');
        });

        $(".btn-actualizar").click(function(e){
            e.preventDefault();
            $('.btn-actualizar').prop("disabled", true);
            $('.btn-actualizar').html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Actualizando'
            );
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                if (form.checkValidity() === false) {
                        //e.stopPropagation();
                        form.classList.add('was-validated');
                }
            });

            $(".print-error-msg").hide();
            
            var password = $("input[name='password']").val();
            var re_password = $("input[name='re_password']").val();
            var files = $('#form_imagen_firma')[0].files;
           
            if(files.length == 0 && $("input[name='hiddFirma']").val() == '' && $("select[name='aplica_fea']").val() == 'true')
            {
                printErrorMsg({'Firma':'Debe ingresar imagen de firma'});
                $('.btn-actualizar').prop("disabled", false);
                $('.btn-actualizar').html( 'Actualizar' );
            }
            else 
            {
                if(password == re_password)
                {
                    var formData = new FormData($("#form_usuario_crear_editar")[0]);
                    $.ajax({
                        url: "{{route('usuarios.update')}}",
                        type:'POST',
                        data:formData,
                        contentType: false,
                        cache: false,
                        processData: false,                    
                        success: function(data) {
                            if(data.status=='400'){
                                toastr.error(data.data.comentario,"Aviso!");

                            }else{
                                if(data.status=='200' || data.status=='201'){
                                    $('#card_usuario_crear_editar').hide();
                                    $('#form_usuario_crear_editar').trigger("reset");
                                    toastr.success("Guardado exitoso","Aviso!");
                                    location.reload();
                                }
                            }
                            $('.btn-actualizar').prop("disabled", false);
                            $('.btn-actualizar').html( 'Actualizar' );
                        },
                        error: function (e) {
                            data = e.responseJSON;
                            if (typeof data.errors !== 'undefined') {
                                printErrorMsg(data.errors);
                            }
                            $('.btn-actualizar').prop("disabled", false);
                            $('.btn-actualizar').html( 'Actualizar' );
                        }
                    });
                }else{
                    printErrorMsg({'password':'Las contraseñas no coinciden.'});
                    $('.btn-actualizar').prop("disabled", false);
                    $('.btn-actualizar').html( 'Actualizar' );
                }
            }
        });

        $(".btn-submit").click(function(e){
            
            e.preventDefault();
            $('.btn-submit').prop("disabled", true);
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
            
            var password = $("input[name='password']").val();
            var re_password = $("input[name='re_password']").val();
            
            var files = $('#form_imagen_firma')[0].files;
           
            if(files.length == 0 && $("input[name='hiddFirma']").val() == '' && $("select[name='aplica_fea']").val() == 'true')
            {
                printErrorMsg({'Firma':'Debe ingresar imagen de firma'});
                $('.btn-submit').prop("disabled", false);
                $('.btn-submit').html( 'Actualizar' );
            }
            else 
            {
                var formData = new FormData($("#form_usuario_crear_editar")[0]);

                if(password == re_password){
                    $.ajax({
                        url: "{{route('usuarios.store')}}",
                        type:'POST',
                        data:formData,
                        contentType: false,
                        cache: false,
                        processData: false,     
                        success: function(data) {
                            if(data.status=='400'){
                                toastr.error(data.data.comentario,"Aviso!");
                            }else{
                                if(data.status=='200' || data.status=='201'){
                                    $('#card_usuario_crear_editar').hide();
                                    $('#form_usuario_crear_editar').trigger("reset");
                                    toastr.success("Guardado exitoso","Aviso!");

                                    location.reload();
                                }
                            }
                            $('.btn-submit').prop("disabled", false);
                            $('.btn-submit').html( 'Guardar' );
                        },
                        error: function (e) {
                            data = e.responseJSON;
                            if (typeof data.errors !== 'undefined') {
                                printErrorMsg(data.errors);
                            }
                            $('.btn-submit').prop("disabled", false);
                            $('.btn-submit').html( 'Guardar' );
                        }
                    });
                }else{
                    printErrorMsg({'password':'Las contraseñas no coinciden.'});
                    $('.btn-submit').prop("disabled", false);
                    $('.btn-submit').html( 'Guardar' );
                }
            }


        });

        // botones de la tabla
        $(document).on('click', '#btnEdit', function () {
            var data = table.row($(this).parents('tr')).data();
            alert(data['name']);
        });

        $(document).on('click', '#btnDelete', function () {
            var data = table.row($(this).parents('tr')).data();
            alert(data['salary']);
        });

        $('#mostrarC').on('click', function(){
            var passInput=$("#form_contrasena");
            if(passInput.attr('type')==='password'){
                passInput.attr('type','text');
                $('#mostrarC').removeClass('fa-eye');
                $('#mostrarC').addClass('fa-eye-slash');
            }else{
                passInput.attr('type','password');
                $('#mostrarC').removeClass('fa-eye-slash');
                $('#mostrarC').addClass('fa-eye');
            }
        });

        $('#mostrarCC').on('click', function(){
            var passInput=$("#form_reescribir_contrasena");
            if(passInput.attr('type')==='password'){
                passInput.attr('type','text');
                $('#mostrarCC').removeClass('fa-eye');
                $('#mostrarCC').addClass('fa-eye-slash');
            }else{
                passInput.attr('type','password');
                $('#mostrarCC').removeClass('fa-eye-slash');
                $('#mostrarCC').addClass('fa-eye');
            }
        });

        $(".btn-volver-listado").on('click',function(){
            $(".btn_cerrar_guardar").trigger('click');
        });
        $(".btn-editar-usuario").on('click',function(){
            editar_usuario($(".btn-editar-usuario").data('identificador'));
        });



        var duallist = $('#buzonusuario').bootstrapDualListbox({
            nonSelectedListLabel: 'Buzones disponibles:',
            selectedListLabel: 'Buzones asignados:',
            preserveSelectionOnMove: false,
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
        // cambiar iconos
        $('#buzonusuario').bootstrapDualListbox('getContainer').find('.move i').removeClass().addClass('fa fa-arrow-right');
        $('#buzonusuario').bootstrapDualListbox('getContainer').find('.remove i').removeClass().addClass('fa fa-arrow-left');

    });

    function editar_usuario(identificador){

        $('#card_usuario_grilla').hide();
        $('#cargando').show();
        $(".print-error-msg").hide();
        $(".btn-editar-usuario").addClass('d-none');
        if(identificador>0){
            $('#form_usuario_crear_editar').trigger("reset");
            $('#card_usuario_crear_editar').hide();
            $.ajax({
                        url: "usuarios/"+identificador,
                        type:'GET',
                        success: function(data) {
                            if(data.status==400){
                                toastr.error(data.data.comentario,"Aviso!");
                            }else{
                                console.log(data);
                                if(data.status==200 || data.status==201){
                                    $('#form_usuario_crear_editar').trigger("reset");
                                    $("select[name='id_perfil']").val(data.data.id_perfil);
                                    $("select[name='id_estado_usuario']").val(data.data.id_estado_usuario);
                                    $("input[name='run']").val(data.data.run);
                                    $("input[name='nombres']").val(data.data.nombres);
                                    $("input[name='primer_apellido']").val(data.data.primer_apellido);
                                    $("input[name='segundo_apellido']").val(data.data.segundo_apellido);
                                    $("input[name='email']").val(data.data.email);
                                    $("input[name='form_id_usuario']").val(data.data.id);
                                    $("input[name='form_contacto']").val(data.data.numero_contacto);
                                    $("input[name='form_cargo']").val(data.data.cargo);
                                    if(data.data.aplica_fea==true){
                                        $("select[name='aplica_fea']").val('true');
                                    }else if(data.data.aplica_fea==false){
                                        $("select[name='aplica_fea']").val('false');
                                    }
                                    if(data.data.genera_pdf==true){
                                        $("select[name='aplica_genera_pdf']").val('true');
                                    }else if(data.data.genera_pdf==false){
                                        $("select[name='aplica_genera_pdf']").val('false');
                                    }
                                    
                                    //cargar imagen                                    
                                    $("input[name='hiddFirma']").val(data.data.img_firma); 
                                    $("input[name='hiddFoto']").val(data.data.img_perfil);

                                    //mostrar imagen
                                    if (data.data.img_firma != "" && data.data.img_firma != null)
                                    {
                                        var pathImg = '/files/imagen_firma/' + data.data.img_firma;
                                        $(".displayImg").html('<img src="'+pathImg+'"  width="300" height="100"/>');
                                    } 
                                    else
                                        $(".displayImg").html('');
                                    
                                    //mostrar foto perfil
                                    if (data.data.img_perfil != "" && data.data.img_perfil != null)
                                    {
                                        var pathImg = '/files/imagen_perfil/' + data.data.img_perfil;
                                        $("#displayImgPerfil").html('<a href="'+pathImg+'" target="_blank" title="Ver foto"><img src="'+pathImg+'"  width="100" height="100"/></a>');
                                    } 
                                    else
                                        $("#displayImgPerfil").html('');
                                    //seleccionar buzones disponibles
                                    data.data.usuarios_buzon.forEach(function(option, index) {
                                        $('#buzonusuario option[value="'+option.id_buzon+'"]').prop('selected', true);
                                    });
                                    $('#buzonusuario').bootstrapDualListbox('refresh', true);

                                }
                            }
                            $('.btn-submit').prop("disabled", false);
                            $('.btn-submit').html( 'Guardar' );
                            $('#cargando').hide();
                            $('.btn-acciones-guardar-editar').hide();
                            $('#titulo_usuario_crear_editar').html('Editar Usuario');
                            $('.btn-actualizar').show();
                            $('#form_run').focus();
                            $('.form-control').prop("disabled", false);
                            $('#card_usuario_crear_editar').show();
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

    function visualizar_usuario(identificador){
        $('#card_usuario_grilla').hide();
        $('#cargando').show();
        $(".print-error-msg").hide();

        $(".btn-editar-usuario").data('identificador',identificador);
        $(".btn-editar-usuario").removeClass('d-none');
        if(identificador>0){
            $('#form_usuario_crear_editar').trigger("reset");
            $('#card_usuario_crear_editar').hide();
            $.ajax({
                        url: "usuarios/"+identificador,
                        type:'GET',
                        success: function(data) {
                            if(data.status==400){
                                toastr.error(data.data.comentario,"Aviso!");

                            }else{
                                console.log(data);
                                if(data.status==200 || data.status==201){
                                    //$('#form_usuario_crear_editar').trigger("reset");
                                    $("select[name='id_perfil']").val(data.data.id_perfil);
                                    $("select[name='id_estado_usuario']").val(data.data.id_estado_usuario);
                                    $("input[name='run']").val(data.data.run);
                                    $("input[name='nombres']").val(data.data.nombres);
                                    $("input[name='primer_apellido']").val(data.data.primer_apellido);
                                    $("input[name='segundo_apellido']").val(data.data.segundo_apellido);
                                    $("input[name='email']").val(data.data.email);
                                    if(data.data.aplica_fea==true){
                                        $("select[name='aplica_fea']").val('true');
                                    }else if(data.data.aplica_fea==false){
                                        $("select[name='aplica_fea']").val('false');
                                    }
                                    if(data.data.genera_pdf==true){
                                        $("select[name='aplica_genera_pdf']").val('true');
                                    }else if(data.data.genera_pdf==false){
                                        $("select[name='aplica_genera_pdf']").val('false');
                                    }
                                    //mostrar imagen
                                    if (data.data.img_firma != "" && data.data.img_firma != null)
                                    {
                                        var pathImg = '/files/imagen_firma/' + data.data.img_firma;
                                        $(".displayImg").html('<img src="'+pathImg+'"  width="300" height="100"/>');
                                    } 
                                    else
                                        $(".displayImg").html('');
                                    //seleccionar buzones disponibles
                                    data.data.usuarios_buzon.forEach(function(option, index) {
                                        $('#buzonusuario option[value="'+option.id_buzon+'"]').prop('selected', true);
                                    });
                                    $('#buzonusuario').bootstrapDualListbox('refresh', true);
                                }
                            }
                            $('#cargando').hide();
                            $('.btn-acciones-guardar-editar').hide();
                            $('#titulo_usuario_crear_editar').html('Visualizar Usuario');
                            $('#form_run').focus();
                            $('.form-control').prop("disabled", true);
                            $('#card_usuario_crear_editar').show();
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
    function estado_usuario(id)
    {
        Swal.fire({
            title: 'Usuario',
            text: "¿Quiere cambiar el estado del usuario?",
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
                        url: "usuarios/"+id,
                        type:'PUT',
                        dataType: 'json',
                        data: {_token :token},
                        success: function(data) {
                            if(data.status == '200')
                            {
                                toastr.success("Usuario Actualizado","Aviso!");
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

    function printErrorMsg(msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").show();
            $.each( msg, function( key, value ) {
                console.log(key);
                if(key=='run'){
                    $("input[name='run']").val('');
                    if(value[0]=='The RUN is not valid.'){
                        value[0]='El RUN no es valido.'
                    }
                }
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
