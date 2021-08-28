<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarpetaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_carpeta' => '1', 'nombre' => 'Por Recibir', 'updated_at' => now(), 'created_at' => now()],
            ['id_carpeta' => '2', 'nombre' => 'Recibidos', 'updated_at' => now(), 'created_at' => now()],
            ['id_carpeta' => '3', 'nombre' => 'Despachados', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('carpeta')->insert($records);
    }
}
