<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PerfilSeeder::class,
            EstadoUsuarioSeeder::class,
            TipoOrigenSeeder::class,
            TipoAvanceSeeder::class,
            TipoFolioSeeder::class,
            TipoAsignacionFolioSeeder::class,
            NivelAccesoSeeder::class,
            TipoBuzonSeeder::class,
            TipoArchivoSeeder::class,
            CarpetaSeeder::class,
            EstadoDocumentoSeeder::class,
            TipoDestinoSeeder::class,
            TipoAccionSeeder::class,
            TipoFlujoSeeder::class,
            AccionSeeder::class,
            TipoFlujoAccionSeeder::class,
            UsersSeeder::class,
            TipoFirmaSeeder::class,
            AnioSeeder::class,
            EstadoTramitacionSeeder::class,
            SolModuleSeeder::class,
        ]);

    }
}
