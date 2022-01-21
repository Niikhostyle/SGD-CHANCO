

@section('title', 'Panel')

@section('content_header')


@stop

@section('content')
<!--<div class="container">-->


    <!-- **DOCUMENTOS** GRILLA CREAR DOCUMENTOS -->
    
    <h1>hola</h1>
    <!-- **DOCUMENTOS** GRILLA CREAR DOCUMENTOS -->

    <!-- Bitacora-->  
        
   
   
    <!-- Bitacora fin-->   


@stop

@section('css')

    <link rel="stylesheet" href="/css/admin_custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" integrity="sha512-jU/7UFiaW5UBGODEopEqnbIAHOI8fO6T99m7Tsmqs2gkdujByJfkCbbfPSN4Wlqlb9TGnsuC0YgUgWkRBK7B9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/tagsinput/app.css') }}">
    <link rel="stylesheet" href="/css/bootstrap-multiselect.css" type="text/css"/>

    <style type="text/css">
   
        .card {
            overflow: visible !important;
        }

        .dropzone {
            border: 2px dashed #ced4da;
        }

        .card-archivos {
            display: flex;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
        }

        .dropzone-files {
            flex:1;
        }

        .dz-max-files-reached {
            pointer-events: none;
            cursor: default;
        }
        .dz-remove { 
            pointer-events: all; cursor: default; 
        }

        .disabled {
            background-color: #e9ecef !important;
        }

        .row_archivar {
            display:none;
        }

        .flex-container {
            display: flex;
            flex-wrap: nowrap;
            background-color: rgb(208, 228, 191);
            border: 1px solid #5a8fc7;
        }

        .flex-container > div {
            background-color: #b5e4b9;
            border: 1px solid #5a8fc7;
            width: 100px;
            margin: 15px;
            text-align: center;
            line-height: 20px;
            font-size: 15px;
        }

        .label-info {
            background-color:#5bc0de
        }
        .label-info[href]:focus,
        .label-info[href]:hover {
            background-color:#31b0d5
        }
        .label {
            display: inline-block;
            padding: .25em .4em;
            font-size: 90%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;

        }

        .label-danger {
            background-color: #d9534f;
        }

        .label-warning{background-color:#f0ad4e;}


        .file-container-all { display: flex; }
        .file-container { position: relative; }
        .file-container img { display: block; }
        .fa-icon1 { position: absolute; bottom:0; left:0; }
        .fa-icon2 { position: absolute; bottom:0; left:30px; }

     </style>
@stop

@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js" integrity="sha512-oQq8uth41D+gIH/NJvSJvVB85MFk1eWpMK6glnkg6I7EdMqC1XVkW7RxLheXwmFdG03qScCM7gKS/Cx3FYt7Tg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="{{ asset('/vendor/ckeditor/ckeditor.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script>
<script src="{{ asset('/vendor/tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="/js/bootstrap-multiselect.js"></script>
<script src="/js/fglobales.js"></script>


<script>
    //globales

   


    $(document).ready(function () {
        $(".nuevo_documento").prop("disabled", true);

        //var div_por_recibir_width = document.getElementById('nav-por-recibir').getBoundingClientRect().width;
        //$('#nav-despachados').attr("style","width:"+div_por_recibir_width+'px');
        //$('#nav-recibidos').attr("style","width:"+div_por_recibir_width+'px');

       
    });


</script>
@stop