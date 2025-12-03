<!-- Modal DocumentoRespuesta -->
<div class="modal fade" id="modalDocumentoRespuesta" tabindex="-1" aria-labelledby="Label" >
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Label">Seleccione Documento de Respuesta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="DocumentosRespuestaSeleccionados">
                </div>
                <table id="tabla_documento_respuesta" class="table dt-responsive " style="width:100%"></table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" aria-hidden="true" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary"   onclick="aplicar_documentos_respuesta()" id="btnAgregarDocumentoRespuesta">Agregar Documento(s)</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal DocumentoRespuesta fin-->



@push('js')
<script>
var TablaDocumentoRespuesta = null;

$().ready(function() {
    
     $.fn.dataTableExt.errMode = 'ignore';
    $('#modalDocumentoRespuesta').on('show.bs.modal', function (event) {
        //console.log("en modal",event);
      
        var button = $(event.relatedTarget) 
        var id_documento = (button.data('id')==null)?0:button.data('id');  
        var id_buzon = (button.data('buzon')==null)?0:button.data('buzon');  
        var modal = $(this);

       
        if(id_buzon==0){
            toastr.error('No se ha podido obtener documentos pendientes de este buzón. Intente nuevamente.', 'Error');
            return false;
        } 
        get_tabla_documento_respuesta(id_buzon, '#tabla_documento_respuesta');
    })

    $('#modalDocumentoRespuesta').on('hide.bs.modal', function (event) {
        $('#DocumentosRespuestaSeleccionados').html('');
        TablaDocumentoRespuesta.fnClearTable();
        //console.log("cerrando modal");
        TablaDocumentoRespuesta = null;
    });

    
    
});



async function get_tabla_documento_respuesta(id_buzon,elemento_tabla='#tabla_documento_respuesta') {
        
        if ($.fn.DataTable.isDataTable(elemento_tabla)) {
            $(elemento_tabla).DataTable().destroy();
        }
        $.getJSON(route('documentos.documentospendientes', { 'id': id_buzon }), function (response) {
            //console.log(response);
            let seleccionados =  $("form#form_crear_editar").find("input[name='documentos_respuesta[]']").map(function() {
                return $(this).val();
            }).get();

            response.forEach((element, index, array) => {
                if(seleccionados.includes(element.id_documento.toString())){
                    $("#DocumentosRespuestaSeleccionados").append(
                            "<p class='p-0 ml-3 text-sm border-bottom'>"+
                            "<b>"+element.id_documento+"</b> - "+element.documento.materia+
                             ((true)?"<a href='#' onclick='seleccionar_documentos_respuesta("+index+")' class=' ml-2'><i class='text-white bg-danger fa fa-xs fa-trash p-1'></i></a>":"") +
                            "</p>");

                    element.seleccionado = true;
                }else{
                    element.seleccionado = false;
                }      
            });

            TablaDocumentoRespuesta = $(elemento_tabla).dataTable({
                bDestroy: true,
                processing: true,
                data: response,
                order: [
                    [0, 'desc']
                ],
                language: lenguaje_datatable,
                columns: [
                    {
                        data:'id_documento',
                        title: 'ID',
                        render: function (data, type, row) {
                             if(row.documento.estado_tramitacion.id > 2)
                                return "<a target='_blank' class='underline' href='"+route('buscador.descargar_plc')+"?idDocumento="+row.id_documento+"' >"+row.id_documento+"</a>";
                            else
                                return row.id_documento;
                        }
                    },
                    {
                        data:'documento.tipo_documento.nombre',
                        title:'Tipo',
                        orderable: false,
                    },
                    {
                        data:'documento.folio',
                        title:'Folio',
                        render: function (data, type, row) {
                            if(row.documento.folio ==null){
                                return "Folio No Asignado";
                            }else{
                                return row.documento.folio +"/"+row.documento.anio_tramitacion;
                            }
                        }
                    },
                    {
                        data:'documento.materia',
                        title:'Materia'
                    },
                    {
                        data:'estado_documento_buzon.nombre',
                        title:'Estado',
                        orderable: false,
                    },
                    {
                        data:'documento.estado_tramitacion.nombre',
                        title:'Estado Documento',
                        orderable: false,
                    },
                    {
                        data: 'accion',
                        render: function (data, type, row,meta) {
                            if(row.seleccionado==undefined || row.seleccionado==false ){
                                return "<button class='btn btn-sm btn-primary accion-documento-pendiente' onclick='seleccionar_documentos_respuesta("+meta.row+")'><i class='fa fa-plus'></i> Agregar</button>";
                            }else{
                                return "<button class='btn btn-sm btn-danger accion-documento-pendiente' onclick='seleccionar_documentos_respuesta("+meta.row+")'><i class='fa fa-trash'></i> Eliminar</button>";
                            }
                            //return row.accion.nombre;
                        }  
                    }
                ],
               rowCallback: function (row, data) {
                    
                },
                initComplete: function (settings, json) {
                     this.api().columns([1,4,5]).every( function (obj) {
                        var column = this;
                        //console.log(this);
                        var select = $('<select data-index="'+obj+'" class="form-control form-control-xs"><option value=""></option></select>')
                            .appendTo( $(column.header()) )
                            .on( 'change', function (e) {
                                TablaDocumentoRespuesta.DataTable()
                                    .column($(this).data('index'))
                                    .search($(e.target).val())
                                    .draw();
                            } );
        
                        column.data().unique().sort().each( function ( d, j ,o) {
                            select.append( '<option value="'+d+'">'+d+'</option>' )
                        } );
                    } );
                   
                    //filtrar por defecto los pendientes
                   this.api().column(4)
                             .search("Pendiente")
                             .draw();
                    //console.log('Tabla cargada');
                }
            });
        }).fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error('Error al obtener los datos de documentos pendientes: '+errorThrown, textStatus, errorThrown);
            //$(elemento_tabla).DataTable().destroy();
            TablaDocumentoRespuesta.fnClearTable()
        });


       
}

function seleccionar_documentos_respuesta(rowindex){
    $("#DocumentosRespuestaSeleccionados").html("");
    let data = TablaDocumentoRespuesta.DataTable().row(rowindex).data();
    //console.log(data);

    if(data.seleccionado==undefined){
        data.seleccionado = true;
    }else{
        //comprobar si el doc seleccionado es del buzon de donde se seleccionó
        if(data.seleccionado){
            Swal.fire({
                title: "Confirmación",
                html: "Desea eliminar el documento "+data.id_documento+" - "+data.documento?.materia,
                icon: "info",
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: "Confirmar",
                cancelButtonText: `Cerrar`
            }).then((result) => {
                if(result.value){
                    data.seleccionado = false;
                    TablaDocumentoRespuesta.DataTable().row(rowindex).data(data).draw();
                     $("#DocumentosRespuestaSeleccionados").html('');
                    TablaDocumentoRespuesta.DataTable().data().each(function(value, index) {
                        if(value.seleccionado){
                           
                             $("#DocumentosRespuestaSeleccionados").append(
                                "<p class='p-0 ml-3 text-sm border-bottom'>"+
                                "<b>"+value.id_documento+"</b> - "+value.documento.materia+
                                ((true)?"<a href='#' onclick='seleccionar_documentos_respuesta("+index+")' class=' ml-2'><i class='text-white bg-danger fa fa-xs fa-trash p-1'></i></a>":"") +
                                "</p>");
                        }
                    });
                }     
            });
        }else{
            data.seleccionado = !data.seleccionado;
        }
    }

    TablaDocumentoRespuesta.DataTable().row(rowindex).data(data).draw();
    $("#DocumentosRespuestaSeleccionados").html('');
    TablaDocumentoRespuesta.DataTable().data().each(function(value, index) {
        if(value.seleccionado){
            
             $("#DocumentosRespuestaSeleccionados").append(
                            "<p class='p-0 ml-3 text-sm border-bottom'>"+
                            "<b>"+value.id_documento+"</b> - "+value.documento.materia+
                             ((true)?"<a href='#' onclick='seleccionar_documentos_respuesta("+index+")' class=' ml-2'><i class='text-white bg-danger fa fa-xs fa-trash p-1'></i></a>":"") +
                            "</p>");
        }
    });
}

function aplicar_documentos_respuesta(event){
        
    $("form#form_crear_editar").find("input[name='documentos_respuesta[]']").remove();

        TablaDocumentoRespuesta.DataTable().data().each(function(value, index) {
            if(value.seleccionado){
                $("form#form_crear_editar").append("<input type='hidden'  name='documentos_respuesta[]' value='"+value.id_documento+"' />");
                
            }
        });
        var contador = $("form#form_crear_editar").find("input[name='documentos_respuesta[]']").length;
        $('#contador_respuesta_a').text(contador);
        $('#modalDocumentoRespuesta').modal('hide');

}

function ver_documentos_respuesta_a(){
    
    let seleccionados =  $("form#form_crear_editar").find("input[name='documentos_respuesta[]']").map(function() {
        return $(this).val();
    }).get();
    let params = new URLSearchParams();
    seleccionados.forEach(e => {
        params.append('seleccionados[]', e);
    });
    if(seleccionados.length==0){
        params.append('seleccionados[]', 0);
    }

    let id_buzon = $("input[name='hiddIdBuzon']").val();

    $.getJSON(route('documentos.informacionresumen')+'?'+params.toString(), function (response) {
        console.log(response);
        if(response.length==0){
            toastr.info('No hay documentos seleccionados', 'Información');
            return;
        }
        let html = "<ul class='text-left'>";
        response.forEach(element => {  
            if(element.estado_tramitacion.id > 2)
                html+="<li><a target='_blank' class='underline' href='"+route('buscador.descargar_plc')+"?idDocumento="+element.id_documento+"' >"+element.id_documento+" - "+element.materia+"</a></li>";
            else
                html+="<li>"+element.id_documento+" - "+element.materia+"</li>";
        });
        html+="</ul>";

        Swal.fire({
            title: "Documentos de Respuesta",
            html: html,
            icon: "info",
            //showDenyButton: true,
            showCancelButton: true,
            showConfirmButton: false,
            confirmButtonText: "Editar",
            cancelButtonText: `Cerrar`
        }).then((result) => {
            if(result.value){
                $('#btn_respuesta_a').trigger('click');
            }     
        });

    }).fail(function (jqXHR, textStatus, errorThrown) {
        toastr.error('Error al obtener los datos de documentos pendientes: '+errorThrown, textStatus, errorThrown);
    });

}

</script>

@endpush