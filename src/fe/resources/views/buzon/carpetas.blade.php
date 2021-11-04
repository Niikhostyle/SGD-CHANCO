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
                                @if($n_docs_por_recibir>0)
                                <span class="badge badge-success right">
                                    {{$n_docs_por_recibir}}
                                </span>
                                @endif
                            </a>
                              <a style="width: 33%" class="nav-item nav-link" id="nav-recibidos-tab" data-toggle="tab" href="#nav-recibidos" role="tab" aria-controls="nav-profile" aria-selected="false" onclick="cambio_texto_boton_carpetas('Recibidos');">
                                Recibidos
                                @if($n_docs_recibidos_pendientes>0)
                                <span class="badge badge-success right">
                                    {{$n_docs_recibidos_pendientes}}
                                </span>
                                @endif
                              </a>
                              <a style="width: 33%" class="nav-item nav-link" id="nav-despachados-tab" data-toggle="tab" href="#nav-despachados" role="tab" aria-controls="nav-contact" aria-selected="false" onclick="cambio_texto_boton_carpetas('Despachados');">
                                Despachados</a>
                            </div>
                        </nav>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-por-recibir" role="tabpanel" aria-labelledby="nav-por-recibir-tab">
                                <table id="grilla_por_recibir"  class="table dt-responsive nowrap" style="width:100%">
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
                                </table>
                            </div>



                            <div class="tab-pane fade" id="nav-recibidos" role="tabpanel" aria-labelledby="nav-recibidos-tab">
                                <table id="grilla_recibidos"  class="table dt-responsive nowrap no-footer dtr-inline dataTable collapsed" style="width:100%">
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
                                </table>


                            </div>
                            <div class="tab-pane fade" id="nav-despachados" role="tabpanel" aria-labelledby="nav-despachados-tab"  style="width: 100%;">
                                    <table id="grilla_despachados"  class="table dt-responsive nowrap no-footer dtr-inline dataTable collapsed" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>E</th>
                                                <th>ID Doc</th>
                                                <th width="100px">Fecha Despacho</th>
                                                <th width="50px">Fecha Recepción</th>
                                                <th>TD</th>
                                                <th>Destinatario</th>
                                                <th>Materia</th>
                                                <th>Rpta a</th>
                                                <th>Fecha Doc</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
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

            <div class="card" id="documento" style="display: none">
                <div class="card-header">
                    <h4 class="card-title">Documento</h4>
                    <div class="linea_content_header"></div>
                </div>
                <div class="card-body">

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
var grilla_por_recibir;
var grilla_recibidos;
var grilla_despachados;

function cambio_texto_boton_carpetas(texto){
    $('#documento').hide();

    if(texto.length>20 || texto.length==0 ){
        texto='';
    }
    $('#boton_carpetas_texto').html('Carpetas - '+texto);
    if(texto=='Recibidos'){
        $('#grilla_recibidos').DataTable().draw();
    }
    if(texto=='Despachados'){
        $('#grilla_despachados').DataTable().draw();
        $(".nuevo_documento").removeAttr('disabled');
    }else{
        $(".nuevo_documento").prop("disabled", true);
    }
}

function mostrar_documento(texto){
    $('#documento .card-title').html('Documento: '+texto);
    $('#documento').show();
}

function ver_recibidos(identificador){
    alert(identificador)
}
function responder_recibidos(identificador){
    alert(identificador)
}
function derivar_recibidos(identificador){
    alert(identificador)
}
function archivar_recibidos(identificador){
    alert(identificador)
}
function bitacora_recibidos(identificador){
    alert(identificador)
}
function favorito_recibidos(identificador){
    alert(identificador)
}

function ver_despachados(identificador){
    alert(identificador)
}
function editar_despachados(identificador){
    alert(identificador)
}
function eliminar_despachados(identificador){
    alert(identificador)
}

async function fn_grilla_por_recibir(){
    $('#documento').hide();
    if ( $.fn.DataTable.isDataTable('#grilla_por_recibir') ) {
            $('#grilla_por_recibir').DataTable().destroy();
    }
    $('#grilla_por_recibir tbody').empty();

      grilla_por_recibir=  $('#grilla_por_recibir').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/buzonesListar?id_buzon={{$id_buzon}}&id_carpeta=1',
            type:'json',
            responsive: true,
            language: lenguaje_datatable,
            columns: [
                { data: 'id_documento', name: 'id_documento' },
                { data: 'fecha_despacho', name: 'fecha_despacho' },
                { data: 'contestas_hasta', name: 'contestas_hasta' },
                { data: 'tipo_documento', name: 'tipo_documento' },
                { data: 'tipo_envio', name: 'tipo_envio' },
                { data: 'origen', name: 'origen' },
                { data: 'materia', name: 'materia' },
            ]
        });
}




async function fn_grilla_recibidos(){

    $('#documento').hide();
    if ( $.fn.DataTable.isDataTable('#grilla_recibidos') ) {
            $('#grilla_recibidos').DataTable().destroy();
    }
    $('#grilla_recibidos tbody').empty();

    grilla_recibidos = $('#grilla_recibidos').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/buzonesListar?id_buzon={{$id_buzon}}&id_carpeta=2',
            type:'json',
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            language: lenguaje_datatable,
            columns: [
                { data: 'recibido',
                  render: function(data, type) {
                    if (type === 'display') {
                        if(data==null){
                            return '';
                        }else{
                            if(data==true){
                                return '<span class="fas fa-check text-green"></span>';
                            }
                        }
                    }
                    return '';
                  }
                },
                { data: 'estado_documento', name: 'estado_documento' },
                { data: 'id_documento', name: 'id_documento' },
                { data: 'fecha_recepcion', name: 'fecha_recepcion' },
                { data: 'contestas_hasta', name: 'contestas_hasta' },
                { data: 'tipo_documento', name: 'tipo_documento' },
                { data: 'tipo_envio', name: 'tipo_envio' },
                { data: 'origen', name: 'origen' },
                { data: 'materia', name: 'materia' },
                { data: 'id_buzon',
                  render: function(data, type) {
                    if (type === 'display') {
                        if(data==null){
                            return '';
                        }else{
                            let botonera = '<div class="dropdown">';
                                botonera += '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                    botonera +=' <i class="fas fa-bars"></i>';
                                    botonera +=' </button>';
                                    botonera +='<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

                                        botonera +=' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos('+data+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                        botonera +=' <a class="dropdown-item btn-menu-editar" onclick="responder_recibidos('+data+')"  href="#"><i class="fas fa-reply text-orange"></i> Responder</a>';
                                        botonera +=' <a class="dropdown-item btn-menu-editar" onclick="derivar_recibidos('+data+')"  href="#"><i class="fas fa-share text-green"></i> Derivar</a>';
                                        botonera +=' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos('+data+')"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>';
                                        botonera +=' <a class="dropdown-item btn-menu-editar" onclick="bitacora_recibidos('+data+')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';
                                        botonera +='<a class="dropdown-item btn-menu-deshabilitar" onclick="favorito_recibidos('+data+')" href="#">';
                                               // if($list['id_doc']==1)
                                               botonera +='<i class="far fa-star text-green"></i> ( + ) Favoritos';
                                               // endif
                                               // if($list['id_doc']==2)
                                                  //  <i class="fas fa-star text-green"></i>  ( - ) Favoritos
                                                //endif
                                        botonera +='</a>';


                                botonera += '</div>';
                                botonera += '</div>';
                            return botonera;
                        }
                    }
                    return '';
                  }
                }
            ]
    });
}



async function fn_grilla_despachados(){
    $('#documento').hide();
    if ( $.fn.DataTable.isDataTable('#grilla_despachados') ) {
            $('#grilla_despachados').DataTable().destroy();
    }
    $('#grilla_despachados tbody').empty();
    grilla_despachados=  $('#grilla_despachados').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/buzonesListar?id_buzon={{$id_buzon}}&id_carpeta=3',
            type:'json',
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            language: lenguaje_datatable,
            columns: [
                { data: 'recibido',
                  render: function(data, type) {
                    if (type === 'display') {
                        if(data==null){
                            return '';
                        }else{
                            if(data==true){
                                return '<span class="fas fa-check text-green"></span>';
                            }
                        }
                    }
                    return '';
                  }
                },
                { data: 'estado_documento', name: 'estado_documento' },
                { data: 'id_documento', name: 'id_documento' },
                { data: 'fecha_despacho', name: 'fecha_despacho' },
                { data: 'fecha_recepcion', name: 'fecha_recepcion' },
                { data: 'tipo_documento', name: 'tipo_documento' },
                { data: 'destinatario', name: 'destinatario' },
                { data: 'materia', name: 'materia' },
                { data: 'respuesta_a', name: 'respuesta_a' },
                { data: 'fecha_documento', name: 'fecha_documento' },
                { data: 'id_buzon',
                  render: function(data, type) {
                    if (type === 'display') {
                        if(data==null){
                            return '';
                        }else{
                            let botonera = '<div class="dropdown">';
                                botonera += '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                    botonera +=' <i class="fas fa-bars"></i>';
                                    botonera +='                 </button>';
                                    botonera +='                 <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                                    botonera +='                     <a class="dropdown-item btn-menu-ver" onclick="ver_despachados('+data+')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                    botonera +='                    <a class="dropdown-item btn-menu-editar" onclick="editar_despachados('+data+')"  href="#"><i class="fas fa-edit text-blue"></i> Editar</a>';
                                    botonera +='                     <a class="dropdown-item btn-menu-editar" onclick="eliminar_despachados('+data+')"  href="#"><i class="fas fa-trash-alt text-red"></i> Eliminar</a>';
                                    botonera +='</div>';
                                botonera += '</div>';
                            return botonera;
                        }
                    }
                    return '';
                  }
                }
            ]
        });
}
 $(document).ready(function () {
    $(".nuevo_documento").prop("disabled", true);

    //var div_por_recibir_width = document.getElementById('nav-por-recibir').getBoundingClientRect().width;
    //$('#nav-despachados').attr("style","width:"+div_por_recibir_width+'px');
    //$('#nav-recibidos').attr("style","width:"+div_por_recibir_width+'px');

    $(function() {

        fn_grilla_por_recibir();

        $('#grilla_por_recibir tbody').on( 'click', 'tr', function () {
            td_seleccionado=grilla_por_recibir.row( this ).data();
            mostrar_documento(td_seleccionado['materia']);
        });

        fn_grilla_recibidos();

        fn_grilla_despachados();

    });
 });


</script>
@stop
