<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;


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
        \Illuminate\Support\Facades\URL::forceScheme('https');

        view()->composer('adminlte::partials.navbar.menu-item-periodo', function ($view) {
            if (Auth::check()) {
                $datosAnio = \App\Models\Anio::all('id_anio', 'descripcion','estado');
                if (session('year') == null)
                    session(['year' => date('Y')]);
                $view->with('listado_anios', $datosAnio);
            } else {
                $view->with('listado_anios', []);
            }
        });

        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            if (Auth::user()->id_perfil == 1) {
                $event->menu->add(
                    [

                        'text' => 'ADMINISTRACIÓN',
                        'icon'    => 'fas fa-users-cog',
                        'submenu' => [
                            [
                                'text'       => 'Usuarios',
                                'icon'    => 'fas fa-fw fa-user',
                                'icon_color' => 'red',
                                'url'        => route('usuarios.index') //'/usuarios',
                            ],
                            [
                                'text'       => 'Buzones',
                                'icon'    => 'fas fa-fw fa-th-list',
                                'icon_color' => 'yellow',
                                'url'        => route('buzones.index') //'/buzones',
                            ],
                            [
                                'text'       => 'Tipos de Documentos',
                                'icon'    => 'fas fa-fw fa-folder-open text-green',
                                'icon_color' => 'green',
                                'url'        => route('tipos_documentos.index') //'/tipos_documentos',
                            ],
                            [
                                'text'       => 'Auditoría de Folios',
                                'icon'    => 'fas fa-fw fa-check-double text-blue',
                                'icon_color' => '',
                                'url'        => route('auditoria_folios.index') //'/auditoria_folios',
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
            $submenu_buzones_usuarios = [];

           // $sesion_key =  AppServiceProvider::session_key_general();
           $sesion_key = session()->getId();

            $menuBuzon = Http::withHeaders(['key' => $sesion_key, 'Content-Type' => 'application/json']) //
                ->timeout(30)
                ->withBody(json_encode([
                    'id_usuario' => Auth::user()->id,
                    'year_actual' => session('year'),
                ]), 'json')
                ->get(config('sgd.api_buzones').'/api/sgd-buzones/menu');
            
            //dd($menuBuzon["data"]);    

            if ($menuBuzon->failed()) {
            
            } else {

                $buzones = $menuBuzon['data'];
                $colores = ['red', 'yellow', 'cyan', 'purple', 'blue', 'orange'];
                $color_personal = 'green';
                foreach ($buzones as $key => $value) {
                    $icon = 'fas fa-fw fa-archive';
                    $seleccion_color = array_rand($colores, 1);
                    if ($value['nombre_buzon'] == 'Personal') {
                        $icon = 'fas fa-fw fa-clipboard';
                        $color_icono = $color_personal;
                    } else {
                        $color_icono = $colores[$seleccion_color];
                    }
                    //perfil externo
                    if (Auth::user()->id_perfil == 3) {
                        array_push(
                            $submenu_buzones_usuarios,
                            [
                                'id'    => $value['id_buzon'],
                                'text'       => $value['nombre_buzon'],
                                'icon'    => $icon,
                                'icon_color' => $color_icono,
                                'url'        => route('buzones.carpetas',['id'=>$value['id_buzon']]),
                                'label1'     => '',
                                'label1_color' => 'warning',
                                'label2'     => '',
                                'label2_color' => 'success'
                            ]
                        );
                    }
                    //perfil funcionario
                    if (Auth::user()->id_perfil == 2) {

                        array_push(
                            $submenu_buzones_usuarios,
                            [
                                'id'    => $value['id_buzon'],
                                'text'       => $value['nombre_buzon'],
                                'icon'    => $icon,
                                'icon_color' => $color_icono,
                                'url'        => route('buzones.carpetas',['id'=>$value['id_buzon']]),
                                'label1'     => '',
                                'label1_color' => 'warning',
                                'label2'     => '',
                                'label2_color' => 'success'
                            ]
                        );

                    }
                    //perfil administrador
                    if (Auth::user()->id_perfil == 1) { 
                        array_push(
                            $submenu_buzones_usuarios,
                            [
                                'id'    => $value['id_buzon'],
                                'text'       => $value['nombre_buzon'],
                                'icon'    => $icon,
                                'icon_color' => $color_icono,
                                'url'        => route('buzones.carpetas',['id'=>$value['id_buzon']]),
                                'label1'     => '',
                                'label1_color' => 'gradient-lightblue',
                                'label2'     => '',
                                'label2_color' => 'success'
                            ]
                        );
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
                    'text' => 'SOLICITUDES',
                    'icon'    => 'fas fa-fw fa-file-signature',
                    'submenu' => [
                        [
                            'text'       => 'Mis solicitudes',
                            'icon'    => 'fas fa-fw fa-list',
                            'icon_color' => 'blue',
                            'url'        => route('solicitudes.index'),
                        ],
                        [
                            'text'       => 'Nueva solicitud',
                            'icon'    => 'fas fa-fw fa-plus',
                            'icon_color' => 'green',
                            'url'        => route('solicitudes.create'),
                        ],
                        [
                            'text'       => 'RRHH / Saldos',
                            'icon'    => 'fas fa-fw fa-calendar-check',
                            'icon_color' => 'orange',
                            'url'        => route('solicitudes.rrhh'),
                        ],
                        [
                            'text'       => 'Administración',
                            'icon'    => 'fas fa-fw fa-cog',
                            'icon_color' => 'red',
                            'url'        => route('solicitudes.admin'),
                        ],
                    ],
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
                            'url'        => route('buscador.index'), //'/buscador',
                        ],
                        [
                            'text'       => 'Favoritos',
                            'icon'    => 'fas fa-fw fa-star',
                            'icon_color' => 'yellow',
                            'url'        => route('favoritos.index'),
                        ],
                    ],
                ]
            );
        });
    }
    public static function session_key_general()
    {
        $sesion_key = 0;
        if (Auth::check()) {
            $sesion = DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where(
                    'user_id',
                    Auth::user()->id
                )
                ->orderBy('last_activity', 'desc')
                ->get();

            if (isset($sesion[0]->id)) {
                $sesion_key = $sesion[0]->id;
            }
        }
        return $sesion_key;
    }
    public static function session_periodo($year)
    {
        $sesion_periodo = 0;
        if (Auth::check() && isset($year)) {
            $sesion_periodo = $year;
        }
        return $sesion_periodo;
    }
}
