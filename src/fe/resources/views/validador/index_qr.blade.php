<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Validación de Documentos</title>

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
    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
    <link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css" />


    <style type="text/css">
        .valido {
            color: #28a745;
        }

        .invalido {
            color: #f71313;
        }

        .contenedor_global {
            width: 50px;
            height: 50px;
        }

        body {
            width: auto;
            height: auto;
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

        h1 {
            font-size: 2rem;
        }

        .cover {
            padding: 20px;
        }
    </style>
</head>

<body class="border container">
    <div class="cover">
        <h1>Validación de Documentos</h1>
    </div>
    @if($status==0)

    @foreach($lista_documentos['data'] as $list)
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="linea_content_header mt-0"></div>
            <div class="card">
                <div class="card-body">
                    <table id="tabla_documento" class="table table-bordered">
                        @if($list['id_nivel_acceso']==1)
                        <tr>
                            <th colspan="2" scope="row"><i>Resultado</i></th>
                        </tr>
                        <tr>
                            <td colspan="2" class="valido">Código de documento válido</td>
                        </tr>
                        <tr>
                            <th scope="row">Tipo</th>
                            <td>{{$list['tipo_documento']}}</td>
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
                        <tr>
                            <th scope="row">Confidencialidad</th>
                            <td>{{$list['nivel_acceso']}}</td>
                        </tr>
                        <tr>
                            <th colspan="2" scope="row"><i>Firmas Digitales</i></th>
                        </tr>
                        <tr>
                            <th scope="row">Entidad certificadora</th>
                            <td>Secretaría General de la Presidencia</td>
                        </tr>
                        <tr>
                            <th scope="row">Firmas</th>
                            <td>
                                @foreach($firmantes as $f)
                                {{$f->id_usuario}}. <b>{{$f->usuario}}</b> {{$f->fecha}} <br />
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2" scope="row"><i>Vistos Buenos</i></th>
                        </tr>
                        <tr>
                            <th scope="row">Visadores</th>
                            <td colspan="2">
                                {!! $txtVisadores !!}
                                <----->
                                    @if(count($visadores) > 0)
                                    @foreach($visadores as $v)
                                    {{$v->id_usuario}}. <b>{{$v->usuario}}</b> {{$v->fecha}}<br />
                                    @endforeach
                                    @else
                                    No aplica
                                    @endif
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2" scope="row"><i>Anexos</i></th>
                        </tr>
                        <tr>
                            <td colspan="2">
                                @if(count($anexos) > 0)
                                <ul>
                                    @foreach($anexos as $a)
                                    <li><b>{{$a->nombre_archivo_original}}</b> <a href="/download_publico_anexo?idDocumento={{$a->id_documento_buzon_archivo}}" target="_blank">Descargar</a></li>
                                    @endforeach
                                </ul>
                                @else
                                No aplica
                                @endif
                            </td>
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

                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-12">
                    @if($list['id_nivel_acceso']==1)
                    <p><a class="btn btn-success" href="/download_publico?idDocumento={{$list['id_documento']}}" target="_blank" class="fas fa-download"></i> Descargar</a></p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-12 border">
            @if($list['id_nivel_acceso']==1)
            <embed class="pdf" src="/descargar_docto?idDocumento={{$list['id_documento']}}" width="100%" height="1000px">
            @endif
        </div>
    </div>

    @endforeach
    @endif

    @if($status==1)
    <div class="linea_content_header"></div>
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
</body>

</html>

<script src="{{ asset('/js/funciones.js') }}"></script>
<script src="{{ asset('/vendor/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script>
    function descargar_documento(id_documento, id_documento_buzon) {
        var codigo = "tc4-191-20220320-58936705";
        $.ajax({
            url: "/descargarPdf/" + codigo,
            type: 'GET',
            dataType: 'json',
            data: {

                idDocumento: id_documento,
                idDocumentoBuzon: id_documento_buzon
            }
        });
    }
</script>