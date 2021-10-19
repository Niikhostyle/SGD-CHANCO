@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')


    <div class="row">
        <div class="col-10">
            <h1>Buzón: {{$nombre_buzon}}</h1>
        </div>
        <div class="col-2">
            <button type="button" class="btn btn-success nuevo_documento" disabled>Nuevo Documento</button>
        </div>
    </div>
    <div class="linea_content_header"></div>

@stop

@section('content')
<!--<div class="container">-->

    <div class="row">
        <div class="col-12">

            <div class="accordion" id="carpetas">
                <div class="card">
                  <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                      <button class="btn btn-block text-left buzones_carpetas_btn_colapsable_text" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <span id="boton_carpetas_texto"> Carpetas - Por Recibir </span>
                        <i class="fa fa-chevron-circle-up" style="float:right;margin-top: 8px;"></i>
                        <i class="fa fa-chevron-circle-down" style="float:right;margin-top: 8px;"></i>
                      </button>
                    </h2>

                  </div>

                  <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#carpetas">
                    <div class="card-body">
                        <nav class="text-center">
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                              <a style="width: 33%" class="nav-item nav-link active" id="nav-por-recibir-tab" data-toggle="tab" href="#nav-por-recibir" role="tab" aria-controls="nav-home" aria-selected="true" onclick="cambio_texto_boton_carpetas('Por Recibir');">
                                Por Recibir
                                <span class="badge badge-success right">
                                    4
                                </span>
                            </a>
                              <a style="width: 33%" class="nav-item nav-link" id="nav-recibidos-tab" data-toggle="tab" href="#nav-recibidos" role="tab" aria-controls="nav-profile" aria-selected="false" onclick="cambio_texto_boton_carpetas('Recibidos');">
                                Recibidos
                                <span class="badge badge-success right">
                                    4
                                </span>
                              </a>
                              <a style="width: 33%" class="nav-item nav-link" id="nav-despachados-tab" data-toggle="tab" href="#nav-despachados" role="tab" aria-controls="nav-contact" aria-selected="false" onclick="cambio_texto_boton_carpetas('Despachados');">
                                Despachados</a>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-por-recibir" role="tabpanel" aria-labelledby="nav-por-recibir-tab">
                                <table id="tabla_por_recibir_grilla" class="table dt-responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID Doc.</th>
                                            <th>F. Entrada</th>
                                            <th>Contestar Hasta</th>
                                            <th>TD</th>
                                            <th>TE</th>
                                            <th>Origen</th>
                                            <th>Materia</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lista_por_recibir['data'] as $list)
                                        <tr>
                                            <td>{{$list['id_doc']}}</td>
                                            <td>{{$list['f_entrada']}}</td>
                                            <td>{{$list['contestar_hasta']}}</td>
                                            <td>{{$list['td']}}</td>
                                            <td>{{$list['te']}}</td>
                                            <td>{{$list['origen']}}</td>
                                            <td>{{$list['materia']}}</td>
                                            <td>
                                                <?php
                                                // foreach($estados_usuario as $estado)
                                                // if($estado['id_estado_usuario']==$list['id_estado_usuario']){
                                                //        echo $estado['nombre'];
                                                // }

                                            ?>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{-- Pagination --}}
                                <div class="d-flex justify-content-center">
                                    <table class="table dt-responsive nowrap" style="width:100%" id="users-table">
                                        <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Nombres</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>




                            </div>
                            <div class="tab-pane fade" id="nav-recibidos" role="tabpanel" aria-labelledby="nav-recibidos-tab">
                                <table id="tabla_recibidos_grilla" class="table dt-responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>E</th>
                                            <th>ID Doc</th>
                                            <th>Fecha Recepción</th>
                                            <th>Contestar Hasta</th>
                                            <th>TD</th>
                                            <th>TE</th>
                                            <th>Origen</th>
                                            <th>Materia</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lista_por_recibir['data'] as $list)
                                        <tr>
                                            <td>{{$list['id_doc']}}</td>
                                            <td>{{$list['f_entrada']}}</td>
                                            <td>{{$list['contestar_hasta']}}</td>
                                            <td>{{$list['td']}}</td>
                                            <td>{{$list['te']}}</td>
                                            <td>{{$list['origen']}}</td>
                                            <td>{{$list['materia']}}</td>

                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                         <i class="fas fa-bars"></i>
                                                     </button>
                                                     <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                         <a class="dropdown-item btn-menu-ver" onclick="visualizar_usuario({{$list['id_doc']}})"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>
                                                         <a class="dropdown-item btn-menu-editar" onclick="editar_usuario({{$list['id_doc']}})"  href="#"><i class="fas fa-reply text-orange"></i> Responder</a>
                                                         <a class="dropdown-item btn-menu-editar" onclick="editar_usuario({{$list['id_doc']}})"  href="#"><i class="fas fa-share text-green"></i> Derivar</a>
                                                         <a class="dropdown-item btn-menu-editar" onclick="editar_usuario({{$list['id_doc']}})"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>
                                                         <a class="dropdown-item btn-menu-editar" onclick="editar_usuario({{$list['id_doc']}})"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>
                                                         <a class="dropdown-item btn-menu-deshabilitar" onclick="estado_usuario({{$list['id_doc']}})" href="#">
                                                            @if($list['id_doc']==1)
                                                                <i class="far fa-star text-green"></i> ( + ) Favoritos
                                                            @endif
                                                            @if($list['id_doc']==2)
                                                                <i class="fas fa-star text-green"></i>  ( - ) Favoritos
                                                            @endif
                                                        </a>
                                                     </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="nav-despachados" role="tabpanel" aria-labelledby="nav-despachados-tab">
                                <table id="tabla_despachados_grilla" class="table dt-responsive nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>E</th>
                                            <th>ID Doc</th>
                                            <th>Fecha Despacho</th>
                                            <th>Fecha Recepción</th>
                                            <th>TD</th>
                                            <th>Destino</th>
                                            <th>Materia</th>
                                            <th>Rpta a</th>
                                            <th>Fecha Doc</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lista_por_recibir['data'] as $list)
                                        <tr >
                                            <td>{{$list['id_doc']}}</td>
                                            <td>{{$list['f_entrada']}}</td>
                                            <td>{{$list['contestar_hasta']}}</td>
                                            <td>{{$list['td']}}</td>
                                            <td>{{$list['te']}}</td>
                                            <td>{{$list['origen']}}</td>
                                            <td>{{$list['materia']}}</td>
                                            <td>
                                                <?php
                                              //   foreach($estados_usuario as $estado)
                                               //  if($estado['id_estado_usuario']==$list['id_estado_usuario']){
                                               //         echo $estado['nombre'];
                                               //  }

                                            ?>
                                            </td>

                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                         <i class="fas fa-bars"></i>
                                                     </button>
                                                     <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                         <a class="dropdown-item btn-menu-ver" onclick="visualizar_usuario({{$list['id_doc']}})"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>
                                                         <a class="dropdown-item btn-menu-editar" onclick="editar_usuario({{$list['id_doc']}})"  href="#"><i class="fas fa-edit text-blue"></i> Editar</a>
                                                         <a class="dropdown-item btn-menu-editar" onclick="editar_usuario({{$list['id_doc']}})"  href="#"><i class="fas fa-trash-alt text-red"></i> Eliminar</a>
                                                     </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>



                    </div>
                  </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card" id="documento">
                <div class="card-header">
                    <h4 class="card-title">Documento</h4>
                    <div class="linea_content_header"></div>
                </div>
                <div class="card-body">

                    Contenido...

                </div>
              </div>

        </div>
    </div>

<!--</div>-->

@stop

@section('css')

    <link rel="stylesheet" href="/css/admin_custom.css">


@stop

@section('js')


<script>
 $(document).ready(function () {
    $(".nuevo_documento").prop("disabled", true);

    $(function() {
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
           // ajax: '{!! route('datatables.data') !!}',
            ajax: 'https://127.0.0.1:451/api/sgd-usuarios/listado',
            type:'json',
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            language: lenguaje_datatable,
            columns: [
                { data: 'id', name: 'id' },
                { data: 'nombres', name: 'nombres' },
                { data: 'email', name: 'email' }
            ]
        });
    });
 });

function cambio_texto_boton_carpetas(texto){
    if(texto.length>20 || texto.length==0 ){
        texto='';
    }
    $('#boton_carpetas_texto').html('Carpetas - '+texto);
    if(texto=='Despachados'){
        $(".nuevo_documento").removeAttr('disabled');
    }else{
        $(".nuevo_documento").prop("disabled", true);
    }


}
</script>
@stop
