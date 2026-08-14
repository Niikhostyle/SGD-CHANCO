<?php

namespace App\Services;

use App\Models\SolTipoDocumento;
use App\Models\SolUsuarioRol;
use App\Models\User;

class PlantillaService
{
    public function resolver(string $tipoSolicitud, ?string $regimen = null): ?SolTipoDocumento
    {
        $q = SolTipoDocumento::where('tipo_solicitud', $tipoSolicitud)->where('activo', true);
        if ($regimen) {
            $doc = (clone $q)->where('regimen_laboral', $regimen)->first();
            if ($doc) {
                return $doc;
            }
        }
        return $q->whereNull('regimen_laboral')->first() ?: $q->first();
    }

    public function renderCuerpo(SolTipoDocumento $plantilla, User $user, array $datos): string
    {
        $html = $plantilla->plantilla_cuerpo_html ?: '';
        $rol = SolUsuarioRol::where('user_id', $user->id)->first();
        $map = [
            '{{nombre}}' => $user->nombreCompleto(),
            '{{run}}' => $user->run ?? '',
            '{{cargo}}' => $user->cargo ?? ($rol->cargo->nombre ?? ''),
            '{{departamento}}' => $rol && $rol->departamento ? $rol->departamento->nombre : '',
            '{{tipo_solicitud}}' => $datos['tipo_solicitud'] ?? '',
            '{{fecha_inicio}}' => $datos['fecha_inicio'] ?? '',
            '{{fecha_termino}}' => $datos['fecha_termino'] ?? '',
            '{{total_dias}}' => (string) ($datos['total_dias'] ?? ''),
            '{{motivo}}' => $datos['motivo'] ?? '',
            '{{explicacion}}' => $datos['explicacion'] ?? '',
            '{{viaticos_destino}}' => $datos['viaticos_destino'] ?? '',
            '{{fecha}}' => date('d-m-Y'),
        ];
        return strtr($html, $map);
    }
}
