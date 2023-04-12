
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
    owl.trigger('destroy.owl.carousel'); 
    owl.find('.owl-stage-outer').children().unwrap();
    owl.removeClass("owl-center owl-loaded owl-text-select-on");

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
                        id_buzon = value.id_buzon;
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
                    $("#idFolio").html("No Asignado");
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
                        $('.row_anexo').show();
                        $('#form_archivo_principal_el').hide();
                        $('#cargar_archivo_principal_el').show();
                    }

                    //archivos    
                    var relDocumentoBuzonArchivo = data.data.rel_archivos;

                    let htmlFile = "";
                    let htmlFileAnexo = '<div class="col-md-12 group-button-alig file-container-all">';
                    let htmlFileOtros = '<div class="col-md-12 group-button-align file-container-all">';
                    let htmlFilePrincipal = '<div class="col-md-12 group-button-align file-container-all">';
                    let htmlFilePrincipal_va = '<div class="col-md-12 file-container-all">';
                    
                    aFilesPrincipal = [];
                    aFilesDelete = [];                  

                    $.each(relDocumentoBuzonArchivo, function(key,value)
                    {   
                        htmlFile = '<div class="file-container '+value.id_documento_buzon_archivo+'">'+
                                       ' <img src="/img/pdf_file.jpg" width="83" height=94" style="" />'+
                                        //   '<a href="/files/'+value.nombre_archivo_codificado+'" class="btn-descargar" target="_blank"><i class="fas fa-download fa-icon1"></i></a>';
                                        '<button onClick="ver_archivo(\''+value.nombre_archivo_codificado+'\')" type="button" class="btn btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="View Details" style="margin-left: 3px;"><i class="fa fa-download"></i></button>'+
                                        '<p style="width: 90px!important;word-break: break-all;font-size: 12px;line-height: 1;margin-top: 15px;margin-bottom: 5px;">'+value.nombre_archivo_original+'</p>';
                        htmlFile_va = '<div class="file-container '+value.id_documento_buzon_archivo+'">'+
                            '  <img src="/img/pdf_file.jpg" width="83" height=94" style="" />'+
                                //'<a href="/files/'+value.nombre_archivo_codificado+'" class="btn-descargar" target="_blank"><i class="fas fa-download fa-icon1"></i></a>';
                                '<button onClick="ver_archivo(\''+value.nombre_archivo_codificado+'\')" type="button" class="btn btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="View Details" style="margin-left: 3px;"><i class="fa fa-download"></i></button>';

                        if (value.id_tipo_archivo == 2) //anexo
                            htmlFileAnexo += htmlFile + '</div>';       

                        if (value.id_tipo_archivo == 3) //otros
                            htmlFileOtros += htmlFile + '</div>'; 
                        
                        if (value.id_tipo_archivo == 1 && value.version == 1) //principal
                            htmlFilePrincipal += htmlFile + '</div>'; 

                        //versiones anteriores 
                        
                        if (value.id_tipo_archivo == 1 && value.version != 1) 
                            htmlFilePrincipal_va += htmlFile_va + '</div>'; 
 
                    });

                    $('#dropzone-principal-view').html(htmlFilePrincipal + '</div>');
                        $('#dropzone-anexo-view').html(htmlFileAnexo + '</div>');
                        $('#dropzone-otros-view').html(htmlFileOtros + '</div>');
                        $('#versiones_anteriores').html(htmlFilePrincipal_va + '</div>');
                     


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

                    /* responder a */                   

                    var sDivActualPrev = "";
                    var sDivActualNext = "";
                    var sDivIzq = "";
                    var jsonRespuesta = $.parseJSON(data.data.json_respuesta_a); 
                    var jsonDocResponder = data.data.rel_responder;
                    $('#form_respuesta_a').empty();
                    for (let j in jsonRespuesta) 
                    {                           
                        //completa carrusel lado izq
                        sDivIzq += ' <div class="item"><div class="item_display"  onclick="visualizar_documento_alerta('+jsonRespuesta[j]['identificador']+','+id_buzon+','+idBuzonOrigen+',\''+jsonRespuesta[j]['materia']+'\')" style="cursor:pointer;">'+jsonRespuesta[j]['identificador']+'<p>'+moment(jsonRespuesta[j]['created_at']).format('DD-MM-YYYY')+'</p></div></div>';                               
                        $('#form_respuesta_a').append("<option selected>"+jsonRespuesta[j]['identificador']+"-"+jsonRespuesta[j]['materia']+"</option>");

                    }

                    //completar carrusel lado der
                    var sDivDer = "";
                    for (let d in jsonDocResponder){
                        sDivDer += ' <div class="item"><div class="item_display"  onclick="visualizar_documento_alerta('+jsonDocResponder[d]['identificador']+','+id_buzon+','+idBuzonOrigen+',\''+jsonDocResponder[d]['materia']+'\')"  style="cursor:pointer;">'+jsonDocResponder[d]['identificador']+'<p>'+moment(jsonDocResponder[d]['created_at']).format('DD-MM-YYYY')+'</p></div></div>';
                    }
                     
                    sDivActual = '<div class="item"><div class="item_display item-doc"  onclick="visualizar_documento_alerta('+data.data.identificador+','+id_buzon+','+idBuzonOrigen+',\''+data.data.materia+'\')" style="cursor:pointer;">'+data.data.identificador+'<p>'+moment(data.data.created_at).format('DD-MM-YYYY')+'</p></div></div>';
                    
                    if (sDivDer != '')
                        sDivActualPrev = '<div class="item"><div class="item_prev"><i class="fas fa-reply-all fa-2x"></i></div></div>';

                    if (sDivIzq != '')
                        sDivActualNext = '<div class="item"><div class="item_next"><i class="fas fa-reply-all fa-2x"></i></div></div>';


                    if (sDivIzq != '' || sDivDer != '')
                    {
                        owl.trigger('destroy.owl.carousel'); 
                        owl.find('.owl-stage-outer').children().unwrap();
                        owl.removeClass("owl-center owl-loaded owl-text-select-on");

                        var content = sDivIzq + sDivActualNext + sDivActual + sDivActualPrev + sDivDer;
                        owl.html(content);

                        //reinitialize the carousel (call here your method in which you've set specific carousel properties)
                        owl.owlCarousel({
                            items:8,
                            margin: 10,
                            dots: true,
                            nav: true,
                            navText: ["<div class='nav-button owl-prev'>‹</div>", "<div class='nav-button owl-next'>›</div>"],
                            
                        }).trigger('refresh.owl.carousel');
                    }             


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

function ver_archivo(file)
{
    window.open('/files/'+file);
    return false;
}

function cargar_datos_bitacora(id_documento)
{
   
    //ocultar campo de busqueda por defecto 

    $('#tabla_bitacora_grilla_filter').hide();   

    if ( $.fn.DataTable.isDataTable('#tabla_bitacora_grilla') ) {
            $('#tabla_bitacora_grilla').DataTable().destroy();
    }

    var aTxtSalida = ['','Creación documento', 'Derivación a buzón ', 'Recepción en', 'Edición en', 'Cambio en archivo principal', 'Visación en', 'Firma PDF en', 'Generación de PDF en', '', 'Finalizado en', '', 'Archivado en', 'Enviado a Firma', 'Desarchivado en'];

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
                                txtTipo = 'DOD';
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
            {data: 'nombre_usuario'},
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
                                else if(row.accion == 12)
                                    return row.comentario;   
                                else if(row.accion == 14)
                                    return row.comentario;                                             
                                else 
                                    return '';    
                            }
                            else if (row.tipo_destino == 2){ 
                                    if(row.accion == 12)   
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

    $("#idAsignado2").text(response.data[0].identificador);
    $("#textMateria").text(response.data[0].materia);
    //window.someGlobalOrWhatever = response.balance
   });

}

function visualizar_documento_alerta(id_documento, id_documento_buzon,id_documento_buzon_padre,materia){
    Swal.fire({
        title: 'Advertencia', 
        html: "Se visualizará Documento: <br><strong>"+id_documento+"-"+materia+"</strong><br>¿Desea continuar?",                      
        showCancelButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.value==true) {
                visualizar_documento(id_documento, id_documento_buzon,id_documento_buzon_padre);
            }
        });  
}


//Archivar
