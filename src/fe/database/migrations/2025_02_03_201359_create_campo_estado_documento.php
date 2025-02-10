<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Documento;
use App\Models\DocumentoBuzonBitacora;

class CreateCampoEstadoDocumento extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('documento', 'estado_tramitacion')) {
            Schema::table('documento', function (Blueprint $table) {
                $table->integer('estado_tramitacion')->nullable();
            });

            ini_set('memory_limit', '-1');
            $documentos = Documento::with('tipo_documento')->orderBy('id_documento', 'ASC')->get();
            echo "Agregando estado tramitación a documentos";
            foreach ($documentos as $documento) {
                $documento->estado_tramitacion = 1;
                //tomar total de firmas del documento
                $totalfirmas = $documento->tipo_documento->numero_firmas;

                //ver si el tipo de documento requiere firma
                if ($documento->tipo_documento->requiere_fe) {

                    //contar las firmas del documento
                    $nfirmas = DocumentoBuzonBitacora::join('documento_buzon', 'documento_buzon_bitacora.id_documento_buzon', '=', 'documento_buzon.id_documento_buzon')
                        ->where('documento_buzon.id_documento', $documento->id_documento)
                        ->where('documento_buzon_bitacora.id_accion', 7)
                        ->get();

                    if ($nfirmas->count() >= $totalfirmas) {
                        $documento->estado_tramitacion = 4;
                    } else if ($nfirmas->count() > 0) {
                        $documento->estado_tramitacion = 3;
                    } else if ($nfirmas->count() == 0) {
                        $documento->estado_tramitacion = 1;
                    }
                    echo "ID ".$documento->id_documento." Totalfirmas = ".$totalfirmas.", NfirmasDocumento = ".$nfirmas->count()." Estado:".$documento->estado_tramitacion."\n";
                } else {
                    $documento->estado_tramitacion = 4;
                    echo "ID ".$documento->id_documento." [SIN FIRMAS] Estado:".$documento->estado_tramitacion."\n";
                }
                
                $documento->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documento', function (Blueprint $table) {
            $table->dropColumn('estado_tramitacion');
        });
        // Schema::dropIfExists('estado_documento');
    }
}
