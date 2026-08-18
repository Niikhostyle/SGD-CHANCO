<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolSolicitud extends Model
{
    protected $table = 'sol_solicitudes';

    protected $fillable = [
        'user_id', 'sol_tipo_documento_id', 'directivo_asignado_id', 'id_buzon_destino',
        'mensaje_para_directivo',
        'otros_destinatarios', 'mensaje_otros_destinatarios',
        'tipo_solicitud', 'regimen_laboral', 'fecha_inicio', 'fecha_termino',
        'total_dias', 'estado', 'paso_actual', 'observaciones', 'motivo', 'explicacion',
        'sobretiempo_referencia', 'viaticos_destino', 'viaticos_hora_inicio',
        'viaticos_hora_termino', 'licencia_folio', 'licencia_tipo',
        'licencia_emisor', 'licencia_documento_path', 'con_goce',
        'documento_cuerpo_html', 'documento_distribucion_html', 'json_tipo',
        'id_documento', 'id_documento_buzon', 'id_tipo_documento',
        'solicitante_firma_path', 'solicitante_firmado_at',
        'directivo_id', 'directivo_decidido_at', 'directivo_observaciones', 'directivo_firma_path',
        'rrhh_id', 'rrhh_decidido_at', 'rrhh_observaciones', 'rrhh_firma_path',
        'alcalde_id', 'alcalde_decidido_at', 'alcalde_observaciones', 'alcalde_firma_path',
        'documento_pdf_path',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_termino' => 'date',
        'con_goce' => 'boolean',
        'json_tipo' => 'array',
        'paso_actual' => 'integer',
        'solicitante_firmado_at' => 'datetime',
        'directivo_decidido_at' => 'datetime',
        'rrhh_decidido_at' => 'datetime',
        'alcalde_decidido_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(SolTipoDocumento::class, 'sol_tipo_documento_id');
    }

    public function buzonDestino()
    {
        return $this->belongsTo(Buzon::class, 'id_buzon_destino', 'id_buzon');
    }

    public function directivoAsignado()
    {
        return $this->belongsTo(User::class, 'directivo_asignado_id');
    }

    public function directivo()
    {
        return $this->belongsTo(User::class, 'directivo_id');
    }

    public function rrhh()
    {
        return $this->belongsTo(User::class, 'rrhh_id');
    }

    public function alcalde()
    {
        return $this->belongsTo(User::class, 'alcalde_id');
    }

    public function pasos()
    {
        return $this->hasMany(SolSolicitudBuzon::class, 'solicitud_id')->orderBy('orden');
    }

    public function bitacora()
    {
        return $this->hasMany(SolSolicitudBitacora::class, 'solicitud_id')->orderBy('id');
    }

    public function usaFlujoBuzones(): bool
    {
        return $this->pasos()->exists();
    }
}
