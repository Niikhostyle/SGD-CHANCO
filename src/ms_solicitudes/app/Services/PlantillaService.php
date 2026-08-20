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
            '{{motivo}}' => $datos['motivo'] ?? '',
            '{{explicacion}}' => $datos['explicacion'] ?? ($datos['motivo'] ?? ''),
            '{{viaticos_destino}}' => $datos['viaticos_destino'] ?? '',
            '{{fecha}}' => $this->fechaLarga(),
            '{{anio}}' => date('Y'),
            '{t_anio}' => date('Y'),
            '{t_fecha}' => $this->fechaLarga(),
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
        if ($a === $b && $a !== '') {
            return 'jornada ' . $a;
        }
        return trim(($a ?: '—') . ' a ' . ($b ?: '—'));
    }

    protected function fechaLarga(): string
    {
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $n = (int) date('n');
        return date('d') . ' de ' . $meses[$n - 1] . ' del ' . date('Y');
    }

    public function renderHtml(?string $html, User $user, array $datos): string
    {
        return strtr($html ?: '', $this->mapaCampos($user, $datos));
    }

    public function renderCuerpo(SolTipoDocumento $plantilla, User $user, array $datos): string
    {
        return $this->renderHtml($plantilla->plantilla_cuerpo_html, $user, $datos);
    }
}
