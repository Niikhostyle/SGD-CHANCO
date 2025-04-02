@extends('adminlte::page')

@section('title', 'Panel')

@section('content_header')


    <div class="row">
        <div class="col-8">
            <h1>Transparencia</h1>
        </div>
       
      </div>
    <div class="linea_content_header"></div>

@stop

@section('content')

<div class="card">
    <div class="card-body">

   

    

    <div class="d-md-flex justify-content-between">
    <div class="mr-2 flex-fill">
            Tipo Documento:<br/><select id="buscar_tipo_documento" xstyle="display:none;" name="tipo_documento" class="form-control " xmultiple="multiple">
                        @foreach($listado_tiposdoc as $list)
                        <option value="{{$list['id_tipo_documento']}}">{{$list['nombre']}}</option>
                        @endforeach
                    </select>
    </div>
<div class="mr-2">
            Estado Tramitación:<br/><select name="estado_tramitacion" class="form-control ">
            <option value=''>Seleccione</option>
            @foreach($listado_tramites as $list)
                        <option value="{{$list['id']}}">{{$list['nombre']}}</option>
                        @endforeach
            </select>
    </div>    
    <div class="mr-2">
            Fecha Desde:<br/><input type="date" id="buscar_fecha_ini" name="buscar_fecha_ini" class="form-control">
    </div>

    <div class="mr-2">
            Fecha Hasta:<br/><input type="date" id="buscar_fecha_fin" name="buscar_fecha_fin" class="form-control">
    </div>    
    <div class="">
    &nbsp;</br>
    <a href="javascript:void(0)" onclick="getDT()" class="btn btn-success">Buscar</a>
    </div>

    </div>
    <hr/>
    <div>
    <table id="myTable" class="table table-bordered nowrap table-responsive responsive"  width="100%">
    <thead></thead>
    <tbody></tbody>
    </table>
</div>


    </div>
</div>


@stop

@section('css')

    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap-duallistbox.css">

    <style type="text/css">
    
        .bootstrap-duallistbox-container .btn.moveall,
        .bootstrap-duallistbox-container .btn.removeall {
            display: none;
        }
      
        .bootstrap-duallistbox-container .btn.move,
        .bootstrap-duallistbox-container .btn.remove {
            width: 40%;
            height: 30%;
            margin: 20px;
        }

        .customButtonBox {
            margin-top:80px;
        }
        
        .form-control.is-valid, .was-validated .form-control:valid {
            border-color: none !important;
            background-image: none;
        }

        .clear1, .clear2
        {
            display:none;
        }
        .multiselect-native-select{
            display: grid;
        }
        td.wrap {
        white-space: normal;
        }

     </style>
@stop

@section('js')


<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" integrity="sha512-XMVd28F1oH/O71fzwBnV7HucLxVwtxf26XV8P4wPk26EDxuGZ91N8bsOttmnomcCD3CS5ZMRL50H0GgOHvegtg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip-utils/0.1.0/jszip-utils.min.js" integrity="sha512-3WaCYjK/lQuL0dVIRt1thLXr84Z/4Yppka6u40yEJT1QulYm9pCxguF6r8V84ndP5K03koI9hV1+zo/bUbgMtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js" integrity="sha512-csNcFYJniKjJxRWRV1R7fvnXrycHP6qDR21mgz1ZP55xY5d+aHLfo9/FcGDQLfn2IfngbAHd8LdfsagcCqgTcQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.13.4/api/processing().js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables-buttons/2.2.3/js/dataTables.buttons.min.js" integrity="sha512-QT3oEXamRhx0x+SRDcgisygyWze0UicgNLFM9Dj5QfJuu2TVyw7xRQfmB0g7Z5/TgCdYKNW15QumLBGWoPefYg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables-buttons/2.2.3/js/buttons.html5.min.js" integrity="sha512-BdN+kHA7QfWIcQE3WMwSj5QAvVUrSGxJLv7/yuEEypMOZBSJ1VKGr9BSpOew+6va9yfGUACw/8Yt7LKNn3RKRA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-bs4/1.11.1/dataTables.bootstrap4.min.js" integrity="sha512-9o2JT4zBJghTU0EEIgPvzzHOulNvo0jq2spTfo6mMmZ6S3jK+gljrfo0mKDAxoMnrkZa6ml2ZgByBQ5ga8noDQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables.net-bs4/1.11.1/dataTables.bootstrap4.min.css" integrity="sha512-+RecGjm1x5bWxA/jwm9sqcn5EV0tNej3Xxq5HtIOLM9YM9VgI2LbhEDn099Xhxg6HuvrmsXR0J6JQxL7tLHFHw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   
<script src="/js/bootstrap-multiselect.js"></script>
<script>

//DataTable.datetime('DD-MM-YYYY');
var table =null;
$(document).ready( function () {

    $('input[name=buscar_fecha_ini]').val(moment().startOf('month').format('YYYY-MM-DD'));
    $('input[name=buscar_fecha_fin]').val(moment().endOf('month').format('YYYY-MM-DD'))

    table = $('#myTable').DataTable({
        dom: 'Brtip',
        order: [[2, 'asc']],
        paging:false,
        responsive: true,
        processing: true,
        buttons: [
            { extend: 'excel', className:'btn btn-info', text: "<i class='fa fa-download'></i>&nbsp;Descargar información en Excel" }
            ,
            {
                text: "<i class='fa fa-download'></i>&nbsp;Descargar Documentos",
                className:'btn btn-info',
                action: function ( e, dt, node, config ) {
                    dt.processing( true );
                    let zip = new JSZip();
                    let urldoc = route('plc.transparenciagetDoc')+'?idDocumento=';
                    let urlanexo = route('plc.transparenciagetDocAnexo')+'?idDocumento=';
                    let items = [];
                    $.each(dt.rows().data(),function(idx,o){
                        setTimeout(zip.file(o.nombre_corto+"_"+o.folio+".pdf", urlToPromise(urldoc+o.id_documento), {binary:true}),500);
                        //iterar anexos 
                        for(let i=0;i<o.num_anexos;i++){
                            setTimeout(zip.file(o.nombre_corto+"_"+o.folio+"_anexo"+(i+1)+".pdf", urlToPromise(urlanexo+o.id_documento+"&nAnexo="+i), {binary:true}),500);
                        }

                    });
                    zip.generateAsync({type:"blob"})
                    .then(function(content) {
                        // see FileSaver.js
                        saveAs(content, "descarga.zip");
                        dt.processing( false );
                    });

                }
            }
        ],
        "columns": [
            {title:"ID",data:'id_documento',className:''},
            {title:"tipo",data:'nombre_corto',className:'-1'},
            {title:"folio",data:'folio',className:'-1'},
            {title:"fecha",data:'fecha',render:function(item){return moment(item).format('DD-MM-YYYY')},className:'-1'},
            {title:"Materia",data:'materia',className:'text-wrap -5'},
            {title:"Estado Tramitación",data:'nombre_estado_tramitacion',className:'text-wrap -1'},
            {title:"Anexos",data:'num_anexos',className:'-1'},
        ],
       
    });
} );
function getDT() {
  let searchData = {
     "tipo_documento": $('select[name=tipo_documento]').val(),
     "buscar_fecha_ini": $('input[name=buscar_fecha_ini]').val(),
     "buscar_fecha_fin": $('input[name=buscar_fecha_fin]').val(),
     "estado_tramitacion": $('select[name=estado_tramitacion]').val(),
    };
table.processing( true );
  $.ajax({
    Type: 'GET',
    url: route('plc.transparenciagetitems'),
    data: searchData
  }).done(function (result) {
    console.log(typeof result);
    //result = JSON.parse(result);
    table.clear();
    table.rows.add(result.data).draw();
    table.columns.adjust().draw();
    table.processing( false );
  });

}

function getZip(){
    console.log("init zip");
    let zip = new JSZip();

    let archivos = [
        route('plc.transparenciagetDoc')+"?idDocumento=28189",
        route('plc.transparenciagetDoc')+"?idDocumento=28172",
        route('plc.transparenciagetDoc')+"?idDocumento=28166",
        route('plc.transparenciagetDoc')+"?idDocumento=28163"
    ];

    $(archivos).each(function (i,o) {
        console.log(o);
        var filename = o.replace(/.*\//g, "");
        var filename = filename.replace('descargar_documento_plc?idDocumento=', "");
        console.log(filename);
        zip.file(filename+".pdf", urlToPromise(o), {binary:true});
    });

    zip.generateAsync({type:"blob"})
    .then(function(content) {
        // see FileSaver.js
        saveAs(content, "example.zip");
    });

}

function urlToPromise(url) {
    return new Promise(function(resolve, reject) {
        JSZipUtils.getBinaryContent(url, function (err, data) {
            if(err) {
                reject(err);
                console.log(url);
            } else {
                console.log(data);
                resolve(data);
            }
        });
    });
}
</script>

@endsection