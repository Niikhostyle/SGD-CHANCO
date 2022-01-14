@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')

    
    <div class="row">
        <div class="col-8">
            <h1>Validación de Documentos</h1>
        </div>
    </div>
    
    <div class="linea_content_header"></div>
    <br>

    <div class="row" id="card_crear_documento">
        <div class="col-12">
            <div class="card">
                <div class="card-header" >
                    <h4 id="titulo_accion">Nuevo Documento</h4>
                    <div class="linea_content_header"></div>
                </div>
                <div class="card-body">

                    <form class="needs-validation" id="form_crear_editar" method="POST" action="{{route('validador.store')}}">
                        @csrf
                        
                        <div class="form-row">                                
                                <div class="col-md-4 md-4">
                                <div class="form-group">
                                <label for="id_documento">Codigo de documento valido: </label>
                                    
                                </div>
                            </div>
                            <div class="col-md-4 md-4">
                                <div class="form-group">
                                
                                    <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Codigo">
                                </div>
                            </div>
                            <div class="col-md-4 md-4">
                                <div class="form-group">
                                <button type="submit"  class="btn btn-success btn_validar">Validar Documento</button>
                                </div>
                            </div>                        
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
    <!--
        <div class="card">
            <div class="card-body">
                    
            <table id="grilla_validador"  class="table table-bordered">
            
                <tbody>
                    <tr>
                        <th>Resultado</th>
                        <th >Folio</th>
                        <th >Fecha</th>
                        <th >Materia</th>
                        
                    </tr>
                    
                </tbody>
            </table>

                </div>
            </div> 
        </div>  
-->
        <div class="card" id="card_validar" >
            <div class="card-body">
                    
                <table id="tabla_documento" class="table table-bordered">
                
                        <tr>
                            @foreach($lista_documentos['data'] as $list)
                                @if($list['hash_validacion']=='XyZ988')
                                    <th scope="row">Resultado</th>
                                    <td class="valido">Codigo de documento valido</td>
                                @endif

                            @endforeach
                            
                        </tr>
                        <tr>
                        @foreach($lista_documentos['data'] as $list)
                            <th scope="row">Folio</th>
                            <td>{{$list['folio']}}</td>
                            @endforeach
                           
                        </tr>
                        <tr>
                        @foreach($lista_documentos['data'] as $list)
                            <th scope="row">Fecha</th>
                            <td colspan="2">{{$list['fecha_documento']}}</td>
                            @endforeach
                            
                        </tr>
                        <tr>
                        @foreach($lista_documentos['data'] as $list)
                            <th scope="row">Materia</th>
                            <td colspan="2">{{$list['materia']}}</td>
                        @endforeach
                            
                        </tr>
                </table>
                <br>
                <button style="display: none;" type="button" class="btn btn-success">Descargar Documento</button>
                <a href="" download=""><i class="fas fa-download"></i> Descargar Documento</a>
                @foreach($lista_documentos['data'] as $list)
                    
                    <a href="" class="btn-descargar" target="_blank"><i class="fas fa-download fa-icon1"></i></a>
                @endforeach

            </div> 
        </div> 
        <div class="card" id="card_invalidar" >
            <div class="card-body">
                    
                <table id="tabla_documento" class="table table-bordered">
                
                <tr>
                            
                               
                        <th scope="row">Resultado</th>
                        <td class="invalido">Codigo de documento valido</td>
                                
                            
                        </tr>
                        <tr>
                       
                            <th scope="row">Folio</th>
                            <td></td>
                            
                           
                        </tr>
                        <tr>
                     
                            <th scope="row">Fecha</th>
                            <td colspan="2"></td>
                            
                            
                        </tr>
                </table>
                

            </div> 
        </div> 
    <!--
        <div class="card">
            <div class="card-body">
                    
                <table class="table table-bordered">
                
                    <tbody>
                        <tr>
                            <th scope="row">Resultado</th>
                            <td class="invalido">Código de documento no válido</td>
                        </tr>
                        <tr>
                            <th scope="row">Folio</th>
                            <td></td>
                        </tr>
                        <tr>
                            <th scope="row">Fecha</th>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <th scope="row">Materia</th>
                            <td colspan="2"></td>
                        </tr>
                    </tbody>
                </table>
                

            </div> 
        </div> -->
    
@stop


@section('content')
<!--<div class="container">-->
    
    

            
    

   <!-- Bitacora fin-->

    
@stop

@section('css')
<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
<link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css"/>


    <style type="text/css">
        .valido { color: #6bff33; }
        .invalido { color: #f71313; }
    </style>

@stop

@section('js')



<script src="{{ asset('/js/funciones.js') }}"></script>
<script src="{{ asset('/vendor/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script>
    

$(document).ready(function(){

        $(".btn_validar").click(function(e){
            
            //hashValidador = $("input[name='codigo']").val();
            $('#card_validar').show();
        });
    });

   

</script>
@stop