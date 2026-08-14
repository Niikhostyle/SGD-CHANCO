<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    public $timestamps = true;

    protected $fillable = [
        'id', 'email', 'nombres', 'primer_apellido', 'segundo_apellido',
        'run', 'cargo', 'img_firma', 'aplica_fea', 'id_perfil',
    ];

    public function solRol()
    {
        return $this->hasOne(SolUsuarioRol::class, 'user_id');
    }

    public function nombreCompleto(): string
    {
        return trim(($this->nombres ?? '') . ' ' . ($this->primer_apellido ?? '') . ' ' . ($this->segundo_apellido ?? ''));
    }
}
