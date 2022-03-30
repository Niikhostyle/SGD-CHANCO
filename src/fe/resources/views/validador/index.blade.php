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
                <div class="card-body">

                    <form class="needs-validation" id="form_crear_editar" method="POST" action="{{route('validador.ver')}}">
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
   
  
       
   
    @foreach($lista_documentos['data'] as $list)    
            <div class="card" id="card_validar" >
                <div class="card-body">

                
                    <table id="tabla_documento" class="table table-bordered">
                        @if($list['id_nivel_acceso']==1)
                            <tr>
                            
                             
                                <th scope="row">Resultado</th>
                                <td class="valido">Codigo de documento valido</td>
                                
                                
                            
                                
                            </tr>
                            <tr>
                            
                                <th scope="row">Folio</th>
                                <td>{{$list['folio']}}</td>
                        
                            
                            </tr>
                            <tr>
                        
                                <th scope="row">Fecha</th>
                                <td colspan="2">{{$list['fecha_documento']}}</td>
                            
                                
                            </tr>
                            <tr>
                        
                                <th scope="row">Materia</th>
                                <td colspan="2">{{$list['materia']}}</td>
                        
                                
                            </tr>
                        
                        @elseif($list['id_nivel_acceso']==2 || $list['id_nivel_acceso']==3)
                        <tr>
                                
                                @if($list['hash_validacion']=!null)
                                    <th scope="row">Resultado</th>
                                    <td class="valido">Codigo de documento valido</td>
                                @endif
                                  
                            </tr>
                            <tr>
                                
                                <th scope="row">Folio</th>
                                <td>{{$list['folio']}}</td>
                        
                            
                            </tr>
                            <tr>
                        
                                <th scope="row">Fecha</th>
                                <td colspan="2">{{$list['fecha_documento']}}</td>
                            
                                
                            </tr>

                        
                        @else
                        <tr>
                                
                                <th scope="row">Resultado</th>
                                <td class="invalido">Codigo de documento invalido</td>
                                
                                  
                            </tr>
                            <tr>
                                
                                <th scope="row">Folio</th>
                                <td></td>
                        
                            
                            </tr>
                            <tr>
                        
                                <th scope="row">Fecha</th>
                                <td colspan="2"></td>
                            
                                
                            </tr>

                        @endif
                    </table>
                    
                    @if($list['id_nivel_acceso']==1)
                        <a class="btn-descargar" onclick="descargar_documento({{$list['id_documento']}})"  href="#"><i class="fas fa-download fa-icon1"></i> Descargar</a>
                    @endif

                </div> 
            </div> 
    @endforeach
        
    

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



//hashValidador = $("input[name='codigo']").val();
//console.log(hashValidador);
$('#card_validar').hide();
$('#card_invalido').hide();

$(".btn_validar").click(function(e){
            
    
    $('#card_validar').show();
    //$('#card_invalido').show();
   
});

function descargar_documento( id_documento, id_documento_buzon)
    {
        
        //var _token = $("input[name='_token']").val();

        $.ajax({
            url: "/descargar_documento2/",
            type: 'GET',
            dataType: 'json',
            data: {
                
                idDocumento:id_documento,
                idDocumentoBuzon:id_documento_buzon             
            },
            success: function(data){
                //console.log(data.data);
                //window.location = '/files/principal_191_.pdf' ;
                if(data.status=='200')
                    {
                        console.log("hola");
                        //window.location = (data.data.data);
                        window.open(data.data.data, 'Download');
                    }
               
                     
                
            }
        });
         
             
    }
$(document).ready(function(){

    

   
});

   

</script>
@stop