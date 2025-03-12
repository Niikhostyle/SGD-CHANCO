<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DocDigitalController extends Controller
{
    
    private $apiurl; 
    private $token;
    private $tipodocs;
    private $entidades;

    public function __construct()
    {
        $this->apiurl = env("DOCDIGITAL_URL", 'https://api-demodoc.digital.gob.cl/api');
        $url = $this->apiurl."/oauth/token";
        $auth = Http::withBasicAuth(env("DOCDIGITAL_CLIENTID", "demo"), env("DOCDIGITAL_CLIENTSECRET", "demo"))
            ->post($url);
        if($auth->successful()){
            $this->token = json_decode($auth->getBody())->access_token;
        }else{
            throw new Exception("No existe key");
        }
        //cargar entidades del token (las asociadas)
        $td = Http::withToken($this->token)->get($this->apiurl."/entidades/token");
        $this->entidades = json_decode($td->getBody())->result;

        //cargar tipos docs
        $td = Http::withToken($this->token)->get($this->apiurl."/tipos/documentos/");
        $this->tipodocs = json_decode($td->getBody())->result;

    }

    public function index()
    {
        
        return view('docdigital.index');
    }
}
