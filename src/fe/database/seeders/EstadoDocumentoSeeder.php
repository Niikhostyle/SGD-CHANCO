<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_estado_documento' => '1', 'nombre' => 'Borrador', 'nombre_corto' => 'B', 'codigo_color' => '#FFFFFF', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_documento' => '2', 'nombre' => 'Enviado', 'nombre_corto' => 'E', 'codigo_color' => '#92d36e', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_documento' => '3', 'nombre' => 'Por recibir', 'nombre_corto' => 'PR', 'codigo_color' => '#FFFFFF', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_documento' => '4', 'nombre' => 'Pendiente', 'nombre_corto' => 'P', 'codigo_color' => '#FFFFFF', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_documento' => '5', 'nombre' => 'Respuesta enviada', 'nombre_corto' => 'R', 'codigo_color' => '#FFFFFF', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_documento' => '6', 'nombre' => 'Archivado', 'nombre_corto' => 'A', 'codigo_color' => '#3d8af7', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_documento' => '7', 'nombre' => 'Derivado', 'nombre_corto' => 'D', 'codigo_color' => '#92d36e', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_documento' => '8', 'nombre' => 'En proceso', 'nombre_corto' => 'EP', 'codigo_color' => '#FFFFFF', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_documento' => '9', 'nombre' => 'Firmado', 'nombre_corto' => 'F', 'codigo_color' => '#92d36e', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_documento' => '10', 'nombre' => 'Firmado y derivado', 'nombre_corto' => 'FD', 'codigo_color' => '#92d36e', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_documento' => '11', 'nombre' => 'Visado', 'nombre_corto' => 'V', 'codigo_color' => '#92d36e', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_documento' => '12', 'nombre' => 'Visado y derivado', 'nombre_corto' => 'VD', 'codigo_color' => '#92d36e', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_documento' => '13', 'nombre' => 'Finalizado', 'nombre_corto' => 'T', 'codigo_color' => '#92d36e', 'updated_at' => now(), 'created_at' => now()]           

        ];

        DB::table('estado_documento')->insert($records);
        
    }
}
