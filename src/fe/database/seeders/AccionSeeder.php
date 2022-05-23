<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_accion' => '1', 'id_tipo_accion' => '2', 'nombre' => 'Crear', 'updated_at' => now(), 'created_at' => now()],
            ['id_accion' => '2', 'id_tipo_accion' => '2', 'nombre' => 'Derivar', 'updated_at' => now(), 'created_at' => now()],
            ['id_accion' => '3', 'id_tipo_accion' => '2', 'nombre' => 'Recibir documento', 'updated_at' => now(), 'created_at' => now()],
            ['id_accion' => '4', 'id_tipo_accion' => '1', 'nombre' => 'Editar', 'updated_at' => now(), 'created_at' => now()],
            ['id_accion' => '5', 'id_tipo_accion' => '2', 'nombre' => 'Subir archivo principal', 'updated_at' => now(), 'created_at' => now()],
            ['id_accion' => '6', 'id_tipo_accion' => '1', 'nombre' => 'Visar', 'updated_at' => now(), 'created_at' => now()],
            ['id_accion' => '7', 'id_tipo_accion' => '1', 'nombre' => 'Firmar (FEA)', 'updated_at' => now(), 'created_at' => now()],
            ['id_accion' => '8', 'id_tipo_accion' => '1', 'nombre' => 'Generar PDF', 'updated_at' => now(), 'created_at' => now()],
            ['id_accion' => '9', 'id_tipo_accion' => '1', 'nombre' => 'Asignar Folio y Fecha', 'updated_at' => now(), 'created_at' => now()],
            ['id_accion' => '10', 'id_tipo_accion' => '1', 'nombre' => 'Finalizar', 'updated_at' => now(), 'created_at' => now()],
            ['id_accion' => '11', 'id_tipo_accion' => '1', 'nombre' => 'No corresponde, no aplica, revisar, reenviar', 'updated_at' => now(), 'created_at' => now()],            
            ['id_accion' => '12', 'id_tipo_accion' => '2', 'nombre' => 'Archivar', 'updated_at' => now(), 'created_at' => now()],
            ['id_accion' => '13', 'id_tipo_accion' => '2', 'nombre' => 'En proceso firma', 'updated_at' => now(), 'created_at' => now()],
            ['id_accion' => '14', 'id_tipo_accion' => '2', 'nombre' => 'Desarchivar', 'updated_at' => now(), 'created_at' => now()]            
        ];

        DB::table('accion')->insert($records);

    }
}
