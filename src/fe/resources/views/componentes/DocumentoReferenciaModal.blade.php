<!-- Modal DocumentoRespuesta -->
<div class="modal fade" id="modalDocumentoReferencia" tabindex="-1" aria-labelledby="Label" >
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="Label">Seleccione Documento de Referencia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" aria-label="Close">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="panel-archivos" id="DocumentosReferenciaSeleccionados">
                </div>
                <div class="d-flex mb-3 justify-content-end align-items-end">
                    <div class="d-inline-block mr-4">
                        <label>AÑO</label>
                        <div>
                            <select id="DocumentoReferencia_anio" class="form-control "></select>
                        </div>
                    </div>
                    <div class="d-inline-block mr-4">
                        <label>TIPO DOCUMENTO</label>
                        <div><select multiple="multiple" id="tipo_documento_referencia" name="tipo_documento_referencia" class="form-control" style="width:auto; display:inline-block; margin-left:10px;">
                                @foreach($listado_tiposdoc as $list)
                                <option value="{{$list['id_tipo_documento']}}">{{$list['nombre']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                    <button type="button" class="btn btn-secondary" onclick="TablaDocumentoReferencia.DataTable().ajax.reload()">Filtrar</button>
                    </div>
                </div>
                <table id="tabla_documento_referencia" class="table dt-responsive " style="width:100%"></table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" aria-hidden="true" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary"   onclick="aplicar_documentos_referencia()" id="btnAgregarDocumentoReferencia">Agregar Documento(s)</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal DocumentoRespuesta fin-->



@push('js')
<script>
var TablaDocumentoReferencia = null;
var DatosReferenciaSeleccionados  = [];

$().ready(function() {
    
     $.fn.dataTableExt.errMode = 'ignore';
    $('#modalDocumentoReferencia').on('show.bs.modal', function (event) {
        console.log("en modal",event);
      
        var button = $(event.relatedTarget) 
        var modal = $(this);
        //cargar los años de select_anio
        $("select[name=select_anio] option").each(function() {
            $('#DocumentoReferencia_anio').append($('<option>', {
                value: $(this).val(),
                text: $(this).text(),
                selected: $(this).attr('selected')
            }));
        });

        $('#tipo_documento_referencia').multiselect({
            includeSelectAllOption: true,
            maxHeight: 400,
            enableFiltering: true,
            multiple: true,
        });
        $('#tipo_documento_referencia').multiselect('selectAll', true);


        //leer los documentos ya seleccionados
        DatosReferenciaSeleccionados = $("form#form_crear_editar").find("input[name='documentos_referencias[]']").map(function() {
                return $(this).val();
            }).get();
        console.log("Documentos de Referencia Seleccionados",DatosReferenciaSeleccionados);

        get_tabla_documento_referencia( '#tabla_documento_referencia');
    })

    $('#modalDocumentoReferencia').on('hide.bs.modal', function (event) {
        $("DocumentosReferenciaSeleccionados").html('');
        TablaDocumentoReferencia.fnClearTable();
        console.log("cerrando modal");
        TablaDocumentoReferencia = null;
    });

    
    
});



async function get_tabla_documento_referencia(elemento_tabla='#tabla_documento_referencia') {
        
        if ($.fn.DataTable.isDataTable(elemento_tabla)) {
            $(elemento_tabla).DataTable().destroy();
        }
        {{-- $.getJSON(route('documentos.documentospendientes', { 'id': id_buzon }), function (response) {
            //console.log(response);
            let seleccionados =  $("form#form_crear_editar").find("input[name='documentos_referencias[]']").map(function() {
                return $(this).val();
            }).get();

            response.forEach((element, index, array) => {
                if(seleccionados.includes(element.id_documento.toString())){
                        $("DocumentosReferenciaSeleccionados").append(
                            "<p class='p-0 ml-3'><a href='#' onclick='seleccionar_documentos_referencia("+index+")'>&times;</a> "+element.id_documento+" - "+element.documento.materia+"</p>");
                    element.seleccionado = true;
                }else{
                    element.seleccionado = false;
                }      
            }); --}}

            TablaDocumentoReferencia = $(elemento_tabla).dataTable({
                bDestroy: true,
                processing: true,
                serverSide: false,
                responsive: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                ajax: {
                    url:route('documentos.documentosreferencia'),
                    method: 'GET',
                    data: function (d) {
                        d.anio = $("#DocumentoReferencia_anio").val();
                        d.tipo_documento = $("#tipo_documento_referencia").val();
                    },
                },
                //data: response,

                order: [
                    [1, 'desc']
                ],
                language: lenguaje_datatable,
                columns: [
                    {
                        data:'id_documento',
                        title: 'ID',
                        render: function (data, type, row) {
                             if(row.estado_tramitacion.id > 2)
                                return "<a target='_blank' class='underline' href='"+route('buscador.descargar_plc')+"?idDocumento="+row.id_documento+"' >"+row.id_documento+"</a>";
                            else
                                return row.id_documento;
                        }
                    },
                    {
                        data:'tipo_documento.nombre',
                        title:'Tipo'
                    },
                    {
                        data:'folio',
                        title:'Folio',
                        render: function (data, type, row) {
                            if(row.folio ==null){
                                return "Folio No Asignado";
                            }else{
                                return row.folio +"/"+row.anio_tramitacion;
                            }
                        }
                    },
                    {
                        data:'materia',
                        title:'Materia'
                    },
                    {
                        data:'estado_tramitacion.nombre',
                        title:'Estado'
                    },
                    {
                        data: 'accion',
                        render: function (data, type, row,meta) {
                            if(row.seleccionado==undefined || row.seleccionado==false){
                                return "<button class='btn btn-sm btn-primary accion-documento-pendiente' onclick='seleccionar_documentos_referencia("+meta.row+")'><i class='fa fa-plus'></i> Agregar</button>";
                            }else{
                                return "<button class='btn btn-sm btn-danger accion-documento-pendiente' onclick='seleccionar_documentos_referencia("+meta.row+")'><i class='fa fa-trash'></i> Eliminar</button>";
                            }
                            //return row.accion.nombre;
                        }  
                    }
                ],
                rowCallback: function (row, data) {
                    
                },
                initComplete: function (settings, json) {
                    //leer si el docto esta cargado en la lista de referencias
                    if(DatosReferenciaSeleccionados.length>0){
                        TablaDocumentoReferencia.DataTable().data().each(function(value, index) {
                            if(DatosReferenciaSeleccionados.includes(value.id_documento.toString())){
                                value.seleccionado = true;
                                TablaDocumentoReferencia.DataTable().row(index).data(value).draw();
                                $("DocumentosReferenciaSeleccionados").append(
                                    "<p class='p-0 ml-3'><a href='#' onclick='seleccionar_documentos_referencia("+index+")'>&times;</a> "+value.id_documento+" - "+value.materia+"</p>");
                            }
                        });
                    }
                    console.log('Tabla cargada');
                }
            });

        {{-- }).fail(function (jqXHR, textStatus, errorThrown) {
            toastr.error('Error al obtener datos de documentos de SGD: '+errorThrown, textStatus, errorThrown);
            //$(elemento_tabla).DataTable().destroy();
            TablaDocumentoReferencia.fnClearTable()
        });      --}}
}

function seleccionar_documentos_referencia(rowindex){
    $("DocumentosReferenciaSeleccionados").html('');
    let data = TablaDocumentoReferencia.DataTable().row(rowindex).data();
    //console.log(data);
    if(data.seleccionado==undefined){
        data.seleccionado = true;
    }else{
        data.seleccionado = !data.seleccionado;
    }

    TablaDocumentoReferencia.DataTable().row(rowindex).data(data).draw();

    TablaDocumentoReferencia.DataTable().data().each(function(value, index) {
        if(value.seleccionado){
            $("DocumentosReferenciaSeleccionados").append(
                "<p class='p-0 ml-3'><a href='#' onclick='seleccionar_documentos_referencia("+index+")'>&times;</a> "+value.id_documento+" - "+value.materia+"</p>");
        }
    });
}

function aplicar_documentos_referencia(event){
        $("form#form_crear_editar").find("input[name='documentos_referencias[]']").remove();
        $("#contenedor_documentos_referencia").html('');

        TablaDocumentoReferencia.DataTable().data().each(function(value, index) {
            if(value.seleccionado){
                $("form#form_crear_editar").append("<input type='hidden' name='documentos_referencias[]' value='"+value.id_documento+"' />");

                let el =$("<div></div>");
                el.addClass("file-container");
                el.append($("<img>",{src:"/img/pdf.png", width:83, height:94}));
                el.append($("<button>",{
                    type:"button",
                    class:"btn text-nowrap btn-min-w  btn-sm btn-arch btn-default btn-outline-secondary rounded-circle",
                    title:"Descargar",
                    style:"mdargin-left: 3px;",
                    click:function(e){
                        e.stopPropagation();
                        console.log( "descargar referencia",$(this));
                        window.open(route('buscador.descargar_plc')+"?idDocumento="+value.id_documento, '_blank');
                    }
                }).html($("<i>",{class:"fa fa-download"})));


                if(!$('#dropzone-anexo').prop("disabled")){
                    el.append($("<button>",{
                        type:"button",
                        class:"btn text-nowrap btn-min-w  btn-sm btn-arch btn-default btn-outline-secondary rounded-circle",
                        title:"Eliminar referencia",
                        style:"margin-left: -27px;",
                        click:function(e){
                            e.stopPropagation();
                            console.log( "eliminar referencia",$(this));
                            $(this).parent().remove();
                            $("form#form_crear_editar").find('input[name="documentos_referencias[]"][value='+value.id_documento+']').remove();
                        }
                    }).html($("<i>",{class:"fas fa-trash"})));
                }
                //formato : [tipo documento] [folio]/[año]

                el.append($("<p>",{
                    style:"width: 90px!important;word-break: break-all;font-size: 12px;line-height: 1;margin-top: 15px;margin-bottom: 5px;"
                }).text(value.nombre_corto+" - "+value.folio +"/"+value.anio_tramitacion));   

                $("#contenedor_documentos_referencia").append(el);
               
            }
        });
        
        
        $('#modalDocumentoReferencia').modal('hide');

}

</script>

@endpush