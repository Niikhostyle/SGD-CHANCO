<!-- Modal Bitácora -->
<div class="modal fade" id="modalBitacoraSGD" tabindex="-1" aria-labelledby="modalBitacoraSGDLabel" >
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalBitacoraSGDLabel">Agregar Referencia de SGD</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-check" style="padding-right: 5px;">
                    <input class="form-check-input" type="checkbox" value="DDP" name="filtro_derivaciones_botacora" id="accion_ddp">
                    <label class="form-check-label" for="defaultCheck1">
                        Derivaciones destinatarios principales (DDP)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="DOD" name="filtro_derivaciones_botacora" id="accion_dop">
                    <label class="form-check-label" for="defaultCheck1">
                        Derivaciones otros destinatarios (DOD)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="CAP" name="filtro_derivaciones_botacora" id="accion_cap">
                    <label class="form-check-label" for="defaultCheck1">
                        Cambios Archivos Principal (CAP)
                    </label>
                </div>


                <table id="tabla_bitacora_modal" class="table dt-responsive " style="width:100%">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Buzón Origen</th>
                            <th>Usuario </th>
                            <th>Acción </th>
                            <th>Mensaje</th>
                        </tr>

                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal Bitacora fin-->
@push('js')
<script>
var TablaModalBitacora;
$().ready(function() {
    $('#modalBitacoraSGD').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var id_documento = button.data('id') // Extract info from data-* attributes
        var modal = $(this)
        modal.find('.modal-title').text('Bitacora ID ' + id_documento)

        if ($.fn.DataTable.isDataTable('#tabla_bitacora_modal')) {
            $('#tabla_bitacora_modal').DataTable().destroy();
        }
        var aTxtSalida = ['', 'Creación documento', 'Derivación a buzón ', 'Recepción en', 'Edición en', 'Cambio en archivo principal', 'Visación en', 'Firma PDF en', 'Generación de PDF en', '', 'Finalizado en', '', 'Archivado en', 'Enviado a Firma', 'Desarchivado en'];

        $.getJSON(route('buscador.show', { 'id': id_documento }), function (response) {
            $("#textMateriaModal").html()
            TablaModalBitacora = $('#tabla_bitacora_modal').dataTable({
                bDestroy: true,
                processing: true,
                data: response.data,
                order: [
                    [1, 'desc']
                ],
                language: lenguaje_datatable,
                columns: [
                    {
                        data: 'tipo_destino',
                        render: function (data, type, row) {
                            let txtTipo = '';
                            if (type === 'display' || type === 'filter') {
                                if (data == 1 && row.accion == 2)
                                    txtTipo = 'DDP';
                                else if (data == 2 && row.accion == 2)
                                    txtTipo = 'DOD';
                                else if (row.accion == 5)
                                    txtTipo = 'CAP';

                                return txtTipo;
                            }
                            return txtTipo;
                        }
                    },
                    { data: 'fecha_documento',
                    
                        render:function (data, type, row) {
                            let fecha = new Date(data);
                            return fecha.toLocaleDateString() +" " +fecha.toLocaleTimeString("en-US", { hour12: false });
                        }
                     },
                    {
                        data: 'buzon_origen',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                return listadoBuzones[data];
                            }
                            return '';
                        }
                    },
                    { data: 'nombre_usuario' },
                    {
                        data: 'accion',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                if (data == null) {
                                    return '';
                                }
                                else {

                                    return aTxtSalida[data] + ' "' + row.buzon_destino + '"';
                                    let txtSalida = 'Derivación a buzón ';
                                    if (data == 2)
                                        return txtSalida + '"' + row.buzon_destino + '"';
                                    else
                                        return '';
                                }
                            }
                            return '';
                        }
                    },
                    {
                        data: 'comentario_principal',
                        render: function (data, type, row) {
                            if (type === 'display') {
                                if (row.tipo_destino == 1) {
                                    //agrega comentario de la tabla bitacora, en caso de errores, principalmente en la firma
                                    let txtComentario = row.comentario;
                                    let txtComentarioPpal = data;

                                    if (row.accion == 2)
                                        return data;
                                    else if (row.accion == 13)
                                        return row.mensaje_respuesta;
                                    else if (row.accion == 5)
                                        return row.mensaje_respuesta;
                                    else if (row.accion == 12)
                                        return row.comentario;
                                    else if (row.accion == 14)
                                        return row.comentario;
                                    else
                                        return '';
                                }
                                else if (row.tipo_destino == 2) {
                                    if (row.accion == 12)
                                        return row.comentario;
                                    else
                                        return row.comentario_secundario;
                                }

                                else
                                    return '';

                            }
                            return '';
                        }
                    }
                ],
            });
        });

        $('input[name="filtro_derivaciones_botacora"]').on('change', function() {
            var types = $('input:checkbox[name="filtro_derivaciones_botacora"]:checked').map(function() {
                return '^' + this.value + '\$';
            }).get().join('|');

            TablaModalBitacora.fnFilter(types, 0, true, false, false, false);
        });
    })
});
</script>

@endpush