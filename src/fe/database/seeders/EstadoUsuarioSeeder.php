<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_estado_usuario' => '1', 'nombre' => 'Activo', 'updated_at' => now(), 'created_at' => now()],
            ['id_estado_usuario' => '2', 'nombre' => 'Inactivo', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('estado_usuario')->insert($records);
    }
}
