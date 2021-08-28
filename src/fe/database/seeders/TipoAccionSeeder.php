<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_tipo_accion' => '1', 'nombre' => 'Requerida', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_accion' => '2', 'nombre' => 'Ejecutada', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('tipo_accion')->insert($records);
    }
}
