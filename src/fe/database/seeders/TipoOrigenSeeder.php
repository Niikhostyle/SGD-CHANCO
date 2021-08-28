<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoOrigenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_tipo_origen' => '1', 'nombre' => 'Interno', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_origen' => '2', 'nombre' => 'Externo', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('tipo_origen')->insert($records);

    }
}
