<div class="tab-pane fade" id="nav-recibidos" role="tabpanel" aria-labelledby="nav-recibidos-tab">
    <div class="row">
        <div class="col-lg-2 col-md-12 col-sm-12">
            ID Doc:<br /><input class="form-control" type="text" id="gr_buscar_id_doc" name="gr_buscar_id_doc" onkeypress="javascript: if (event.key=='Enter') $('#gr_btn_buscar').trigger('click');">
        </div>
        <div class="col-lg-3 col-md-12 col-sm-12">
            Tipo Documento:<br /><select id="gr_buscar_tipo_doc" style="display:grid;" name="gr_buscar_tipo_doc" class="form-control " multiple="multiple">
                @foreach($listado_tiposdoc as $list)
                <option value="{{$list['id_tipo_documento']}}">{{$list['nombre']}}</option>
                <!-- <option value="{{$list['nombre']}}">{{$list['nombre']}}</option> -->
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-12 col-sm-12">
            Estado:<br /><select class="form-control" id="gr_buscar_estado" name="gr_buscar_estado" multiple="multiple">

                @foreach($listado_parametros['estado_documento'] as $estado_documento)
                @if(in_array($estado_documento['id_estado_documento'],[4,5,6,8,9,11,13]))
                <option value='{{$estado_documento['id_estado_documento']}}'> {{$estado_documento['nombre']}} </option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="col-lg-5 col-md-12 col-sm-12 d-flex ">
            <div class="flex-fill">
                Materia:<br /><input type="search" aria-controls="grilla_recibidos" class="form-control" id="gr_buscar_origen_materia" name="gr_buscar_origen_materia" onkeypress="javascript: if (event.key=='Enter') $('#gr_btn_buscar').trigger('click');">
            </div>
            <div class="pt-4 d-flex justify-content-end" id="botones_grilla_recibidos">
        </div>
       

        </div>
    </div>


    <p>&nbsp;</p>
    <div class="d-flex justify-content-between row">
        <div class="d-flex">
            <label class="text-bold px-2">Acciones Masivas</label>
            <select id="select-acciones-masivas" class="form-control-sm" onchange="seleccionarAccionMasiva(this.value);" id="selAccion">
                <option value="">Seleccione</option>
                <option value="1">Derivar</option>
                <option value="0">Archivar</option>
                <option value="2">Firmar</option>
            </select>
            &nbsp;&nbsp;<button class="btn text-nowrap btn-min-w  btn-primary btn-aplicar" id="btnAplicar" style="display:none">Aplicar</button>
        </div>
        <div class="">
            <select id='filtro-td' multiple>
                <option>Principal</option>
                <option>Secundario</option>
            </select>
            <div class="d-none">
                <button onClick="">A-</button>
                <button onClick="">A+</button>
            </div>
        </div>

    </div>
    <br />
    <table id="grilla_recibidos" class="table dt-responsive  no-footer dtr-inline dataTable collapsed" style="width:100%;">
        {{-- <thead>
            <tr class="grilla_header">
                <th data-priority="1">Sel</th>
                <th data-priority="1"></th>
                <th data-priority="1"></th>
                <th data-priority="2"></th>
                <th data-priority="1">E</th>
                <th data-priority="0">ID Doc</th>

                <th data-priority="3">Fecha Recepción</th>
                <th data-priority="0">Materia</th>
                <th data-priority="2">TD</th>
                <th data-priority="2">TE</th>
                <th data-priority="0">Folio</th>
                <th data-priority="2">Origen</th>
                <th data-priority="2">Contestar Hasta</th>

                <th data-priority="1">Acciones</th>
                <th data-priority="1">TIPO DOCUMENTO</th>

            </tr>
        </thead> --}}
    </table>
</div>


@push('js')

<script>
    var grilla_recibidos;
    var allBuzones = @json($allBuzones);
    var allBuzones2 = @json($allBuzones2);

    function addBtnFirma() {
        if ($('#chkFrm').prop("checked")) {
            $('#btnFirma').show();
            var buttonFrmMasiva = '<button onClick="envioFrm()" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit">Firma Masiva</button> ';
            $('#btnFirma').html(buttonFrmMasiva);
        } else {
            $('#btnFirma').hide();
        }

        var column = grilla_recibidos.column(1);
        column.visible(!column.visible());
    }
    function grillas_recibidos_texto(sTexto) {
        console.log("grillas_recibidos_texto");
        $('#documento').hide();

        fn_grilla_recibidos(sTexto);

        {{-- 
        
        if ($.fn.DataTable.isDataTable('#grilla_recibidos')) {
            $('#grilla_recibidos').DataTable().destroy();
        }
        $('#grilla_recibidos tbody').empty();

        grilla_recibidos = $('#grilla_recibidos').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax: '/buzonesListar?id_buzon={{$id_buzon}}&id_carpeta=2&texto=' + sTexto,
            type: 'json',
            order: [
                [5, 'desc']
            ],
            responsive: true,
            language: lenguaje_datatable,
            'columnDefs': [{
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                },
                {
                    'targets': 1,

                    'checkboxes': {
                        'selectRow': true
                    }
                },
                {
                    'targets': 2,
                    'checkboxes': {
                        'selectRow': true
                    }
                }
            ],
            'select': {
                'style': 'multi'
            },
            buttons: ['copy', 'excel', 'pdf'],
            columns: [{
                    data: 'id_documento',
                    name: 'documento.id_documento',
                    title:'Sel',
                    render: function(data, type, row) {
                        if (row.id_estado_documento == 4) {
                            return '<input type="checkbox" class="dt-checkboxes chkArchivar" name="chkArchivar" id="chkArchivar" value="' + row.id_documento + '" />';
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'id_documento',
                    name: 'documento.id_documento',
                    title:'Sel',
                    render: function(data, type, row) {
                        if (row.id_estado_documento == 4 || row.id_estado_documento == 9 || row.id_estado_documento == 11) {
                            return '<input type="checkbox" class="dt-checkboxes chkDerivar" name="chkDerivar" id="chkDerivar" value="' + row.id_documento + '" />';
                        } else {
                            return '';
                        }
                    }
                },
                {
                    data: 'estado_documento',
                    name: 'estado_documento.nombre_corto',
                    targets: 2,
                    searchable: false,
                    orderable: false,
                    className: 'dt-body-center',
                    title:'Sel',
                    render: function(data, type, row, full, meta) {
                        if (type === 'display') {
                            if (data == null) {
                                return '';
                            } else {
                                if (row.id_tipo_destino == 1) //principal
                                {
                                    //agrega listado de acciones

                                    if (row.id_estado_documento != 6 && row.id_estado_documento != 5 && row.id_estado_documento != 7 && row.id_estado_documento != 8 && row.id_estado_documento != 10 && row.id_estado_documento != 12 && row.id_estado_documento != 13) {
                                        if (row.json_acciones != null) {
                                            var accionesSolicitadas = row.json_acciones

                                            accionesSolicitadas = $.parseJSON(accionesSolicitadas.replace(/(&quot\;)/g, "\""));
                                            jsonTipoDoc = $.parseJSON(row.json_tipo_documento.replace(/(&quot\;)/g, "\""));

                                            for (let i in accionesSolicitadas) {
                                                if (accionesSolicitadas[i]['id_accion'] == 7) //Firmar                                                   
                                                    return '<input class="dt-checkboxes" type="checkbox" name="checkFrm" value="' + row.id_documento + '-' + row.id_documento_buzon + '">';
                                            }
                                        }

                                    }
                                }
                                return '';
                            }
                        }
                        return '';
                    }
                },
                {
                    data: 'recibido',
                    title:'Sel',
                    render: function(data, type) {
                        if (type === 'display') {
                            if (data == null) {
                                return '<div id="addChkFrm"></div>';
                            } else {
                                if (data == true) {
                                    return '<span class="fas fa-check text-green"></span><div id="addChkFrm"></div>';
                                }
                            }
                        }
                        return '<div id="addChkFrm"></div>';
                    }
                },

                {
                    data: 'estado_documento',
                    title:'Sel',
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
                    data: 'id_documento',
                    name: 'documento.id_documento',
                    render: function(data, type, row) {
                        return "<a href='javascript:ver_recibidos(" + row.id_documento + "," + row.id_documento_buzon + "," + row.id_documento_buzon_padre + ")'>" + data + "</a>";
                    }
                },

                {
                    data: 'fecha_envio_recepcion',
                    render: function(data) {
                        return moment(data).format('DD-MM-YYYY HH:mm');
                    }
                },
                {
                    data: 'materia',
                    name: 'documento.materia',
                    'width': 200,
                    render: function(data) {
                        if (data == null) {
                            return '';
                        }
                        return data;
                    }
                },
                {
                    data: 'tipo_documento'
                   
                },
                {
                    //data: 'folio',
                    data: 'documento.folio',
                    name:'documento.folio'
                },
                // { data: 'buzon_origen', name: 'tipo_origen.nombre' },
                {
                    data: 'buzon_origen',
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
                    data: 'contestas_hasta',
                    name: 'documento_buzon.contestar_hasta',
                    render: function(data) {
                        if (data == null)
                            return '';
                        else
                            return moment(data).format('DD-MM-YYYY');
                    }
                },
                 {
                    data: 'tipo_envio',
                    name: 'tipo_destino.nombre'
                },
                {
                    data: 'id_documento',
                    name: 'documento.id_documento',
                    render: function(data, type, row) {
                        if (type === 'display') {
                            if (data == null) {
                                return '';
                            } else {
                                let botonera = '<div class="dropdown">';
                                botonera += '<button class="btn text-nowrap btn-min-w  btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                botonera += ' <i class="fas fa-bars"></i>';
                                botonera += ' </button>';
                                botonera += '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';


                                if (row.id_tipo_destino == 1) //principal
                                {
                                    //agrega listado de acciones                                            
                                    if (row.id_estado_documento != 5 && row.id_estado_documento != 6 && row.id_estado_documento != 7 && row.id_estado_documento != 8 && row.id_estado_documento != 10 && row.id_estado_documento != 12 && row.id_estado_documento != 13) {
                                        if (row.json_acciones != null) {
                                            var accionesSolicitadas = row.json_acciones

                                            accionesSolicitadas = $.parseJSON(accionesSolicitadas.replace(/(&quot\;)/g, "\""));
                                            jsonTipoDoc = $.parseJSON(row.json_tipo_documento.replace(/(&quot\;)/g, "\""));

                                            for (let i in accionesSolicitadas) {
                                                if (accionesSolicitadas[i]['id_accion'] == 4) //editar  
                                                    botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_editar(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-edit text-blue"></i> ' + accionesFlujo1[accionesSolicitadas[i]['id_accion']] + '</a>';
                                                if (accionesSolicitadas[i]['id_accion'] == 6) //visar                                                   
                                                    botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_visar(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-check-circle text-blue"></i> ' + accionesFlujo1[accionesSolicitadas[i]['id_accion']] + '</a>';
                                                if (accionesSolicitadas[i]['id_accion'] == 7) //Firmar                                                   
                                                    botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_firmar(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-file text-blue"></i> ' + accionesFlujo1[accionesSolicitadas[i]['id_accion']] + '</a>';
                                                if (accionesSolicitadas[i]['id_accion'] == 8) //Generar pdf                                                   
                                                    botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_pdf(' + data + ',' + row.id_documento_buzon + ')"  href="#"><i class="fas fa-file text-blue"></i> ' + accionesFlujo1[accionesSolicitadas[i]['id_accion']] + '</a>';
                                                if (accionesSolicitadas[i]['id_accion'] == 10) //finalizar                                                   
                                                    botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_finalizar(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-file text-blue"></i> ' + accionesFlujo1[accionesSolicitadas[i]['id_accion']] + '</a>';
                                            }
                                        }

                                        if (jsonTipoDoc['id_tipo_flujo'] == 1)
                                            botonera += ' <a class="dropdown-item btn-menu-editar" onclick="responder_recibidos(' + data + ')" href="#"><i class="fas fa-reply text-orange"></i> Responder</a>';

                                        botonera += ' <a class="dropdown-item btn-menu-editar" onclick="accion_derivar_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-share text-green"></i> Derivar</a>';
                                    }

                                    botonera += ' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';

                                    if (row.id_estado_documento != 6 && row.id_estado_documento != 7 && row.id_estado_documento != 8 && row.id_estado_documento != 13)
                                        botonera += ' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ',0)"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>';

                                    if (row.id_estado_documento == 6)
                                        botonera += ' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ',1)"  href="#"><i class="fas fa-save text-blue"></i> Desarchivar</a>';

                                    botonera += ' <a class="dropdown-item btn-menu-editar" onclick="bitacora(' + data + ')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';
                                } else {
                                    botonera += ' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';

                                    if (row.id_estado_documento != 5 && row.id_estado_documento != 6 && row.id_estado_documento != 7 && row.id_estado_documento != 8 && row.id_estado_documento != 10 && row.id_estado_documento != 12 && row.id_estado_documento != 13)
                                        botonera += ' <a class="dropdown-item btn-menu-editar" onclick="derivar_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-share text-green"></i> Derivar</a>';

                                    if (row.id_estado_documento != 6)
                                        botonera += ' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ',0)"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>';

                                    if (row.id_estado_documento == 6)
                                        botonera += ' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ',1)"  href="#"><i class="fas fa-save text-blue"></i> Desarchivar</a>';

                                    botonera += ' <a class="dropdown-item btn-menu-editar" onclick="bitacora(' + data + ')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';
                                }

                                botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_clonar(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ',\'' + row.materia + '\')"  href="#"><i class="fas fa-clone text-blue"></i> Copiar Documento</a>';

                                if (row.favorito == null)
                                    botonera += '<a class="dropdown-item btn-menu-deshabilitar" onclick="add_favorito(' + data + ')" href="#"><i class="far fa-star text-green"></i> ( + ) Favoritos</a>';
                                else
                                    botonera += '<a class="dropdown-item btn-menu-deshabilitar" onclick="del_favorito(' + data + ')" href="#"><i class="fas fa-star text-green"></i> ( - ) Favoritos</a>';

                                botonera += '</div>';
                                botonera += '</div>';
                                return botonera;
                            }
                        }
                        return '';
                    }
                },
                {
                    data: 'id_tipo_documento',
                    name: 'tipo_documento.id_tipo_documento'
                }
            ],

            initComplete: function() {
                var input = $('#gr_buscar_origen_materia input').unbind(),
                    self = this.api(),
                    $clearButton = $('<button class="btn text-nowrap btn-min-w  btn-secondary btn_cerrar_guardar mx-1">')
                    .html("<i class='fa fa-eraser'></i>")
                    .click(function() {
                        $('#gr_buscar_origen_materia').val('');
                        $('#gr_buscar_estado').multiselect('selectAll', true);
                        $('#gr_buscar_estado').multiselect('deselect', ["6"]);


                        $('#gr_buscar_id_doc').val('');
                        $searchButton.click();
                    }),
                    $searchButton = $('<button class="btn text-nowrap btn-min-w  btn-success buscar_btn_buscar">')
                    .html("<i class='fa fa-search'></i>")
                    .click(function() {
                        let estados = $('#gr_buscar_estado').val().join("|");
                        grilla_recibidos
                            .columns(4).search("" + estados + "", true, false)
                            .columns(5).search($('#gr_buscar_id_doc').val())
                            .columns(7).search($('#gr_buscar_origen_materia').val())
                            .columns(14).search($('#gr_buscar_tipo_doc').val().join("|"), true, false)
                            .draw();
                    })

                $('#botones_grilla_recibidos').html('');
                $('#botones_grilla_recibidos').append($clearButton, $searchButton);
                $('#grilla_recibidos_filter').html('');

                if (aplicaFrm == 1)
                    $("div.addFrm").append("<input type='checkbox' name='chkFrm' id='chkFrm' onClick='addBtnFirma()'> Solo mostrar documentos por firmar <div class='btnFirma' id='btnFirma'></div>");



                //filtro por TD
                $("div.addFrm").append("<select id='filtro-td' multiple><option>Principal</option><option>Secundario</option></select>");
                $('#filtro-td').multiselect('select', 'Principal');
                $('#filtro-td').on("change", function() {
                    grilla_recibidos.columns(9).search($('#filtro-td').val().join("|"), true, false).draw();
                });
                $('#filtro-td').trigger("change");
                grilla_recibidos.column(0).visible(false);
                grilla_recibidos.column(1).visible(false);
                grilla_recibidos.column(2).visible(false);
            }

        });

        var column = grilla_recibidos.column(2);
        column.visible(!column.visible());
        $('#grilla_recibidos').on('error.dt', function(e, settings, techNote, message) {
            console.log('Error DataTables: ', message);
        }); 
        
        --}}

    }


    async function fn_grilla_recibidos(q) {
        console.log("fn_grilla_recibidos",q);
        $('#documento').hide();
        {{-- if ($.fn.DataTable.isDataTable('#grilla_recibidos')) {
             $('#grilla_recibidos').DataTable().destroy();
        } --}}
        
        if (!$.fn.DataTable.isDataTable('#grilla_recibidos')) {
            console.log("grilla_recibidos no inicializada");
            grilla_recibidos = $('#grilla_recibidos').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                "cache": true,
                //ajax: '/buzonesListar?id_buzon={{$id_buzon}}&id_carpeta=2',
                ajax:{
                    url:'/buzonesListar',
                    data: function(d,obj){
                        d.estados = $('#gr_buscar_estado').val().join("|");
                        d.id_buzon= {{$id_buzon}};
                        d.id_carpeta= 2;
                        console.log("recarga con texto de busqueda superior",q);
                        if($("#buscador_general").val()!=""){
                            d.texto=$("#buscador_general").val();
                        }   
                    },
                    type: 'GET',
                }, 
                order: [
                    [5, 'desc']
                ],
                responsive: true,

                language: lenguaje_datatable,
                'columnDefs': [{
                        'targets': 0,
                        'checkboxes': {
                            'selectRow': true
                        }
                    },
                    {
                        'targets': 1,
                        'checkboxes': {
                            'selectRow': true
                        }
                    },
                    {
                        'targets': 2,
                        'checkboxes': {
                            'selectRow': true
                        }
                    }
                ],
                'select': {
                    'style': 'multi'
                },
                buttons: ['copy', 'excel', 'pdf'],
                columns: [{
                        data: 'id_documento',
                        title:'Sel',
                        name: 'documento.id_documento',
                        render: function(data, type, row) {
                            if (row.id_estado_documento == 4) {
                                return '<input type="checkbox" class="dt-checkboxes chkArchivar" name="chkArchivar" id="chkArchivar" value="' + row.id_documento + '" />';
                            } else {
                                return '';
                            }
                        }
                    },
                    {
                        data: 'id_documento',
                        title:'Sel',
                        name: 'documento.id_documento',
                        render: function(data, type, row) {
                            if (row.id_estado_documento == 4 || row.id_estado_documento == 9 || row.id_estado_documento == 11) {
                                return '<input type="checkbox" class="dt-checkboxes chkDerivar" name="chkDerivar" id="chkDerivar" value="' + row.id_documento + '" />';
                            } else {
                                return '';
                            }
                        }
                    },
                    {
                        data: 'estado_documento',
                        name: 'estado_documento.nombre_corto',
                        targets: 2,
                        searchable: false,
                        orderable: false,
                        className: 'dt-body-center',
                        render: function(data, type, row, full, meta) {
                            if (type === 'display') {
                                if (data == null) {
                                    return '';
                                } else {
                                    if (row.id_tipo_destino == 1) //principal
                                    {
                                        //agrega listado de acciones

                                        if (row.id_estado_documento != 6 && row.id_estado_documento != 5 && row.id_estado_documento != 7 && row.id_estado_documento != 8 && row.id_estado_documento != 10 && row.id_estado_documento != 12 && row.id_estado_documento != 13) {
                                            if (row.json_acciones != null) {
                                                var accionesSolicitadas = row.json_acciones

                                                accionesSolicitadas = $.parseJSON(accionesSolicitadas.replace(/(&quot\;)/g, "\""));
                                                jsonTipoDoc = $.parseJSON(row.json_tipo_documento.replace(/(&quot\;)/g, "\""));

                                                for (let i in accionesSolicitadas) {
                                                    if (accionesSolicitadas[i]['id_accion'] == 7 && row.id_estado_documento == 4) //Firmar                                                   
                                                        return '<input class="dt-checkboxes" type="checkbox" name="checkFrm" value="' + row.id_documento + '-' + row.id_documento_buzon + '">';
                                                }
                                            }

                                        }
                                    }
                                    return '';
                                }
                            }
                            return '';
                        }
                    },
                    {
                        data: 'recibido',
                        title:'Sel',
                        visible:false,
                        render: function(data, type) {
                            if (type === 'display') {
                                if (data == null) {
                                    return '<div id="addChkFrm"></div>';
                                } else {
                                    if (data == true) {
                                        return '<span class="fas fa-check text-green"></span><div id="addChkFrm"></div>';
                                    }
                                }
                            }
                            return '<div id="addChkFrm"></div>';
                        }
                    },

                    {
                        data: 'estado_documento',
                        title:'E',
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
                        data: 'id_documento',
                        title:'ID',
                        name: 'documento.id_documento',
                        responsivePriority:0,
                        render: function(data, type, row) {
                            return "<a href='javascript:ver_recibidos(" + row.id_documento + "," + row.id_documento_buzon + "," + row.id_documento_buzon_padre + ")'>" + data + "</a>";
                        }
                    },

                    {
                        data: 'fecha_envio_recepcion',
                        title:'Recibido',
                        render: function(data) {
                            return moment(data).format('DD-MM-YYYY HH:mm');
                        }
                    },
                    {
                        data: 'materia',
                        title:'Materia',
                        name: 'documento.materia',
                        responsivePriority:0,
                        'width': 200,
                        {{-- render: function(data) {
                            if (data == null) {
                                return '';
                            }
                            return data.length > 60 ? data.substr(0, 60) + '…' : data;
                        } --}}
                    },
                    {
                        data: 'tipo_documento',
                        title:'Tipo Documento',
                    },
                     {
                        data: 'folio',
                        title:'Folio',
                        //name: 'documento.folio'
                    },
                   
                    // { data: 'buzon_origen', name: 'tipo_origen.nombre' },
                    {
                        data: 'buzon_origen',
                        title:'Desde',
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
                        data: 'tipo_envio',
                        name: 'tipo_destino.nombre',
                        title:'TE',
                    },
                    {
                        title:'Estado Tramitación',
                        data: 'etapa_tramitacion',
                    },
                    {
                        title:'Contestar Hasta',
                        data: 'contestas_hasta',
                        name: 'documento_buzon.contestar_hasta',
                        render: function(data) {
                            if (data == null)
                                return '';
                            else
                                return moment(data).format('DD-MM-YYYY');
                        }
                    }, 
                    {
                        data: 'id_documento',
                        title:'',
                        name: 'documento.id_documento',
                        responsivePriority:0,
                        ordenable:false,
                        searchable:false,
                        render: function(data, type, row) {
                            if (type === 'display') {
                                if (data == null) {
                                    return '';
                                } else {
                                    let botonera = '<div class="dropdown">';
                                    botonera += '<button class="btn text-nowrap btn-sm  btn-min-w  btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                                    botonera += ' <i class="fas fa-bars"></i>';
                                    botonera += ' </button>';
                                    botonera += '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';


                                    if (row.id_tipo_destino == 1) //principal
                                    {
                                        //agrega listado de acciones                                            
                                        if (row.id_estado_documento != 5 && row.id_estado_documento != 6 && row.id_estado_documento != 7 && row.id_estado_documento != 8 && row.id_estado_documento != 10 && row.id_estado_documento != 12 && row.id_estado_documento != 13) {
                                            if (row.json_acciones != null) {
                                                var accionesSolicitadas = row.json_acciones

                                                accionesSolicitadas = $.parseJSON(accionesSolicitadas.replace(/(&quot\;)/g, "\""));
                                                jsonTipoDoc = $.parseJSON(row.json_tipo_documento.replace(/(&quot\;)/g, "\""));

                                                for (let i in accionesSolicitadas) {
                                                    if (accionesSolicitadas[i]['id_accion'] == 4) //editar  
                                                        botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_editar(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-edit text-blue"></i> ' + accionesFlujo1[accionesSolicitadas[i]['id_accion']] + '</a>';
                                                    if (accionesSolicitadas[i]['id_accion'] == 6) //visar                                                   
                                                        botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_visar(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-check-circle text-blue"></i> ' + accionesFlujo1[accionesSolicitadas[i]['id_accion']] + '</a>';
                                                    if (accionesSolicitadas[i]['id_accion'] == 7) //Firmar                                                   
                                                        botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_firmar(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-file text-blue"></i> ' + accionesFlujo1[accionesSolicitadas[i]['id_accion']] + '</a>';
                                                    if (accionesSolicitadas[i]['id_accion'] == 8) //Generar pdf                                                   
                                                        botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_pdf(' + data + ',' + row.id_documento_buzon + ')"  href="#"><i class="fas fa-file text-blue"></i> ' + accionesFlujo1[accionesSolicitadas[i]['id_accion']] + '</a>';
                                                    if (accionesSolicitadas[i]['id_accion'] == 10) //finalizar                                                   
                                                        botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_finalizar(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-file text-blue"></i> ' + accionesFlujo1[accionesSolicitadas[i]['id_accion']] + '</a>';
                                                }
                                            }

                                            if (jsonTipoDoc['id_tipo_flujo'] == 1)
                                                botonera += ' <a class="dropdown-item btn-menu-editar" onclick="responder_recibidos(' + data + ')" href="#"><i class="fas fa-reply text-orange"></i> Responder</a>';

                                            botonera += ' <a class="dropdown-item btn-menu-editar" onclick="accion_derivar_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-share text-green"></i> Derivar</a>';
                                        }

                                        botonera += ' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';

                                        if (row.id_estado_documento != 6 && row.id_estado_documento != 7 && row.id_estado_documento != 8 && row.id_estado_documento != 13)
                                            botonera += ' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ',0)"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>';

                                        if (row.id_estado_documento == 6)
                                            botonera += ' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ',1)"  href="#"><i class="fas fa-save text-blue"></i> Desarchivar</a>';

                                        botonera += ' <a class="dropdown-item btn-menu-editar" onclick="bitacora(' + data + ')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';
                                    } else {
                                        botonera += ' <a class="dropdown-item btn-menu-ver" onclick="ver_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-eye text-blue"></i> Ver</a>';

                                        if (row.id_estado_documento != 5 && row.id_estado_documento != 6 && row.id_estado_documento != 7 && row.id_estado_documento != 8 && row.id_estado_documento != 10 && row.id_estado_documento != 12 && row.id_estado_documento != 13)
                                            botonera += ' <a class="dropdown-item btn-menu-editar" onclick="derivar_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ')"  href="#"><i class="fas fa-share text-green"></i> Derivar</a>';

                                        if (row.id_estado_documento != 6)
                                            botonera += ' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ',0)"  href="#"><i class="fas fa-save text-blue"></i> Archivar</a>';

                                        if (row.id_estado_documento == 6)
                                            botonera += ' <a class="dropdown-item btn-menu-editar" onclick="archivar_recibidos(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ',1)"  href="#"><i class="fas fa-save text-blue"></i> Desarchivar</a>';

                                        botonera += ' <a class="dropdown-item btn-menu-editar" onclick="bitacora(' + data + ')"  href="#"><i class="fas fa-history text-blue"></i> Bitacora</a>';
                                    }

                                    botonera += ' <a class="dropdown-item btn-menu-ver" onclick="accion_clonar(' + data + ',' + row.id_documento_buzon + ',' + row.id_documento_buzon_padre + ',\'' + row.materia + '\')"  href="#"><i class="fas fa-clone text-blue"></i> Copiar Documento</a>';

                                    if (row.favorito == null)
                                        botonera += '<a class="dropdown-item btn-menu-deshabilitar" onclick="add_favorito(' + data + ')" href="#"><i class="far fa-star text-green"></i> ( + ) Favoritos</a>';
                                    else
                                        botonera += '<a class="dropdown-item btn-menu-deshabilitar" onclick="del_favorito(' + data + ')" href="#"><i class="fas fa-star text-green"></i> ( - ) Favoritos</a>';

                                    botonera += '</div>';
                                    botonera += '</div>';
                                    return botonera;
                                }
                            }
                            return '';
                        }
                    },
                    {{-- {
                        data: 'id_tipo_documento',
                        name: 'tipo_documento.id_tipo_documento'
                    } --}}

                ],

                initComplete: function() {
                    
                    var input = $('#gr_buscar_origen_materia input').unbind(),
                        self = this.api(),
                        $clearButton = $('<button class="btn text-nowrap btn-min-w  btn-secondary btn_cerrar_guardar mx-1">')
                        .html("<i class='fa fa-eraser'></i>")
                        .click(function() {

                            
                            $('#gr_buscar_id_doc').val('');
                            $('#gr_buscar_origen_materia').val('');
                            $('#gr_buscar_estado').multiselect('deselectAll', true);
                            $('#gr_buscar_estado').multiselect('select', ["4"]);
                            //$('#gr_buscar_id_doc').val('');
                            $searchButton.click();
                        }),
                        $searchButton = $('<button class="btn text-nowrap btn-min-w  btn-success buscar_btn_buscar" id="gr_btn_buscar">')
                        .html("<i class='fa fa-search'></i>")
                        .click(function() {
                        
                        //limpiar buscador general
                        $("#resultadoBusquedaGral").html('');
                        $("#buscador_general").val('');

                        var tipos = $('#gr_buscar_tipo_doc').val();
                        grilla_recibidos
                            .columns(5).search($('#gr_buscar_id_doc').val())
                            .columns(7).search($('#gr_buscar_origen_materia').val())
                            .columns(14).search(tipos.map(valor => '^' + valor + '$').join('|'), true, true)
                            .draw();

                        //grilla_recibidos.columns(8).search($('#gr_buscar_tipo_doc').val().join("|"),true,false,true).draw();

                        })

                    $('#botones_grilla_recibidos').html('');
                    $('#botones_grilla_recibidos').append($clearButton, $searchButton);
                    $('#grilla_recibidos_filter').html('');
                    if (aplicaFrm == 1)
                        $("div.addFrm").append("<input type='checkbox' name='chkFrm' id='chkFrm' onClick='addBtnFirma()'> Solo mostrar documentos por firmar <div class='btnFirma' id='btnFirma'></div>");

                    //filtro por TD
                    $('#filtro-td').on("change", function() {
                        grilla_recibidos.columns(11).search($('#filtro-td').val().join("|"), true, false).draw();
                    });
                    //$('#filtro-td').trigger("change");
                    grilla_recibidos.column(0).visible(false);
                    grilla_recibidos.column(1).visible(false);
                    grilla_recibidos.column(2).visible(false);
                    //grilla_recibidos.column(15).visible(false);
                }

            });
        } else {
           
            //renovar con params de texto           
             grilla_recibidos.draw();
        }

        // var column = grilla_recibidos.column(2);
        //column.visible(!column.visible());
        $('#grilla_recibidos').on('error.dt', function(e, settings, techNote, message) {
            console.log('Error DataTables: ', message);
            toastr.error('Error en cargar la tabla de datos');
        });

         $('#grilla_recibidos').on('processing.dt', function (e, settings, processing) {
            if(processing)
                $("#grilla_recibidos tbody").addClass('text-hide');
            else{
                $("#grilla_recibidos tbody").removeClass('text-hide');
            }
        })

        $('#grilla_recibidos .addFrm').append('<b>---</b>');


    }
    
    function archivar_masiva() {
        let arr_chequeados = new Array();
        $(".chkArchivar").each(function() {
            if ($(this).is(":checked")) {
                arr_chequeados.push($(this).val())
            }
        });
        if (arr_chequeados.length > 0) {
            var rows_selected = grilla_recibidos.column(0).checkboxes.selected();
            $('.btn-aplicar').html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Archivando'
            );
            deshabilita_boton('btn-aplicar');

            Swal.fire({
                title: 'Archivar',
                input: 'textarea',
                inputPlaceholder: 'Ingrese fundamentación para archivar',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debe ingresar un fundamento'
                    }
                },
                html: "Se archivará(n) <b>" + arr_chequeados.length + "</b> Documento(s) <br>¿Desea Continuar?",
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                //console.log(result);
                if (result.value) { //==true || result.value.length > 0) {
                    var promiseArray = [];
                    let comentario_archivo = $('.swal2-textarea').val();

                    $.each(rows_selected, function(index, obj) {
                        $.each(grilla_recibidos.rows().data(), function(idx, data) {
                            if (data.id_documento == obj) {
                                if (arr_chequeados.includes('' + data.id_documento + '')) {
                                    var p = new Promise(function(resolve, reject) {

                                        $.ajax({
                                            url: "/archivar_documento/" + data.id_documento_buzon,
                                            type: 'PUT',
                                            dataType: 'json',
                                            data: {
                                                _token: "{{csrf_token()}}",
                                                hiddIdDocumento: data.id_documento,
                                                buzon: data.id_buzon,
                                                comentario: comentario_archivo,
                                                accion: "0"
                                            },
                                            success: function(data) {
                                                if (data.status == '200') {
                                                    return resolve();

                                                } else {
                                                    return reject();
                                                }

                                            },
                                            error: function(jqXHR, textStatus, errorThrown) {

                                                toastr.error("Falla en el documento", "¡Aviso!");

                                                habilita_boton('btn-aplicar');
                                                $('.btn-aplicar').html('Aplicar');
                                            }
                                        });
                                    });
                                }
                                promiseArray.push(p);
                            }
                        });
                    });
                    Swal.fire({
                        title: 'Archivando documentos',
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        onOpen: () => {
                            swal.showLoading();
                        }
                    })
                    Promise.all(promiseArray).then(function(obj) {
                        Swal.close();
                        toastr.success("Documentos Archivados", "¡Aviso!");
                        fn_grilla_por_recibir();
                        window.location.reload();
                    });
                } else {
                    habilita_boton('btn-aplicar');
                    $('.btn-aplicar').html('Aplicar');
                }
            })
        } else {
            toastr.error("No hay documentos seleccionados para archivar.", "¡Aviso!");
        }
    }

    function derivar_masiva() {
        let arr_chequeados_der = new Array();
        let continuar = 1;
        $(".chkDerivar").each(function() {
            if ($(this).is(":checked")) {
                arr_chequeados_der.push($(this).val())
            }
        });
        if (arr_chequeados_der.length > 0) {
            let nSecundarios = 0;
            let nPrincipal = 0;
            var rows_selected = grilla_recibidos.column(1).checkboxes.selected();
            $.each(rows_selected, function(index, obj) {
                $.each(grilla_recibidos.rows().data(), function(idx, data) {
                    if (data.id_documento == obj) {
                        if (arr_chequeados_der.includes('' + data.id_documento + '')) {
                            if (data.id_tipo_destino == 1) {
                                nPrincipal++;
                            }
                            if (data.id_tipo_destino == 2) {
                                nSecundarios++;
                            }
                        }
                    }
                });
            });
            if (nSecundarios > 0 && nPrincipal > 0) { //verificar que se haya seleccionado solo un tipo de destino
                continuar = 0;
                Swal.fire({
                    title: '<span style="font-size:30px"><i class="fa fa-exclamation-triangle fa-2x" aria-hidden="true" style="color:orange"></i><br/><strong>Aviso</strong></span>',
                    html: '<p>Ud. ha seleccionado documentos principales y secundarios.</p><p>La funcionalidad permite derivar masivamente solo documentos principales o solo documentos secundarios.</p><p> Favor seleccione nuevamente.</p>',
                    icon: 'warning',
                    showCloseButton: true,
                    focusConfirm: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar'
                });
            } else {
                ///////
                if (arr_chequeados_der.length > 0 && continuar > 0) {
                    //var rows_selected = grilla_recibidos.column(1).checkboxes.selected();
                    $('.btn-aplicar').html(
                        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Derivando'
                    );
                    deshabilita_boton('btn-aplicar');
                    setTimeout(function() {
                        $('#fDerivarMasivaDestPpal').select2();
                        $("#fDerivarMasivaDestPpal").trigger('change');
                        $('#fDerivarMasivaAcciones').multiselect('enable');
                        $('#fDerivarMasivaAcciones').multiselect({
                            nonSelectedText: 'Seleccione Acciones',
                            numberDisplayed: 6,
                            buttonWidth: '100%'
                        });
                        $("#fDerivarMasivaAcciones option[value='9']").remove();
                        $('#fDerivarMasivaAcciones').multiselect('rebuild');
                        if (nSecundarios > 0) {
                            $('#fDerivarMasivaDestPpal').prop("disabled", true);
                            $('#fDerivarMasivaComPpal').prop("disabled", true);
                            $('#fDerivarMasivaAcciones').multiselect('disable');
                        }
                        $('#fDerivarMasivaDestOtros').tagsinput({
                            tagClass: function(item) {
                                return (item.tipo == 2 ? 'label label-info' : 'label label-warning');
                            },
                            itemValue: 'value',
                            itemText: 'text',
                            typeaheadjs: {
                                name: 'allBuzones',
                                displayKey: 'text',
                                source: allBuzones.ttAdapter()
                            }
                        });

                    }, 300);
                    Swal.fire({
                        title: 'Derivar',
                        html: "<p>Se Derivará(n) <b>" + arr_chequeados_der.length + "</b> Documento(s) <br>¿Desea Continuar?</p>" +
                            "<br/>" +
                            "<form class='needs-validation text-left' id='fDerivarMariva' method='POST' action=''>" +
                            "                       <div class='form-row'>" +
                            "                            <div class='col-md-8 mb-3'>" +
                            "                                <label for='inputState'>Destinatario Principal:</label><br>" +
                            "                                <select class='form-control' style='width: 100%' id='fDerivarMasivaDestPpal' name='fDerivarMasivaDestPpal'>" +
                            "                                   <option value=''>Seleccione</option>" +
                            "                                    @foreach($allBuzones2 as $b)" +
                            "                                        <option value='{{$b['id']}}'>{{$b['text']}}</option>" +
                            "                                    @endforeach    " +
                            "                                </select>" +
                            "                            </div>" +
                            "                            <div class='col-md-4 mb-3'>" +
                            "                                <label for='inputState'>Acciones Solicitadas:</label><br>" +
                            "                                <select id='fDerivarMasivaAcciones' class='form-control' multiple='multiple' style='text-align:left !important'>                                    " +
                            "                                    @foreach($listadoAcciones as $accion)" +
                            "                                        @if($accion['id_tipo_accion'] == 1)" +
                            "                                            <option value='{{$accion['id_accion']}}'>{{$accion['nombre']}}</option>" +
                            "                                        @endif    " +
                            "                                    @endforeach    " +
                            "                                </select>" +
                            "                                </div>" +
                            "                        </div>" +
                            "                        <div class='form-row'>" +
                            "                            <div class='col-md-12 mb-3'>" +
                            "                                <label for='floatingTextarea'>Comentario a Destinatario Principal:</label>" +
                            "                                <textarea class='form-control'  id='fDerivarMasivaComPpal' ></textarea>" +
                            "                            </div>" +
                            "                        </div>" +
                            "                        <div class='form-row'>" +
                            "                            <div class='col-md-12 mb-3'>" +
                            "                                <label for='inputState'>Otro(s) Destinatario(s):</label>" +
                            "                                <input type='text' class='form-control' id='fDerivarMasivaDestOtros' data-role='tagsinput' >" +
                            "                            </div>" +
                            "                        </div>" +
                            "                        <div class='form-row'>" +
                            "                            <div class='col-md-12 mb-3'>" +
                            "                                <label for='floatingTextarea'>Comentario(s) Otro(s) Destinatario(s): </label>" +
                            "                                <textarea class='form-control' id='fDerivarMasivaComOtro'></textarea>" +
                            "                            </div>" +
                            "                        </div>" +
                            "                    </form>",
                        showCancelButton: true,
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Aceptar',
                        width: '950px'
                    }).then((result) => {
                        //console.log(result);
                        var destinatarioPrincipal = $('#fDerivarMasivaDestPpal').val();
                        var acciones_solicitadas = $('#fDerivarMasivaAcciones').val();
                        var otrosDestinatarios = $('#fDerivarMasivaDestOtros').val();
                        var comentarioPrincipal = $('#fDerivarMasivaComPpal').val();
                        var comentarioOtros = $('#fDerivarMasivaComOtro').val();
                        let msgValidacion = "";
                        if (nPrincipal > 0) {
                            if (destinatarioPrincipal == '') {
                                msgValidacion = msgValidacion + 'Debe seleccionar un destinatario principal.<br>';
                            }
                            if (acciones_solicitadas == '') {
                                msgValidacion = msgValidacion + 'Debe seleccionar al menos una acción.<br>';
                            }
                        }
                        if (nSecundarios > 0) {
                            if (otrosDestinatarios == '') {
                                msgValidacion = msgValidacion + 'Debe seleccionar un destinatario.<br>';
                            }
                        }
                        if (result.value) { //==true || result.value.length > 0) {
                            if (msgValidacion == '') {
                                var promiseArray = [];
                                $.each(rows_selected, function(index, obj) {
                                    $.each(grilla_recibidos.rows().data(), function(idx, data) {
                                        if (data.id_documento == obj) {
                                            if (arr_chequeados_der.includes('' + data.id_documento + '')) {
                                                var hiddIdBuzon = data.id_buzon;
                                                var hiddIdDocumento = data.id_documento;
                                                var hiddIdDocumentoBuzon = data.id_documento_buzon;
                                                var tipo_destino = data.id_tipo_destino;
                                                var p = new Promise(function(resolve, reject) {
                                                    $.ajax({
                                                        url: "{{route('buzones.update_documento')}}",
                                                        type: 'PUT',
                                                        dataType: 'json',
                                                        data: {
                                                            _token: "{{csrf_token()}}",
                                                            buzon: data.id_buzon,
                                                            destinatarioPrincipal: destinatarioPrincipal,
                                                            destinatarioOtros: otrosDestinatarios,
                                                            comentarioPrincipal: comentarioPrincipal,
                                                            comentarioOtros: comentarioOtros,
                                                            acciones_solicitadas: acciones_solicitadas,
                                                            hiddIdDocumento: data.id_documento,
                                                            hiddIdDocumentoBuzon: data.id_documento_buzon,
                                                            carpeta: 2,
                                                            opcionGuardar: 1,
                                                            id_tipo_destino: tipo_destino
                                                        },
                                                        success: function(data) {
                                                            if (data.status == '200') {
                                                                var p2 = new Promise(function(resolve, reject) {
                                                                    $.ajax({
                                                                        url: "../buzonesCarpetas/" + hiddIdDocumento,
                                                                        type: 'PUT',
                                                                        dataType: 'json',
                                                                        data: {
                                                                            _token: "{{csrf_token()}}",
                                                                            hiddIdDocumento: hiddIdDocumento,
                                                                            id_tipo_destino: tipo_destino,
                                                                            hiddIdDocumentoBuzon: hiddIdDocumentoBuzon,
                                                                            buzon: hiddIdBuzon,
                                                                            destinatarioPrincipal: destinatarioPrincipal,
                                                                            destinatarioOtros: otrosDestinatarios,
                                                                            acciones_solicitadas: acciones_solicitadas,
                                                                            carpeta: 2
                                                                        },
                                                                        success: function(data) {
                                                                            if (data.status == '200') {
                                                                                toastr.success("Documento Derivado", "¡Aviso!");
                                                                                location.reload();
                                                                            } else {
                                                                                toastr.error("Falla en la derivación del documento (2)", "¡Aviso!");
                                                                            }
                                                                        },
                                                                        error: function(jqXHR, textStatus, errorThrown) {
                                                                            toastr.error("Falla en la derivación del documento", "¡Aviso!");
                                                                            Swal.close();
                                                                            habilita_boton('btn-aplicar');
                                                                            $('.btn-aplicar').html('Aplicar');
                                                                        }
                                                                    });
                                                                });
                                                                promiseArray.push(p2);
                                                            } else {
                                                                toastr.error("Falla al guardar destinatarios", "¡Aviso!");
                                                            }
                                                        },
                                                        error: function(jqXHR, textStatus, errorThrown) {
                                                            toastr.error("Falla en la actualización del documento", "¡Aviso!");
                                                        }

                                                    });
                                                    Promise.all(promiseArray).then(function(obj) {
                                                        Swal.close();
                                                        toastr.success("Documentos Derivados", "¡Aviso!");
                                                        fn_grilla_por_recibir();
                                                        //location.reload();
                                                    });
                                                });
                                            }
                                            promiseArray.push(p);
                                        }
                                    })
                                });
                                Swal.fire({
                                    title: 'Derivando documentos',
                                    allowEscapeKey: false,
                                    allowOutsideClick: false,
                                    onOpen: () => {
                                        swal.showLoading();
                                    }
                                })
                                Promise.all(promiseArray).then(function(obj) {
                                    Swal.close();
                                    toastr.success("Documentos Derivados...", "¡Aviso!");
                                    fn_grilla_por_recibir();
                                    window.location.reload();
                                });
                            } else {
                                toastr.error(msgValidacion, "¡Aviso!");
                                habilita_boton('btn-aplicar');
                                $('.btn-aplicar').html('Aplicar');
                            }
                        } else {
                            habilita_boton('btn-aplicar');
                            $('.btn-aplicar').html('Aplicar');
                        }
                    })
                } else {
                    toastr.error("No hay documentos seleccionados para derivar.", "¡Aviso!");
                }
                ///////
            }
        } else {
            toastr.error("No hay documentos seleccionados para derivar.", "¡Aviso!");
        }
    }


    function seleccionarAccionMasiva(nOpcion) {
        console.log("seleccionarAccionMasiva", nOpcion);
        let opTotal = 3;
        let esVisible = "";
        if (nOpcion != "") {
            $('.btn-aplicar').show();
            for (let n = 0; n < opTotal; n++) {
                let column = grilla_recibidos.column(n);
                if (n == nOpcion) {
                    esVisible = true;
                } else {
                    esVisible = false;
                }
                column.visible(esVisible);
            }
        } else {
            $('.btn-aplicar').hide();
            for (let n = 0; n < opTotal; n++) {
                let column = grilla_recibidos.column(n);
                column.visible(false);
            }
        }

    }

    function setea_sesiones_recibidos() {
        sessionStorage.setItem('id_recibidos', $('#gr_buscar_id_doc').val());
        sessionStorage.setItem('td_recibidos', $('#gr_buscar_tipo_doc').val().join("|"));
        sessionStorage.setItem('estados_recibidos', $('#gr_buscar_estado').val().join("|"));
        sessionStorage.setItem('materia_recibidos', $('#gr_buscar_origen_materia').val());
    }

    function recarga_grilla_recibidos() {
        console.log("recarga_grilla_recibidos");
        let estados_r = $('#gr_buscar_estado').val().join("|");
        $('#grilla_recibidos').DataTable()
            .columns(4).search("" + estados_r + "", true, false)
            .columns(5).search($('#gr_buscar_id_doc').val())
            .columns(7).search($('#gr_buscar_origen_materia').val())
            .columns(8).search($('#gr_buscar_tipo_doc').val().join("|"), true, false)
            .draw();
    }
    function ver_recibidos(id_documento, id_documento_buzon, id_documento_buzon_padre) {
        $('#titulo_accion').html('Ver Documento ID '+id_documento);

        deshabilita_campos();
        cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre, 2, 11); //se incorpora accion 11 para identificar la acción de ver el documento 

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();


        $('.btn-enviar-submit').hide();
        $('#addButton').html('');

    }

    function ver_recibidos_alerta(id_documento, id_documento_buzon, id_documento_buzon_padre, materia) {
        Swal.fire({
            title: 'Advertencia',
            html: "Se visualizará Documento: <br><strong>" + id_documento + "-" + materia + "</strong><br>¿Desea continuar?",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.value == true) {
                ver_recibidos(id_documento, id_documento_buzon, id_documento_buzon_padre);
            }
        });
    }

    function responder_recibidos(id_documento) {
        $("input[name='hiddIdResponder']").val('');
        $("input[name='hiddIdResponder']").val(id_documento);
        fn_grilla_despachados();
        cambio_texto_boton_carpetas('Despachados');
        $('#nav-despachados-tab').tab('show');
        $("#add_documento").trigger("click");
    }
    function setea_sesiones_recibidos() {
        sessionStorage.setItem('id_recibidos', $('#gr_buscar_id_doc').val());
        sessionStorage.setItem('td_recibidos', $('#gr_buscar_tipo_doc').val().join("|"));
        sessionStorage.setItem('estados_recibidos', $('#gr_buscar_estado').val().join("|"));
        sessionStorage.setItem('materia_recibidos', $('#gr_buscar_origen_materia').val());
    }



$(document).ready(function() {
    console.log("ready recibidos");
$('#gr_buscar_tipo_doc').multiselect({
            includeSelectAllOption: true,
            maxHeight: 400,
            enableFiltering: true,
        });
        $('#gr_buscar_tipo_doc').multiselect('selectAll', true);

        $('#gr_buscar_estado').multiselect({
            includeSelectAllOption: true,
            maxHeight: 400,
            enableFiltering: true,
        });
});

$(function() {
    
        var allBuzones = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: allBuzones
    });
    allBuzones.initialize();

    var allBuzonesT2 = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        local: allBuzonesT2
    });
    allBuzonesT2.initialize();
    $('#form_destinatario_principal').select2({
        data: allBuzones2,
        maximumSelectionLength: 1,
        placeholder: '',
        tags: false,
        language: {
            maximumSelected: function(args) {
                var message = 'Sólo puede seleccionar ' + args.maximum + ' elemento';
                if (args.maximum != 1) {
                    message += 's';
                }
                return message;
            },
            noResults: function() {
                return 'No se encontraron resultados';
            }
        }
    }).on('select2:unselect', function(e) {
        var data = e.params.data;
        $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
    }).on('select2:select', function(e) {
        if (form_acciones_solicitadas_el.disabled == true)
            $('#form_acciones_solicitadas_el').multiselect('select', 6);
        else
            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
    });

    $('#form_otros_destinatarios_el').tagsinput({
        tagClass: function(item) {
            return (item.tipo == 2 ? 'label label-info' : 'label label-warning');
        },
        itemValue: 'value',
        itemText: 'text',
        typeaheadjs: {
            name: 'allBuzones',
            displayKey: 'text',
            source: allBuzones.ttAdapter()
        }
    });

     

    form_acciones_solicitadas_el.disabled = true;
    form_comentario_el.disabled = true;
    form_otros_destinatarios_el.disabled = true;
    form_comentario_otro_el.disabled = true;
    $(".bootstrap-tagsinput").addClass("disabled");
 // guardar y mantener
    $(".btn-guardar-submit-edit").click(function(e) {
        var materia = $("input[name='materia']").val();
        var tipo_documento = $("select[name='tipo_documento']").val();
        var nivel_acceso = $("select[name='nivel_acceso']").val();
        var efectos_terceros = $("select[name='efectos_terceros']").val();


        var errores = "";
        if (materia.length == 0 || materia.length > 500) {
            errores = errores + "La materia debe tener entre 1 y 500 caracteres.<br>";
        }
        if (tipo_documento == "") {
            errores = errores + "Seleccione un tipo de documento.<br>";
        }
        if (nivel_acceso == "") {
            errores = errores + "Seleccione un nivel de acceso.<br>";
        }
        if (efectos_terceros == "") {
            errores = errores + "Seleccione un efecto sobre terceros.<br>";
        }

        if (errores != "") {
            toastr.error(errores, "¡Aviso!");
            habilita_boton('btn-guardar-submit');
            habilita_boton('btn-guardar-submit-edit');
            habilita_boton('btn-enviar-submit');
            habilita_boton('btn_cerrar_guardar');
            habilita_boton('btn-visar');
            habilita_boton('btn-firmar');
            habilita_boton('btn-archivar');
            habilita_boton('btn-recibir-submit');
            habilita_boton('btn-derivar');
            habilita_boton('btn-derivar-2');
        } else {
            e.preventDefault();
            $('.btn-guardar-submit-edit').html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando'
            );
            $(".print-error-msg").hide();
            deshabilita_boton('btn-guardar-submit-edit');
            deshabilita_boton('btn-guardar-submit');
            deshabilita_boton('btn-enviar-submit');
            deshabilita_boton('btn_cerrar_guardar');
            deshabilita_boton('btn-visar');
            deshabilita_boton('btn-firmar');
            deshabilita_boton('btn-archivar');
            deshabilita_boton('btn-visar-derivar');
            deshabilita_boton('btn-firmar-derivar');

            accion_editar_guardar(3);
        }
    });
    

        $('.btn-aplicar').click(function() {
            let accion = $('#select-acciones-masivas').val();
            console.log(".btn-aplicar", accion);
            switch (accion) {
                case "0":
                    archivar_masiva();
                    break;
                case "1":
                    derivar_masiva();
                    break;
                case "2":
                    envioFrm();
                    break;
            }
        });


       
       // $('#gr_buscar_estado').multiselect('selectAll', false);
        //$('#gr_buscar_estado').multiselect('select', ["4"]);

    $("div.addFrm").append("<select id='filtro-td' multiple><option>Principal</option><option>Secundario</option></select>");
    $('#filtro-td').multiselect('select', 'Principal');
    
    });

    //$('#gr_buscar_estado').multiselect('select', ["4"]);


</script>
@endpush