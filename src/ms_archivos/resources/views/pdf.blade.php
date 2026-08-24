<!DOCTYPE html>
<html>
<head>
    <title>{{$materia}}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>       
            footer { 
                position: fixed; 
                bottom: 60px;              
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
            .content p {
                max-width: 100%;
            }
            .content [style*="text-align:right"],
            .content [style*="text-align: right"] {
                margin-left: 0 !important;
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
    {!! $visadores !!}  
    {!! $distribucion !!}
    </footer>
</div>
</body>
</html>