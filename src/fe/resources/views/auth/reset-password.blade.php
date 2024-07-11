<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="theme-color" content="#ffffff">

        <link rel="shortcut icon" href="https://www.purranque.cl/portal-municipal/images/logo1.png" type="image/x-icon">



        <title>{{ config('app.name', 'Laravel') }}</title>


        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <style>
            .box-login{
                border-radius: 50px !important;
                max-width: 50%;
                margin: auto;
            }
            @media all and (max-width: 500px) {
                .box-login{
                    border-radius: 50px;
                    max-width: 90%;
                    margin: auto;
                }
            }
            blockquote {
                background: orange;
                border-left: 10px solid #c2c2c2;
                font-size: 12px !important; 
                margin: 1.5em 10px;
                padding: 0.5em 10px;
            }
            blockquote:before {
                color: #fff;
                font-size: 10px !important; 
                margin-right: 0.25em;
                vertical-align: -0.4em;
            }
            blockquote p {
                display: inline;
            }
        </style>
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <!-- Bootstrap 3.4.1 -->
        <link rel="stylesheet" href="../css/inicio/bootstrap.min.css">
        <link rel="stylesheet" href="../css/inicio/font-awesome.min.css">
        <link rel="stylesheet" href="../css/inicio/ionicons.min.css">
        <link rel="stylesheet" href="../css/inicio/gijgo.min.css"  type="text/css" />
        <link rel="stylesheet" href="../css/inicio/AdminLTE.min.css">
        <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
        <link rel="stylesheet" href="../css/inicio/_all-skins.min.css">
        <link rel="stylesheet" href="../css/inicio/pace.min.css">
        <link rel="stylesheet" href="../css/inicio/adminlte_config.css">
        <link rel="stylesheet" href="../css/admin_custom.css">
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        <script>
            function validar(){
                var password = $("input[name='password']").val();
                var re_password = $("input[name='password_confirmation']").val();
                var correo = $("input[name='email']").val();
                var errores = 0;
                var texto_error = [];
                const regex_mail = /^\w+([.-_+]?\w+)*@\w+([.-]?\w+)*(\.\w{2,10})+$/;
                
                if(correo != ""){
                    if(!(regex_mail.test(correo))){
                        texto_error.push("- El formato del correo electrónico no es válido.");
                        errores = errores + 1;
                    }
                }
                else{
                    texto_error.push("- Debe ingrear un correo electrónico.");
                    errores = errores + 1;
                }
                
                const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)([A-Za-z\d$@$!%*?&]|[^ ]){8,}$/;
                if(!(regex.test(password))){
                    texto_error.push("- La contraseña debe contener mínimo 8 caracteres, letras y números y al menos una letra en mayúscula.");
                    errores = errores + 1;
                }
                if(password != re_password){
                    texto_error.push("- Las contraseñas no coinciden.");
                    errores = errores + 1;
                }
                

                if(errores == 0 ) {
                    $('#fReset').submit();
                }
                else{
                    printErrores(texto_error);
                }
            }

            function printErrores(texto) {
                $(".print-error-msg").find("ul").html('');
                $(".print-error-msg").show();
                texto.forEach(function(elemento) {
                    $(".print-error-msg").find("ul").append('<li>'+elemento+'</li>');
                });
            }
        </script>


    </head>
    <body class="row hold-transition skin-blue ">
        <!-- <header class="main-header-guest">
                <span class="logo-lg">SISTEMA DE GESTIÓN DOCUMENTAL - PADRE LAS CASAS</span>
        </header> -->
        <div class="font-sans text-gray-900 antialiased">
            <section class="content bg">
                <div class="container">
                    <div class="d-flex align-items-center">
                        <div class="box box-login p-5">
                            @if (session('status'))
                                <div class="mb-4 font-medium text-sm text-green-600">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <div class="box-body" style="padding: 2em;">
                                <div class="row">
                                    <div class="col-md-5 col-xs-5 col-lg-5 p-4 d-none d-lg-block">
                                        <img class="img mx-auto " src="{{ asset('img/LogoPurranque.png') }}" alt="" id="logo_purranque">
                                    </div>
                                    <div class="col-md-2 col-xs-2 col-lg-2">
                                        &nbsp;
                                    </div>

                                    <div class="col-md-5 col-xs-5 col-lg-5 pb-4 d-none d-lg-block">
                                        <img class="img mx-auto" src="{{ asset('img/EscudoPurranque.png') }}" alt="" id="escudo_purranque">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 text-center">
                                        <h2 id="span-titulo" style="color:#444;font-size: 30px;word-spacing: 2px;"> SISTEMA DE GESTIÓN<br/>DOCUMENTAL </h2>
                                    </div>
                                </div>
                                <div class="row">
                                    <x-jet-validation-errors class="mb-12" />
                                    @if (session('status'))
                                        <div class="mb-4 font-medium text-sm text-green"> 
                                            {{ session('status') }}
                                        </div>
                                    @endif
                                    <div class="alert alert-warning print-error-msg" style="display:none;">
                                    <ul style="color:#333;"></ul>
                                    </div>
                                    <form method="POST" id="fReset" action="{{ route('password.update') }}">
                                        @csrf

                                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                        <div class="block">
                                            <!-- <x-jet-label for="email" value="{{ __('Email') }}" /> -->
                                            <x-jet-input id="email" class="form-control text-center" type="email" name="email" :value="old('email', $request->email)" required autofocus placeholder="Ingrese su correo electrónico municipal" />
                                        </div>

                                        <div class="mt-4">
                                            <!-- <x-jet-label for="password" value="{{ __('Password') }}" /> -->
                                            <x-jet-input style="float:left;" id="password" class="form-control text-center" type="password" name="password" required autocomplete="new-password" placeholder="Nueva contraseña" />
                                            <i id="togglePassword" class="fa fa-eye" style="margin-left: -30px;padding-top:10px; cursor: pointer;"></i>
                                        </div>

                                        <div class="mt-4">
                                            <!-- <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" /> -->
                                            <x-jet-input style="float:left;" id="password_confirmation" class="form-control text-center" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirme contraseña" />
                                            <i id="togglePasswordC" class="fa fa-eye" style="margin-left: -30px;padding-top:10px; cursor: pointer;"></i>
                                        </div>

                                        <div class="row text-center pt-4">
                                            <button class="btn" style="background-color: #2e71e8; color:#fff" type="button" onclick="validar()">
                                                REESTABLECER
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                <!-- <div class="row pt-5">
                                    <div class="col-12">
                                    <img class="img mx-auto d-block" src="{{ asset('img/logoCalidad.png') }}" alt="" id="logo_calidad" style="max-width: 30%;">
                                    </div>
                                </div> -->
                                </div>
                            </div>
                    </div>
                </div>
            </section>
        </div>
        <footer class="footer_login  m-b-0 text-sm">
            <div class="row">
                <div class="col-md-12">
                <span class="help-block m-r-20 m-l-20 text-center">
                    2023 © MUNICIPALIDAD DE PURRANQUE<br/>Av. Pedro Montt 249, Purranque - Décima Region de Los Lagos, Teléfono (64-2) 35 11 35
                </span>
                </div>
            </div>

        </footer>


    </body>

<!-- jQuery 3.3.1 -->
<script src="../js/inicio/jquery.min.js"></script>
<!-- Bootstrap 3.4.1 -->
<script src="../js/inicio/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>
<script src="../js/inicio/pace.min.js"></script>
<script src="../js/inicio/jquery.slimscroll.min.js"></script>
<script src="../js/inicio/adminlte.js"></script>

<!-- page script -->
<script type="text/javascript">
// To make Pace works on Ajax calls
$(document).ajaxStart(function() { Pace.restart(); });

// Ajax calls should always have the CSRF token attached to them, otherwise they won't work
$.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });


var activeTab = $('[href="' + location.hash.replace("#", "#tab_") + '"]');
location.hash && activeTab && activeTab.tab('show');
$('.nav-tabs a').on('shown.bs.tab', function (e) {
  location.hash = e.target.hash.replace("#tab_", "#");
});

    jQuery(document).ready(function () {    
        jQuery('#togglePassword').on('click', function(){
            var passInput=jQuery("#password");
            if(passInput.attr('type')==='password'){
                passInput.attr('type','text');
                jQuery('#togglePassword').removeClass('fa-eye');
                jQuery('#togglePassword').addClass('fa-eye-slash');
            }else{
                passInput.attr('type','password');
                jQuery('#togglePassword').removeClass('fa-eye-slash');
                jQuery('#togglePassword').addClass('fa-eye');
            }
        });

        jQuery('#togglePasswordC').on('click', function(){
            var passInput=jQuery("#password_confirmation");
            if(passInput.attr('type')==='password'){
                passInput.attr('type','text');
                jQuery('#togglePasswordC').removeClass('fa-eye');
                jQuery('#togglePasswordC').addClass('fa-eye-slash');
            }else{
                passInput.attr('type','password');
                jQuery('#togglePasswordC').removeClass('fa-eye-slash');
                jQuery('#togglePasswordC').addClass('fa-eye');
            }
        });

    });
</script>


<!-- JavaScripts -->


</html>