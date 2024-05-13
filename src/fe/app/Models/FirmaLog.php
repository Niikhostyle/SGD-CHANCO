<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FirmaLog extends Model
{
    use HasFactory;
    protected $table = "firma_log";
    protected $primaryKey = 'id';

    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'id_documento','mensaje'
    ];
}
