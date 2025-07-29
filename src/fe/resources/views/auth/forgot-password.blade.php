<x-guest-layout>
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

<section class="content bg">
        <div class="container">
            <div class="d-flex align-items-center">
                <div class="bg-white box box-login my-2 my-md-3 p-3 p-md-5">
                    <!-- @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif -->
                    <div class="box-body">
                        <div class="align-items-center d-flex img-logos justify-content-center mb-5">
                            <img class="img img-fluid w-50 h-100" src="{{ asset(env('CODIGO_SGD').'/img/logo1.png') }}" alt="" id="logo_home">
                            <img class="img img-fluid w-50 h-100" src="{{ asset(env('CODIGO_SGD').'/img/logo2.png') }}" alt="" id="logo2_home">
                        </div>
                        <div class="row">
                            <div class="col-12 text-center">
                                <h2 id="span-titulo" style="color:#444;font-size: 30px;word-spacing: 2px;"> SISTEMA DE GESTIÓN<br/>DOCUMENTAL </h2>
                                <h1 id="span-titulo" style="color:#2E71EA;font-size: 25px;word-spacing: 2px;">¿Olvidó su contraseña?</h1>
                                <p>
                                Favor ingrese su correo electrónico y enviaremos un enlace para restablecer la contraseña.
                                </p>
                            </div>
                        </div>
                        <div class="">
                            <x-jet-validation-errors class="mb-12" />
                            @if (session('status'))
                                <div class="mb-4 font-medium text-sm text-green">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <div class="block">
                                    <x-jet-label for="email"  />
                                    <x-jet-input id="email" class="form-control text-center" type="email" name="email" :value="old('email')" required autofocus placeholder="Ingrese su correo electrónico municipal" />
                                </div>
                                <div class=" text-center p-3">
                                    <x-jet-button class="btn" style="background-color: #2e71e8;">
                                        {{ __('Reestablecer') }}
                                    </x-jet-button>
                                </div>
                            </form>
                            <div class="text-center p-2">
                            
                                <x-jet-button class="btn" style="background-color: #2e71e8;" onclick="location.href='/login';">
                                VOLVER
                                </x-jet-button>
                            </div>
                        </div>
                        <div class=" pt-5">
                            <div class="">
                            <img class="img mx-auto d-block wm-img" src="{{ asset(env('CODIGO_SGD').'/img/logo5.png') }}" alt="" id="logo5_home">
                            </div>
                        </div> 
                     </div>
                 </div>
            </div>
        </div>
    </section>
</x-guest-layout>
