<?php

namespace App\Services;

use App\Models\SolUsuarioRol;
use App\Models\User;
use Exception;

class RolService
{
    public function rolDe(int $userId): ?SolUsuarioRol
    {
        return SolUsuarioRol::where('user_id', $userId)->first();
    }

    public function ensureRol(int $userId): SolUsuarioRol
    {
        return SolUsuarioRol::firstOrCreate(
            ['user_id' => $userId],
            ['rol' => 'usuario', 'firmagob_enabled' => false]
        );
    }

    public function assertRoles(int $userId, array $roles): SolUsuarioRol
    {
        $rol = $this->ensureRol($userId);
        if ($rol->rol === 'admin_solicitudes') {
            return $rol;
        }
        // Admin SGD (id_perfil=1) también puede administrar
        $user = User::find($userId);
        if ($user && (int) $user->id_perfil === 1) {
            return $rol;
        }
        if (!in_array($rol->rol, $roles, true)) {
            throw new Exception('No autorizado para esta acción.');
        }
        return $rol;
    }

    public function isAdmin(int $userId): bool
    {
        $user = User::find($userId);
        if ($user && (int) $user->id_perfil === 1) {
            return true;
        }
        $rol = $this->ensureRol($userId);
        return $rol->rol === 'admin_solicitudes';
    }

    public function puedeGestionarSaldos(int $userId): bool
    {
        if ($this->isAdmin($userId)) {
            return true;
        }
        $rol = $this->ensureRol($userId);
        if ($rol->rol === 'rrhh') {
            return true;
        }
        $flujo = new FlujoService();
        $rrhh = $flujo->resolverBuzonConfig('buzon_rrhh_id', ['departamento de personal', 'recursos humanos', 'rrhh']);
        return $rrhh ? $flujo->usuarioEnBuzon($userId, (int) $rrhh->id_buzon) : false;
    }
}
