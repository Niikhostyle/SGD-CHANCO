<form method="POST" action="/captura">
@csrf
    <select name="select_anio" onchange="this.form.submit()" class="form-select">
    @foreach($listado_anios as $list)
        @if ($list['estado'] == 1 )
            @if ($list['id_anio'] == session('year'))
                <option selected value="{{$list['id_anio']}}">{{$list['descripcion']}}</option>
            @else
                 <option value="{{$list['id_anio']}}">{{$list['descripcion']}}</option>
            @endif           
        @endif
    @endforeach    
    </select>
</form>
