<?php

namespace App\Libraries;

use Exception;
use JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

require_once('JWT.php');

class FirmaBase //extends FirmaDigitalBase
{   
    const HASH_FILE_ALGO = 'sha256';

    public function __construct($config)
    {
        if (is_array($config)) {
            foreach ($config as $param => $value) {
                if (empty($value)) {
                    Log::error('No se encontro el parámetro '.$param.' en la configuración'); 
                    throw new Exception("No se encontro el parámetro '{$param}' en la configuración");
                }
            }

            $this->api = rtrim($config[ 'api' ], '/');
            $this->tokenKey = trim($config[ 'tokenKey' ]);
            $this->secretKey = trim($config[ 'secretKey' ]);
            $this->purpose = $config[ 'purpose' ];
            $this->entity = $config[ 'entity' ];
            $this->files = array();
            $this->run = '';
        }

    }

    public function setRUN($run)
    {
        // Reemplaza usuario firmante por usuario de prueba,
        // cuando la configuración de la firma es de test.

        if ($this->tokenKey == 'sandbox') {
            $this->run = '22222222';
        } else {
            $this->run = $run;
        }

        return $this;
    }

    public function setOTP($otp)
    {
        $this->otp = $otp;

        return $this;
    }

    public function addPDF($file, $description = '', $layout = null)
    { 
        
        try{

            $obj = $this->getObject($file);

            $data = array(
                'content-type' => $obj['type'],
                'content'      => base64_encode($obj['binary']),
                'description'  => $description,
                'checksum'     => $obj['hash'],
            );
            
            if ($layout) {
                
                $layout = $this->generateLayout($layout);

                $aLayout = array('layout' => $layout);

                $resultLayout = array_merge($data, $aLayout);
            }
            
        } catch (Exception $e) {            
            
            throw $e;
        }
        
        array_push($this->files, $resultLayout);

        return $this;
    }

    public function sign()
    {
        $data = array(
            'api_token_key' => $this->tokenKey,
            'token'         => $this->generateToken(),
            'files'         => $this->files
        );
        //$this->removeFiles();

        $salida = Http::withHeaders(['Content-Type'=>'application/json'])
        ->timeout(120)
        ->withBody(json_encode($data), 'json')
        ->post($this->api);

        return $salida;
    }
    
    private function generateToken()
    {
        $payload = array(
            'purpose'    => $this->purpose,
            'entity'     => $this->entity,
            'expiration' => date('Y-m-d\TH:i:s', strtotime('+29 minutes')),
            'run'        => $this->run
        );

        $jwt = new JWT();

        return $jwt->encode($payload, $this->secretKey);
    }

    protected function getObject($filename)
    {
        // file in local storage
        return array(
            'binary' => $this->getFileContent($filename),
            'hash'   => hash_file(self::HASH_FILE_ALGO, $filename),
            'type'   => 'application/pdf'
        );
    }

    protected function getFileContent($filename)
    {
        if ( ! file_exists($filename)) {     
            Log::error('Archivo no encontrado '.$filename);       
            throw new Exception("Archivo no encontrado.");
        }
        $file_handler = fopen($filename, 'r');
        if ( ! $file_handler) {
            Log::error('No se puede acceder al archivo '.$filename);       
            throw new Exception("No se puede acceder al archivo");
        }
        $fileContent = fread($file_handler, filesize($filename));
        fclose($file_handler);

        return $fileContent;
    }

    protected function encodeFile($file)
    {
        try {
            $binary = $this->getFileContent($file);

            return base64_encode($binary);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function removeFiles()
    {
        $this->files = array();

        return $this;
    }

    protected function generateLayout($config)
    {
        try {
            $encodedFile = $this->encodeFile($config['filename']);
            //$encodedFile = base64_encode('/src/storage/logo-min.png');
        } catch (Exception $e) {
            throw $e;
        }

        return "<AgileSignerConfig><Application id=\"THIS-CONFIG\"><pdfPassword/><Signature>".
            "<Visible active=\"true\" layer2=\"false\" label=\"true\" pos=\"1\">".
            "<llx>{$config['llx']}</llx><lly>{$config['lly']}</lly><urx>{$config['urx']}</urx><ury>{$config['ury']}</ury>".
            "<page>{$config['page']}</page><image>BASE64</image><BASE64VALUE>{$encodedFile}</BASE64VALUE>".
            "</Visible></Signature></Application></AgileSignerConfig>";
    }

}