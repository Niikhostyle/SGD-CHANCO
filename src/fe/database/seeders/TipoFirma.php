<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoFirmaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_tipo_firma' => '1', 'nombre' => 'Titular', 'sigla' => '', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_firma' => '2', 'nombre' => 'Subrogante', 'sigla' => '(S)', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('tipo_firma')->insert($records);

    }
}
