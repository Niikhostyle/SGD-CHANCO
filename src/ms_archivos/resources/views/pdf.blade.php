<!DOCTYPE html>
<html>
<head>
    <title>{{$materia}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>       

            footer { position: fixed; bottom: 40px; }
            footer .pagenum:before 
            {
                content: counter(page);
            }
            .page {
               page-break-after: avoid;               
            }
            .page:last-child {
                /* Ocultar el contenido de la última página */
                display: none;
            }
                       
        </style>
        
</head>
<body>
<div class="content page">
    {!! $encabezado !!}
    {!! $cuerpo !!} 
</div>
<div style="height:{{$altoFirmas}}">
</div>
<div class="page">
    <footer>{!! $distribucion !!}</footer>
</div>
<div style="height:300px;page-break-inside: avoid !important;">
<p>&nbsp;</p>
</div>
</body>
</html>