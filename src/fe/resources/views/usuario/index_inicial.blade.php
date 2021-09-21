@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')


    <div class="row">
        <div class="col-8">
            <h1>Usuarios</h1>
          <input type="hidden" id="sesion_key" value="{{ $sesion_key }}">
        </div>
        <div class="col">
            <button type="button" class="btn btn-success nuevo_usuario">Nuevo Usuario</button>

        </div>
      </div>
    <div class="linea_content_header"></div>

@stop

@section('content')
<div class="container">
    <div class="card" id="card_usuario_grilla">
        <div class="card-body">
            <table id="tabla_usuario_grilla" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>RUN</th>
                        <th>Email(Cuenta)</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="card" id="card_usuario_crear_editar">
        <h5 id="titulo_usuario_crear_editar"class="card-header bg-success" >Nuevo Usuario</h5>
        <div class="card-body">
            <form>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_run">RUN</label>
                        <input type="text" class="form-control" id="form_run" aria-describedby="run_error" placeholder="99229922-1">
                        <small id="run_error" class="form-text text-muted"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_perfil">Perfil</label>
                        <select  class="form-control" id="form_perfil">
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_estado">Estado</label>
                        <select  class="form-control" id="form_estado">
                            <option value="1">Activo</option>
                            <option value="2">Inactivo</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_nombres">Nombres</label>
                        <input type="text" class="form-control" id="form_nombres" aria-describedby="nombres_error" placeholder="">
                        <small id="nombres_error" class="form-text text-muted"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_primer_apellido">Primer Apellido</label>
                        <input type="text" class="form-control" id="form_primer_apellido" aria-describedby="primer_apellido_error" placeholder="">
                        <small id="primer_apellido_error" class="form-text text-muted"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_segundo_apellido">Segundo Apellido</label>
                        <input type="text" class="form-control" id="form_segundo_apellido" aria-describedby="segundo_apellido_error" placeholder="">
                        <small id="segundo_apellido_error" class="form-text text-muted"></small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_contrasena">Contraseña</label>
                        <input type="password" class="form-control" id="form_contrasena" aria-describedby="contrasena_error" >
                        <small id="contrasena_error" class="form-text text-muted"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_reescribir_contrasena">Reescribir-Contraseña</label>
                        <input type="password" class="form-control" id="form_reescribir_contrasena" aria-describedby="reescribir_contrasena_error">
                        <small id="reescribir_contrasena_error" class="form-text text-muted"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_email">Email (Usuario)</label>
                        <input type="email" class="form-control" id="form_email" aria-describedby="email_error" placeholder="">
                        <small id="email_error" class="form-text text-muted"></small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_firma_electronica">Firma electrónica avanzada</label>
                        <select  class="form-control" id="form_firma_electronica">
                            <option value="1">Habilitada</option>
                            <option value="0">Denegada</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_generacion_pdf">Generación PDF</label>
                        <select  class="form-control" id="form_generacion_pdf">
                            <option value="1">Habilitada</option>
                            <option value="0">Denegada</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4"> </div>
            </div>
            <div class="row">
                <div class="col-md-8"> </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-secondary w-100">Cerrar</button>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success w-100">Guardar</button>

                </div>
            </div>

            </form>

        </div>
    </div>


</div>

@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script>

    //Despuesde cargada la pagina, se proceden a enlazar los elementos con jquery
    $(document).ready(function () {
        //Enlazando tabla con datos AJAX
        sesion_key= $('#sesion_key').val();
        var table = $('#tabla_usuario_grilla').DataTable({
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            language: lenguaje_datatable,
            //processing: true,
            //serverSide: true,
            destroy:true,
            dom: "lftip",
            ajax: function (data, callback, settings) {

                //error  url: 'https://run.mocky.io/v3/56edfff2-9303-4917-a27b-4e7cf519f4bb'
                //https://run.mocky.io/v3/f7cdacf3-1c92-4d62-abd7-a8550f9ba1ca
                $.ajax({
                    url: 'https://run.mocky.io/v3/1ddc16d2-39bf-4303-a945-5f6c6a60ed03',
                    type: 'GET',
                    beforeSend: function(request) {
                        request.setRequestHeader("key", sesion_key);
                    },
                    success:function(data){
                        callback(data);
                    },
                    error: function (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: e.responseJSON.data.comentario,
                            confirmButtonText: 'Cerrar',
                        })

                    }
                    });
            },
            columns: [{
                    data: 'id_usuario'
                }, {
                    data: 'nombres'
                }, {
                    data: 'rut'
                }, {
                    data: 'e-mail'
                },
                {
                    data: 'estado_usuario'
                },
                {
                    data: null,
                    render: function (data, type, row, meta) {
                       return  '<div class="dropdown">'
                           +'<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                            +'    <i class="fas fa-bars"></i>'
                            +'</button>'
                            +'<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">'
                            +'    <a class="dropdown-item" href="#"><i class="fas fa-eye text-blue"></i> Ver</a>'
                            +'    <a class="dropdown-item" href="#"><i class="fas fa-edit text-blue"></i> Editar</a>'
                            +'    <a class="dropdown-item" href="#"><i class="fas fa-trash-alt text-red"></i> Deshabilitar</a>'
                            +'</div>'
                            +'</div>';
                    }
                }
            ],
        });
        /*Combo Perfiles*/
        $.ajax({
            url: 'https://run.mocky.io/v3/a6883c01-9d3f-4792-a519-468bc0bfb74c',
            type: 'GET',
            beforeSend: function(request) {
                request.setRequestHeader("key", sesion_key);
            },
            success:function(d){
                var perfiles = $('#form_perfil');
                perfiles.find('option').remove();
                    $(d.data).each(function(i, v){ // indice, valor
                        perfiles.append('<option value="' + v.id_perfil + '">' + v.nombre + '</option>');
                    })
                },
                error: function (e) {
                    Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: e.responseJSON.data.comentario,
                            confirmButtonText: 'Cerrar',
                    })
                }
        });

        //En lazando botones de la tabla
        $(document).on('click', '#btnEdit', function () {
            var data = table.row($(this).parents('tr')).data();
            alert(data['name']);
        });

        $(document).on('click', '#btnDelete', function () {
            var data = table.row($(this).parents('tr')).data();
            alert(data['salary']);
        });
    });
</script>
@stop
