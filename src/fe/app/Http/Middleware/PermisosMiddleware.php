<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermisosMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $tipo)
    {
        $id = $request->route('id');
        switch($tipo){
            case 'buzoncarpeta':
                if(Auth::user()->id_perfil == 1){
                    return $next($request);
                }else{
                    //ver si ese usuario tiene permisos para acceder a esa carpeta
                    $res = \App\Models\Buzon::find($id)->usuarios_asignados()->get();
                    if(!$res->contains('id_usuario', Auth::user()->id)){
                        toast('No tienes permisos para realizar esta acción', 'error');
                        return redirect()->route('panel.index');
                    }
                }
                break;
            case 'admin':
                if(Auth::user()->id_perfil != 1){
                    toast('No tienes permisos para realizar esta acción', 'error');
                    return redirect()->route('panel.index');
                }
            
        }
        return $next($request);
    }
}
