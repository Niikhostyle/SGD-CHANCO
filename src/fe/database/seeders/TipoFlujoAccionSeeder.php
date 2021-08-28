<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoFlujoAccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_tipo_flujo_accion' => '1', 'id_tipo_flujo' => '2', 'id_accion' => '3', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo_accion' => '2', 'id_tipo_flujo' => '2', 'id_accion' => '5', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo_accion' => '3', 'id_tipo_flujo' => '2', 'id_accion' => '6', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo_accion' => '4', 'id_tipo_flujo' => '2', 'id_accion' => '7', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo_accion' => '5', 'id_tipo_flujo' => '2', 'id_accion' => '8', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo_accion' => '6', 'id_tipo_flujo' => '2', 'id_accion' => '9', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo_accion' => '7', 'id_tipo_flujo' => '3', 'id_accion' => '3', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo_accion' => '8', 'id_tipo_flujo' => '3', 'id_accion' => '5', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo_accion' => '9', 'id_tipo_flujo' => '3', 'id_accion' => '6', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo_accion' => '10', 'id_tipo_flujo' => '3', 'id_accion' => '7', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo_accion' => '11', 'id_tipo_flujo' => '3', 'id_accion' => '8', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_flujo_accion' => '12', 'id_tipo_flujo' => '3', 'id_accion' => '9', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('tipo_flujo_accion')->insert($records);
    }
}
