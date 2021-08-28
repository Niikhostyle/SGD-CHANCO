<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDestinoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_tipo_destino' => '1', 'nombre' => 'Principal', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_destino' => '2', 'nombre' => 'Secundario', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('tipo_destino')->insert($records);
    }
}
