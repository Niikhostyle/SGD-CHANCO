

    <!-- Content Header (Page header) -->
    <x-guest-layout>
    <!-- Main content -->
    <style>
        .box-login{
            border-radius: 50px;
            width: 50%;
            margin: auto;
        }
    </style>
    <script src="js/inicio/jquery.min.js"></script>
    <script>
        jQuery(document).ready(function () {    
            jQuery('#togglePassword').on('click', function(){
                var passInput=$("#password");
                if(passInput.attr('type')==='password'){
                    passInput.attr('type','text');
                    $('#togglePassword').removeClass('fa-eye');
                    $('#togglePassword').addClass('fa-eye-slash');
                }else{
                    passInput.attr('type','password');
                    $('#togglePassword').removeClass('fa-eye-slash');
                    $('#togglePassword').addClass('fa-eye');
                }
            });
        });
    </script>
    <section class="content bg">
        <div class="container">
            <div class="col-md-12">
                <div class="box box-login p-5">
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="box-body" style="padding: 2em;">
                        <div class="row">
                            <div class="col-md-5 col-xs-5 col-lg-5 p-4 d-none d-lg-block">
                                <img class="img mx-auto " src="{{ asset('img/logoPLC.png') }}" alt="" id="logo_plc">
                            </div>
                            <div class="col-md-2 col-xs-2 col-lg-2">
                                &nbsp;
                            </div>

                            <div class="col-md-5 col-xs-5 col-lg-5 pb-4 d-none d-lg-block">
                                <img class="img mx-auto" src="{{ asset('img/logoSGD.png') }}" alt="" id="logo_sgd">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 text-center">
                                <h2 id="span-titulo" style="color:#444;font-size: 30px;word-spacing: 2px;"> SISTEMA DE GESTIÓN<br/>DOCUMENTAL </h2>
                                <h1 id="span-titulo" style="color:#2E71EA;font-size: 25px;word-spacing: 2px;">Bienvenido/a</h1>
                            </div>
                        </div>
                        <div class="row">
                            <x-jet-validation-errors class="mb-12" />
                            @if (session('status'))
                                <div class="mb-4 font-medium text-sm ">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="row p-5">
                                    <!-- <label for="email" value="{{ __('Correo Electronico') }}" >Correo Electronico</label> -->
                                    <p>
                                    <input type="email"  style="float:left;"  class="form-control text-center" id="email" type="email" name="email" :value="old('email')" placeholder="Ingrese su correo municipal" required autofocus >
                                    <span><i id="mail" class="fa fa-envelope" style="margin-left: -30px;padding-top:10px;"></i></span>
                                    </p>
                                    <!-- <br/> -->
                                    <p>
                                    <input id="password"  style="float:left;" class="form-control text-center" type="password" name="password" placeholder="Ingrese su contraseña" required autocomplete="current-password" >
                                    <span id="showPwd"><i id="togglePassword" class="fa fa-eye" style="margin-left: -30px;padding-top:10px; cursor: pointer;"></i></span>
                                    </p>
                                </div>
                                <div class="text-center">
                                    <x-jet-button class="btn" style="background-color: #2e71e8;">
                                        {{ __('Acceder') }}
                                    </x-jet-button>
                                </div>
                                <div class="row pt-3">
                                    <div class="col-md-6 text-center">
                                        <label for="remember_me" >
                                            <x-jet-checkbox id="remember_me" name="remember" />
                                            <span class="">&nbsp;{{ __('Recordar Contraseña') }}</span>
                                        </label>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        @if (Route::has('password.request'))
                                            <a class="" style="color:#2e71e8;font-weight:bold" href="{{ route('password.request') }}" >
                                                {{ __('¿Olvidó su contraseña?') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="row pt-5">
                            <div class="col-12">
                            <img class="img mx-auto d-block" src="{{ asset('img/logoCalidad.png') }}" alt="" id="logo_calidad" style="max-width: 30%;">
                            </div>
                        </div>
                     </div>
                 </div>
            </div>
        </div>
    </section>
</x-guest-layout>
    <!-- /.content -->



