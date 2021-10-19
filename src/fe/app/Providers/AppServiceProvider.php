<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Contracts\Events\Dispatcher;
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
                            ],

                    ]
                );
            }
            $submenu_buzones_usuarios=[];

            array_push($submenu_buzones_usuarios,
            [
                'text'       => 'Personal',
                'icon'    => 'fas fa-fw fa-clipboard',
                'icon_color' => 'red',
                'url'        => '#',
                'label'     => 4,
                'label_color'=>'success'
            ]);
            array_push($submenu_buzones_usuarios,
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
            ]);


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
