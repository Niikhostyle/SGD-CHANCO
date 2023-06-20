<!DOCTYPE html>
<html>
<head>
    <title>{{$materia}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>       
            footer { 
                position: fixed; 
                bottom: 40px;              
            }
            footer .pagenum:before 
            {
                content: counter(page);
            }
            .page {
               page-break-after: avoid;               
            }
            .page:last-child {
                /* Ocultar el contenido de la última página */
                display: block;
            }
            .content {               
                margin-bottom: 60px;
            }
            .pie {
                height: {{$altoTotal}}px;                
                page-break-inside: avoid !important;                   
            }                       
        </style>        
</head>
<body>
<div class="content page">
    {!! $encabezado !!}
    {!! $cuerpo !!}    
</div>
<div class="page pie">
    <footer>    
    {!! $distribucion !!}
    </footer>
</div>
</body>
</html>