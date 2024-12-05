<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // in_array(Auth::user()->role, ['admin', 'user'])
        if(Auth::check() && Auth::user()->role == 'user'){
            return $next($request);
        }

        abort(403, 'Unauthorized access. Admin And User access only');
        // return redirect()->route('login')->withErrors(['access' => 'User access only.']);
    }
}
