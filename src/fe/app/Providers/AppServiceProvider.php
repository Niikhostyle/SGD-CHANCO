<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

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
    public function boot()
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
