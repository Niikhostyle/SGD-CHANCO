@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')


    <div class="row">
        <div class="col-8">
            <h1>Buzón: {{$nombre_buzon}}</h1>
        </div>
        <div class="col">
            <button type="button" class="btn btn-success nuevo_buzon">Nuevo Documento</button>

        </div>
    </div>
    <div class="linea_content_header"></div>

@stop

@section('content')
<div class="container">

    <div class="row">
        <div class="col-12">

            <div class="accordion" id="carpetas">
                <div class="card">
                  <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                      <button class="btn btn-block text-left buzones_carpetas_btn_colapsable_text" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Carpetas
                        <i class="fa fa-chevron-circle-up" style="float:right;margin-top: 8px;"></i>
                        <i class="fa fa-chevron-circle-down" style="float:right;margin-top: 8px;"></i>
                      </button>
                    </h2>

                  </div>

                  <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#carpetas">
                    <div class="card-body">
                        <nav class="text-center">
                            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                              <a style="width: 33%" class="nav-item nav-link active" id="nav-por-recibir-tab" data-toggle="tab" href="#nav-por-recibir" role="tab" aria-controls="nav-home" aria-selected="true">
                                Por Recibir
                                <span class="badge badge-success right">
                                    4
                                </span>
                            </a>
                              <a style="width: 33%" class="nav-item nav-link" id="nav-recibidos-tab" data-toggle="tab" href="#nav-recibidos" role="tab" aria-controls="nav-profile" aria-selected="false">
                                Recibidos
                                <span class="badge badge-success right">
                                    4
                                </span>
                              </a>
                              <a style="width: 33%" class="nav-item nav-link" id="nav-despachados-tab" data-toggle="tab" href="#nav-despachados" role="tab" aria-controls="nav-contact" aria-selected="false">
                                Despachados</a>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-por-recibir" role="tabpanel" aria-labelledby="nav-por-recibir-tab">
                                Por Recibir
                            </div>
                            <div class="tab-pane fade" id="nav-recibidos" role="tabpanel" aria-labelledby="nav-recibidos-tab">
                                Recibidos
                            </div>
                            <div class="tab-pane fade" id="nav-despachados" role="tabpanel" aria-labelledby="nav-despachados-tab">
                                Despachados
                            </div>
                        </div>



                    </div>
                  </div>
                </div>
            </div>

        </div>
    </div>

</div>

@stop

@section('css')

    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-duallistbox.css">


@stop

@section('js')


<script>

</script>
@stop
