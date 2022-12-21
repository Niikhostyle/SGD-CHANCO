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
            ['id_anio' => '2022', 'descripcion' => '2022', 'estado' => '1', 'updated_at' => now(), 'created_at' => now()],
            ['id_anio' => '2023', 'descripcion' => '2023', 'estado' => '1', 'updated_at' => now(), 'created_at' => now()]
        ];

        DB::table('anio')->insert($records);
    }
}
