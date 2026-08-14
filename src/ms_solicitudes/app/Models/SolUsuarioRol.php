<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolUsuarioRol extends Model
{
    protected $table = 'sol_usuario_rol';

    protected $fillable = [
        'user_id', 'rol', 'cargo_id', 'departamento_id',
        'regimen_laboral', 'firmagob_enabled',
    ];

    protected $casts = [
        'firmagob_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cargo()
    {
        return $this->belongsTo(SolCargo::class, 'cargo_id');
    }

    public function departamento()
    {
        return $this->belongsTo(SolDepartamento::class, 'departamento_id');
    }

    public function hasRole(...$roles): bool
    {
        return in_array($this->rol, $roles, true);
    }
}
