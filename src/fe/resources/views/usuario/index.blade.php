@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')


    <div class="row">
        <div class="col-8">
            <h1>Usuarios</h1>
        </div>
        <div class="col">
            <button type="button" class="btn btn-success nuevo_usuario">Nuevo Usuario2</button>

        </div>
      </div>
    <div class="linea_content_header"></div>

@stop

@section('content')
<div class="container">
    <div class="card" id="card_usuario_grilla">
        <div class="card-body">
            <table id="tabla_usuario_grilla" class="table dt-responsive nowrap" style="width:100%">
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
                <tbody>
                    @foreach($lista_usuarios['data'] as $list)
                    <tr>
                        <td>{{$list['id_usuario']}}</td>
                        <td>{{$list['nombres']}}</td>
                        <td>{{$list['rut']}}</td>
                        <td>{{$list['e-mail']}}</td>
                        <td>{{$list['estado_usuario']}}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                     <i class="fas fa-bars"></i>
                                 </button>
                                 <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                     <a class="dropdown-item" href="#"><i class="fas fa-eye text-blue"></i> Ver</a>
                                     <a class="dropdown-item" href="#"><i class="fas fa-edit text-blue"></i> Editar</a>
                                     <a class="dropdown-item" href="#"><i class="fas fa-trash-alt text-red"></i> Deshabilitar</a>
                                 </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="alert alert-warning print-error-msg" style="display:none">
        <ul></ul>
    </div>
    <div class="card" id="card_usuario_crear_editar">
        <h5 id="titulo_usuario_crear_editar"class="card-header bg-success" >Nuevo Usuario</h5>
        <div class="card-body">
            <form method="POST" action="{{route('usuarios.store')}}"  >
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_run">RUN</label>
                        <input type="text" class="form-control @error('run') is-invalid @enderror" id="form_run" name="run" aria-describedby="run_error" placeholder="99229922-1">
                        @error('run')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_perfil">Perfil</label>
                        <select  class="form-control @error('id_perfil') is-invalid @enderror" id="form_perfil" name="id_perfil">
                            <option value="">Seleccionar</option>
                            @foreach($perfiles['data'] as $perfil)
                                <option value="{{$perfil['id_perfil']}}">{{$perfil['nombre']}}</option>
                            @endforeach
                        </select>
                        @error('id_perfil')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_estado">Estado</label>
                        <select  class="form-control @error('id_estado_usuario') is-invalid @enderror" id="form_estado" name="id_estado_usuario">
                            <option value="">Seleccionar</option>
                            <option value="1">Activo</option>
                            <option value="2">Inactivo</option>
                        </select>
                        @error('id_estado_usuario')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_nombres">Nombres</label>
                        <input type="text" class="form-control @error('nombres') is-invalid @enderror" id="form_nombres" aria-describedby="nombres_error" placeholder="" name="nombres">
                        @error('nombres')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_primer_apellido">Primer Apellido</label>
                        <input type="text" class="form-control @error('primer_apellido') is-invalid @enderror" id="form_primer_apellido" name="primer_apellido" aria-describedby="primer_apellido_error" placeholder="">
                        @error('primer_apellido')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_segundo_apellido">Segundo Apellido</label>
                        <input type="text" class="form-control @error('segundo_apellido') is-invalid @enderror" id="form_segundo_apellido" name="segundo_apellido" aria-describedby="segundo_apellido_error" placeholder="">
                        @error('segundo_apellido')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_contrasena">Contraseña</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="form_contrasena" name="password" aria-describedby="contrasena_error" >
                        @error('password')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_reescribir_contrasena">Reescribir-Contraseña</label>
                        <input type="password" class="form-control" id="form_reescribir_contrasena" name="re_password" aria-describedby="reescribir_contrasena_error">

                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="input_email">Email (Usuario)</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="form_email" name="email" aria-describedby="email_error" placeholder="">
                        @error('email')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_firma_electronica">Firma electrónica avanzada</label>
                        <select  class="form-control @error('aplica_fea') is-invalid @enderror" id="form_aplica_fea" name="aplica_fea">
                            <option value="1">Habilitada</option>
                            <option value="0">Denegada</option>
                        </select>
                        @error('aplica_fea')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="select_generacion_pdf">Generación PDF</label>
                        <select  class="form-control @error('aplica_genera_pdf') is-invalid @enderror" id="form_genera_pdf" name="aplica_genera_pdf">
                            <option value="1">Habilitada</option>
                            <option value="0">Denegada</option>
                        </select>
                        @error('aplica_genera_pdf')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror
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
                    <button type="button" class="btn btn-success btn-submit w-100">Guardar</button>
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
 $(document).ready(function () {

        $('#tabla_usuario_grilla').DataTable({
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            responsive: true,
            language: lenguaje_datatable
        });


        $(".btn-submit").click(function(e){
            e.preventDefault();
            $(".print-error-msg").hide();
            var _token = $("input[name='_token']").val();
            var id_perfil = $("select[name='id_perfil']").val();
            var id_estado_usuario = $("select[name='id_estado_usuario']").val();
            var run = $("input[name='run']").val();
            var nombres = $("input[name='nombres']").val();
            var primer_apellido = $("input[name='primer_apellido']").val();
            var segundo_apellido = $("input[name='segundo_apellido']").val();
            var password = $("input[name='password']").val();
            var re_password = $("input[name='re_password']").val();
            var email = $("input[name='email']").val();
            var aplica_fea = $("select[name='aplica_fea']").val();
            var aplica_genera_pdf = $("select[name='aplica_genera_pdf']").val();
            if(password.length>0){
                if(password==re_password){
                    $.ajax({
                        url: "{{route('usuarios.store')}}",
                        type:'POST',
                        data: { _token:_token, run:run, id_perfil:id_perfil,
                                id_estado_usuario:id_estado_usuario, nombres:nombres,
                                primer_apellido:primer_apellido, segundo_apellido:segundo_apellido,
                                password:password,email:email,aplica_fea:aplica_fea,aplica_genera_pdf:aplica_genera_pdf
                                },
                        success: function(data) {
                            console.log(data);
                        },
                        error: function (e) {
                            data = e.responseJSON;
                            if (typeof data.errors !== 'undefined') {
                                printErrorMsg(data.errors);
                            }
                        }
                    });
                }else{
                    printErrorMsg({'password':'Las contraseñas no coinciden.'})
                }
            }


        });

        function printErrorMsg(msg) {
            $(".print-error-msg").find("ul").html('');
            $(".print-error-msg").css('display','block');

            $.each( msg, function( key, value ) {
                console.log(value[0]);
                if(value[0]=='The RUN is not valid.'){
                    value[0]='El RUN no es valido.'
                }
                $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
            });
        }


        // botones de la tabla
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
