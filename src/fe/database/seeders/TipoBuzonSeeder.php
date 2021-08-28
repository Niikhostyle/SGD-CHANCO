<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoBuzonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_tipo_buzon' => '1', 'nombre' => 'Personal', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_buzon' => '2', 'nombre' => 'Grupal', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('tipo_buzon')->insert($records);
    }
}
