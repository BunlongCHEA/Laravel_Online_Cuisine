<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuditLogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            $user = Auth::user();

            // Log user action
            AuditLog::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
                'action' => $request->method() . ' ' . $request->path(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->header('User-Agent'),
                'status' => 'success', // Indicates authenticated activity
            ]);

            // Log::info('User Activity Logged', [
            //     'user_id' => $user->id,
            //     'email' => $user->email,
            //     'ip' => $request->ip(),
            //     'action' => $request->method() . ' ' . $request->path(),
            //     'url' => $request->fullUrl(),
            //     'user_agent' => $request->header('User-Agent'),
            // ]);

        } else {
            // Log unauthorized access attempts
            AuditLog::create([
                'user_id' => $user->id ?? null,
                'email' => $user->email ?? 'Guest',
                'ip_address' => $request->ip(),
                'action' => $request->method() . ' ' . $request->path(),
                'url' => $request->fullUrl(),
                'user_agent' => $request->header('User-Agent'),
                'status' => 'unauthorized',
            ]);

            // Log::warning('Unauthorized access attempt', [
            //     'ip' => $request->ip(),
            //     'action' => $request->method() . ' ' . $request->path(),
            //     'url' => $request->fullUrl(),
            //     'user_agent' => $request->header('User-Agent'),
            // ]);
        }

        return $next($request);
    }
}
