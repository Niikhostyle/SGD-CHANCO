<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = [
            ['id_perfil' => '2', 'id_estado_usuario' => '1', 'run' => '11111111-1', 'nombres' => 'Administrador', 'email' => 'admin@mail.com', 'password' => '$2y$10$ATn2qykWPjNg2VhQ1q6SMuVT5HYNxZdcsbNGgBwc8o5ccSuF3PEkS','updated_at' => now(), 'created_at' => now()],           
        ];

        DB::table('users')->insert($records);
    }
}
