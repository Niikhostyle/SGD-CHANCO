@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')
<div class="row">
    <div class="col-8">
        <h1>Perfil de Usuario</h1>
    </div>
</div>
<div class="linea_content_header"></div>

@stop
@section('content')

<div id="collapseOne" class="card" aria-labelledby="headingOne" data-parent="#carpetas">
    <div class="card-body">
        <div class="alert alert-warning print-error-msg" style="display:none">
            <ul></ul>
        </div>
        <form class="needs-validation" id="fEditarPerfil" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" value="{{ $datos[0]->run }}" name="run" />
            <input type="hidden" value="{{ $datos[0]->nombres }}" name="nombres" />
            <input type="hidden" value="{{ $datos[0]->primer_apellido }}" name="primer_apellido" />
            <input type="hidden" value="{{ $datos[0]->segundo_apellido }}" name="segundo_apellido" />
            <input type="hidden" value="{{ $datos[0]->email }}" name="email" />
            <input type="hidden" value="{{ $datos[0]->aplica_fea }}" name="aplica_fea" />
            <input type="hidden" value="{{ $datos[0]->genera_pdf }}" name="genera_pdf" />
            <input type="hidden" value="{{ $datos[0]->id_perfil }}" name="id_perfil" />
            <input type="hidden" value="{{ $datos[0]->id_estado_usuario }}" name="id_estado_usuario" />
            <input type="hidden" value="{{ $datos[0]->img_firma }}" id="hiddFirma" name="hiddFirma" />
            <div class="row">
                <div class="col-lg-5 d-block d-lg-none text-center">
                    <div class="form-group">
                        <span id="foto_perfil_md"></span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="form_run">RUT</label>
                            </div>
                            <div class="col-md-9">
                                <input disabled type="text" class="form-control" id="form_run" name="form_run" aria-describedby="run_error" placeholder="99229922-1" value="{{$datos[0]->run}}">
                            </div>
                        </div>
                        <input type="hidden" name="form_id_usuario" value="{{$datos[0]->id}}">
                    </div>
                    <div class="form-group">
                        <div class="row">
                                <div class="col-md-3">
                                    <label for="form_nombres">Nombres</label>
                                </div>
                                <div class="col-md-9">
                                    <input disabled type="text" class="form-control" id="form_nombres" name="form_nombres" aria-describedby="nombres_error" value="{{$datos[0]->nombres}}" required="">
                                </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="form_nombres">Apellido Paterno</label>
                            </div>
                            <div class="col-md-9">
                                <input disabled type="text" class="form-control" id="form_primer" name="form_primer" aria-describedby="primer_error" value="{{$datos[0]->primer_apellido}}" required="">
                            </div>
                        </div>
                        
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="form_nombres">Apellido Materno</label>
                            </div>
                            <div class="col-md-9">
                                <input disabled type="text" class="form-control" id="form_segundo" name="form_segundo" aria-describedby="segundo_error" value="{{$datos[0]->segundo_apellido}}" required="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="for_correo">Correo Electrónico</label>
                            </div>
                            <div class="col-md-9">
                                <input disabled type="text" class="form-control" id="form_correo" name="form_correo" aria-describedby="run_error" value="{{$datos[0]->email}}" required="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="for_foto">Fotografía Perfil</label>
                            </div>
                            <div class="col-md-9">
                                <input type="file" accept="image/*" class="form-control" id="form_foto" name="form_foto" aria-describedby="imagen_error" placeholder="">
                                <input type="hidden" id="hiddFoto" name="hiddFoto" value="{{ $datos[0]->img_perfil }}" >
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="for_cargo">Cargo</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control" id="form_cargo" name="form_cargo" placeholder="administrativo" aria-describedby="cargo_error"  value="{{$datos[0]->cargo}}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="form_contacto">Número Contacto</label>
                            </div>
                            <div class="col-md-9">
                                <input  type="text" class="form-control" id="form_contacto" name="form_contacto" aria-describedby="run_error" placeholder="999999999" value="{{$datos[0]->numero_contacto}}" >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <div class="form-group">
                        <span id="foto_perfil"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        Cambiar Contraseña&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="btnCambiar"  data-toggle="toggle" data-on=" " data-off=" " data-onstyle="success" data-offstyle="secondary"  data-size="mini">
                    </div>
                </div>
            </div>
            <div class="row" id="divClave" style="display:none;">
                <div class="col-md-7">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="form_clave">Contraseña</label>
                            </div>
                            <div class="col-md-8">
                                <input  type="password" class="form-control" id="form_contrasena" name="password" aria-describedby="contrasena_error" placeholder="" value="" >
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <i class="fa fa-eye" id="mostrarC"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="form_confirmar_clave">Reescribir Contraseña</label>
                            </div>
                            <div class="col-md-8">
                                <input type="password" class="form-control" id="form_confirmar_contrasena" name="re_password" aria-describedby="reescribir_contrasena_error" placeholder="" value="" required>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <i class="fa fa-eye" id="mostrarCC"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button type="button" class="btn btn-success btn-actualizar" >Actualizar</button>
                    </div>
                </div>
            </div>
            </form>
    </div>
</div>

@stop
@section('css')

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

    .buttons-excel {
        margin-bottom: 10px;
        float:right;
    }

    .buscar_fila {
        padding-top:30px;
        padding-left:50px !important; 
    }
    .bigdrop {
        width: 100% !important;
    }
    
</style>
<link rel="stylesheet" href="/css/admin_custom.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">

@stop
@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js" integrity="sha512-oQq8uth41D+gIH/NJvSJvVB85MFk1eWpMK6glnkg6I7EdMqC1XVkW7RxLheXwmFdG03qScCM7gKS/Cx3FYt7Tg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ url('js/ckfinder/ckfinder.js') }}"></script>
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script src="/js/fglobales.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

<!-- Select2 JS --> 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
    $(document).ready(function () {
        if('{{ $datos[0]->img_perfil }}' != ''){
            var pathImg = '/files/imagen_perfil/{{$datos[0]->img_perfil}}';
            $("#foto_perfil").html('<a href="'+pathImg+'" target="_blank" title="Ver foto"><img src="'+pathImg+'" style="border-radius: 50%;padding: 20px; width:50%" /></a>');
            $("#foto_perfil_md").html('<a href="'+pathImg+'" target="_blank" title="Ver foto"><img src="'+pathImg+'" style="border-radius: 50%;padding: 20px; width:50%" /></a>');
        }

        $('#btnCambiar').change(function() {
            if($(this).prop('checked')){
                $('#divClave').toggle();
                if($('#divClave').is(":visible")){
                    $('.btn-cambiar').html('Ocultar Contraseña');
                }
                else{
                    $('.btn-cambiar').html('Ver Contraseña');
                }
                $("#form_clave").attr('type','password');
                $("#form_confirmar_clave").attr('type','password');
            }
            else{
                $('#divClave').toggle();
            }
        })
    });

    $('.btn-cambiar').click(function() {
        $('#divClave').toggle();
        if($('#divClave').is(":visible")){
            $('.btn-cambiar').html('Ocultar Contraseña');
        }
        else{
            $('.btn-cambiar').html('Ver Contraseña');
        }
        $("#form_clave").attr('type','password');
        $("#form_confirmar_clave").attr('type','password');
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
        var passInput=$("#form_confirmar_contrasena");
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

    $(".btn-actualizar").click(function(e){
        e.preventDefault();
        $('.btn-actualizar').prop("disabled", true);
        $('.btn-actualizar').html(
            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Actualizando'
        );
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            if (form.checkValidity() === false) {
                    form.classList.add('was-validated');
            }
        });

        $(".print-error-msg").hide();
        
        var password = $("input[name='password']").val();
        var re_password = $("input[name='re_password']").val();
        var files = $('#form_foto')[0].files;
        var errores = 0;
        var texto_error = [];
        var files = $('#form_foto')[0].files;
        
        if($('#form_contacto').val() != ""){
            if(!$.isNumeric($('#form_contacto').val())){
                texto_error.push("Número de contacto no válido");
                errores = errores + 1;
            }
        }

        if($('#btnCambiar').is(':checked')){
            const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)([A-Za-z\d$@$!%*?&]|[^ ]){8,}$/;
            if(!(regex.test(password))){
                texto_error.push("La contraseña  debe contener mínimo 8 caracteres, letras y números y al menos una letra en mayúscula");
                errores = errores + 1;
            }
            if(password != re_password){
                texto_error.push("Las contraseñas no coinciden");
                errores = errores + 1;
            }
        }

        

        if(errores == 0 ) {
            var formData = new FormData($("#fEditarPerfil")[0]);
            $.ajax({
                url: "{{route('usuarios.update')}}",
                type:'POST',
                data:formData,
                contentType: false,
                cache: false,
                processData: false,                    
                success: function(data) {
                    console.log(data);
                    if(data.status=='400'){
                        toastr.error(data.data.comentario,"¡Aviso!");

                    }else{
                        if(data.status=='200' || data.status=='201'){
                            //$('#fEditarPerfil').trigger("reset");
                            toastr.success("Guardado exitoso","¡Aviso!");
                            location.reload();
                        }
                    }
                    
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
        }
        else{
            printErrores(texto_error);
            $('.btn-actualizar').prop("disabled", false);
            $('.btn-actualizar').html( 'Actualizar' );
        }
        
    });

    function printErrores(texto) {
        $(".print-error-msg").find("ul").html('');
        $(".print-error-msg").show();
        texto.forEach(function(elemento) {
            $(".print-error-msg").find("ul").append('<li>'+elemento+'</li>');
        });
    }

    function printErrorMsg(msg) {
        $(".print-error-msg").find("ul").html('');
        $(".print-error-msg").show();
        $.each( msg, function( key, value ) {
            $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
        });
    }
</script>
@include('ckfinder::setup')
@stop