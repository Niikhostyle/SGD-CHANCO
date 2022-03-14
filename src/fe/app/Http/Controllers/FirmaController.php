<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Libraries\PaymentHelper;
use App\Libraries\FirmaBase;


class FirmaController 
{
    public function index()
    {
        
        $firmaDigitalConfig = array(
            'api'       => config('app.sgd_url'),
            'purpose'   => config('app.sgd_proposito'),
            'entity'    => config('app.sgd_entidad'),
            'tokenKey'  => config('app.sgd_token_key'),
            'secretKey' => config('app.sgd_secreto')
        );

        $classFirma = new FirmaBase($firmaDigitalConfig);
        //http://192.168.1.101:82/imagenes/TDXY-220-20220201-95947609
        //principal_220_.pdf

        $resp = $classFirma//->setRUN('22222222')                        //->generateToken();
                    ->addPDF(storage_path('app/public/files/principal_220_.pdf'), 'descripcion de prueba');
                   // ->sign();

        return $resp;                    

        return $classFirma->getConfig();
        //return $classFirma->setRUN('13319766');


       // $this->load->library("segpres/v{$apiVersion}/FirmaDigital", $firmaDigitalConfig, 'FirmaDigital');
        
        //return config('app.sgd_entidad');

        //$Payment = new PaymentHelper();

       // return $Payment->getConfig();

    }
}
