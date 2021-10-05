@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')

    
        <div class="row">
            <div class="col-8">
                <h1>Buscar Documentos</h1>
            </div>
        </div>
        
        <div class="linea_content_header"></div>
        <br>
    <div class="card">
        <div class="card-body">
            <div class="row">   
                <div class="col">  
                    <input id="name" name="name" class="form-control" type="text" size="50" />
                </div>
                <div class="col">  
                    <button class="btn btn-light" type="button" id="id_btn_filtrar"><i class="fas fa-search text-blue"></i> </button> 
                </div>
                <div class="col" >  
                    <a href="#" class="bck white text small padding-left padding-right border-left border-bottom border-right radius-bl radius-br desplegar_opciones_avanzadas">
                    <i class="fa fa-angle-double-down "></i> Búsqueda avanzadas</a>
                    <a href="#" style="display:none" class="bck white text small padding-left padding-right border-left border-bottom border-right radius-bl radius-br cerrar_opciones_avanzadas">
                    <i class="fa fa-angle-double-up "></i> Búsqueda simple</a>
                </div>
            </div> 
        </div>
    </div>
    <div class="card" id="card_opciones_avanzadas" style="display:none">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                    <label for="id_documento">ID Documento: </label>
                        <input type="text" class="form-control" id="id_documento" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_tipo_documento" >Tipo Documento</label>
                        <br>
                        <select class="form-control" id="tipo_documento" name="tipo_documento" required>
                            <option value="">Seleccionar</option>
                                <option value="">----------</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_buzon_origen">Buzón Orígen</label>
                        <br>
                        <select  class="form-control" id="buzon_origen" name="buzon_origen" required>
                            <option value="">Seleccionar</option>
                                <option value="">Oficio</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="">Rango de Fechas </label>
                    
                        <br>
                        <input type="date" id="birthday" name="birthday">
                        <input type="date" id="birthday" name="birthday">
                    </div>
                </div>
                
                
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="checkbox" name="Efectos_sobre_terceros" id="id_efectos_sobre_terceros" class="valign middle">
                        <label for="check_efectos_sobre_terceros">Efectos Sobre Terceros</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <br>
                        <button type="button" class="btn btn-success nuevo_usuario">Buscar</button>
                    </div>    
                </div>
            </div>    
        </div>
    </div>
@stop


@section('content')
    <div class="container">
        <div class="card" id="card_usuario_grilla">
            <div class="card-body">
            <table id="" class="table dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>TD</th>
                        <th>Folio</th>
                        <th>Buzón origen</th>
                        <th>Buzón Actual</th>
                        <th>Materia</th>
                        <th>Acciones</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody> 
                    @foreach($lista_usuarios['data'] as $list)
                        <tr>
                            <td>{{$list['id']}}</td>
                            <td>{{$list['fecha']}}</td>
                            <td>{{$list['td']}}</td>
                            <td>{{$list['folio']}}</td>
                            <td>{{$list['buzon_origen']}}</td>
                            <td>{{$list['buzon_actual']}}</td>
                            <td>{{$list['materia']}}</td>
                            
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-bars"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="#"><i class="fas fa-eye text-blue"></i> Ver</a>
                                        <a class="dropdown-item" href="#"><i class="fas fa-download text-blue"></i> Descargar</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <ul></ul>
    
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>
        

        $(document).ready(function(){
            
            $('#tabla_usuario_grilla').DataTable({
                rowReorder: {
                    selector: 'td:nth-child(2)'
                },
                responsive: true,
                language: lenguaje_datatable
            });

            $(".desplegar_opciones_avanzadas").click(function(e){
                $('#card_opciones_avanzadas').show();
                $(".desplegar_opciones_avanzadas").hide();
                $(".cerrar_opciones_avanzadas").show();
            });

            $(".cerrar_opciones_avanzadas").click(function(e){
                $('#card_opciones_avanzadas').hide();
                $(".cerrar_opciones_avanzadas").hide();
                $(".desplegar_opciones_avanzadas").show();
            });

            $('#buzon_origen').select2();
            $('#tipo_documento').select2();
            
        })
</script>
@stop