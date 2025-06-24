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
var TablaBitacora = null;
var DatosBitacora = null;
$().ready(function() {
     $.fn.dataTableExt.errMode = 'ignore';
    $('#modalBitacoraSGD').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var id_documento = button.data('id') // Extract info from data-* attributes
        var modal = $(this)
        modal.find('.modal-title').text('Bitacora ID ' + id_documento)
|       modal.find('.modal-body input[name="id_documento"]').val(id_documento);
        get_tabla_bitacora(id_documento, '#tabla_bitacora_modal');
    })
    $('#modalBitacoraSGD').on('hidden.bs.modal', function (event) {
        TablaBitacora.fnClearTable();
    });
});



async function get_tabla_bitacora(id_documento,elemento_tabla='#tabla_bitacora_modal') {
        
        if ($.fn.DataTable.isDataTable(elemento_tabla)) {
            $(elemento_tabla).DataTable().destroy();
        }
        $.getJSON(route('buscador.bitacora', { 'id': id_documento }), function (response) {
            //console.log(response);
            DatosBitacora = response.info;
            $('#BitIdAsignado').text(id_documento);
            $('#BitTipo').text(DatosBitacora.tipo_documento.nombre);
            $('#BitFolio').text((DatosBitacora.folio!=null)?DatosBitacora.folio+'/'+ DatosBitacora.anio_tramitacion: 'Sin Folio');
            $('#BitMateria').text(DatosBitacora.materia);
            $('#BitEstado').text(DatosBitacora.estado_tramitacion.nombre);
            
            TablaBitacora = $(elemento_tabla).dataTable({
                bDestroy: true,
                processing: true,
                data: response.data,
                order: [
                    [1, 'desc']
                ],
                language: lenguaje_datatable,
                columns: [
                    {
                        data: 'id_accion',
                        render: function (data, type, row) {
                            let txtTipo = '<span title="'+row.accion.nombre+'" class="rounded-circle border border-secondary border-width-3 p-1 ';
                            let iconos = [
                                '<i class="fa fa-fw p-1 fa-file"></i>', // 0
                                '<i class="fa fa-fw fa-file-medical"></i>', // 1
                                '<i class="fa fa-fw p-1 fa-file-export"></i>', // 2
                                '<i class="fa fa-fw fa-file-import"></i>', // 3
                                '<i class="fa fa-fw p-1 fa-pencil-alt"></i>', // 4
                                '<i class="fa fa-fw p-1 fa-file-upload"></i>', // 5
                                '<i class="fa fa-fw fa-check-double"></i>', // 6
                                '<i class="fa fa-fw p-1 fa-file-signature"></i>', // 7
                                '<i class="fa fa-fw p-1 fa-file"></i>', // 8
                                '<i class="fa fa-fw p-1 fa-file"></i>', // 9
                                '<i class="fa fa-fw p-1 fa-file-invoice"></i>', // 10
                                '<i class="fa fa-fw p-1 fa-file-export"></i>', // 11
                                '<i class="fa fa-fw fa-box-open"></i>' // 12
                            ];
                            let tipo= '';
                            if (type === 'display') {
                                let color= '';
                               
                                if (row.id_tipo_destino == 1) {
                                    color = 'bg-blue';
                                    tipo = (data==2 || data==3)?'DDP': 'CAP';
                                } else if (row.id_tipo_destino == 2) {
                                    color = 'bg-gray';
                                    tipo = (data==2 || data==3)?'DOD': 'CAP';
                                } else {
                                    color = 'bg-lightgray';
                                }
                                if(data==12 && row.id_tipo_destino==2){
                                    tipo = "ARCHIVO";
                                }

                                if(iconos[data] == undefined){
                                    iconos[data] = '<i class="fa fa-fw p-1 fa-file"></i>';
                                }
                               
                                txtTipo = txtTipo + color + '">'+iconos[data]+'</span><span class="d-none">'+tipo+'</span>';
                                return txtTipo;
                            }
                            // Para otros tipos de renderizado, como 'filter' o 'sort', simplemente retornamos el tipo
                            if (type === 'filter' || type === 'sort') {
                                if(data==12 && row.id_tipo_destino==2){
                                    return "ARCHIVO";
                                }
                                if (row.id_tipo_destino == 1) {
                                    return (data==2 || data==3)?'DDP': 'CAP';
                                } else if (row.id_tipo_destino == 2) {
                                    return (data==2 || data==3)?'DOD': 'CAP';
                                } else {
                                    return 'CAP';
                                }
                                return tipo;
                            }
                            return data;
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
                    { data: 'usuario',
                        render: function (data, type, row) {
                            if (type === 'display' || type === 'filter') {
                                 return row.usuario.nombres +" "+ row.usuario.primer_apellido + " " + row.usuario.segundo_apellido ;
                            }
                           
                        }
                    },
                    {
                        data: 'accion',
                        render: function (data, type, row) {
                            switch(row.id_accion){
                                case 1:
                                    return "Creación de Documento";
                                    break;
                                case 2:
                                    //buscar referencia nombre buzon
                                    return "Derivación "+((row.id_tipo_destino==2)?'<b>copia</b> ':'')+"a '"+(row.buzon_destino ??'-')+"'";
                                    break;
                                case 3:
                                    return  "Recepción "+((row.id_tipo_destino==2)?'<b>copia</b> ':'')+"en '"+row.buzon+"'";  
                                    break;
                                default:
                                    return row.accion.nombre;                 
                            }


                            //return row.accion.nombre;
                        }
                        
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

                    $('input[name="filtro_derivaciones_bitacora"]').on('change', function() {
                        var types = $('input:checkbox[name="filtro_derivaciones_bitacora"]:checked').map(function() {
                            console.log(this.value);
                            return '^' + this.value + '\$';
                        }).get().join('|');
                        console.log(types);
                        TablaBitacora.fnFilter(types, 0, true, false, false, false);
                    }); 

                }
            });
        }).fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error('Error al obtener los datos de la bitácora: '+errorThrown, textStatus, errorThrown);
            //$(elemento_tabla).DataTable().destroy();
            TablaBitacora.fnClearTable()
           // $(elemento_tabla).empty();
            $('#BitIdAsignado').text('');
            $('#BitTipo').text('');
            $('#BitFolio').text('');
            $('#BitMateria').text('');
            $('#BitEstado').text('');
            //carpetas
            if(elemento_tabla != '#tabla_bitacora_modal'){
                $('#card_bitacora').hide();
                setTimeout(function() {
                    $("#collapseOne").collapse('show');
                }, 1000);
            }
            
            
        });

       
       

       
}
</script>

@endpush