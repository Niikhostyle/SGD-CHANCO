<?php   
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoBuzon extends Model{

    protected $table = "documento_buzon";
    protected $primaryKey = 'id_documento_buzon';

    public function buzon()
    {
        return $this->belongsTo(Buzon::class, 'id_buzon', 'id_buzon');
    }
}