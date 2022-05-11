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

<div class="row" id="card_documento" style="display:none">
            <div class="col-12">
                <div class="card">
                    <div class="card-header" >
                        <h4 id="titulo_accion">Ver Documento</h4>
                        <div class="linea_content_header"></div>
                    </div>
                    <div class="card-body">
    
                       

                            
                            
                            
                               
                          
                          
                           
    
                          
                           
                                                    
                            <div class="form-row">                                
                                    <div class="col-md-12 group-button-align">
                                        <input type="hidden" name="hiddIdDocumento" id="hiddIdDocumento" value="">
                                        <input type="hidden" name="hiddIdDocumentoBuzon" id="hiddIdDocumentoBuzon" value="">
                                        <input type="hidden" name="hiddIdBuzon" id="hiddIdBuzon" value="">
                                        <input type="hidden" name="hiddIdOrigen" id="hiddIdOrigen" value="">
    
                                    </div>                          
                            </div>
                        </form>
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
<script src="/js/fglobales.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>

<script>






</script>


@stop