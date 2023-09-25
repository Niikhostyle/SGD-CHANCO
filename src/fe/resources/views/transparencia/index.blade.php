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

   

    

    <div class="row">
    <div class="col-lg-3 col-md-12 col-sm-12">
            Tipo Documento:<br/><select id="buscar_tipo_documento" xstyle="display:none;" name="tipo_documento" class="form-control " xmultiple="multiple">
                        @foreach($listado_tiposdoc as $list)
                        <option value="{{$list['id_tipo_documento']}}">{{$list['nombre']}}</option>
                        @endforeach
                    </select>
    </div>

    <div class="col-lg-3 col-md-12 col-sm-12">
            Fecha Desde:<br/><input type="date" id="buscar_fecha_ini" name="buscar_fecha_ini" class="form-control">
    </div>

    <div class="col-lg-3 col-md-12 col-sm-12">
            Fecha Hasta:<br/><input type="date" id="buscar_fecha_fin" name="buscar_fecha_fin" class="form-control">
    </div>    
    <div class="col-lg-3 col-md-12 col-sm-12">
    &nbsp;</br>
    <a href="javascript:void(0)" onclick="getDT()" class="btn btn-success">Buscar</a>
    </div>

    </div>
    <hr/>
    <table id="myTable" class="table table-bordered nowrap">
    <thead></thead>
    <tbody></tbody>
    </table>


<div class="d-none">
    <p>Docs de ejemplo:</p>
     <a href="https://sgd.padrelascasas.cl:9443/descargar_documento_plc?idDocumento=28189">28189</a><br/>
     <a href="https://sgd.padrelascasas.cl:9443/descargar_documento_plc?idDocumento=28172">28172</a><br/>
     <a href="https://sgd.padrelascasas.cl:9443/descargar_documento_plc?idDocumento=28166">28166</a><br/>
     <a href="https://sgd.padrelascasas.cl:9443/descargar_documento_plc?idDocumento=28163">28163</a><br/>

        <a href="javascript:void(0)" onclick="getZip()" class="btn btn-success">ZIP Ejemplo</a>

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

<script src="js/jquery.bootstrap-duallistbox.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" integrity="sha512-XMVd28F1oH/O71fzwBnV7HucLxVwtxf26XV8P4wPk26EDxuGZ91N8bsOttmnomcCD3CS5ZMRL50H0GgOHvegtg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip-utils/0.1.0/jszip-utils.min.js" integrity="sha512-3WaCYjK/lQuL0dVIRt1thLXr84Z/4Yppka6u40yEJT1QulYm9pCxguF6r8V84ndP5K03koI9hV1+zo/bUbgMtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js" integrity="sha512-csNcFYJniKjJxRWRV1R7fvnXrycHP6qDR21mgz1ZP55xY5d+aHLfo9/FcGDQLfn2IfngbAHd8LdfsagcCqgTcQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.13.4/api/processing().js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/datatables-buttons/2.2.3/js/dataTables.buttons.min.js" integrity="sha512-QT3oEXamRhx0x+SRDcgisygyWze0UicgNLFM9Dj5QfJuu2TVyw7xRQfmB0g7Z5/TgCdYKNW15QumLBGWoPefYg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
   
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
        //responsive: true,
        processing: true,
        buttons: ['copy', 'excel', 'pdf',
            {
                text: 'Descargar Documentos',
                className:'btn btn-info',
                action: function ( e, dt, node, config ) {
                    dt.processing( true );
                    let zip = new JSZip();
                    let urldoc = 'https://sgd.padrelascasas.cl:9443/descargar_documento_plc?idDocumento=';
                    let urlanexo = 'https://sgd.padrelascasas.cl:9443/descargar_documento_plc_anexo?idDocumento=';
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
            {title:"ID",data:'id_documento',className:'col-1'},
            {title:"tipo",data:'nombre_corto',className:'col-1'},
            {title:"folio",data:'folio',className:'col-1'},
            {title:"fecha",data:'fecha',render:function(item){return moment(item).format('DD-MM-YYYY')},className:'col-1'},
            {title:"Materia",data:'materia',width: "200px",className:'text-wrap col-7'},
            {title:"Anexos",data:'num_anexos',className:'col-1'},
        ],
        // "ajax": {
        //     "url": "https://sgd.padrelascasas.cl:9443/transparencia/getitems",
        //     "type": "GET"
        // },
    });
} );
function getDT() {
  let searchData = {
     "tipo_documento": $('select[name=tipo_documento]').val(),
     "buscar_fecha_ini": $('input[name=buscar_fecha_ini]').val(),
     "buscar_fecha_fin": $('input[name=buscar_fecha_fin]').val(),
    };
table.processing( true );
  $.ajax({
    Type: 'GET',
    url: 'https://sgd.padrelascasas.cl:9443/transparencia/getitems',
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
        "https://sgd.padrelascasas.cl:9443/descargar_documento_plc?idDocumento=28189",
        "https://sgd.padrelascasas.cl:9443/descargar_documento_plc?idDocumento=28172",
        "https://sgd.padrelascasas.cl:9443/descargar_documento_plc?idDocumento=28166",
        "https://sgd.padrelascasas.cl:9443/descargar_documento_plc?idDocumento=28163"
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