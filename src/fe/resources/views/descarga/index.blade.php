@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')


<div class="row">
    <div class="col-12">
        <h1>Descargas</h1>
    </div>    
</div>
<div class="linea_content_header"></div>

@stop

    
@section('content')    

        <div class="row" >
            <div class="col-12">
                <div class="card">
                    <div class="card-header" >
                        <h4 id="titulo_accion">Seleccionar Descarga</h4>
                        <div class="linea_content_header"></div>
                    </div>
                    

                    <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_firma_electronica">Firma electrónica avanzada</label>
                        <select  class="form-control" id="tipo_descarga" name="tipo_descarga">
                            <option value="0">Seleccionar</option>
                            <option value="1">Registro de Descargas de documento principal</option>
                            <option value="2">---------------</option>
                        </select>
                    </div>
                </div>

               
            </div>  

            <div class="col-12">
                <div class="card " id="bloque_descarga" >
                    <div class="form-group">
                        <label for="">Rango de Fechas </label>  
                        <div class="form-row">

                        
                        <div class="col-md-3 md-1">
                            <div class="form-group">
                                                
                                <input type="date" id="buscar_fecha_ini" name="buscar_fecha_ini" class="form-control">
                            </div>

                            
                        </div>

                        <div class="col-md-3 md-1">
                            <div class="form-group">
                                                
                                <input type="date" id="buscar_fecha_fin" name="buscar_fecha_fin" class="form-control">
                            </div>
                        </div>  
                        
                        
                        

                        <div class="col-md-2 md-5 ">
                            <div class="form-group">
                        
                            <button class="btn btn-success"> Download</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                            
                            
@stop

@section('css')

<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
<link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">

    <style type="text/css">

     

    </style>
@stop

@section('js')

<script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script src="/js/fglobales.js?ver=1"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>

<script>




$(function() {
    $('#bloque_descarga').hide(); 
    $('#tipo_descarga').change(function(){
        if($('#tipo_descarga').val() == '1') {
            $('#bloque_descarga').show(); 
        } else {
            $('#bloque_descarga').hide(); 
        } 
    });
});
                           







</script>


@stop