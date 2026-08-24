@php
    $s = $saldo ?? [];
    $anio = $s['anio'] ?? date('Y');
    $compact = !empty($compact);
    $fmt = function ($v) {
        $n = round((float) $v, 1);
        if (abs($n - (int) $n) < 0.05) {
            return (string) (int) $n;
        }
        return rtrim(rtrim(number_format($n, 1, ',', ''), '0'), ',');
    };
    $items = [
        ['key' => 'dias_administrativos', 'label' => 'Administrativos', 'color' => 'primary'],
        ['key' => 'feriados_legales', 'label' => 'Feriados legales', 'color' => 'info'],
        ['key' => 'dias_compensatorios', 'label' => 'Compensatorios', 'color' => 'success'],
    ];
    $numSize = $compact ? '1.75rem' : '2.75rem';
    $padY = $compact ? 'py-2' : 'py-3';
@endphp
<div class="row {{ $class ?? 'mb-3' }}">
    @foreach($items as $it)
        @php $n = $s[$it['key']] ?? 0; @endphp
        <div class="col-md-4 mb-3">
            <div class="card border-{{ $it['color'] }} h-100 mb-0">
                <div class="card-body text-center {{ $padY }}">
                    <div class="text-muted text-uppercase small font-weight-bold">{{ $it['label'] }}</div>
                    <div class="text-{{ $it['color'] }}" style="font-size:{{ $numSize }};font-weight:800;line-height:1.05">{{ $fmt($n) }}</div>
                    <div class="small text-muted">días disponibles {{ $anio }}</div>
                    @if(!empty($s['asignados'][$it['key']]) || !empty($s['usados'][$it['key']]))
                        <div class="small text-muted">asignados {{ $fmt($s['asignados'][$it['key']] ?? 0) }} · usados {{ $fmt($s['usados'][$it['key']] ?? 0) }}</div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
