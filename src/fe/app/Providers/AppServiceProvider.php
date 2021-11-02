<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Http;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {

        view()->composer('*', function($view)
        {
            if (Auth::check()) {
                $sesion_key=0;
                $sesion = DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', Auth::user()->id
                    )
                ->orderBy('last_activity', 'desc')
                ->get();

                if(isset($sesion[0]->id)){
                    $sesion_key= $sesion[0]->id;
                    Config::set('sesion', $sesion_key);
                }
                $view->with('sesion_key', $sesion_key);
            }else {
                $view->with('sesion_key', null);
            }
        });

        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            if(Auth::user()->id_perfil==1){
                $event->menu->add(
                    [

                            'text' => 'ADMINISTRACIÓN',
                            'icon'    => 'fas fa-users-cog',
                            'submenu' => [
                                [
                                    'text'       => 'Usuarios',
                                    'icon'    => 'fas fa-fw fa-user',
                                    'icon_color' => 'red',
                                    'url'        => '/usuarios',
                                ],
                                [
                                    'text'       => 'Buzones',
                                    'icon'    => 'fas fa-fw fa-th-list',
                                    'icon_color' => 'yellow',
                                    'url'        => '/buzones',
                                ],
                                [
                                    'text'       => 'Tipos de Documentos',
                                    'icon'    => 'fas fa-fw fa-folder-open text-green',
                                    'icon_color' => 'green',
                                    'url'        => '/tipos_documentos',
                                ],
                            ],

                    ]
                );
            }
            $submenu_buzones_usuarios=[];

            $sesion_key =  AppServiceProvider::session_key_general();

            $menuBuzon = Http::withHeaders(['key'=>$sesion_key,'Content-Type'=>'application/json']) //
            ->timeout(30)
            ->withBody(json_encode([
                'id_usuario' => Auth::user()->id,
            ]), 'json')
            ->get('http://sgd_ms_buzones:3333/api/sgd-buzones/menu');

            if($menuBuzon->failed()){

            }else{

                $buzones = $menuBuzon['data'];
                $colores = ['red','yellow','cyan','purple', 'blue', 'orange'];
                $color_personal='green';
                foreach ($buzones as $key => $value)
                {
                    /*"id_buzon": 3,
                    "nombre_buzon": "Personal",
                    "nombre_corto_buzon": "PRSNAL",
                    "tipo_buzon": 1,
                    "n_docs_por_recibir": 4,
                    "n_docs_recibidos_pendientes": 2*/
                  $icon = 'fas fa-fw fa-archive';
                  $seleccion_color= array_rand($colores,1);
                  if($value['nombre_buzon']=='Personal'){
                    $icon = 'fas fa-fw fa-clipboard';
                    $color_icono = $color_personal;
                  }else{
                    $color_icono = $colores[$seleccion_color];
                  }

                  if($value['n_docs_por_recibir']>0){
                    array_push($submenu_buzones_usuarios,
                    [
                            'text'       => $value['nombre_buzon'],
                            'icon'    => $icon,
                            'icon_color' => $color_icono,
                            'url'        => '/buzonesCarpetas'.'/'.$value['id_buzon'],
                            'label'     => $value['n_docs_por_recibir'],
                            'label_color'=>'success'
                    ]);
                  }else{
                    array_push($submenu_buzones_usuarios,
                    [
                            'text'       => $value['nombre_buzon'],
                            'icon'    => $icon,
                            'icon_color' => $color_icono,
                            'url'        => '/buzonesCarpetas'.'/'.$value['id_buzon']
                    ]);
                  }

                }



            }


            /*array_push($submenu_buzones_usuarios,
            [
                'text'       => 'Tránsito',
                    'icon'    => 'fas fa-fw fa-archive',
                    'icon_color' => 'yellow',
                    'url'        => '#',
                    'label'     => 3,
                    'label_color'=>'success'
            ]);
            array_push($submenu_buzones_usuarios,
            [
                'text'       => 'Alcaldia',
                'icon'    => 'fas fa-fw fa-archive',
                'icon_color' => 'cyan',
                'url'        => '#',
                'label'     => 5,
                'label_color'=>'success'
            ]);*/


            $event->menu->add(
            [
                    'text' => 'BUZONES',
                    'icon'    => 'fas fa-fw fa-th-list',
                    'submenu' => $submenu_buzones_usuarios
            ]
            );
            $event->menu->add(
                [
                    'text' => 'HERRAMIENTAS',
                    'icon'    => 'fas fa-fw fa-cogs',
                    'submenu' => [
                        [
                            'text'       => 'Buscar Documentos',
                            'icon'    => 'fas fa-fw fa-search',
                            'icon_color' => 'red',
                            'url'        => '/buscador',
                        ],
                        [
                            'text'       => 'Favoritos',
                            'icon'    => 'fas fa-fw fa-star',
                            'icon_color' => 'yellow',
                            'url'        => '#',
                        ],
                    ],
                ]
            );
        });

    }
    public static function session_key_general(){
        $sesion_key=0;
        if (Auth::check()) {
            $sesion = DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
            ->where('user_id', Auth::user()->id
                )
            ->orderBy('last_activity', 'desc')
            ->get();

            if(isset($sesion[0]->id)){
                $sesion_key= $sesion[0]->id;
            }
        }
        return $sesion_key;
    }

}
