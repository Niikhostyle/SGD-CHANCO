
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
        //url: "/documentos/"+id_documento,
        //buscador.show
        url:route('buscador.show',{'id':id_documento}),
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
                        var extension = value.nombre_archivo_original.split('.').pop();
                        var imagen = "";
                        imagen = "pdf.png";
                            switch (extension) {
                                case "xls":
                                case "xlsx":
                                    imagen = "excel.png";
                                    break;
                                case "doc":
                                case "docx":
                                    imagen = "word.png";
                                    break;
                                case "rar":
                                    imagen = "rar.png";
                                    break;
                                case "zip":
                                    imagen = "zip.png";
                                    break;
                                // default:
                                    
                                //     break;
                            }
                        
                        htmlFile = '<div class="file-container '+value.id_documento_buzon_archivo+'">'+
                                       ' <img src="/img/'+imagen+'" width="83" height=94" style="" />'+
                                        //   '<a href="/files/'+value.nombre_archivo_codificado+'" class="btn-descargar" target="_blank"><i class="fas fa-download fa-icon1"></i></a>';
                                        '<button onClick="ver_archivo(\''+value.nombre_archivo_codificado+'\')" type="button" class="btn btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="View Details" style="margin-left: 3px;"><i class="fa fa-download"></i></button>'+
                                        '<p style="width: 90px!important;word-break: break-all;font-size: 12px;line-height: 1;margin-top: 15px;margin-bottom: 5px;">'+value.nombre_archivo_original+'</p>';
                        htmlFile_va = '<div class="file-container '+value.id_documento_buzon_archivo+'">'+
                            '  <img src="/img/'+imagen+'" width="83" height=94" style="" />'+
                                //'<a href="/files/'+value.nombre_archivo_codificado+'" class="btn-descargar" target="_blank"><i class="fas fa-download fa-icon1"></i></a>';
                                '<button onClick="ver_archivo(\''+value.nombre_archivo_codificado+'\')" type="button" class="btn btn-sm btn-arch btn-default btn-outline-secondary rounded-circle" title="View Details" style="margin-left: 3px;"><i class="fa fa-download"></i></button>';

                        if (value.id_tipo_archivo == 2) //anexo
                            htmlFileAnexo += htmlFile + '</div>';  
                            

                        if (value.id_tipo_archivo == 3){ //otros
                            htmlFileOtros += htmlFile + '</div>'; 
                        }     
                        
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
                        sDivIzq += ' <div class="item"><div class="item_display" ><a href="" onclick="visualizar_documento_alerta('+jsonRespuesta[j]['identificador']+','+id_buzon+','+idBuzonOrigen+',\''+jsonRespuesta[j]['materia']+'\')">'+jsonRespuesta[j]['identificador']+'</a><p>'+moment(jsonRespuesta[j]['created_at']).format('DD-MM-YYYY')+'</p></div></div>';                               
                        $('#form_respuesta_a').append("<option selected>"+jsonRespuesta[j]['identificador']+"-"+jsonRespuesta[j]['materia']+"</option>");

                    }

                    //completar carrusel lado der
                    var sDivDer = "";
                    for (let d in jsonDocResponder){
                        sDivDer += ' <div class="item"><div class="item_display" "><a href="#" onclick="visualizar_documento_alerta('+jsonDocResponder[d]['identificador']+','+id_buzon+','+idBuzonOrigen+',\''+jsonDocResponder[d]['materia']+'\')" >'+jsonDocResponder[d]['identificador']+'</a><p>'+moment(jsonDocResponder[d]['created_at']).format('DD-MM-YYYY')+'</p></div></div>';
                    }
                     
                    sDivActual = '<div class="item"><div class="item_display item-doc" ><a href="#" onclick="visualizar_documento_alerta('+data.data.identificador+','+id_buzon+','+idBuzonOrigen+',\''+data.data.materia+'\')">'+data.data.identificador+'</a><p>'+moment(data.data.created_at).format('DD-MM-YYYY')+'</p></div></div>';
                    
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
   console.log("cargar_datos_bitacora",id_documento);
    //ocultar campo de busqueda por defecto 

    $('#tabla_bitacora_grilla_filter').hide();   

    if ( $.fn.DataTable.isDataTable('#tabla_bitacora_grilla') ) {
            $('#tabla_bitacora_grilla').DataTable().destroy();
    }

    $.getJSON(route('buscador.bitacora', { 'id': id_documento }), function (response) {
        $("#textMateriaModal").html();
        console.log(response);
        gridBitacora = $('#tabla_bitacora_grilla').dataTable({
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
        $("#idAsignado2").text(response[0].identificador);
        $("#textMateria").text(response[0].materia);


        $('input[name="filtro_derivaciones_bitacora"]').on('change', function() {
            var types = $('input:checkbox[name="filtro_derivaciones_bitacora"]:checked').map(function() {
                return '^' + this.value + '\$';
            }).get().join('|');
            TablaModalBitacora.fnFilter(types, 0, true, false, false, false);
        });

    });

    
    //window.someGlobalOrWhatever = response.balance
 

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

export { add_favorito,del_favorito,cargar_datos_grilla,ver_archivo,cargar_datos_bitacora,visualizar_documento_alerta };


//Archivar
