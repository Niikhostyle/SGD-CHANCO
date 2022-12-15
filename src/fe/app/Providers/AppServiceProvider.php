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
                                [
                                    'text'       => 'Auditoría de Folios',
                                    'icon'    => 'fas fa-fw fa-check-double text-blue',
                                    'icon_color' => '',
                                    'url'        => '/auditoria_folios',
                                ],
                                //[
                                  //  'text'       => 'Descargas',
                                    //'icon'    => 'fas fa-fw fa-download text-blue',
                                    //'icon_color' => 'blue',
                                    //'url'        => '/descargas',
                                //],
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
                  $icon = 'fas fa-fw fa-archive';
                  $seleccion_color= array_rand($colores,1);
                  if($value['nombre_buzon']=='Personal'){
                    $icon = 'fas fa-fw fa-clipboard';
                    $color_icono = $color_personal;
                  }else{
                    $color_icono = $colores[$seleccion_color];
                  }
                  //perfil externo
                  if(Auth::user()->id_perfil==3)
                  {
                    if($value['n_docs_por_recibir']>0){
                        array_push($submenu_buzones_usuarios,
                        [
                                'text'       => $value['nombre_buzon'],
                                'icon'    => $icon,
                                'icon_color' => $color_icono,
                                'url'        => '/buzonesCarpetasExterno'.'/'.$value['id_buzon'],
                                'label'     => $value['n_docs_por_recibir'],
                                'label_color'=>'success'
                        ]);
                      }else{
                        array_push($submenu_buzones_usuarios,
                        [
                                'text'       => $value['nombre_buzon'],
                                'icon'    => $icon,
                                'icon_color' => $color_icono,
                                'url'        => '/buzonesCarpetasExterno'.'/'.$value['id_buzon']
                        ]);
                      }

                  }
                  //perfil funcionario
                  if(Auth::user()->id_perfil==2)
                  {
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
                  //perfil administrador
                  if(Auth::user()->id_perfil==1)
                  {
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



            }

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
                            'url'        => '/favoritos',
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
