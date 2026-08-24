<?php

namespace App\Services;

use App\Models\SolTipoDocumento;
use App\Models\SolUsuarioRol;
use App\Models\User;

class PlantillaService
{
    public function resolver(?string $tipoSolicitud, ?string $regimen = null, ?int $id = null): ?SolTipoDocumento
    {
        if ($id) {
            $doc = SolTipoDocumento::with('buzonesFlujo')->find($id);
            if ($doc && $doc->activo) {
                return $doc;
            }
        }
        if (!$tipoSolicitud) {
            return null;
        }
        $q = SolTipoDocumento::where('tipo_solicitud', $tipoSolicitud)->where('activo', true);
        if ($regimen) {
            $doc = (clone $q)->where('regimen_laboral', $regimen)->first();
            if ($doc) {
                return $doc->load('buzonesFlujo');
            }
        }
        $found = $q->whereNull('regimen_laboral')->first() ?: $q->first();
        return $found ? $found->load('buzonesFlujo') : null;
    }

    public function mapaCampos(User $user, array $datos): array
    {
        $rol = SolUsuarioRol::where('user_id', $user->id)->with(['cargo', 'departamento'])->first();
        $saldos = $this->datosSaldo($user, $datos);
        $decision = $datos['alcalde_decision'] ?? null;
        return [
            '{{nombre}}' => $user->nombreCompleto(),
            '{{run}}' => $user->run ?? '',
            '{{cargo}}' => $user->cargo ?? ($rol && $rol->cargo ? $rol->cargo->nombre : ''),
            '{{departamento}}' => $rol && $rol->departamento ? $rol->departamento->nombre : '',
            '{{tipo_solicitud}}' => $datos['tipo_solicitud'] ?? ($datos['nombre_tipo'] ?? ''),
            '{{fecha_inicio}}' => !empty($datos['fecha_inicio']) ? date('d-m-Y', strtotime($datos['fecha_inicio'])) : '',
            '{{fecha_termino}}' => !empty($datos['fecha_termino']) ? date('d-m-Y', strtotime($datos['fecha_termino'])) : '',
            '{{total_dias}}' => (string) ($datos['total_dias'] ?? ''),
            '{{jornada_inicio}}' => strtoupper((string) ($datos['jornada_inicio'] ?? '')),
            '{{jornada_termino}}' => strtoupper((string) ($datos['jornada_termino'] ?? '')),
            '{{jornada}}' => $this->textoJornada($datos),
            '{{horario_permiso}}' => $this->horarioPermiso($datos),
            '{{horario_trabaja}}' => $this->horarioTrabaja($datos),
            '{{motivo}}' => $datos['motivo'] ?? '',
            '{{explicacion}}' => $datos['explicacion'] ?? ($datos['motivo'] ?? ''),
            '{{viaticos_destino}}' => $datos['viaticos_destino'] ?? '',
            '{{fecha}}' => $this->fechaLarga(),
            '{{dia}}' => date('d'),
            '{{mes}}' => $this->nombreMes(),
            '{{anio}}' => date('Y'),
            '{t_anio}' => date('Y'),
            '{t_dia}' => date('d'),
            '{t_mes}' => $this->nombreMes(),
            '{t_fecha}' => $this->fechaLarga(),
            '{dia}' => date('d'),
            '{mes}' => $this->nombreMes(),
            '{t_folio}' => (string) ($datos['folio'] ?? 'SIN FOLIO'),
            '{{ha_solicitado}}' => (string) $saldos['ha_solicitado'],
            '{{solicita}}' => (string) $saldos['solicita'],
            '{{saldo}}' => (string) $saldos['saldo'],
            '{{total}}' => (string) $saldos['total'],
            '{{alcalde_autorizado}}' => $decision === 'autorizado' ? 'X' : '______',
            '{{alcalde_denegado}}' => $decision === 'denegado' ? 'X' : '______',
            '{{alcalde_observaciones}}' => (string) ($datos['alcalde_observaciones'] ?? '________________'),
        ];
    }

    protected function datosSaldo(User $user, array $datos): array
    {
        $saldos = new SaldoService();
        $resumen = $saldos->resumen((int) $user->id);
        $campo = $saldos->campoPorPlantilla($datos['tipo_solicitud'] ?? null, $datos['categoria'] ?? null) ?: 'dias_administrativos';
        $ha = (float) ($resumen['usados'][$campo] ?? 0);
        $asig = (float) ($resumen['asignados'][$campo] ?? 0);
        $solicita = (float) ($datos['total_dias'] ?? 0);
        return [
            'ha_solicitado' => $ha,
            'solicita' => $solicita,
            'saldo' => max(0, round($asig - $ha - $solicita, 1)),
            'total' => $asig,
        ];
    }

    protected function textoJornada(array $datos): string
    {
        $a = strtoupper(trim((string) ($datos['jornada_inicio'] ?? '')));
        $b = strtoupper(trim((string) ($datos['jornada_termino'] ?? '')));
        if ($a === '' && $b === '') {
            return '';
        }
        if ($a === $b && $a === 'AM') {
            return 'media jornada mañana (AM) 08:30 a 13:00';
        }
        if ($a === $b && $a === 'PM') {
            return 'media jornada tarde (PM) 13:00 a 17:30';
        }
        if ($a === $b) {
            return 'jornada ' . $a;
        }
        return trim(($a ?: '—') . ' a ' . ($b ?: '—'));
    }

    protected function horarioPermiso(array $datos): string
    {
        $a = strtolower(trim((string) ($datos['jornada_inicio'] ?? '')));
        $b = strtolower(trim((string) ($datos['jornada_termino'] ?? '')));
        if ($a === 'am' && $b === 'am') {
            return '08:30 a 13:00';
        }
        if ($a === 'pm' && $b === 'pm') {
            return '13:00 a 17:30';
        }
        return '08:30 a 17:30';
    }

    protected function horarioTrabaja(array $datos): string
    {
        $a = strtolower(trim((string) ($datos['jornada_inicio'] ?? '')));
        $b = strtolower(trim((string) ($datos['jornada_termino'] ?? '')));
        if ($a === 'am' && $b === 'am') {
            return '13:00 a 17:30';
        }
        if ($a === 'pm' && $b === 'pm') {
            return '08:30 a 13:00';
        }
        return '';
    }

    protected function nombreMes(): string
    {
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        return $meses[(int) date('n') - 1];
    }

    protected function fechaLarga(): string
    {
        return date('d') . ' de ' . $this->nombreMes() . ' del ' . date('Y');
    }

    public function renderHtml(?string $html, User $user, array $datos): string
    {
        $html = $this->normalizarUrlsImagen($html ?: '');
        $html = $this->normalizarEstilosDoc($html);
        $html = $this->normalizarLlavesToken($html);
        $html = strtr($html, $this->mapaCampos($user, $datos));
        return $this->completarDiaFecha($html, date('d'));
    }

    /**
     * Reescribe URLs absolutas de producción (sgd.chanco.cl) a rutas /files/...
     * para que el PDF y la vista previa usen el storage local.
     */
    public function normalizarUrlsImagen(string $html): string
    {
        $html = preg_replace(
            '#https?://[^"\'\s>]+/files/#i',
            '/files/',
            $html
        ) ?? $html;
        return $html;
    }

    /** Evita margin-left excesivo que oculta el día de la fecha en PDF/vista previa. */
    public function normalizarEstilosDoc(string $html): string
    {
        $html = preg_replace_callback(
            '/\bstyle=(["\'])(.*?)\1/is',
            function ($m) {
                $style = $m[2];
                if (preg_match('/margin-left\s*:\s*(\d+)px/i', $style, $mm) && (int) $mm[1] >= 200) {
                    $style = preg_replace('/margin-left\s*:\s*\d+px\s*;?/i', '', $style);
                    if (!preg_match('/text-align\s*:/i', $style)) {
                        $style = 'text-align:right;' . $style;
                    }
                }
                $style = trim($style, " \t\n\r\0\x0B;") . ';';
                return 'style=' . $m[1] . $style . $m[1];
            },
            $html
        ) ?? $html;
        return preg_replace('/<img\b[^>]*\bsrc=["\']file:[^"\']*["\'][^>]*>/i', '', $html) ?? $html;
    }

    /** Quita spans/entidades dentro de {tokens} rotos por CKEditor. */
    protected function normalizarLlavesToken(string $html): string
    {
        return preg_replace_callback('/\{([^{}]+)\}/u', function ($m) {
            $inner = preg_replace('/<[^>]+>/', '', $m[1]) ?? $m[1];
            $inner = html_entity_decode($inner, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $inner = preg_replace('/\s+/u', '', $inner) ?? $inner;
            return '{' . $inner . '}';
        }, $html) ?? $html;
    }

    /** Normaliza fecha: quita duplicados, une dia+br y rellena huecos solo si falta la fecha. */
    protected function completarDiaFecha(string $html, string $dia): string
    {
        $meses = 'enero|febrero|marzo|abril|mayo|junio|julio|agosto|septiembre|octubre|noviembre|diciembre';
        $patMes = 'de\s+(' . $meses . ')\s+del?\s+\d{4}';
        $fechaCompleta = '/\d{1,2}\s+(' . $meses . ')\s+del?\s+\d{4}/iu';

        $html = $this->deduplicarDiaFecha($html, $patMes);
        $html = preg_replace('/(\d{1,2})\s*<br\s*\/?>\s*(' . $patMes . ')/iu', '$1 $2', $html) ?? $html;

        if (preg_match($fechaCompleta, $html)) {
            return $this->deduplicarDiaFecha($html, $patMes);
        }

        $html = preg_replace_callback(
            '/(^|>|&nbsp;|[\s\x{00A0}_\.·…]+)(' . $patMes . ')/iu',
            function ($m) use ($dia, $html) {
                $pos = $m[0][1];
                $before = substr($html, max(0, $pos - 200), $pos - max(0, $pos - 200));
                $before = html_entity_decode(strip_tags(str_replace('&nbsp;', ' ', $before)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $before = preg_replace('/\s+/u', ' ', $before) ?? $before;
                if (preg_match('/\d{1,2}\s*$/u', rtrim($before))) {
                    return $m[0][0];
                }
                return $m[1][0] . $dia . ' ' . $m[2][0];
            },
            $html,
            -1,
            $count,
            PREG_OFFSET_CAPTURE
        ) ?? $html;

        return $this->deduplicarDiaFecha($html, $patMes);
    }

    protected function deduplicarDiaFecha(string $html, string $patMes): string
    {
        return preg_replace('/(\d{1,2})\s+\1\s+(' . $patMes . ')/iu', '$1 $2', $html) ?? $html;
    }

    public function renderCuerpo(SolTipoDocumento $plantilla, User $user, array $datos): string
    {
        return $this->renderHtml($plantilla->plantilla_cuerpo_html, $user, $datos);
    }
}
