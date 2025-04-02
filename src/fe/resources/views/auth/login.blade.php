

    <!-- Content Header (Page header) -->
    <x-guest-layout>
    <!-- Main content -->
    <style>
       .box-login{
            border-radius: 50px;
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
            <div class="d-flex align-items-center">
                <div class="box box-login p-5">
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="box-body" style="padding: 2em;">
                        <div class="mb-3">
                        <div class="d-flex media">
                            <div class="col-sm-6">                                
                                <img class="img img-responsive mx-auto " src="{{ asset(env('CODIGO_SGD').'/img/logo1.png') }}" alt="" id="logo_home">

                            </div>
                            <div class="col-sm-6">
                                <img class="img img-responsive mx-auto" src="{{ asset(env('CODIGO_SGD').'/img/logo2.png') }}" alt="" id="logo2_home">
                            </div>
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
                                    <div class="py-3">
                                    <input type="email"  style="float:left;padding:20px;"  class="form-control text-center" id="email" type="email" name="email" :value="old('email')" placeholder="Ingrese su correo municipal" required autofocus >
                                    <span><i id="mail" class="fa fa-envelope" style="margin-left: -30px;padding-top:10px;"></i></span>
                                    </div>
                                    <!-- <br/> -->
                                    <div class="py-3 my-3">
                                    <input id="password"  style="float:left;padding:20px;" class="form-control text-center" type="password" name="password" placeholder="Ingrese su contraseña" required autocomplete="current-password" >
                                    <span id="showPwd"><i id="togglePassword" class="fa fa-eye" style="margin-left: -30px;padding-top:10px; cursor: pointer;"></i></span>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <x-jet-button class="btn mb-4" style="background-color: #2e71e8;padding:15px 40px !important">
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
                        <div class="d-flex">
                            <div class="d-flex col-sm-6 text-center text-base text-bold">
                                {!! env('GENTILEZA_TXT') !!}                            
                                <img class="img img-responsive mx-auto d-block wm-img w-48" src="{{ asset(env('CODIGO_SGD').'/img/logo3.png') }}" alt="" id="logo3_home">
                            </div>
                            </div>
                        </div>
                     </div>
                 </div>
            </div>
        </div>
    </section>
</x-guest-layout>
    <!-- /.content -->



