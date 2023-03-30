<x-guest-layout>
<style>
    .box-login{
        border-radius: 50px;
        width: 50%;
        margin: auto;
    }
</style>
<script src="js/inicio/jquery.min.js"></script>
<section class="content bg">
        <div class="container">
            <div class="col-md-12">
                <div class="box box-login p-5">
                    <!-- @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif -->
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
                                <h1 id="span-titulo" style="color:#2E71EA;font-size: 25px;word-spacing: 2px;">¿Olvidó su contraseña?</h1>
                                <p>
                                Favor ingrese su correo electrónico y enviaremos un enlace para restablecer la contraseña.
                                </p>
                            </div>
                        </div>
                        <div class="row">
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
                                <div class="row text-center p-3">
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
