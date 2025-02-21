
<div class="tab-pane fade show active" id="nav-por-recibir" role="tabpanel" aria-labelledby="nav-por-recibir-tab">
<div class="row">
        <div class="col-lg-2 col-md-12 col-sm-12">
            ID Doc:<br /><input class="form-control" type="text" id="gp_buscar_id_doc" name="gp_buscar_id_doc" onkeypress="javascript: if (event.key=='Enter') $('#gp_btn_buscar').trigger('click');">
        </div>
        <div class="col-lg-3 col-md-12 col-sm-12">
            Tipo Documento:<br /><select id="gp_buscar_tipo_doc" style="display:grid;" name="gp_buscar_tipo_doc" class="form-control " multiple="multiple">
                @foreach($listado_tiposdoc as $list)
                <option value="{{$list['id_tipo_documento']}}">{{$list['nombre']}}</option>
                <!-- <option value="{{$list['nombre']}}">{{$list['nombre']}}</option> -->
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-12 col-sm-12">
            Estado:<br /><select class="form-control" id="gp_buscar_estado" name="gp_buscar_estado" multiple="multiple">
                @foreach($listado_parametros['estado_documento'] as $estado_documento)
                @if(in_array($estado_documento['id_estado_documento'],[3]))
                <option value='{{$estado_documento['id_estado_documento']}}'> {{$estado_documento['nombre']}} </option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="col-lg-5 col-md-12 col-sm-12 d-flex ">
            <div class="flex-fill">
                Materia:<br /><input type="search" aria-controls="grilla_porrecibir" class="form-control" id="gp_buscar_origen_materia" name="gp_buscar_origen_materia" onkeypress="javascript: if (event.key=='Enter') $('#gp_btn_buscar').trigger('click');">
            </div>
            <div class="pt-4 d-flex justify-content-end" id="botones_grilla_porrecibir">
        </div>
       

        </div>
    </div>

    <div class="pb-4 pt-2">
        <button onclick="recepcion_masiva()" class="btn text-nowrap btn-min-w  btn-sm btn-primary btn-recepcion-masiva">Recibir Masivo</button>
    </div>
    <table id="grilla_por_recibir" class="table dt-responsive " style="width:100%">
        <thead>
            <tr class="grilla_header">
                
            </tr>
        </thead>
    </table>
</div>


@push('js')
<script>

     var grilla_por_recibir;

    function recibir_documento(destino) {
        var _token = $("input[name='_token']").val();
        $('.btn-recibir-submit').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Recibiendo');
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        setea_sesiones_recibidos();
        setea_sesiones_despachados();
        Swal.fire({
            title: 'Recibir',
            html: "Se recepcionará el documento: <br>" +
                "<b>" + $("input[name='materia']").val() + "</b><br>",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            //console.log(result);
            if (result.value == true) {
                $.ajax({
                    //url: "/actualizar_estado_documento/" + hiddIdDocumentoBuzon,
                    url: route('documentos.actualizar_estado',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                        _token: _token,
                        hiddIdDocumento: hiddIdDocumento,
                        buzon: hiddIdBuzon,
                        destino: destino,
                        accion: 3
                    },
                    success: function(data) {
                        if (data.status == '200') {
                            toastr.success("Documento Recepcionado", "¡Aviso!");
                            $('.btn-recibir-submit').html('Recibir');
                            $('#card_crear_documento').hide();
                            clear_form();
                            fn_grilla_recibidos();
                            location.reload();
                        } else {
                            toastr.error(data.data.comentario, "¡Aviso!");
                            $('.btn-recibir-submit').html('Recibir');
                        }

                        $('.btn-enviar-submit').html('Enviar');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {

                        toastr.error("Falla en el documento", "¡Aviso!");

                        $('.btn-enviar-submit').html('Enviar');
                    }
                });
            } else {
                $('.btn-recibir-submit').html('Recibir');
            }
        })

    }

    function devolver_documento(destino) {
        var _token = $("input[name='_token']").val();
        $('.btn-devolver-submit').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Devolviendo');
        $('.btn-devolver-submit').attr('disabled','disabled');
        var hiddIdBuzon = $("input[name='hiddIdBuzon']").val();
        var hiddIdDocumento = $("input[name='hiddIdDocumento']").val();
        var hiddIdDocumentoBuzon = $("input[name='hiddIdDocumentoBuzon']").val();
        //setea_sesiones_recibidos();
        //setea_sesiones_despachados();

        //buscar datos bitacora
        console.log(objDoc);
        //console.log(listadoBuzones);


        let IdDBpadre = objDoc.rel_documento_buzon_actual[0].id_documento_buzon_padre;
        let objDevol = objDoc.rel_documento_buzon.find(e => e.id_documento_buzon ==IdDBpadre );

        Swal.fire({
            title: 'Devolver',
            html: "Se Devolverá el documento: <br>" +
                "<b>" + $("input[name='materia']").val() + "</b><br>" +
                "<p>al Buzón <br/><b>"+listadoBuzones[objDevol.id_buzon]+"</b>" +
                    "<form class='needs-validation text-left' id='formDevolverDoc' method='POST' action=''>" +
                    "                        <div class='form-row'>" +
                    "                            <div class='col-md-12 mb-3'>" +
                    "                                <label for='floatingTextarea'>Comentario de devolución:</label>" +
                    "                                <textarea class='form-control' autofocus id='fDevolverComentario' aria-describedby='fDevolverComentario-error' ></textarea>" +
                    "                                <p id='fDevolverComentario-error' class='invalid-feedback' >Falta agregar comentario de devolución</p>" +
                    "                            </div>" +
                    "                        </div>" +
                    "<div class=''>" +
                    "                                <label for='inputState'>Acciones Solicitadas:</label><br>" +
                    "                                <select id='fDevolverAcciones' aria-describedby='fDevolverAcciones-error' class='form-control w-100' multiple='multiple' style='text-align:left !important;width:100%'>                                    " +
                    "                                    @foreach($listadoAcciones as $accion)" +
                    "                                        @if($accion['id_tipo_accion'] == 1)" +
                    "                                            <option value='{{$accion['id_accion']}}'>{{$accion['nombre']}}</option>" +
                    "                                        @endif    " +
                    "                                    @endforeach    " +
                    "                                </select>" +
                    "                                <p id='fDevolverAcciones-error' class='invalid-feedback' >Falta agregar acciones a la devolución</p>" +
                    "                                </div>" +
                    "                    </form>",
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Devolver',
            width: '950px',
            onBeforeOpen:function(){
                $("#fDevolverAcciones").select2();
                let acciones = JSON.parse(objDevol.json_acciones);
                $.each(acciones, function(i, item) {
                    $("#fDevolverAcciones").val(item.id_accion);
                    $('#fDevolverAcciones').trigger('change');
                }); 
                $("#fDevolverComentario").focus();
            },
            preConfirm:function(){
                $("#fDevolverComentario").removeClass("is-invalid");
                $("#fDevolverAcciones").removeClass("is-invalid");

                if($("#fDevolverComentario").val()==""){
                    $("#fDevolverComentario").addClass("is-invalid");
                    console.log("falta comentario");
                    return false;
                }
                console.log($("#fDevolverAcciones").val());
                if($("#fDevolverAcciones").val().length==0){
                    // swal.fire("falta indicar acciones");
                    $("#fDevolverAcciones").addClass("is-invalid");
                    return false;
                }
               // return true;
            }

        }).then((result) => {
            //console.log(result);
            if (result.value == true) {
                $.ajax({
                    //url: "/actualizar_estado_documento/" + hiddIdDocumentoBuzon,
                    url:route('documentos.devolver',{'buzon':hiddIdDocumentoBuzon,'id':hiddIdDocumento}),
                    type: 'PUT',
                    dataType: 'json',
                    data: {
                         _token: _token, 
                        hiddIdDocumentoBuzon: hiddIdDocumentoBuzon,
                        //buzon:objDoc.rel_documento_buzon_actual[0].id_buzon,
                        destinatarioPrincipal:objDevol.id_buzon,
                        comentarioPrincipal:$("#fDevolverComentario").val(),
                        acciones:$("#fDevolverAcciones").val(),
                    
                    },
                    success: function(data) {
                        if (data.status == '200') {
                            toastr.success("Documento Devuelto", "¡Aviso!");
                            $('.btn-devolver-submit').html('<i class="fa fa-reply mx-1"></i>Devolver</button>').removeAttr('disabled');
                            $('#card_crear_documento').hide();
                            clear_form();
                            fn_grilla_recibidos();
                            location.reload();
                        } else {
                            toastr.error(data.data.comentario, "¡Aviso!");
                            $('.btn-devolver-submit').html('<i class="fa fa-reply mx-1"></i>Devolver</button>').removeAttr('disabled');
                        }

                        $('.btn-enviar-submit').html('Enviar');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        toastr.error("Falla en la devolución del documento. "+jqXHR.responseJSON.message, "¡Aviso!");
                        $('.btn-devolver-submit').html('<i class="fa fa-reply mx-1"></i>Devolver</button>').removeAttr('disabled');
                    }
                });
            } else {
                $('.btn-devolver-submit').html('<i class="fa fa-reply mx-1"></i>Devolver</button>').removeAttr('disabled');
            }
        })

    }

    function visualizar_documento_por_recibir(id_documento, id_documento_buzon, id_documento_buzon_padre, destino) {
        $('#titulo_accion').html('Ver Documento ID '+id_documento);

        deshabilita_campos();
        cargar_datos_grilla(id_documento, id_documento_buzon, id_documento_buzon_padre, 1, 0);

        

        $('.btn-guardar-submit').hide();
        $('.btn-guardar-submit-edit').hide();
        $('.btn-enviar-submit').hide();
        $('#addButton').html('');
        console.log(id_documento_buzon_padre);
        console.log(destino);

        var buttonDevolver = '<button onClick="devolver_documento(' + id_documento_buzon_padre + ')" type="button" class="btn text-nowrap btn-min-w mx-1 btn-light btn-outline-secondary btn-devolver-submit "><i class="fa fa-reply mx-1"></i>Devolver</button>';
        var buttonRecibir = '<button onClick="recibir_documento(' + destino + ')" type="button" class="btn text-nowrap btn-min-w  btn-success btn-recibir-submit "><i class="fa fa-chevron-right mx-1"></i>Recibir</button>';
        if(destino==1)
            $('#addButton').append(buttonDevolver);
        
        $('#addButton').append(buttonRecibir);
    }
function grilla_por_recibir_texto(sTexto) {
        fn_grilla_por_recibir();
    }
    async function fn_grilla_por_recibir() {
        $('#documento').hide();
        if (!$.fn.DataTable.isDataTable('#grilla_por_recibir')) {
           
        grilla_por_recibir = $('#grilla_por_recibir').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            searching: false,
            layout: {
                topStart: function () {
                    let toolbar = document.createElement('div');
                    toolbar.innerHTML = '<b>Custom tool bar! Text/images etc.</b>';
        
                    return toolbar;
                }
            },
            ajax:{
                url: route('buzones.listar',{'id': {{$id_buzon}} }) , //'/buzonesListar',
                data: function(d,obj){
                    d.tipodocs = $('#gp_buscar_tipo_doc').val().join("|");
                    //d.estados = $('#gp_buscar_estado').val().join("|");
                    d.id_buzon= {{$id_buzon}};
                    d.id_carpeta= 1;
                    if($("#buscador_general").val()!=""){
                        d.texto=$("#buscador_general").val();
                    }   
                },
                type: 'GET',
            }, 
            type: 'json',
            responsive: true,
            language: lenguaje_datatable,
            'columnDefs': [{
                'targets': 0,
                'checkboxes': {
                    'selectRow': true
                }
            }],
            'select': {
                'style': 'multi'
            },
            'order': [
                [1, 'asc']
            ],
            columns: [{
                    title: 'Sel.',
                    data: 'id_documento',
                    name: 'documento.id_documento',
                },
                {
                    title: 'ID Doc.',
                    data: 'id_documento',
                    name: 'documento.id_documento',

                    render: function(data, type, row) {
                        return "<a href='javascript:visualizar_documento_por_recibir(" + row.id_documento + "," + row.id_documento_buzon + "," + row.id_documento_buzon_padre + "," + row.id_tipo_destino + ")'>" + data + "</a>";
                    }

                },
                {
                    title: 'F. Entrada',
                    data: 'fecha_envio',
                    render: function(data) {
                        if (data == null)
                            return '';
                        else
                            return moment(data).format('DD-MM-YYYY HH:mm');
                    }
                },
                {
                    title: 'Materia',
                    data: 'materia',
                    name: 'documento.materia',
                    'width': 200,
                    render: function(data) {
                        if (data == null) {
                            return '';
                        }
                        return data.length > 60 ? data.substr(0, 60) + '…' : data;
                    },
                },

                {
                    title: 'TD',
                    data: 'tipo_documento',
                    name: 'tipo_documento.nombre'
                },
                {
                    title: 'TE',
                    data: 'tipo_envio',
                    name: 'tipo_destino.nombre'
                },
                {
                    title: 'Origen',
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
                    title: 'Contestar Hasta',
                    data: 'contestas_hasta',
                    render: function(data) {
                        if (data == null)
                            return '';
                        else
                            return moment(data).format('DD-MM-YYYY');
                    }
                }

            ],
            initComplete: function() {
                var input = $('#gp_buscar_origen_materia input').unbind(),
                    self = this.api(),
                    $clearButton = $('<button class="btn text-nowrap btn-min-w  btn-secondary btn_cerrar_guardar mx-1">')
                    .html("<i class='fa fa-eraser'></i>")
                    .click(function() {
                        $('#gp_buscar_id_doc').val('');
                        $('#gp_buscar_origen_materia').val('');
                        $searchButton.click();
                    }),
                    $searchButton = $('<button class="btn text-nowrap btn-min-w  btn-success buscar_btn_buscar" id="gp_btn_buscar">')
                    .html("<i class='fa fa-search'></i>")
                    .click(function() {
                    console.log("click search")
                    //limpiar buscador general
                    $("#resultadoBusquedaGral").html('');
                    $("#buscador_general").val('');

                    var tipos = $('#gp_buscar_tipo_doc').val();
                    grilla_por_recibir
                        .columns(1).search($('#gp_buscar_id_doc').val())
                        .columns(3).search($('#gp_buscar_origen_materia').val())
                        .columns(4).search(tipos.map(valor => '^' + valor + '$').join('|'), true, true)
                        .draw();

                    //grilla_recibidos.columns(8).search($('#gr_buscar_tipo_doc').val().join("|"),true,false,true).draw();

                    })

                $('#botones_grilla_porrecibir').html('');
                $('#botones_grilla_porrecibir').append($clearButton, $searchButton);
                $('#grilla_porrecibir_filter').html('');
               
            },
            rowCallback: function(row, data, index) {}
        });
        $('#grilla_por_recibir').on('error.dt', function(e, settings, techNote, message) {
            console.log('Error DataTables: ', message);
        });
        }else {         
             grilla_por_recibir.draw();
        }

        $('#grilla_por_recibir').on('processing.dt', function (e, settings, processing) {
            if(processing)
                $("#grilla_por_recibir tbody").addClass('text-hide');
            else{
                $("#grilla_por_recibir tbody").removeClass('text-hide');
            }
        })
    }

    function recepcion_masiva() {

        var rows_selected = grilla_por_recibir.column(0).checkboxes.selected();
        if (rows_selected.length > 0) {
            $('.btn-recepcion-masiva').html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Recibiendo'
            );
            deshabilita_boton('btn-recepcion-masiva');
            Swal.fire({
                title: 'Recibir',
                html: "Se recepcionará(n) <b>" + rows_selected.length + "</b> Documento(s) <br>¿Desea Continuar?",
                showCancelButton: true,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                console.log(result);
                if (result.value == true) {
                    var promiseArray = [];
                    $.each(rows_selected, function(index, obj) {
                        $.each(grilla_por_recibir.rows().data(), function(idx, data) {
                            //¡OJO! posible inconsistencia de datos
                            if (data.id_documento == obj) {
                                var p = new Promise(function(resolve, reject) {
                                    $.ajax({
                                        url: route('documentos.actualizar_estado',{'buzon':data.id_documento_buzon,'id':data.id_documento}),//"/actualizar_estado_documento/" + data.id_documento_buzon,
                                        type: 'PUT',
                                        dataType: 'json',
                                        data: {
                                            _token: "{{csrf_token()}}",
                                            hiddIdDocumento: data.id_documento,
                                            buzon: data.id_buzon,
                                            destino: data.id_tipo_destino,
                                            accion: 3
                                        },
                                        success: function(data) {
                                            //console.log("success",data)
                                            if (data.status == '200') {
                                                return resolve();

                                            } else {
                                                return reject();
                                            }
                                        },
                                        error: function(jqXHR, textStatus, errorThrown) {
                                            console.log(textStatus);
                                            reject(new Error('Error : ' + textStatus));
                                            $('.btn-recepcion-masiva').html('Recibir Masivo');
                                            habilita_boton('btn-recepcion-masiva');
                                        }
                                    });
                                });
                                promiseArray.push(p);

                            }
                        });
                    });
                    Swal.fire({
                        title: 'Recepcionando documentos',
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        onOpen: () => {
                            swal.showLoading();
                        }
                    })
                    Promise.all(promiseArray).then(function(obj) {
                        Swal.close();
                        toastr.success("Documentos Recepcionados", "¡Aviso!");
                        fn_grilla_por_recibir();
                        habilita_boton('btn-recepcion-masiva');
                        $('.btn-recepcion-masiva').html('Recibir Masivo');
                        
                        location.reload();
                    });
                } else {
                    habilita_boton('btn-recepcion-masiva');
                    $('.btn-recepcion-masiva').html('Recibir Masivo');
                }
            })
        } else {
            toastr.error("No hay documentos seleccionados para recibir.", "¡Aviso!");
        }
    }


    $(function() {
        
            $('#gp_buscar_tipo_doc').multiselect({
                includeSelectAllOption: true,
                maxHeight: 400,
                enableFiltering: true,
            });
            $('#gp_buscar_tipo_doc').multiselect('selectAll', true)
            $('#gp_buscar_estado').multiselect({
                includeSelectAllOption: true,
                maxHeight: 400,
                enableFiltering: true,
            });
            $('#gp_buscar_estado').multiselect('select', 3)
            $("#gp_buscar_estado").multiselect("disable");
            fn_grilla_por_recibir();

    });

</script>



@endpush