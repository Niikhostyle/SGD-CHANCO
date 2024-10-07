<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->create([
            'id_perfil' => '1', 
            'id_estado_usuario' => '1', 
            'run' => '12345678-5', 
            'nombres' => 'Administrador SGD', 
            'email' => 'admin@mail.com', 
            'password' => 'Secreto.123',
        ]);
    }
}
