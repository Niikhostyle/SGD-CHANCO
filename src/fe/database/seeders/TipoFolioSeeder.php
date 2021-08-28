<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoFolioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_tipo_folio' => '1', 'nombre' => 'Por tipo de documento y año', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_folio' => '2', 'nombre' => 'Por tipo de documento, buzón y año', 'updated_at' => now(), 'created_at' => now()],
            ['id_tipo_folio' => '3', 'nombre' => 'Sin Folio', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('tipo_folio')->insert($records);
    }
}
