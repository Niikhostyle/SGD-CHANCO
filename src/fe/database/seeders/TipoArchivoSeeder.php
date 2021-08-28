<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoArchivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_tipo_archivo' => '1', 'nombre' => 'Principal', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_archivo' => '2', 'nombre' => 'Anexo', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_archivo' => '3', 'nombre' => 'Otro', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('tipo_archivo')->insert($records);
    }
}
