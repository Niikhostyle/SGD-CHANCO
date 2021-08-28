<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAvanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_tipo_avance' => '1', 'nombre' => 'Unidireccional', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_avance' => '2', 'nombre' => 'Unidireccional con reinicio', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_avance' => '3', 'nombre' => 'Bidireccional', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_avance' => '4', 'nombre' => 'Bidireccional con reinicio', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('tipo_avance')->insert($records);
    }
}
