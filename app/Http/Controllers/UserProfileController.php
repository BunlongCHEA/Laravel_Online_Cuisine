<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    public function edit() {
        $authUser = Auth::user();
        $users = [];

        if ($authUser->role === 'admin') {
            // Admin can manage other users
            $users = User::all();
        }
        
        return view('profiles.edit', compact('authUser', 'users'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request, $id) {
        $authUser = Auth::user();
        $user = User::findOrFail($id);

        // instanceof : determine whether a variable ($user in this case) is an object of a specified class or a subclass of that class.
        // This ensures - $user - variable is an instance of the User class (App\Models\User).
        // If - $user - is null or not properly retrieved, calling methods like $user->save() would throw an error
        if ($user instanceof User) {
            
            // Only allow self-updates for users with 'user' role
            if ($authUser->role === 'user' && $authUser->id !== $user->id) {
                return redirect()->route('profile.edit')->withErrors('You are not authorized to update this profile.');
            }

            // Track changes for logging
            $changes = [];

            // Prevent user roles from editing name and password of other users
            if ($authUser->role === 'user') {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'password' => 'nullable|min:6|confirmed',
                ]);

                // If current user name != request name, write record for audit logs
                if ($user->name !== $request->name) {
                    $changes['name'] = ['old' => $user->name, 'new' => $request->name];
                    $user->name = $request->name;
                }

                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }
            }


            // Allow admin roles to edit email, name, and password for themselves and others
            if ($authUser->role === 'admin') {
                $request->validate([
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|unique:users,email,' . Auth::id(),
                    'password' => 'nullable|min:6|confirmed',
                ]);

                // If current user name != request name, write record for audit logs
                if ($user->name !== $request->name) {
                    $changes['name'] = ['old' => $user->name, 'new' => $request->name];
                    $user->name = $request->name;
                }
    
                // If current user email != request email, write record for audit logs
                if ($user->email !== $request->email) {
                    $changes['email'] = ['old' => $user->email, 'new' => $request->email];
                    $user->email = $request->email;
                }
                
                if ($request->filled('password')) {
                    $user->password = Hash::make($request->password);
                }
            }

            $user->save();

            // Log the activity
            AuditLog::create([
                'user_id' => $authUser->id ?? null, // Log `null` for guest users
                'email' => $authUser->email ?? 'Guest',
                'ip_address' => $request->ip(),
                'action' => 'Update',
                'url' => $request->fullUrl(),
                'user_agent' => $request->header('User-Agent'),
                'model' => 'UserProfile',
                'data' => json_encode([
                    'updated_user_id' => $user->id,
                    'updated_user_email' => $user->email,
                    'updated_user_name' => $user->name,
                    'changes' => $changes,
                ]),        
            ]);

            return redirect()->route('profiles.edit')->with('success', 'Profile updated successfully!');
        } else {
            return redirect()->route('profiles.edit')->with('error', 'Unable to update profile!');
        } 
    }
}
