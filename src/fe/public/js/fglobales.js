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
