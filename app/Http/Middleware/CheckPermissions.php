<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\DB;
use App\Models\UserPermission;
use Auth;

use Closure;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $routes = array(
            'add',
            'create',
        );
        
        $is_admin = auth()->user()->is_admin;
        if($is_admin != 1){

            $routeName = request()->segment(2);
            if(in_array($routeName, $routes)){
                die('You dont have permission to access this page.');
            }
        }
        return $next($request);
    }
}
