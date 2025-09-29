

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
                <div class="bg-white box-login  my-2 my-md-3 p-3 p-md-5  box ">
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


                <p class="text-center">Ingresar con:</p>
                    @if (config('sgd.claveunica_enabled'))
                    <div>
                        @if (session('claveunica'))
                            <div class="alert alert-danger mb-4 text-center font-medium text-sm text-danger">
                                {{ session('claveunica') }}
                            </div>
                        @endif
                        <div class="d-flex justify-content-center py-3">
                            <a class="btn-cu btn-m btn-color-estandar w-100 rounded-middle" type="button" href="{{ route('claveunica.autenticar') }}" aria-label="Continuar con ClaveÚnica">
                                <span class="cl-claveunica" aria-hidden="true"></span>
                                <span class="texto" aria-hidden="true">ClaveÚnica</span>
                            </a>
                        </div>
                    </div>
                    @endif
                    <div>
                        @if (config('sgd.claveunica_enabled'))
                        <a class="btn-cu btn-m btn-light w-100 rounded-middle" type="button" href="javascript:toggleLoginMail()" aria-label="Continuar con Correo Institucional">
                                <span class="align-content-center fa fa-envelope mx-2" ></span>
                                <span class="texto" aria-hidden="true">Correo Institucional</span>
                        </a>
                        @endif
                         <x-jet-validation-errors class="alert alert-danger mb-4 text-center font-medium text-sm text-danger" />
                            @if (session('status'))
                                <div class="mb-4 font-medium text-sm text-danger">
                                    {{ session('status') }}
                                </div>
                            @endif
                        <div id="contenedor-userlogin" @if (config('sgd.claveunica_enabled')) class="d-none" @endif >
                           
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
                        <div class="">
                            <div class="d-flex flex-column align-items-center text-center text-base text-bold">
                                <sub class="mb-2 text-secondary">{!! env('GENTILEZA_TXT') !!}</sub>                            
                                <img class="img img-responsive logo-3" src="{{ asset(env('CODIGO_SGD').'/img/logo3.png') }}" alt="" id="logo3_home">
                            </div>
                            </div>
                        </div>
                     </div>
                 </div>
                 </div>
                <!-- /.box-body -->

            </div>
        </div>
    </section>

    @if (config('sgd.claveunica_enabled'))
        <!-- Si está activado claveunica, deslogear en claveunica en segundo plano -->
    <script>
        setTimeout(function() {
            //deslogear de claveunica en segundo plano
            console.log("Redirigiendo a logout de claveunica");
            window.location.href = "{{ route('claveunica.logout') }}";
        }, 1000); // 1000 milliseconds = 1 seconds

        function toggleLoginMail(){
            var contenedorLogin=$("#contenedor-userlogin");
            var btnLoginMail=$("#btn-login-mail");
            if(contenedorLogin.hasClass('d-none')){
                contenedorLogin.removeClass('d-none');
                btnLoginMail.addClass('d-none');
            }else{
                contenedorLogin.addClass('d-none');
                btnLoginMail.removeClass('d-none');
            }
        }
    </script>
    @endif
  
</x-guest-layout>
    <!-- /.content -->
