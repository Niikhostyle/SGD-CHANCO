

    <!-- Content Header (Page header) -->
    <x-guest-layout>
    <!-- Main content -->
    <style>
       .box-login{
            border-radius: 10px;
            max-width: 550px;
            margin: auto;
        }
        @media all and (max-width: 500px) {
            .box-login{
                border-radius: 10px;
                max-width: 90%;
                margin: auto;
            }
        }
    </style>
    {{-- <script src="js/inicio/jquery.min.js"></script> --}}
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
                <div class="bg-white box box-login my-2 my-md-3 p-3 p-md-5">
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="box-body">
                        <div class="align-items-center d-flex img-logos justify-content-center mb-5">
                            <img class="img img-fluid w-50 h-100" src="{{ asset(env('CODIGO_SGD').'/img/logo1.png') }}" alt="" id="logo_home">
                            <img class="img img-fluid w-50 h-100" src="{{ asset(env('CODIGO_SGD').'/img/logo2.png') }}" alt="" id="logo2_home">
                        </div>
                        <div class="">
                            <div class="text-center">
                                <h2 id="span-titulo" style="color:#444;font-size: 30px;word-spacing: 2px;"> SISTEMA DE GESTIÓN<br/>DOCUMENTAL </h2>
                                <h1 id="span-titulo" style="color:#2E71EA;font-size: 25px;word-spacing: 2px;">Bienvenido/a</h1>
                            </div>
                        </div>
                        <div class="">
                            <x-jet-validation-errors class="mb-12" />
                            @if (session('status'))
                                <div class="mb-4 font-medium text-sm ">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="">
                                    <!-- <label for="email" value="{{ __('Correo Electronico') }}" >Correo Electronico</label> -->
                                    <div class="py-3">
                                    <input type="email"  style="float:left;padding:20px;"  class="form-control text-center" id="email" type="email" name="email" :value="old('email')" placeholder="Ingrese su correo municipal" required autofocus >
                                    <span><i id="mail" class="fa fa-envelope" style="margin-left: -30px;padding-top:10px;"></i></span>
                                    </div>
                                    <!-- <br/> -->
                                    <div class="py-3">
                                    <input id="password"  style="float:left;padding:20px;" class="form-control text-center" type="password" name="password" placeholder="Ingrese su contraseña" required autocomplete="current-password" >
                                    <span id="showPwd"><i id="togglePassword" class="fa fa-eye" style="margin-left: -30px;padding-top:10px; cursor: pointer;"></i></span>
                                    </div>
                                </div>
                                <div class="text-center py-3">
                                    <x-jet-button class="btn mb-4" style="background-color: #2e71e8;padding:15px 40px !important">
                                        {{ __('Acceder') }}
                                    </x-jet-button>
                                </div>
                                
                                <div class="d-flex justify-content-around pt-3">
                                    <div class=" text-center">
                                        <label for="remember_me" >
                                            <x-jet-checkbox id="remember_me" name="remember" />
                                            <span class="">&nbsp;{{ __('Recordar Contraseña') }}</span>
                                        </label>
                                    </div>
                                    <div class=" text-center">
                                        @if (Route::has('password.request'))
                                            <a class="" style="color:#2e71e8;font-weight:bold" href="{{ route('password.request') }}" >
                                                {{ __('¿Olvidó su contraseña?') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class=" pt-5">
                        <div class="d-flex">
                            <div class="d-flex  text-center text-base text-bold">
                                <sub>{!! env('GENTILEZA_TXT') !!}                            
                                <img class="img img-responsive" style="width:75px" src="{{ asset(env('CODIGO_SGD').'/img/logo3.png') }}" alt="" id="logo3_home"></sub>
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
