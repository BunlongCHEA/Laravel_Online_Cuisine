<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for Authorization header
        // Example: Authorization: Basic dXNlckBleGFtcGxlLmNvbTpwYXNzd29yZA==.
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Basic ')) {
            return response()->json(['message' => 'Unauthorized. Incorrect authorization header'], 401);
        }

        // Decode the Basic Auth credentials
        // The string after Basic is Base64-encoded.
        // The code removes the Basic prefix and decodes the Base64 string.
        // It splits the decoded string into email and password using the colon (:) delimiter.
        $encodeCredentials = substr($authHeader, 6);
        $decodeCredentials = base64_decode($encodeCredentials);
        [$email, $password] = explode(':', $decodeCredentials);

        // Fetch user from the database
        $user = User::where('email', $email)->where('role', 'admin')->first();

        // If the user does not exist or the password does not match (using Laravel's Hash::check), the request is rejected with a 401 Unauthorized respons
        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['message' => 'Unauthorized. Incorrect user Email Or Password Or Not admin role'], 401);
        }

        return $next($request);
    }
}
