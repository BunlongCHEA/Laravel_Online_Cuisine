<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class AuthController extends Controller
{
    public function showRegisterForm() {
        return view('auths.register');
    }

    public function register(Request $request) {

        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:6'
        ]);

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('login')->with('success', 'Register Successfully.');
    }

    public function showLoginForm() {
        return view('auths.login');
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (Auth::attempt($request->only('email', 'password'))) {
            // Check the authenticated user's role
            $role = Auth::user()->role;
    
            // return redirect()->route('cuisines.index');
            if ($role === 'admin') {
                return redirect()->route('admins.cuisines.index'); // Admin route
            } elseif ($role === 'user') {
                return redirect()->route('users.cuisines.index'); // User-specific route
            }
    
            // Fallback if role is not recognized
            return redirect()->route('login')->with('error', 'Role not recognized.');
        }
    
        return back()->withErrors([
            'email' => 'Invalid Email.',
            'password' => 'Invalid Password.',
        ]);
    }

    public function logout(Request $request) {
        Auth::logout();
        
        return redirect()->route('login');
    }
}
