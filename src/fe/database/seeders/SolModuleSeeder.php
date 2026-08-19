<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SolModuleSeeder extends Seeder
{
    public function run()
    {
        $tipos = [
            ['tipo_solicitud' => 'dias_administrativos', 'nombre' => 'Días administrativos', 'cuerpo' => '<p>Yo, {{nombre}}, RUN {{run}}, solicito días administrativos desde {{fecha_inicio}} hasta {{fecha_termino}} ({{total_dias}} días). Motivo: {{motivo}}.</p>'],
            ['tipo_solicitud' => 'feriados_legales', 'nombre' => 'Feriados legales', 'cuerpo' => '<p>Yo, {{nombre}}, solicito feriado legal desde {{fecha_inicio}} hasta {{fecha_termino}} ({{total_dias}} días).</p>'],
            ['tipo_solicitud' => 'dias_compensatorios', 'nombre' => 'Días compensatorios', 'cuerpo' => '<p>Yo, {{nombre}}, solicito días compensatorios desde {{fecha_inicio}} hasta {{fecha_termino}} ({{total_dias}} días). Ref: {{explicacion}}.</p>'],
            ['tipo_solicitud' => 'licencia_medica', 'nombre' => 'Licencia médica', 'cuerpo' => '<p>Informo licencia médica desde {{fecha_inicio}} hasta {{fecha_termino}}.</p>'],
            ['tipo_solicitud' => 'viaticos', 'nombre' => 'Viáticos', 'cuerpo' => '<p>Solicito viáticos a {{viaticos_destino}} desde {{fecha_inicio}} hasta {{fecha_termino}}.</p>'],
        ];

        foreach ($tipos as $t) {
            $exists = DB::table('sol_tipo_documentos')
                ->where('tipo_solicitud', $t['tipo_solicitud'])
                ->whereNull('regimen_laboral')
                ->exists();
            if (!$exists) {
                $row = [
                    'tipo_solicitud' => $t['tipo_solicitud'],
                    'regimen_laboral' => null,
                    'nombre' => $t['nombre'],
                    'activo' => true,
                    'plantilla_cuerpo_html' => $t['cuerpo'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (Schema::hasColumn('sol_tipo_documentos', 'categoria')) {
                    $row['categoria'] = $t['tipo_solicitud'] === 'feriados_legales'
                        ? 'vacaciones'
                        : ($t['tipo_solicitud'] === 'viaticos'
                            ? 'viaticos'
                            : ($t['tipo_solicitud'] === 'licencia_medica'
                                ? 'licencias'
                                : ($t['tipo_solicitud'] === 'dias_compensatorios' ? 'compensatorios' : 'dias')));
                    $row['consume_saldo'] = in_array($t['tipo_solicitud'], ['dias_administrativos', 'feriados_legales', 'dias_compensatorios'], true);
                    $row['requiere_fe'] = true;
                    $row['numero_firmas'] = 1;
                    $row['primer_buzon_editable'] = true;
                }
                DB::table('sol_tipo_documentos')->insert($row);
            }
        }

        // Admin SGD (id=1) como admin_solicitudes con FirmaGob
        if (DB::table('users')->where('id', 1)->exists()) {
            DB::table('sol_usuario_rol')->updateOrInsert(
                ['user_id' => 1],
                [
                    'rol' => 'admin_solicitudes',
                    'firmagob_enabled' => true,
                    'regimen_laboral' => 'administrativo',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            DB::table('sol_saldos_anuales')->updateOrInsert(
                ['user_id' => 1, 'anio' => (int) date('Y')],
                [
                    'dias_administrativos' => 6,
                    'feriados_legales' => 15,
                    'dias_compensatorios' => 5,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        // Nicolas Figueroa si existe
        $nico = DB::table('users')->where('email', 'nfigueroa@chanco.cl')->first();
        if ($nico) {
            DB::table('sol_usuario_rol')->updateOrInsert(
                ['user_id' => $nico->id],
                [
                    'rol' => 'admin_solicitudes',
                    'firmagob_enabled' => true,
                    'regimen_laboral' => 'administrativo',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
            DB::table('sol_saldos_anuales')->updateOrInsert(
                ['user_id' => $nico->id, 'anio' => (int) date('Y')],
                [
                    'dias_administrativos' => 6,
                    'feriados_legales' => 15,
                    'dias_compensatorios' => 5,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        if (!DB::table('sol_cargos')->where('nombre', 'Funcionario')->exists()) {
            DB::table('sol_cargos')->insert(['nombre' => 'Funcionario', 'created_at' => now(), 'updated_at' => now()]);
            DB::table('sol_cargos')->insert(['nombre' => 'Directivo', 'created_at' => now(), 'updated_at' => now()]);
        }

        if (!DB::table('sol_departamentos')->where('nombre', 'Dirección de Administración')->exists()) {
            DB::table('sol_departamentos')->insert([
                'nombre' => 'Dirección de Administración',
                'directivo_id' => $nico->id ?? 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (Schema::hasTable('sol_configuraciones') && Schema::hasTable('buzon')) {
            $now = now();
            if (!DB::table('sol_configuraciones')->where('clave', 'buzon_alcalde_id')->exists()) {
                $alcaldeId = DB::table('buzon')->whereNull('deleted_at')->whereRaw('lower(nombre) = ?', ['alcalde'])->value('id_buzon');
                DB::table('sol_configuraciones')->insert([
                    'clave' => 'buzon_alcalde_id',
                    'valor' => $alcaldeId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            if (!DB::table('sol_configuraciones')->where('clave', 'buzon_rrhh_id')->exists()) {
                $rrhhId = DB::table('buzon')->whereNull('deleted_at')->whereRaw("nombre ilike ?", ['%departamento de personal%'])->value('id_buzon');
                if (!$rrhhId) {
                    $rrhhId = DB::table('buzon')->whereNull('deleted_at')->whereRaw("nombre ilike ?", ['%recursos humanos%'])->value('id_buzon');
                }
                DB::table('sol_configuraciones')->insert([
                    'clave' => 'buzon_rrhh_id',
                    'valor' => $rrhhId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
