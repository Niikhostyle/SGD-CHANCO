<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Valida tu documento</title>

    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.0/js/bootstrap.min.js"></script>

    <!-- Datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs4-4.1.1/dt-1.10.18/datatables.min.css">
    <script src="https://cdn.datatables.net/v/bs4-4.1.1/dt-1.10.18/datatables.min.js"></script>

    <!-- Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.53/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.53/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>

</head>   
<body class="container" >
    
        <div class="cover">
            <h1>Validación de Documentos</h1>

        
        </div>

    

    
    <div class="linea_content_header" ></div>
    <br>

    <div class="card" id="card_crear_documento" >

        <div class="card-body">

            <form class="needs-validation" id="form_crear_editar" method="POST" action="{{route('validador.store')}}">
                @csrf
                
                <div class="form-row">                                
                        <div class="col-md-4 md-4">
                        <div class="form-group">
                        <label for="id_documento" >Código de documento válido: </label>
                            
                        </div>
                    </div>
                    <div class="col-md-4 md-4">
                        <div class="form-group">
                        
                            <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Codigo" >
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

   
  
    <div class="linea_content_header" ></div>
    <br>
    
        @if($status==0)
        @foreach($lista_documentos['data'] as $list)    
            <div class="card">
                <div class="card-body">

                
                    <table id="tabla_documento" class="table table-bordered">
                        @if($list['id_nivel_acceso']==1)
                            <tr>
                            
                             
                                <th scope="row">Resultado</th>
                                <td class="valido">Código de documento válido</td>
                                
                                
                            
                                
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
                        

                        @endif
                    </table>
                    
                    @if($list['id_nivel_acceso']==1)
                        <a class="btn-descargar"   href="/descargarPdf/{{$list['hash_validacion']}}"><i class="fas fa-download fa-icon1"></i> Descargar</a>
                    @endif

                </div> 
            </div> 
        @endforeach
        @endif
    
    @if($status==1)
      <div class="card" id="card_invalido" style="border: 2px;">
                <div class="card-body">

                
                    <table id="tabla_documento" class="table table-bordered">
                        

                        
                       
                        <tr>
                                
                                <th scope="row">Resultado</th>
                                <td class="invalido">Código de documento inválido</td>
                                
                                  
                            </tr>
                    </table>
                    
                    

                </div> 
            </div> 
    @endif 
    

<!--<div class="container">-->
    
    

            
    

   <!-- Bitacora fin-->

    

<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
<link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css"/>


    <style type="text/css">
        .valido { color: #28a745; }
        .invalido { color: #f71313; }

        .contenedor_global{
            width: 50px;
            height: 50px;
        }
        body{
            
           
            width:auto;
            height:auto;
        }
 









h1 {
  font-size: 3rem;
  font-weight: 700;
  color: #000000;
  margin: 0 0 1.5rem;
}

i {
  font-size: 1.3rem;
}





/** .cover  {
  height: 100vh;
  width: 100%;
  background: -webkit-gradient(linear, left top, left bottom, from(rgba(0,0,0,0.05)), to(rgba(0,0,0,0)));
  background: -webkit-linear-gradient(top, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0) 100%);
  background: linear-gradient(to bottom, rgba(0,0,0,0.05) 0%, rgba(0,0,0,0) 100%);
  padding: 20px 50px;
  display: -webkit-box;
  display: flex;
  -webkit-box-orient: vertical;
  -webkit-box-direction: normal;
  flex-direction: column;
  -webkit-box-pack: center;
  justify-content: center;
  -webkit-box-align: center;
  align-items: center;
}*/





  

  h1 {
    font-size: 2rem;
  }

  .cover {
    padding: 20px;
  }

  

  </style>







</body>

</html>

<script src="{{ asset('/js/funciones.js') }}"></script>
<script src="{{ asset('/vendor/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script> 



//hashValidador = $("input[name='codigo']").val();
//console.log(hashValidador);
//$('#card_validar').hide();
//$('#card_invalido').hide();


$(".btn_validar").click(function(e){
    
    //document.getElementById('#card_crear_documento').style.visibility = "hidden";
    $('#card_crear_documento').hide();
    $('#card_documento').show();
    //$('#card_invalido').show();
 
});


function descargar_documento( id_documento, id_documento_buzon)
    {
        
        //var _token = $("input[name='_token']").val();
        var codigo = "tc4-191-20220320-58936705";

        $.ajax({
            url: "/descargarPdf/"+codigo,
            type: 'GET',
            dataType: 'json',
            data: {
                
                idDocumento:id_documento,
                idDocumentoBuzon:id_documento_buzon             
            }
        });
         
             
    }


   

</script>