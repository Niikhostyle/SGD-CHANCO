<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EstadoTramitacion;

class EstadoTramitacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $records = [
        //     ['id' => '1', 'nombre' => 'En Edición'],
        //     ['id' => '2', 'nombre' => 'En Visación'],
        //     ['id' => '3', 'nombre' => 'En Proceso de Firma'],
        //     ['id' => '4', 'nombre' => 'Finalizado'],
        // ];

        // DB::table('estado_tramitacion')->insert($records);

        $estados = [
            'En Edición',
            'En Visación',
            'En Proceso de Firma',
            'Finalizado',
        ];
        foreach ($estados as $estado) {
            EstadoTramitacion::create([
                'nombre' => $estado,
            ]);
        }
    }
}
