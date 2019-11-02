<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckApiToken
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
        if(!empty($request->bearerToken())){
            if(User::where('id' , Auth::guard('api')->id())->exists()){
                return $next($request);
            } else
                return response()->json([
                    'success' => false,
                    'error' => 'Для просмотра этой страницы необходимо авторизоваться'
                ], 401);
        }
        return response()->json([
            'success' => false,
            'error' => 'Для просмотра этой страницы необходимо авторизоваться'
        ], 401);
    }
}
