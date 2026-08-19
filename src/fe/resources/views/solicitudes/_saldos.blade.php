@php
    $s = $saldo ?? [];
    $anio = $s['anio'] ?? date('Y');
    $items = [
        ['key' => 'dias_administrativos', 'label' => 'Administrativos', 'color' => 'primary'],
        ['key' => 'feriados_legales', 'label' => 'Feriados legales', 'color' => 'info'],
        ['key' => 'dias_compensatorios', 'label' => 'Compensatorios', 'color' => 'success'],
    ];
@endphp
<div class="row {{ $class ?? 'mb-3' }}">
    @foreach($items as $it)
        @php $n = (int) ($s[$it['key']] ?? 0); @endphp
        <div class="col-md-4 mb-3">
            <div class="card border-{{ $it['color'] }} h-100 mb-0">
                <div class="card-body text-center py-3">
                    <div class="text-muted text-uppercase small font-weight-bold">{{ $it['label'] }}</div>
                    <div class="text-{{ $it['color'] }}" style="font-size:2.75rem;font-weight:800;line-height:1.05">{{ $n }}</div>
                    <div class="small text-muted">días disponibles {{ $anio }}</div>
                    @if(!empty($s['asignados'][$it['key']]) || !empty($s['usados'][$it['key']]))
                        <div class="small text-muted">asignados {{ (int) ($s['asignados'][$it['key']] ?? 0) }} · usados {{ (int) ($s['usados'][$it['key']] ?? 0) }}</div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
