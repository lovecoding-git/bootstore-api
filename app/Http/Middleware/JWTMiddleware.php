<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException){
                return response()->preferredFormat(['status' => 'false', 'message'=> 'Token is Invalid']);
            }else if ($e instanceof TokenExpiredException){
                return response()->preferredFormat(['status' => 'false', 'message'=>'Token is Expired']);
            }else{
                return response()->preferredFormat(['status'  => 'false', 'message'=> 'Authorization Token not found']);
            }
        }
        return $next($request);
    }
}
