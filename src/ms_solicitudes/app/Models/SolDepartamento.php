<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolDepartamento extends Model
{
    protected $table = 'sol_departamentos';
    protected $fillable = ['nombre', 'directivo_id'];

    public function directivo()
    {
        return $this->belongsTo(User::class, 'directivo_id');
    }

    public function subrogantes()
    {
        return $this->belongsToMany(User::class, 'sol_departamento_subrogante', 'departamento_id', 'user_id');
    }
}
