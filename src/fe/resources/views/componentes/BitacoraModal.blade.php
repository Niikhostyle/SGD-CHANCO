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
                    <input class="form-check-input" type="checkbox" value="DDP" name="filtro_derivaciones_bitacora" id="accion_ddp">
                    <label class="form-check-label" for="defaultCheck1">
                        Derivaciones destinatarios principales (DDP)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="DOD" name="filtro_derivaciones_bitacora" id="accion_dop">
                    <label class="form-check-label" for="defaultCheck1">
                        Derivaciones otros destinatarios (DOD)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="CAP" name="filtro_derivaciones_bitacora" id="accion_cap">
                    <label class="form-check-label" for="defaultCheck1">
                        Cambios Archivos Principal (CAP)
                    </label>
                </div>


                <table id="tabla_bitacora_modal" class="table dt-responsive " style="width:100%">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Buzón</th>
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
  
        $.getJSON(route('buscador.bitacora', { 'id': id_documento }), function (response) {
            $("#textMateriaModal").html();
            console.log(response);
            TablaModalBitacora = $('#tabla_bitacora_modal').dataTable({
                bDestroy: true,
                processing: true,
                data: response,
                order: [
                    [1, 'desc']
                ],
                language: lenguaje_datatable,
                columns: [
                    {
                        data: 'id_tipo_destino',
                        render: function (data, type, row) {
                            let txtTipo = '';
                            if (type === 'display' || type === 'filter') {
                                if (data == 1 && (row.id_accion == 2 || row.id_accion == 3))
                                    txtTipo = 'DDP';
                                else if (data == 2 && (row.id_accion == 2 || row.id_accion == 3))
                                    txtTipo = 'DOD';
                                else
                                    txtTipo = 'CAP';

                                return txtTipo;
                            }
                            return txtTipo;
                        }
                    },
                    {   
                        data: 'fecha',
                        render:function (data, type, row) {
                            return "<span class='d-none'>"+row.id_documento_buzon_bitacora+"</span>"+moment(data).format('DD-MM-YYYY HH:mm:ss');
                        }
                     },
                    {
                        data: 'buzon',
                    },
                    { data: 'nombre_usuario' },
                    {
                        data: 'accion',
                        
                    },
                    {
                        data: 'mensaje',
                       
                    }
                ],
               rowCallback: function (row, data) {
                    if (data.id_tipo_destino == 1) {
                        
                        //$(row).addClass('bg-lightblue');
                        //$('td:eq(4)', row).html('<b>A</b>');
                    }
                },
                initComplete: function (settings, json) {
                    
                    //marcar principales y cambios
                    //$('input:checkbox[name="filtro_derivaciones_bitacora"][value=DDP]').prop("checked",true).trigger("change");
                    //$('input:checkbox[name="filtro_derivaciones_bitacora"][value=CAP]').prop("checked",true).trigger("change"); 
                }
            });
        });

       
        $('input[name="filtro_derivaciones_bitacora"]').on('change', function() {
            var types = $('input:checkbox[name="filtro_derivaciones_bitacora"]:checked').map(function() {
                return '^' + this.value + '\$';
            }).get().join('|');
            TablaModalBitacora.fnFilter(types, 0, true, false, false, false);
        });


    })
});
</script>

@endpush