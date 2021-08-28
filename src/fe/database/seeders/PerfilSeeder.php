<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PerfilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_perfil' => '1', 'nombre' => 'Administrador', 'updated_at' => now(), 'created_at' => now()],
            ['id_perfil' => '2', 'nombre' => 'Funcionario', 'updated_at' => now(), 'created_at' => now()],
            ['id_perfil' => '3', 'nombre' => 'Externo', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('perfil')->insert($records);
    }
}
