<?php

namespace App\Services;

use App\Models\SolSolicitud;
use App\Models\User;
use Dompdf\Dompdf;
use Dompdf\Options;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PdfFirmaService
{
    public function generarPdf(SolSolicitud $solicitud): string
    {
        $user = $solicitud->usuario;
        $html = '<html><head><meta charset="utf-8"><style>
            body{font-family: DejaVu Sans, sans-serif; font-size:12px; color:#222;}
            h1{font-size:16px;} .meta{margin:12px 0;} .box{border:1px solid #ccc; padding:10px; margin-top:12px;}
        </style></head><body>';
        $esc = static function ($v) {
            return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        };
        $html .= '<h1>Solicitud #' . $solicitud->id . ' — ' . $esc($solicitud->tipo_solicitud) . '</h1>';
        $fi = $solicitud->fecha_inicio;
        $ft = $solicitud->fecha_termino;
        $fiFmt = $fi instanceof \DateTimeInterface ? $fi->format('d-m-Y') : (string) $fi;
        $ftFmt = $ft instanceof \DateTimeInterface ? $ft->format('d-m-Y') : (string) $ft;
        $html .= '<div class="meta"><strong>Solicitante:</strong> ' . $esc($user ? $user->nombreCompleto() : '') .
            '<br><strong>RUN:</strong> ' . $esc($user->run ?? '') .
            '<br><strong>Período:</strong> ' . $esc($fiFmt) . ' al ' . $esc($ftFmt) .
            ' (' . (int) $solicitud->total_dias . ' días)' .
            '<br><strong>Estado:</strong> ' . $esc($solicitud->estado) . '</div>';
        $html .= '<div class="box">' . ($solicitud->documento_cuerpo_html ?: '<p>Sin contenido</p>') . '</div>';
        if ($solicitud->documento_distribucion_html) {
            $html .= '<div class="box"><strong>Distribución</strong><br>' . $solicitud->documento_distribucion_html . '</div>';
        }
        $html .= '<div class="meta"><strong>Motivo:</strong> ' . $esc($solicitud->motivo ?? '-') . '</div>';
        $html .= '</body></html>';

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dir = storage_path('app/public/files/solicitudes');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $name = 'solicitud-' . $solicitud->id . '-' . Str::random(8) . '.pdf';
        $path = $dir . DIRECTORY_SEPARATOR . $name;
        file_put_contents($path, $dompdf->output());

        $relative = 'solicitudes/' . $name;
        $solicitud->documento_pdf_path = $relative;
        $solicitud->save();

        return $relative;
    }

    public function firmarConFirmaGob(SolSolicitud $solicitud, int $idUsuarioFirmante, string $sessionKey): string
    {
        if (!$solicitud->documento_pdf_path) {
            $this->generarPdf($solicitud);
            $solicitud->refresh();
        }

        $abs = storage_path('app/public/files/' . $solicitud->documento_pdf_path);
        if (!is_file($abs)) {
            throw new Exception('No existe el PDF de la solicitud para firmar.');
        }

        $pdfBase64 = base64_encode(file_get_contents($abs));
        $apiFirma = rtrim(env('API_SGD_FIRMA', 'http://sgd_ms_firma:3333'), '/');

        $response = Http::withHeaders([
            'key' => $sessionKey,
            'Content-Type' => 'application/json',
        ])->timeout(120)->put($apiFirma . '/api/sgd-firma/firmar_pdf', [
            'id_usuario' => $idUsuarioFirmante,
            'pdf_base64' => $pdfBase64,
            'nombre_salida' => 'solicitud-' . $solicitud->id . '-firmado-' . time() . '.pdf',
            'carpeta' => 'solicitudes',
        ]);

        if ($response->failed()) {
            throw new Exception('Error FirmaGob: ' . $response->body());
        }

        $json = $response->json();
        if (empty($json['path']) && empty($json['pdf_base64'])) {
            throw new Exception('Respuesta de firma inválida.');
        }

        if (!empty($json['pdf_base64'])) {
            $name = $json['nombre'] ?? ('solicitud-' . $solicitud->id . '-firmado.pdf');
            $rel = 'solicitudes/' . basename($name);
            file_put_contents(storage_path('app/public/files/' . $rel), base64_decode($json['pdf_base64']));
            $solicitud->documento_pdf_path = $rel;
        } else {
            $solicitud->documento_pdf_path = ltrim($json['path'], '/');
        }
        $solicitud->save();

        return $solicitud->documento_pdf_path;
    }
}
