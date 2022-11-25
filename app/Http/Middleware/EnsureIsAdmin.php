<?php

namespace App\Http\Middleware;

use App\Custom\CustomResponse;
use Closure;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->user()) {
            if($request->user()->is_admin) {
                return $next($request);
            }
        }
        throw new Error("Vous n'avez le droit d'opérer cette action...");
    }
}
