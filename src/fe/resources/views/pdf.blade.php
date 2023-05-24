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
<div style="min-height:{{$altoFirmas}} px; border: 2px solid #333;">

            <!-- @if($altoFirmas == 165)
                <img src="img/firma_vp.png" style="width:255px;height:85px" > &nbsp; <img src="img/firma_vp.png" style="width:255px;height:85px"  >
            @endif
            @if($altoFirmas == 265)
                <img src="img/firma_vp.png" style="width:255px;height:85px"  > &nbsp; <img src="img/firma_vp.png" style="width:255px;height:85px"  >
                <div style="height:15px"></div>
                <img src="img/firma_vp.png" style="width:255px;height:85px"  > &nbsp; <img src="img/firma_vp.png" style="width:255px;height:85px"  >
                <div style="height:15px"></div>
            @endif
            @if($altoFirmas == 365)
                <img src="img/firma_vp.png" style="width:255px;height:85px;margin-right:15px;margin-bottom:15px"  > &nbsp; <img src="img/firma_vp.png" style="width:255px;height:85px;margin-right:15px;margin-bottom:15px"  >
                <div style="height:15px"></div>
                <img src="img/firma_vp.png" style="width:255px;height:85px;margin-right:15px;margin-bottom:15px"  > &nbsp; <img src="img/firma_vp.png"  style="width:255px;height:85px;margin-right:15px;margin-bottom:15px" >
                <div style="height:15px"></div>
                <img src="img/firma_vp.png" style="width:255px;height:85px;margin-right:15px;margin-bottom:15px" > &nbsp; <img src="img/firma_vp.png" style="width:255px;height:85px;margin-right:15px;margin-bottom:15px"  >                 
            @endif -->
            <br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sección para firma(s)
</div>
<div class="page">
    <footer>{!! $distribucion !!}</footer>
</div>
<div style="height:300px;page-break-inside: avoid !important;">
</div>
</body>
</html>