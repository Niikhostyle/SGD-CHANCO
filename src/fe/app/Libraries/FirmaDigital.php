<?php

namespace App\Libraries;
use FirmaDigitalBase;

class FirmaDigital //extends FirmaDigitalBase
{
    /**
     * FirmaDigital constructor.
     *
     * @param array $config configuration params
     *
     * @throws Exception
     */
    public function __construct($config)
    {
        //parent::__construct($config);

        // Remplaza usuario firmante por usuario de prueba,
        // cuando la configuración de la firma es de test.
        if ($this->tokenKey == 'sandbox') {
            $this->run = '22222222';
        }
    }

    public $key='test_key';

function getConfig(){
    return $this->key;
}

    /**
     * Set user RUN
     *
     * @param string $run
     *
     * @return FirmaDigital
     */
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
                array_merge($data, array('layout' => $layout));
            }
        } catch (Exception $e) {
            throw $e;
        }
        array_push($this->files, $data);

        return $this;
    }


    /**
     * Sign PDF files
     *
     * @return array
     */
    public function sign()
    {
        $data = array(
            'api_token_key' => $this->tokenKey,
            'token'         => $this->generateToken(),
            'files'         => $this->files
        );
        //$this->removeFiles();

        return json_decode(
            json_encode(RestCurl::post($this->api, $data)), true
        );
    }


    /**
     * Generate JWT HS256 token
     *
     * @return string
     */
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
}