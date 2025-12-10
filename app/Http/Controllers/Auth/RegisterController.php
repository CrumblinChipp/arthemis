<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Campus;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        // Fetch the campuses, just like in your DashboardController
        $campuses = Campus::all(['id', 'name']); 
        
        // Pass them to the view
        return view('auth.login-register', compact('campuses')); // <-- Adjust view name if needed
    }
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sr_code' => 'required|string|max:255|unique:users,sr_code',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'accepted',
            'campus_id' => 'required|integer|exists:campuses,id',

        ]);

        $user = User::create([
            'name' => $validated['name'],
            'sr_code' => $validated['sr_code'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $request->role ?? 'user',
            'campus_id' => $validated['campus_id'],
        ]);

        Auth::login($user);
        return redirect('/dashboard');
    }
}