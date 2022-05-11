<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documento</title>
</head>
<body>
    <b>{{ $user->nombres }}</b> <b>{{ $user->primer_apellido }}</b>,
    <br>Usted ha recibido un documento en su buzón personal:
    <ul>
        <li>Enviado por: <b>{{ $buzon->nombre}}</li>
        <li>Fecha: <b>{{ $documento->fecha }}</li>
        <li>Materia: <b>{{ $documento->materia}}</li>
        <li>Descipción: <b>{{ $documento->descripcion}}</li>
        <li>Comentario:  <b>{{ $documentoBuzon->comentario_principal }}</li>
    </ul>
    
    <br>Atte.
    <br>Sistema de Gestión Documental.
    <br>Municipalidad de Padre las Casas.

</html>