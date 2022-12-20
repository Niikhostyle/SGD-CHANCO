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
            <div class="col-md-4 md-4">
                <div class="form-group">
                    <label for="id_documento">Tipo Folio: </label>
                    <select id="tipo_folio" name="tipo_folio" onchange="obtenerTiposDocumentos(this.value)">
                        <option value="">Seleccionar</option>
                        @foreach($tipos_folio as $tp)
                            <option value="{{ $tp['id_tipo_folio'] }}">{{ $tp['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4 md-4">
                <div class="form-group">
                    <label for="id_documento">Tipo Documento: </label>
                    <select id="tipo_documento" name="tipo_documento">
                        <option value="">Seleccionar tipo folio</option>
                    </select>
                    <span id="mensaje" style="display:none;">Procesando...</span>
                </div>
            </div>
            <div class="col-md-4 md-4">
                <div class="form-group">
                    <i id="botones_grilla_despachados"></i>
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
                        <th>Folio</th>
                        <th>Fecha Asignación Folio</th>
                        <th>ID Documento</th>
                        <th>Buzón Actual</th>
                        <th>Materia</th>                                
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
<script>

    $(document).ready(function () {
        $(function() {         
            fn_grilla_folios();
        });

    });

    async function fn_grilla_folios(){

        $('#grilla_folios tbody').empty();        

        grilla_folios = $('#grilla_folios').DataTable({
            //dom: 'Brtip', 
            buttons: {
                dom:{
                    button:{
                        className: 'btn'
                    }
                },
                buttons:[
                    {
                        extend:"excel",
                        exportOptions: { 
                            columns: function(column, data, node) {
                                
                                if (column > 8) {
                                    return false;
                                }
                                return true;
                            }
                        },
                        text:'Descargar busqueda',
                        className: 'btn btn-success',
                        excelStyles:{
                            temlate:'header_blue'
                        }
                    }
                ]
            },
            processing: true,
            serverSide: false,
            searching:true,
            order: [[ 0, 'desc' ]], 
            responsive: true,                
            language: lenguaje_datatable,
            columns: [
                { data: 'folio', name: 'folio' },
                { data:'fecha_folio',name:'fecha_folio'},
                { data: 'id_documento', name: 'id_documento' },
                { data: 'buzon', name: 'buzon' },
                { data: 'materia', name: 'materia' 
                }
            ],
            initComplete : function() {
                self = this.api(),
                $clearButton = $('<button class="btn btn-secondary btn_cerrar_guardar btn_busqueda">')
                        .text('Limpiar')
                        .click(function() {
                            $('#tipo_documento').find('option:eq(0)').prop('selected', true);
                            $('#tipo_folio').find('option:eq(0)').prop('selected', true);
                            $searchButton.click();
                        }),
                    $searchButton = $('<button class="btn btn-success buscar_btn_buscar btn_busqueda">')
                        .text('Buscar')
                        .click(function() {
                                grilla_folios.clear();
                                $('.dataTables_processing', $('#grilla_folios').closest('.dataTables_wrapper')).show();
                                //buscar en servidor
                                console.log($('#tipo_folio').val())
                                //construir objeto de busqueda
                                let params = new FormData();
                                let queries =["tipo_folio","tipo_documento"];
                                for (let i = 0; i < queries.length; i++) {
                                    //console.log(item);
                                    //console.log($('#'+queries[i]).val());
                                    if($('#'+queries[i]).val()!=''){
                                        params.append(queries[i],$('#'+queries[i]).val());
                                    }
                                }
                                $.ajax({
                                    Type: 'GET',
                                    url: '/obtener_folios',
                                    data:[...params].reduce((o, [k, v]) => {o[k] = v;return o;}, {}),
                                    processing: true,
                                    serverSide: true,
                                    order: [[ 0, 'desc' ]], 
                                }).done(function (result) {
                                    console.log(typeof result);
                                    //result = JSON.parse(result);
                                    
                                    grilla_folios.rows.add(result.data).draw();
                                    $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
                                    $('.dataTables_processing', $('#grilla_folios').closest('.dataTables_wrapper')).hide();
                                    grilla_folios.columns(0).search($('#buscar_id_documento').val()).draw();
                                    grilla_folios.columns(2).search($('#buscar_tipo_documento').val()).draw();
                                    grilla_folios.columns(3).search($('#buscar_folio').val()).draw();                               
                                    
                                    grilla_folios.columns(4).search($('#buscar_buzon_origen').val()).draw();  
                                    if ($('#buscar_efectos_sobre_terceros').is(':checked'))
                                        grilla_folios.columns(7).search($('#buscar_efectos_sobre_terceros').is(':checked')).draw(); 
                                    else
                                        grilla_folios.columns(7).search("true|false", true, false).draw(); 

                                });
                        })
                $('#botones_grilla_despachados').html('');
                $('#botones_grilla_despachados').append($clearButton,$searchButton);
                $('#grilla_despachados_filter').html('');
            }
        }); 
    }    

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
               
                $('#tipo_documento').append('<option value="">Seleccionar</option>');
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
</script>
@include('ckfinder::setup')
@stop