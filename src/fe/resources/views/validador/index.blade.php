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

            <div class="card" id="card_crear_documento" style="border: 1px solid #cededf;   background-color: #cededf;">
        
                
            <form  class="flex-form">
                <label for="from">
                    <i class="ion-location"></i>
                </label>
                <input type="search" id="codigo" name="codigo" placeholder="Codigo de documento valido:">
                <input type="submit"  class="btn btn-success btn_validar" value="Validar Documento">
            </form>

            </div>

        
        </div>

    
    
    <div class="linea_content_header" style="border: 1px solid #cededf;"></div>
    <br>

   
   
  
    <div class="linea_content_header" style="border: 1px solid #cededf;"></div>
   
    @foreach($lista_documentos['data'] as $list)    
            <div class="card" id="card_validar" style="border: 2px solid #cededf;   background-color: #cededf;">
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
        
    

<!--<div class="container">-->
    
    

            
    

   <!-- Bitacora fin-->

    

<link rel="stylesheet" href="/css/admin_custom.css">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
<link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css"/>


    <style type="text/css">
        .valido { color: #6bff33; }
        .invalido { color: #f71313; }

        .contenedor_global{
            width: 50px;
            height: 50px;
        }
        body{
            background-color: #5EFFDF;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 1600 800'%3E%3Cg stroke='%2325CA9E' stroke-width='10.7' stroke-opacity='0.1' %3E%3Ccircle fill='%235EFFDF' cx='0' cy='0' r='1800'/%3E%3Ccircle fill='%2354f3d8' cx='0' cy='0' r='1700'/%3E%3Ccircle fill='%234be7d2' cx='0' cy='0' r='1600'/%3E%3Ccircle fill='%2342dccb' cx='0' cy='0' r='1500'/%3E%3Ccircle fill='%233ad0c3' cx='0' cy='0' r='1400'/%3E%3Ccircle fill='%2333c4bb' cx='0' cy='0' r='1300'/%3E%3Ccircle fill='%232db9b3' cx='0' cy='0' r='1200'/%3E%3Ccircle fill='%2328aeab' cx='0' cy='0' r='1100'/%3E%3Ccircle fill='%2324a2a3' cx='0' cy='0' r='1000'/%3E%3Ccircle fill='%2320979a' cx='0' cy='0' r='900'/%3E%3Ccircle fill='%231e8c91' cx='0' cy='0' r='800'/%3E%3Ccircle fill='%231c8188' cx='0' cy='0' r='700'/%3E%3Ccircle fill='%231b777e' cx='0' cy='0' r='600'/%3E%3Ccircle fill='%231a6c75' cx='0' cy='0' r='500'/%3E%3Ccircle fill='%2319626b' cx='0' cy='0' r='400'/%3E%3Ccircle fill='%23195861' cx='0' cy='0' r='300'/%3E%3Ccircle fill='%23184e57' cx='0' cy='0' r='200'/%3E%3Ccircle fill='%2317444D' cx='0' cy='0' r='100'/%3E%3C/g%3E%3C/svg%3E");
            background-attachment: fixed;
            background-size: cover;
        }
        @import url('https://fonts.googleapis.com/css?family=Lato:400,700');

*, *:before, *:after {
  -webkit-box-sizing: inherit;
  -moz-box-sizing: inherit;
  box-sizing: inherit;
}

::-webkit-input-placeholder {
  color: #56585b;
}

::-moz-placeholder {
  color: #56585b;
}

:-moz-placeholder {
  color: #56585b;
}

html {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}

body {
  font-family: 'Lato', sans-serif;
  margin: 0;
  background: url('https://source.unsplash.com/G8rRItjrwkA') no-repeat center center fixed;
  -webkit-background-size: cover;
  -moz-background-size: cover;
  background-size: cover;
  color: #0a0a0b;
  overflow: hidden;
}

ul, nav{
  list-style: none;
  padding: 0;
}

a {
  color: #fff;
  text-decoration: none;
  cursor: pointer;
  opacity: 0.9;
}

a:hover {
  opacity: 1;
}

h1 {
  font-size: 3rem;
  font-weight: 700;
  color: #fff;
  margin: 0 0 1.5rem;
}

i {
  font-size: 1.3rem;
}

header {
  position: absolute;
  top: 0;
  left: 0;
  z-index: 10;
  width: 100%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #fff;
  padding: 10px 100px 0;
}

header nav ul {
  display: flex;
}

header nav li{
  margin: 0 15px;
}

header nav li:first-child{
  margin-left: 0; 
}

header nav li:last-child{
  margin-right: 0;  
}

a.btn {
  color: #fff;
  padding: 10px;
  border: 1px solid rgba(255,255,255,0.5);
  -webkit-transition: all 0.2s;
  -moz-transition: all 0.2s;
  transition: all 0.2s;
}

a.btn:hover {
  background: #d73851;
  border: 1px solid #d73851;
  color: #fff;
}

.cover  {
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
}

.flex-form input[type="submit"] {
  background: #ef3f5a;
  border: 1px solid #ef3f5a;
  color: #fff;
  padding: 0 30px;
  cursor: pointer;
  -webkit-transition: all 0.2s;
  -moz-transition: all 0.2s;
  transition: all 0.2s;
}

.flex-form input[type="submit"]:hover {
  background: #d73851;
  border: 1px solid #d73851;
}

.flex-form {
  display: -webkit-box;
  display: flex;
  z-index: 10;
  position: relative;
  width: 500px;
  box-shadow: 4px 8px 16px rgba(0, 0, 0, 0.3);
}

.flex-form > * {
  border: 0;
  padding: 0 0 0 10px;
  background: #fff;
  line-height: 50px;
  font-size: 1rem;
  border-radius: 0;
  outline: 0;
  -webkit-appearance: none;
}

input[type="search"] {
  flex-basis: 500px;
}

#madeby {
  position: absolute;
  bottom: 0;
  right: 0;
  padding: 25px 100px;
  color: #fff;
}

@media all and (max-width:800px) {
  body {
    font-size: 0.9rem;
  }

  .flex-form {
    width: 100%;
  }

  input[type="search"] {
    flex-basis: 100%;
  }

  .flex-form > * {
    font-size: 0.9rem;
  }

  header {
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    flex-direction: column;
    padding: 10px !important;
  }

  header h2 {
    margin-bottom: 15px;
  }

  h1 {
    font-size: 2rem;
  }

  .cover {
    padding: 20px;
  }

  #madeby {
    padding: 30px 20px;
  }
}

@media all and (max-width:360px) {
  header nav li{
    margin: 0 10px;
  }

  .flex-form {
    display: -webkit-box;
    display: flex;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    flex-direction: column;
  }

  input[type="search"] {
    flex-basis: 0;
  }

  label {
    display: none;
  }
}
    </style>





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

</body>

</html>