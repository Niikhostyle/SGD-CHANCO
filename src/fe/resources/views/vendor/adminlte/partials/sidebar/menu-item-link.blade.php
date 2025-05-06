<li @isset($item['id']) id="buzon_{{ $item['id'] }}" @endisset class="nav-item">

    <a class="nav-link  px-2 {{ $item['class'] }} @isset($item['shift']) {{ $item['shift'] }} @endisset"
       href="{{ $item['href'] }}" @isset($item['target']) target="{{ $item['target'] }}" @endisset
       {!! $item['data-compiled'] ?? '' !!}>
        <div class="align-items-baseline d-flex">
        <i class="{{ $item['icon'] ?? 'far fa-fw fa-circle' }} {{
            isset($item['icon_color']) ? 'text-'.$item['icon_color'] : ''
        }}"></i>
        <p class="flex-fill ml-1">
            {{ $item['text'] }}    
        </p>
        <span class=" ">
            @isset($item['label1'])
                <span id="cantidad_porrecibir_{{ $item['id'] }}" style="color:white!important" class="badge text-white bg-{{ $item['label1_color'] ?? 'primary' }}  {{ $item['label1']!=''? $item['label1']:'d-none'}}">
                    {{ $item['label1'] }}
                </span>
            @endisset

            @isset($item['label2'])
                <span id="cantidad_pendientes_{{ $item['id'] }}" class="badge bg-{{ $item['label2_color'] ?? 'primary' }} {{ $item['label2']!=''? $item['label2']:'d-none'}}">
                    {{ $item['label2'] }}
                </span>
            @endisset
        </span>

        </div>
    </a>

</li>
