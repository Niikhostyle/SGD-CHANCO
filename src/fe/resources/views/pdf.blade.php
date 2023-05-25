<!DOCTYPE html>
<html>
<head>
    <title>{{$materia}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>       

            footer { position: fixed; bottom: 40px;width:100% }
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
            .seccion-firma{
                border:2px solid #c2c2c2; 
                min-height:{{$altoFirmas  + 80 }} px !important;
                width:100% !important;
                margin-botom:15px;
                text-align:right;
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
    <footer>
        <p class="seccion-firma">
            <img src="img/firma_vp.png" style="width:295px;height:110px;margin-right:55px;margin-top:15px" />
        </p>
        {!! $distribucion !!}
    </footer>
</div>
<div style="height:300px;page-break-inside: avoid !important;">
<p>&nbsp;</p>
</div>
</body>
</html>
