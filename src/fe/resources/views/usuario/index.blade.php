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
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lista_usuarios['data'] as $list)
                        <tr @if($list['id_estado_usuario']==2)style="background-color:#e2e2e2"@endif>
                            <td>{{$list['id']}}</td>
                            <td>{{$list['nombres'].' '.$list['primer_apellido'].' '.$list['segundo_apellido']}}</td>
                            <td>{{$list['run']}}</td>
                            <td>{{$list['email']}}</td>
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
            <h4 id="titulo_usuario_crear_editar">Nuevo Usuario</h4>
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_contrasena">Contraseña</label>
                        <input type="password" class="form-control" id="form_contrasena" name="password" aria-describedby="contrasena_error"  >
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_reescribir_contrasena">Reescribir-Contraseña</label>
                        <input type="password" class="form-control" id="form_reescribir_contrasena" name="re_password" aria-describedby="reescribir_contrasena_error" >

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
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_imagen_firma">Imagen de firma</label>                        
                        <input type="file" accept="image/*" class="form-control" id="form_imagen_firma" name="form_imagen_firma" aria-describedby="imagen_error" placeholder="">
                        <input type="hidden" id="hiddFirma" name="hiddFirma" >

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <img id="displayImg" name="displayImg" src= "files/imagen_firma/66666666-6" with="150px" height="50px"> 
                        <span id="uploaded_image"></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                       
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8"> </div>
                <div class="col-md-2">
                    <button type="button"  class="btn btn-secondary w-100 btn_cerrar_guardar">Cerrar</button>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success btn-acciones-guardar-editar btn-submit w-100">
                        Guardar
                    </button>
                    <button type="button" class="btn btn-success btn-acciones-guardar-editar btn-actualizar w-100">
                        Actualizar
                    </button>
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
@stop

@section('js')
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
        });

        $(".btn_cerrar_guardar").click(function(e){
            $('#card_usuario_crear_editar').hide();
            $('#form_usuario_crear_editar').trigger("reset");
            $(".print-error-msg").hide();
            $('#form_usuario_crear_editar').removeClass("was-validated");
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
    });

    function editar_usuario(identificador){
        $('#cargando').show();
        $(".print-error-msg").hide();
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

                                    //mostrar imagen
                                    if (data.data.img_firma != "")
                                    {
                                        var pathImg = '/files/imagen_firma/'.data.data.img_firma;
                                       // $('#displayImg').attr('src', data.data.img_firma);
                                        
                                        //$('#displayImg').show();
console.log(pathImg);
                                        img.src = "/images/img1.gif";
                                        $('#displayImgd').html(img); 
                                    }                           
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
        $('#cargando').show();
        $(".print-error-msg").hide();
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
