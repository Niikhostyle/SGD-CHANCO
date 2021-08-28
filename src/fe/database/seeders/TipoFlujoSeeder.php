<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoFlujoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_tipo_flujo' => '1', 'nombre' => 'Libre', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo' => '2', 'nombre' => 'Controlado', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo' => '3', 'nombre' => 'Mixto', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('tipo_flujo')->insert($records);

    }
}
