<div class="tab-pane fade" id="nav-despachados" role="tabpanel" aria-labelledby="nav-despachados-tab" style="width: 100%;">
        <div class="row mb-4">
            <div class="col-lg-2 col-md-12 col-sm-12">
                ID Doc:<br /><input class="form-control" type="text" id="gd_buscar_id_doc" name="gd_buscar_id_doc" onkeypress="javascript: if (event.key=='Enter') $('#gd_btn_buscar').trigger('click');">
            </div>
            <div class="col-lg-3 col-md-12 col-sm-12">
                Tipo Documento:<br /><select id="gd_buscar_tipo_doc" style="display:grid;" name="gd_buscar_tipo_doc" class="form-control " multiple="multiple">
                @foreach($listado_tiposdoc as $list)
                <option value="{{$list['id_tipo_documento']}}">{{$list['nombre']}}</option>
                <!-- <option value="{{$list['nombre']}}">{{$list['nombre']}}</option> -->
                @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-12 col-sm-12">
                Estado:<br /><select class="form-control" id="gd_buscar_estado" name="gd_buscar_estado" multiple="multiple">

                    @foreach($listado_parametros['estado_documento'] as $estado_documento)
                    @if(in_array($estado_documento['id_estado_documento'],[1,2,7,10,12]))
                    <option value='{{$estado_documento['id_estado_documento']}}'> {{$estado_documento['nombre']}} </option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-lg-5 col-md-12 col-sm-12 d-flex ">
                <div class="flex-fill">
                    Materia:<br /><input type="search" aria-controls="grilla_despachados" class="form-control" id="gd_buscar_origen_materia" name="gd_buscar_origen_materia" onkeypress="javascript: if (event.key=='Enter') $('#gd_btn_buscar').trigger('click');">
                </div>
                <div class="pt-4 d-flex justify-content-end" id="botones_grilla_despachados"></div>
            </div>
        </div>

    <table id="grilla_despachados" class="table dt-responsive  no-footer dtr-inline dataTable collapsed " style="width:100%"></table>
</div>

@push('js')
<script>
var grilla_despachados;

async function fn_grilla_despachados(q) {
    $('#documento').hide();
    if (!$.fn.DataTable.isDataTable('#grilla_despachados')) {
        grilla_despachados = $('#grilla_despachados').DataTable({
            processing: true,
            serverSide: true,
            pageLength: {{ config('sgd.ndocs_perpage') }},
            lengthMenu: [[{{ config('sgd.ndocs_perpage') }}, 5, 15, 25, 100, -1 ], [ {{ config('sgd.ndocs_perpage') }},5, 15, 25, 100, "Todos" ]],
            ajax:{
                url:route('buzones.listar',{'id': {{$id_buzon}} }),
                data: function(d,obj){
                        d.estados = $('#gd_buscar_estado').val().join("|");
                        d.id_buzon= {{$id_buzon}};
                        d.tipodocs = $('#gd_buscar_tipo_doc').val().join("|");
                        d.id_carpeta= 3;
                        if($("#buscador_general").val()!=""){
                            d.texto=$("#buscador_general").val();
                        }   
                    },
                type: 'GET',
            },
            type: 'json',
            order: [
                [2, 'desc']
            ],
            responsive: true,
            language: lenguaje_datatable,
            columns: [{
                    data: 'recibido',
                    render: function(data, type) {
                        if (type === 'display') {
                            if (data == null) {
                                return '';
                            } else {
                                if (data == true) {
                                    return '<span class="fas fa-check text-green"></span>';
                                }
                            }
                        }
                        return '';
                    }
                },
                {
                    title:'E',
                    data: 'estado_documento',
                    name: 'documento_buzon.id_estado_documento',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            let htmlColor = '<div class="fondo_estado" style=" background-color: ' + row.codigo_estado + ';">' + data + '</div>';
                            return htmlColor;
                        }
                        return data;
                    }
                },
                {
                    title:'ID',
                    data: 'identificador',
                    name: 'documento.identificador',
                    responsivePriority:0,
                    render: function(data, type, row) {
                        return "<a href='javascript:ver_despachados(" + row.id_documento + "," + row.id_documento_buzon + "," + row.id_documento_buzon_padre + ")'>" + data + "</a>";
                    }
                },
                {
                    title:'Fecha Despacho',
                    data: 'fecha_envio',
                },
                {
                    title:'TD',
                    data: 'tipo_documento'
                },
                {
                    title:'Destino',
                    data: 'destinatario',
                    searchable:false,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if (data != null)
                                return listadoBuzones[data];
                            else
                                return '';
                        }
                        return '';
                    }
                },
                {
                    title:'Materia',
                    data: 'materia',
                    name: 'documento.materia',
                    responsivePriority:0,
                },
                {
                    title:'Rpta a',
                    data: 'respuesta_a',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            var docsRespuesta = row.respuesta_a;
                            var docs = '';

                            if (docsRespuesta != null) {
                                docsRespuesta = $.parseJSON(docsRespuesta.replace(/(&quot\;)/g, "\""));

                                if (docsRespuesta.length > 0) {
                                    for (let i in docsRespuesta) {
                                        docs += docsRespuesta[i]['identificador'] + ' - ';
                                    }

                                    return docs.substring(0, docs.length - 2);
                                } else
                                    return '--';
                            }
                        }
                        return '--';
                    }
                },
                 {
                    title:'Estado Tramitación',
                    data: 'etapa_tramitacion',
                },
                {
                    title:'Fecha Doc',
                    data: 'fecha_creacion',
                    responsivePriority:10002,
                    render: function(data) {
                        return moment(data).format('DD-MM-YYYY HH:mm');
                    }
                },
                {
                    title:'',
                    data: 'id_documento',
                    responsivePriority:0,
                    ordenable:false,
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if (data == null) {
                                return '';
                            } else {
                                let botonera = '<div class="dropdown">';
                                botonera += '<button class="btn btn-sm text-nowrap btn-min-w  btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                botonera += ' <i class="fas fa-bars"></i>';
                                botonera += ' </button>';
                                botonera += ' <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';

                                if (row.id_estado_documento == 1) //B
                                {
                                    botonera += '<a class="dropdown-item btn-menu-editar" onclick="editar_despachados(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ',1)"  href="#"><i class="fas fa-edit text-blue"></i> Editar</a>';
                                    botonera += '<a class="dropdown-item btn-menu-editar" onclick="eliminar_despachados(' + data + ',' + row.id_documento_buzon + ')"  href="#"><i class="fas fa-trash-alt text-red"></i> Eliminar</a>';
                                }
                                let estadosDespachados = [2,7,10,12];

                                if (estadosDespachados.includes(row.id_estado_documento) ) //E
                                {
                                    botonera += '<a class="dropdown-item btn-menu-ver" onclick="ver_despachados(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';
                                    if (row.eliminar > 0) {
                                        botonera += '<a class="dropdown-item btn-menu-editar" onclick="eliminar_enviado(' + data + ',' + row.id_documento_buzon + ')"  href="#"><i class="fas fa-trash-alt text-red"></i> Eliminar Envío</a>';
                                    }
                                    botonera += '<a class="dropdown-item btn-menu-editar" onclick="bitacora(' + data + ')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';

                                    if (row.favorito == null)
                                        botonera += '<a class="dropdown-item btn-menu-deshabilitar" onclick="add_favorito(' + data + ')" href="#"><i class="far fa-star text-green"></i> ( + ) Favoritos</a>';
                                    else
                                        botonera += '<a class="dropdown-item btn-menu-deshabilitar" onclick="del_favorito(' + data + ')" href="#"><i class="fas fa-star text-green"></i> ( - ) Favoritos</a>';

                                }
                                botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_clonar(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ',\'' + row.materia + '\')"  href="#"><i class="fas fa-clone text-blue"></i> Copiar Documento</a>';


                                botonera += '</div>';
                                botonera += '</div>';
                                return botonera;
                            }
                        }
                        return '';
                    }
                }
            ],
            initComplete: function() {
                var input = $('#gd_buscar_origen_materia input').unbind(),
                    self = this.api(),
                    $clearButton = $('<button class="btn text-nowrap btn-min-w  btn-secondary btn_cerrar_guardar mx-1">')
                    .html("<i class='fa fa-eraser'></i>")
                    .click(function() {
                        $('#gd_buscar_id_doc').val('');
                        $('#gd_buscar_origen_materia').val('');
                        $('#gd_buscar_estado').multiselect('selectAll', true);
                        $('#gd_buscar_estado').multiselect('deselect', ["A"]);
                        $('#gd_buscar_tipo_doc').multiselect('selectAll', true);
                        $('#gd_buscar_tipo_doc').multiselect('deselect', ["A"]);
                        $('#gd_buscar_id_doc').val('');
                        $searchButton.click();
                    }),
                    $searchButton = $('<button class="btn text-nowrap btn-min-w  btn-success buscar_btn_buscar" id="gd_btn_buscar">')
                    .html("<i class='fa fa-search'></i>")
                    .click(function() {
                        //limpiar buscador general
                        $("#resultadoBusquedaGral").html('');
                        $("#buscador_general").val('');

                        grilla_despachados
                            .columns(2).search($('#gd_buscar_id_doc').val())
                            .columns(5).search($('#gd_buscar_tipo_doc').val().join("|"), true, false)
                            .columns(6).search($('#gd_buscar_origen_materia').val())
                        .draw();

                    })
                $('#botones_grilla_despachados').html('');
                $('#botones_grilla_despachados').append($clearButton, $searchButton);
                $('#grilla_despachados_filter').html('');
            }
        });

    }else {
        grilla_despachados.draw();
    }

    $('#grilla_despachados').on('error.dt', function(e, settings, techNote, message) {
        console.log('Error DataTables: ', message);
    });
    grilla_despachados.on('processing.dt', function (e, settings, processing) {
        if(processing)
            $("#grilla_despachados tbody").addClass('text-hide');
        else{
            $("#grilla_despachados tbody").removeClass('text-hide');
        }
    }) 
    
}
    function grillas_despachados_texto(stexto) {
        fn_grilla_despachados(stexto);
    }

    

    function setea_sesiones_despachados() {
        sessionStorage.setItem('id_despachados', $('#gd_buscar_id_doc').val());
        sessionStorage.setItem('td_despachados', $('#gd_buscar_tipo_doc').val().join("|"));
        sessionStorage.setItem('estados_despachados', $('#gd_buscar_estado').val().join("|"));
        sessionStorage.setItem('materia_despachados', $('#gd_buscar_origen_materia').val());

    }

    function recarga_grilla_despachados() {
        if (!$.fn.DataTable.isDataTable('#grilla_despachados')) {
            fn_grilla_despachados();
        }else{
            let estadosd = $('#gd_buscar_estado').val().join("|");
            $('#grilla_despachados').DataTable().columns(1).search("" + estadosd + "", true, false).draw();
            $('#grilla_despachados').DataTable().columns(2).search($('#gd_buscar_id_doc').val()).draw();
            $('#grilla_despachados').DataTable().columns(5).search($('#gd_buscar_tipo_doc').val().join("|"), true, false).draw();
            $('#grilla_despachados').DataTable().columns(7).search($('#gd_buscar_origen_materia').val()).draw();
        }
    }

    $(function() {
        
        $('#gd_buscar_tipo_doc').multiselect({
            includeSelectAllOption: true,
            maxHeight: 300,
            enableFiltering: true
        });
        $('#gd_buscar_tipo_doc').multiselect('selectAll', true);
        $('#gd_buscar_estado').multiselect({
            includeSelectAllOption: true,
            maxHeight: 300,
            enableFiltering: true
        });
        $('#gd_buscar_estado').multiselect('selectAll', true);
        $('#select-acciones-masivas').multiselect('select', null);
    });
</script>
@endpush