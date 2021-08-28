<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAsignacionFolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_tipo_asignacion_folio' => '1', 'nombre' => 'Evento Creación', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_asignacion_folio' => '2', 'nombre' => 'Evento Recepción', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_asignacion_folio' => '3', 'nombre' => 'Sin Asignación', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('tipo_asignacion_folio')->insert($records);
    }
}
