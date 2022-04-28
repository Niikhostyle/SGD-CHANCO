function add_favorito(id_documento){
    Swal.fire({
        title: 'Agregar a favoritos',
        text: "¿Quiere agregar este documento a sus favoritos?",
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
        }).then((result) => {
        if (result.value==true) {
            $(".print-error-msg").hide();
            var token = $("input[name='_token']").val();
            $.ajax({
                    url: "/favoritos/"+id_documento,
                    type:'PUT',
                    dataType: 'json',
                    data: {
                        _token:token,
                        accion:1                        
                    },
                    success: function(data) {
                        if(data.status == '200')
                        {
                            toastr.success("Favorito Actualizado","Aviso!");
                        }
                        else
                        {
                            toastr.error(data.data.comentario,"Aviso!");
                        }
                    },
                    error: function (e) {
                        data = e.responseJSON;
                        //if (typeof data.errors !== 'undefined') {
                        // printErrorMsg(data.errors);
                        console.log(e);
                            printErrorMsg(data);
                    }
                //}
            });
        }
    })

}

function del_favorito(id)
{
    Swal.fire({
        title: 'Quitar de favoritos',
        html: "¿Está seguro (a) que desea quitar este <br>" +
                "     documento de sus favoritos?<br>",   
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
        }).then((result) => {
            console.log(result);
        if (result.value==true) {
            $(".print-error-msg").hide();
            var token = $("input[name='_token']").val();
            $.ajax({
                    url: "favoritos/"+id,
                    type:'PUT',
                    dataType: 'json',
                    data: {
                        _token:token,
                        accion:2                        
                    },
                    success: function(data) {
                        if(data.status == '200')
                        {
                            toastr.success("Favorito eliminado","Aviso!");
                            autoRefresh();
                        }
                        else
                        {
                            toastr.error(data.data.comentario,"Aviso!");
                        }
                    },
                    error: function (e) {
                        data = e.responseJSON;
                        //if (typeof data.errors !== 'undefined') {
                        // printErrorMsg(data.errors);
                        console.log(e);
                            printErrorMsg(data);
                    }
                //}
            });
        }
    })
}

function cargar_datos_grilla(id_documento)
{
    $.ajax({
        url: "/documentos/"+id_documento,
        type:'GET',
        dataType: 'json',
        success: function(data) {
            if(data.status=='400') {
                toastr.error(data.data.comentario,"Aviso!");
            }
            else
            {
                if(data.status == '200')
                {
                    var json_tipo_doc = $.parseJSON(data.data.json_tipo_documento);
                    
                    if (data.data.rel_documento_buzon[0]['contestar_hasta'] != null)
                        {
                            var fechaContestarHasta = data.data.rel_documento_buzon[0]['contestar_hasta'].split(' ');
                            $("input[name='contestar_hasta']").val(fechaContestarHasta[0]);
                        }

                    var idBuzon = $("input[name='hiddIdBuzon']").val();
                    var carpeta = "";
                    var idBuzonOrigen = "";

                    $.each(data.data.rel_documento_buzon, function(key,value)
                    {
                        if (value.id_documento_buzon_padre == null) //buzon origen id_documento_buzon_padre = null
                            idBuzonOrigen = value.id_buzon;

                    });

                    $("#textBuzonorigen").text(listadoBuzones[idBuzonOrigen]);
                    
                    $("select[name='tipo_documento']").prepend("<option value='"+json_tipo_doc['id_tipo_documento']+"' selected='selected'>"+json_tipo_doc['nombre']+"</option>");
                    $("select[name='nivel_acceso']").val(data.data.id_nivel_acceso);
                    $("select[name='efectos_terceros']").val(""+data.data.efectos_terceros+"");
                    $("input[name='materia']").val(data.data.materia);
                    $("input[name='anterior']").val(data.data.anterior);
                    $("textarea[name='descripcion']").val(data.data.descripcion);                     

                    $("input[name='hiddIdOrigen']").val(json_tipo_doc['id_tipo_origen']);
                    editor_cuerpo.setData(data.data.cuerpo);
                    
                    $("input[name='hiddIdDocumento']").val(data.data.id_documento);
                    //$("input[name='hiddIdDocumentoBuzon']").val(id_documento_buzon);

                    $("#idAsignado").text(data.data.identificador);
                    
                    if (data.data.folio != null)
                        $("#idFolio").html("<b>"+data.data.folio+"</b>");

                    if (data.data.fecha != null)
                        $("#idFecha").html("<b>"+data.data.fecha+"</b>");

                    
                    if (json_tipo_doc['id_tipo_origen'] == 1) //interno
                    {
                        $('.row_cuerpo').show();
                        $('.row_arch_ppal').hide();
                        $('.row_anexo').show();
                        $('#cargar_anexo').show();
                    }
                    if (json_tipo_doc['id_tipo_origen'] == 2) //externo
                    {
                        $('.row_cuerpo').hide();
                        $('.row_arch_ppal').show();
                        $('.row_anexo').hide();
                        $('#form_archivo_principal_el').hide();
                        $('#cargar_archivo_principal_el').show();
                    }

                    //archivos    
                    var relDocumentoBuzonArchivo = data.data.rel_archivos;

                    let htmlFile = "";
                    let htmlFileAnexo = '<div class="col-md-12 group-button-alig file-container-all">';
                    let htmlFileOtros = '<div class="col-md-12 group-button-align file-container-all">';
                    let htmlFilePrincipal = '<div class="col-md-12 group-button-align file-container-all">';

                    $.each(relDocumentoBuzonArchivo, function(key,value)
                    {   
                        htmlFile = '<div class="file-container '+value.id_documento_buzon_archivo+'">'+
                                    ' <img src="https://upload.wikimedia.org/wikipedia/commons/8/87/PDF_file_icon.svg" width="75" height=75" style="height:75px;" />'+
                                        '<a href="/files/'+value.nombre_archivo_codificado+'" class="btn-descargar" target="_blank"><i class="fas fa-download fa-icon1"></i></a>';
                            
                        if (value.id_tipo_archivo == 2) //anexo
                            htmlFileAnexo += htmlFile + '</div>';       

                        if (value.id_tipo_archivo == 3) //otros
                            htmlFileOtros += htmlFile + '</div>'; 
                        
                        if (value.id_tipo_archivo == 1 && value.version == 1) //principal
                            htmlFilePrincipal += htmlFile + '</div>'; 
 
                    });

                    $('#dropzone-principal-view').html(htmlFilePrincipal + '</div>');
                    $('#dropzone-anexo-view').html(htmlFileAnexo + '</div>');
                    $('#dropzone-otros-view').html(htmlFileOtros + '</div>');

                    //destinatarios

                    var relDocumentoBuzon = data.data.rel_documento_buzon_actual;
                    
                    $.each(relDocumentoBuzon, function(i, item)
                    {                       
                        if (item.id_tipo_destino == 1)
                        {
                            $('#form_destinatario_principal_el').tagsinput('add', {"value": item.id_buzon, "text": listadoBuzones[item.id_buzon]});
                            $("textarea[id='form_comentario_el']").val(item.comentario_principal);

                            //seleccionar acciones

                            var accionesSolicitadas = $.parseJSON(item.json_acciones);
                                
                            $('#form_acciones_solicitadas_el').multiselect('deselectAll', true);
                            for (let i in accionesSolicitadas) {
                                $('#form_acciones_solicitadas_el').multiselect('select', accionesSolicitadas[i]['id_accion']);
                            }
                        }

                        if (item.id_tipo_destino == 2)
                        {
                            $('#form_otros_destinatarios_el').tagsinput('add', {"value": item.id_buzon, "text": listadoBuzones[item.id_buzon]});
                            $("textarea[id='form_comentario_otro_el']").val(item.comentario_secundario);
                        }  
                    });

               }
            }
        },
        error: function (e) {
            data = e.responseJSON;
            if (typeof data.errors !== 'undefined') {
                printErrorMsg(data.errors);
            }
        }
    });
}


function cargar_datos_bitacora(id_documento)
{
   
    //ocultar campo de busqueda por defecto 

    $('#tabla_bitacora_grilla_filter').hide();   


    if ( $.fn.DataTable.isDataTable('#tabla_bitacora_grilla') ) {
            $('#tabla_bitacora_grilla').DataTable().destroy();
    }

    var aTxtSalida = ['','Creación documento', 'Derivación a buzón ', 'Recepción en', 'Edición en', 'Cambio en archivo principal', 'Visación en', 'Firma PDF en', 'Generación de PDF en', '', 'Finalizar en', '', 'Archivar en', 'Enviado a Firma'];

    $.getJSON('/buscador/'+id_documento, function(response) {
    gridBitacora = $('#tabla_bitacora_grilla').dataTable({
        bDestroy : true,
        processing: true,
        data: response.data,
       // destroy: true, 
       // bProcessing:false,
       language: lenguaje_datatable,
        columns: [
            {data: 'tipo_destino', 
                    render: function(data, type, row) {
                        let txtTipo = '';
                        if (type === 'display' || type === 'filter' ) 
                        { 
                            if (data == 1 && row.accion == 2)
                                txtTipo = 'DDP';
                            else if (data == 2 && row.accion == 2)
                                txtTipo = 'DOO';
                            else if (row.accion == 5)
                                txtTipo = 'CAP';

                            return txtTipo;    
                        }
                        return txtTipo;
                    }     
            },
            {data: 'fecha_documento'},
            {data: 'buzon_origen',
                    render: function(data, type, row) {
                        if (type === 'display') 
                        {
                            return listadoBuzones[data];                        

                        }
                        return '';
                    }     
            },
            {data: 'accion',
                    render: function(data, type, row) {
                        if (type === 'display') 
                        {
                            if(data == null){
                                return '';
                            }
                            else
                            {                                
                                
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
            {data: 'comentario_principal',
                    render: function(data, type, row) {
                        if (type === 'display') 
                        {                            
                            if (row.tipo_destino == 1)
                            {
                                //agrega comentario de la tabla bitacora, en caso de errores, principalmente en la firma
                                let txtComentario = row.comentario;
                                let txtComentarioPpal = data;
                                
                                if(row.accion == 2)
                                    return data;
                                else if(row.accion == 13)
                                    return row.mensaje_respuesta; 
                                else if(row.accion == 5)
                                    return row.mensaje_respuesta;     
                                else 
                                    return '';    
                            }
                            else if (row.tipo_destino == 2)    
                                return row.comentario_secundario;
                            else
                                return '';                                
                            
                        }
                        return '';
                    }                   
            }
        ],
    });

    $("#idAsignado2").text(response.data[0].identificador);
    $("#textMateria").text(response.data[0].materia);
    //window.someGlobalOrWhatever = response.balance
   });

}

