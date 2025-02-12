
<div class="tab-pane fade show active" id="nav-por-recibir" role="tabpanel" aria-labelledby="nav-por-recibir-tab">
    <div class="pb-4 pt-2">
        <button onclick="recepcion_masiva()" class="btn text-nowrap btn-min-w  btn-sm btn-primary btn-recepcion-masiva">Recibir Masivo</button>
    </div>
    <table id="grilla_por_recibir" class="table dt-responsive " style="width:100%">
        <thead>
            <tr class="grilla_header">
                <th data-priority="1">Sel</th>
                <th data-priority="1">ID Doc.</th>
                <th data-priority="1">F. Entrada</th>
                <th data-priority="1">Materia</th>

                <th data-priority="2">TD</th>
                <th data-priority="2">TE</th>
                <th data-priority="2">Origen</th>
                <th data-priority="2">Contestar Hasta</th>
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
        if ($.fn.DataTable.isDataTable('#grilla_por_recibir')) {
            $('#grilla_por_recibir').DataTable().destroy();
        }
        $('#grilla_por_recibir tbody').empty();

        grilla_por_recibir = $('#grilla_por_recibir').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 25,
            ajax:{
                url: route('buzones.listar',{'id': {{$id_buzon}} }) , //'/buzonesListar',
                data: function(d,obj){
                    //d.estados = $('#gr_buscar_estado').val().join("|");
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
                    data: 'id_documento',
                    name: 'documento.id_documento',
                },
                {
                    data: 'id_documento',
                    name: 'documento.id_documento',

                    render: function(data, type, row) {
                        return "<a href='javascript:visualizar_documento_por_recibir(" + row.id_documento + "," + row.id_documento_buzon + "," + row.id_documento_buzon_padre + "," + row.id_tipo_destino + ")'>" + data + "</a>";
                    }

                },
                {
                    data: 'fecha_envio',
                    render: function(data) {
                        if (data == null)
                            return '';
                        else
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
                        return data.length > 60 ? data.substr(0, 60) + '…' : data;
                    },
                },

                {
                    data: 'tipo_documento',
                    name: 'tipo_documento.nombre'
                },
                {
                    data: 'tipo_envio',
                    name: 'tipo_destino.nombre'
                },
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
                    render: function(data) {
                        if (data == null)
                            return '';
                        else
                            return moment(data).format('DD-MM-YYYY');
                    }
                }

            ],
            rowCallback: function(row, data, index) {}
        });
        $('#grilla_por_recibir').on('error.dt', function(e, settings, techNote, message) {
            console.log('Error DataTables: ', message);
        });
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
        fn_grilla_por_recibir();

    });

</script>



@endpush