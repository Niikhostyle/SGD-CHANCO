

<div class="titulo"><h1>Documento Interno</h1> </div>

<div class="page-break"></div>


    @foreach ($datosDocumentos as $dato)

    <div style="text-align: center;"><p>{{$dato->encabezado}}</p></div>
    <div class="cuerpo">{{$dato->cuerpo}}</div>
    <div class="page-break"></div>

    @endforeach

    @foreach ($datosArchivos as $file)

  
    <a href="#"><embed src="{{asset($file->nombre_archivo_original)}}" alt="archivo" style="width: 120px; height: 50px; margin: 10px; border: 1px solid #5a8fc7;"></a>
    
    @endforeach


  

<style>
.page-break {
    page-break-after: always;
}

.titulo {
    text-align: center;
    font: 2rem;
    color: black;
}

.cuerpo {
  text-align: justify;
  text-justify: inter-word;
}
</style>

