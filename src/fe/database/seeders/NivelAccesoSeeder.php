<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NivelAccesoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_nivel_acceso' => '1', 'nombre' => 'Público', 'updated_at' => now(), 'created_at' => now()],
            ['id_nivel_acceso' => '2', 'nombre' => 'Reservado', 'updated_at' => now(), 'created_at' => now()],
            ['id_nivel_acceso' => '3', 'nombre' => 'Confidencial', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('nivel_acceso')->insert($records);
    }
}
