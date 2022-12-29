@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')
<div class="row">
    <div class="col-8">
        <h1>Auditoría de Folios</h1>
    </div>
</div>
<div class="linea_content_header"></div>

@stop
@section('content')
<div class="card" id="">
    <div class="card-body">
        <div class="form-row">
            <div class="col-md-3 md-3">
                <div class="form-group">
                    <label for="id_documento">Tipo Folio: </label>
                    <select id="tipo_folio" name="tipo_folio" onchange="obtenerTiposDocumentos(this.value)" class="form-control-sm">
                        <option value="">Seleccione</option>
                        @foreach($tipos_folio as $tp)
                            <option value="{{ $tp['id_tipo_folio'] }}">{{ $tp['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4 md-4">
                <div class="form-group">
                    <label for="id_documento">Tipo Documento: </label>
                    <select id="tipo_documento" name="tipo_documento" class="form-control-sm">
                        <option value="">Seleccionar tipo folio</option>
                    </select>
                    <span id="mensaje" style="display:none;">Procesando...</span>
                </div>
            </div>
            <div class="col-md-4 md-4">
                <div class="form-group">
                    <label for="id_buzon">Buzón Firma: </label>
                    <select id="id_buzon" name="id_buzon" class="form-control-sm">
                    <option value="">Todos</option>
                        @foreach($buzones as $b)
                            <option value="{{ $b['id_buzon'] }}">{{ $b['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-1 md-1">
                <div class="form-group">
                    <!-- i id="botones_grilla_despachados"></i-->
                    <button id="btnBuscar" class="btn btn-success">Buscar</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="collapseOne" class="card" aria-labelledby="headingOne" data-parent="#carpetas">
    <div class="" id="card_buscador_grilla">
        <div class="card-body">
            <table id="grilla_folios"  class="table dt-responsive nowrap no-footer dtr-inline dataTable collapsed" style="width:100%">
                <thead>
                    <tr class="grilla_header">
                        <th class="text-center">Folio</th>
                        <th class="text-left">Buzon Firma</th>
                        <th class="text-center">Fecha Asignación Folio</th>
                        <th class="text-center">ID Documento</th>
                        <th class="text-left">Buzón Actual</th>
                        <th class="text-left">Materia</th>                                
                        <th class="text-left">Tipo Documento</th>                                
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center">Ningún dato disponible en esta tabla</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@stop
@section('css')

<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
<link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
<link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css"/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">


<style type="text/css">
    .disabled {
        background-color: #e9ecef;
    }
    .label-info {
        background-color:#5bc0de
    }
    .label-info[href]:focus,
    .label-info[href]:hover {
        background-color:#31b0d5
    }
    .label {
        display: inline-block;
        padding: .25em .4em;
        font-size: 90%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25rem;
        transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;

    }

    .flex-container {
        display: flex;
        flex-wrap: nowrap;
        background-color: rgb(208, 228, 191);
        border: 1px solid #5a8fc7;
    }
    
    .flex-container > div {
        background-color: #b5e4b9;
        border: 1px solid #5a8fc7;
        width: 100px;
        margin: 15px;
        text-align: center;
        line-height: 20px;
        font-size: 15px;
    }

    .odd:hover, .even:hover{
        background: whitesmoke;
    }

    .buttons-excel {
        margin-bottom: 10px;
        float:right;
    }

    .buscar_fila {
        padding-top:30px;
        padding-left:50px !important; 
    }

    
</style>
<link rel="stylesheet" href="/css/admin_custom.css">
@stop
@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js" integrity="sha512-oQq8uth41D+gIH/NJvSJvVB85MFk1eWpMK6glnkg6I7EdMqC1XVkW7RxLheXwmFdG03qScCM7gKS/Cx3FYt7Tg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="{{ url('js/ckeditor/ckeditor.js') }}"></script>
<script src="{{ url('js/ckfinder/ckfinder.js') }}"></script>
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script src="/js/fglobales.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>

<!-- Select2 JS --> 
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $('#grilla_folios').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/obtener_folios?tipo_folio=&tipo_documento=',
            order:[[0,'DESC'], [4,'asc'] ],
            language: lenguaje_datatable,
            columns:[
                {data:"folio",className: 'dt-body-center'},
                {data:"buzon_origen",className:'dt-body-left'},
                {data:"fecha_folio","orderable": false,className: 'dt-body-center'},
                {data:"id_documento","orderable": false,className: 'dt-body-center'},
                {data:"buzon","orderable": false,className: 'dt-body-left'},
                {data:"materia","orderable": false,className: 'dt-body-left'},
                {data:"nombre_documento","orderable": true,className: 'dt-body-left'}
            ]
            //,visible:true, searchable: true
        });
        $("#tipo_folio").select2();
        $("#tipo_documento").select2();
        $("#id_buzon").select2();
    });

    $('#btnBuscar').click(function() {
        let tipo_folio = $('#tipo_folio').val();
        let tipo_documento = $('#tipo_documento').val();
        let id_buzon = $('#id_buzon').val();
        $('#grilla_folios').DataTable().ajax.url('/obtener_folios?tipo_folio='+tipo_folio+'&tipo_documento='+tipo_documento+'&buzon='+id_buzon).load();
    });
    

    function obtenerTiposDocumentos(nTipoFolio){
        $('#tipo_documento').hide();
        $("#mensaje").show();
        $.ajax({
            url: "/obtener_tipos_documentos/",
            type: 'GET',
            dataType: 'json',
            data: {
                idTipo:nTipoFolio          
            },
            success: function(data){
                $('#tipo_documento option').remove();
                let nFilas = 0;
               
                $('#tipo_documento').append('<option value="">Todos</option>');
                for (x of data) {
                    nFilas++;
                    $('#tipo_documento').append('<option value="'+x.id_tipo_documento+'">'+x.nombre+'</option>');
                }
                if(nFilas == 0){
                    $('#tipo_documento option').remove();
                    $('#tipo_documento').append('<option value="">No existen tipos de documentos</option>');
                }
                $('#tipo_documento').show();
                $("#mensaje").hide();
            }
        });    
    }

    function obtenerBuzones(nTipoFolio,nTipoDoc){
        $('#id_buzon').hide();
        $("#mensaje").show();
        $.ajax({
            url: "/obtener_buzones/",
            type: 'GET',
            dataType: 'json',
            data: {
                idTipo:nTipoFolio,
                idTipoDoc:nTipoDoc      
            },
            success: function(data){
                $('#id_buzon option').remove();
                let nFilas = 0;
               
                $('#id_buzon').append('<option value="">Todos</option>');
                for (x of data) {
                    nFilas++;
                    $('#id_buzon').append('<option value="'+x.id_buzon+'">'+x.buzon_origen+'</option>');
                }
                if(nFilas == 0){
                    $('#id_buzon option').remove();
                    $('#id_buzon').append('<option value="">No existen buzones</option>');
                }
                $('#id_buzon').show();
                $("#mensaje").hide();
            }
        });    
    }
</script>
@include('ckfinder::setup')
@stop