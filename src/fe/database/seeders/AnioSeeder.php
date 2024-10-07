<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    
        $records = [
            ['id_anio' => date('Y'), 'descripcion' => date('Y'), 'estado' => '1', 'updated_at' => now(), 'created_at' => now()],
        ];

        DB::table('anio')->insert($records);
    }
}
