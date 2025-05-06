<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Routing\Controller as BaseController;

class ClaveUnicaController extends BaseController
{

    public function autenticar(Request $request)
    {
        /* Primer paso, redireccionar al login de clave única */
        $url_base       = "https://accounts.claveunica.gob.cl/openid/authorize/";
        $client_id      = env("CLAVEUNICA_CLIENT_ID");
        $redirect_uri   = urlencode(env('APP_URL') . "/claveunica/callback");

        //ver si es denunciante o funcionario
        $stateInfo =[
            "token"=>csrf_token(),
            "tipo"=> 'denuncia'
        ];
        $state             = base64_encode(json_encode($stateInfo));
        $scope          = 'openid run name';

        $params         = '?client_id=' . $client_id .
            '&redirect_uri=' . $redirect_uri .
            '&scope=' . $scope .
            '&response_type=code' .
            '&state=' . $state;

        return redirect()->to($url_base . $params)->send();
    }

    public function callback(Request $request)
    {
        $code   = $request->input('code');
        $state  = $request->input('state');
        
        $url_base       = "https://accounts.claveunica.gob.cl/openid/token/";
        $client_id      = env("CLAVEUNICA_CLIENT_ID",'123456');
        $client_secret  = env("CLAVEUNICA_SECRET_ID",'123456');
        $redirect_uri   = urlencode(env('APP_URL') . "/claveunica/callback");
        $scope = 'openid+run+name';

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('POST', $url_base, [
                'form_params' => [

                    'client_id'     => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri'  => $redirect_uri,
                    'grant_type'    => 'authorization_code',
                    'code'          => $code,
                    'state'         => $state,
                ]
            ])->getBody();
            $token = $response->getContents();

            if (!isset(json_decode($token)->access_token)) {
                return redirect()->route('login');
            }
            $access_token = json_decode($token)->access_token;
            
            //leer variable si es login denuncia o login funcionario
            //decode state para ver que tipo de login recibo
            $state = json_decode(base64_decode($state));
            //dd($access_token);
            $url_base = "https://accounts.claveunica.gob.cl/openid/userinfo/";

            $response = $client->post($url_base, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $access_token,
                ],
            ])->getBody();

            $userClaveUnica = json_decode($response, true);

            $rut = $userClaveUnica["RolUnico"]['numero'] . "-" . $userClaveUnica["RolUnico"]["DV"];
            //$nombre=implode(" ",$userClaveUnica["name"]['nombres'])." ".implode(" ",$userClaveUnica["name"]["apellidos"]);

            //revisar si rut tiene usuario creado
            $dbuser = User::where("rut", $rut)->first();
            //no existe usuario, enviar a pedir correo
            if (!$dbuser) {
                //return view("auth.login")->withErrors(['claveunica'=>'RUT No registrado en el sistema']);
                //return Inertia::render('Auth/Login')->withErrors(['claveunica'=>'RUT No registrado en el sistema']);
            }

            Auth::login($dbuser);
                return redirect()->route('index');
        } catch (\Exception $ex) {
            //return Inertia::render("Auth/Login")->withErrors(['claveunica'=>$ex->getMessage()]);
        }
    }

    public function postlogin(Request $request)
    {
        //registrar usuario con correo
        $datos  =   $request->all();

        $validator = Validator::make(
            $datos,
            [
                'name' => 'required',
                'username' => 'required|unique:App\Models\User,username',
                'email' => 'required|unique:App\Models\User,email|email',
            ],
            [
                "email.required" => "Debe proporcionar un correo electrónico",
                "email.unique" => "Este correo está registrado en el sistema",
            ]
        );

        if ($validator->fails()) {
            //return Inertia::Render("Auth/Register", $datos)->withErrors($validator->errors());
        }

        try {
            DB::beginTransaction();

            $user = new User();
            $user->name = $datos["name"];
            $user->username = $datos["username"];
            $user->email = $datos["email"];
            $user->password = bcrypt(substr(md5(mt_rand()), 0, 7));
            $user->save();
            DB::commit();

            Auth::login($user);
        } catch (\Exception $ex) {
            DB::rollback();
            //session()->flash('mensaje', $ex->getMessage());
            //return Inertia::Render("Auth/Register", $datos)->withErrors($ex->getMessage());
        }
        return redirect()->route("index");
    }

    public function logout()
    {
        /* Nos iremos al cerrar sesión en clave única y luego volvermos a nuestro sistema */
        if (env('APP_ENV') == 'local') {
            /* Si estamos desarrollando cerramos localmente no más */
            return redirect()->route('logout');
        } else {
            /** Cerrar sesión clave única */
            /* Url para cerrar sesión en clave única */
            $url_logout     = "https://accounts.claveunica.gob.cl/api/v1/accounts/app/logout?redirect=";

            /* Url para luego cerrar sesión en nuestro sisetema */
            $url_redirect   = env('APP_URL') . "/logout";
            $url            = $url_logout . urlencode($url_redirect);
            return redirect($url);
        }

        /** REVISAR LoginController, ahí está el logout local */
    }
}
