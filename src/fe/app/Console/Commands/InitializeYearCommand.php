<?php

namespace App\Console\Commands;

use App\Models\Anio;
use App\Models\TipoDocumento;
use App\Models\TipoDocumentoBuzonFolio;
use Illuminate\Console\Command;

class InitializeYearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'year:initialize {year? : El año a inicializar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializar el sistema para un nuevo año';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $year = $this->argument('year') ?? date('Y');


        $anio = Anio::where('id_anio', $year)->first();
        if ($anio) {
            $this->error("El año {$year} ya está inicializado.");
            return 0;
        }

        $this->info("Iniciando año {$year}...");

        try {
            // Crear registro en la tabla anio
            $anio = Anio::updateOrCreate(
                ['id_anio' => $year],
                [
                    'id_anio' => $year,
                    'descripcion' => "{$year}",
                    'estado' => 1
                ]
            );

            $this->info("✓ Año {$year} registrado en la tabla anio");
            $this->info("✓ Año {$year} inicializado correctamente");

            //continuar los contadores con tipo de asignacion de folio 4 (continuo)
            $tiposDocumentoId = TipoDocumento::where('id_tipo_folio', 4)
                ->pluck('id_tipo_documento');

            $registros = TipoDocumentoBuzonFolio::whereIn('id_tipo_documento', $tiposDocumentoId)
                ->where('anio', $year - 1)
                ->get();
            
            //dd($registros->toArray());
            foreach ($registros as $registro) {
                TipoDocumentoBuzonFolio::updateOrCreate(
                    [
                        'id_tipo_documento' => $registro->id_tipo_documento,
                        'id_buzon' => null,
                        'anio' => $year
                    ],
                    [
                        'id_tipo_documento' => $registro->id_tipo_documento,
                        'id_buzon' => null,
                        'anio' => $year,
                        'valor' => $registro->valor,
                    ]
                );
            }

            $this->info("✓ Contadores de folios continuados para el año {$year}");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error al inicializar el año: " . $e->getMessage());
            return 1;
        }
    }
}
