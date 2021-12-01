

    <!-- Content Header (Page header) -->
    <x-guest-layout>
    <!-- Main content -->
    
    <section class="content bg">
        <div class="row  m-t-10 m-b-10">
            <div class="col-md-1 col-sm-1"></div>
            <div class="col-md-4 col-sm-7">
            <br><br><br><br>
                <div class="box modif box-login">
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <div class="box-body">
                        <span class="help-block p-l-10">Ingrese su correo electrónico y contraseña</span>


                                <x-jet-validation-errors class="mb-12" />

                                @if (session('status'))
                                    <div class="mb-4 font-medium text-sm ">
                                        {{ session('status') }}
                                    </div>
                                @endif

                                
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf

                                    <div>
                                        <label for="email" value="{{ __('Correo Electronico') }}" >Correo Electronico</label>
                                        <input type="email" class="form-control" id="email" type="email" name="email" :value="old('email')" placeholder="Correo electrónico" required autofocus >
                                    </div>

                                    <div class="mt-4">
                                        <label for="password" value="{{ __('Contraseña') }}" >Contraseña</label>
                                        <input id="password" class="form-control" type="password" name="password" placeholder="Contraseña" required autocomplete="current-password" >
                                    </div>

                                    <div class="block mt-4">
                                        <label for="remember_me" class="flex items-center">
                                            <x-jet-checkbox id="remember_me" name="remember" />
                                            <span class="ml-2 text-sm text-gray-600">{{ __('Recordar Contraseña') }}</span>
                                        </label>
                                    </div>

                                    <div class="flex items-center justify-end mt-4">
                        

                                        <x-jet-button class="btn btn-block login-btn mb-4" style="background-color: #2e71e8;">
                                            {{ __('Iniciar') }}
                                        </x-jet-button>
                                    </div>
                                    <div class="flex items-center justify-end mt-4">
                                        @if (Route::has('password.request'))
                                            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}" style="padding-right: 81%;">
                                                {{ __('Olvido la clave?') }}
                                            </a>
                                        @endif

                                    </div>
                                </form>
                        

                     </div>
                     
                 </div>

                 <div>
                     <br><br><br><br><br><br><br><br><br><br>
                    <img src="{{ asset('img/logo_calidad_plc.png') }}" alt="" style="width: 50%; margin-right: auto;">
                </div>
            </div>

            
            <div class="col-md-1 col-sm-1"></div>
            <div class="col-md-5 text-center">
                <div>
                        <img src="{{ asset('img/logo_plc.png') }}" alt="" id="logo_plc" style="margin: 10%; ">
                </div>
               
                <div>
                    <div><span id="span-titulo1"> SISTEMA DE GESTIÓN DOCUMENTAL </span></div>
                    <!--<div><span id="span-titulo2">Municipalidad De Padre Las Casas</span></div>-->
                </div>
            </div>
            <div class="col-md-1 col-sm-1"></div>

        </div>
    </section>
</x-guest-layout>
    <!-- /.content -->



