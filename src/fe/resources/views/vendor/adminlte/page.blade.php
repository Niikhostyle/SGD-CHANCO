@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    <div class="wrapper">

        {{-- Top Navbar --}}
        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if(!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty

        {{-- Footer --}}
        @hasSection('footer')
            @include('adminlte::partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if(config('adminlte.right_sidebar'))
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>
@stop

@section('adminlte_js')
    @routes
    @stack('js')
    @yield('js')


     <script>
     $(document).ready(function () {
        getContadores();
     });

    function getContadores(){
        $.ajax({
            url: route('index.getContadores'),
            type: 'GET',
            data: {},
            success: function(data){  
                //console.log(data);
                for (const [key, value] of Object.entries(data)) {
                    if(value.por_recibir > 0){
                        $("#cantidad_porrecibir_"+key).removeClass("d-none");
                        $("#cantidad_porrecibir_"+key).html(value.por_recibir);
                    }else{
                        $("#cantidad_porrecibir_"+key).addClass("d-none");
                    }
                    if(value.pendientes > 0){
                        $("#cantidad_pendientes_"+key).removeClass("d-none");
                        $("#cantidad_pendientes_"+key).html(value.pendientes);
                    }else{
                        $("#cantidad_pendientes_"+key).addClass("d-none");
                    }

                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                toastr.error("Falla en la carga de contadores","¡Aviso!");
            }
        
        });
    }
    </script>
@stop
